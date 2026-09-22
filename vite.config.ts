/**
 * This project's Vite build — the only one: the engine never builds, and
 * nothing here references "../Skeleton" anymore.
 *
 * Formerly called into the engine's shared factory (Skeleton/vite/config.ts,
 * defineSkeletonConfig), which scanned this root AND a sibling "../Skeleton"
 * checkout together into one manifest. That merge is gone: this theme now
 * owns every block/view entry itself. @shared/@use (the engine's shared JS
 * helpers/hooks) are plain local files now too — copied into
 * resources/scripts/skeleton-shared/ and skeleton-hooks/ (not this project's
 * own resources/scripts/shared/, which is @app's target — same folder name,
 * different alias, kept apart on purpose). See deploy-and-decoupling-plan.md
 * (in Skeleton-Child, the project this template feeds) for the reasoning; an
 * npm-package version of this was tried and dropped as unnecessary — this is
 * a feature layer new projects fork and then own, not something worth a live
 * dependency for.
 *
 * The last engine-owned admin-only pieces (admin.js's admin-boot import,
 * admin.css's editor.css import, plus the Docs/Debug admin pages) used to
 * pull raw files from "../Skeleton" through a @skeleton alias. They're gone
 * too now — the engine enqueues those directly (plain wp_enqueue_script/
 * wp_enqueue_style/add_editor_style, no bundling, they never needed one), so
 * a "../Skeleton" checkout is no longer required for this build at all — only
 * for WordPress itself to find the parent theme at runtime, same as any
 * parent/child theme pair.
 */
import { defineConfig, type Plugin } from "vite";
import tailwindcss from "@tailwindcss/vite";
import laravel from "laravel-vite-plugin";
import { glob } from "glob";
import path from "node:path";
import fs from "node:fs/promises";
import { existsSync } from "node:fs";

const root = __dirname;
const port = 3001;

// Where @app points; its modules get a chunk of their own (manualChunks).
const appShared = path.resolve(root, "resources/scripts/shared");
const appSharedId = appShared.split(path.sep).join("/");

// @shared/@use — moved-in engine helpers, plain local files.
const skeletonShared = path.resolve(root, "resources/scripts/skeleton-shared");
const skeletonHooks = path.resolve(root, "resources/scripts/skeleton-hooks");

/** Entries discovered by convention. */
const entries = [
	...glob.sync(["blocks/*/index.js", "views/*/index.js"], { cwd: root }),
	"resources/scripts/app.js",
	"resources/scripts/admin.js",
].filter((entry) => existsSync(path.join(root, entry)));

/** Templates whose edits trigger a full page reload in dev. */
const REFRESH_GLOBS = [
	"blocks/**/*.{twig,php}",
	"views/**/*.{twig,php}",
	"ui/**/*.{twig,php}",
];
const REFRESH_DIRS = REFRESH_GLOBS.map((glob) => glob.split("/**")[0]);

/** Flags entries whose JS chunk only ever imported a stylesheet, so PHP can
 *  enqueue the CSS without registering an empty script module (many blocks
 *  are `import "./index.css"` and nothing else). */
function flagCssOnlyEntries(): Plugin {
	const cssOnlyOutputFiles = new Set<string>();

	const isCssOnlyChunkCode = (code: string): boolean => {
		if (!code) return true;
		let cleaned = code
			.replace(/\/\*[\s\S]*?\*\//g, "")
			.replace(/\/\/[^\n\r]*/g, "")
			.replace(/['"]use strict['"];?/g, "")
			.replace(/\bexport\s*\{\s*\}\s*;?/g, "")
			.replace(
				/\bimport\s*(?:['"])([^'"]+\.(?:css|scss|sass|less|styl))(?:\?[^'"]*)?['"]\s*;?/g,
				"",
			);
		return cleaned.replace(/[\s;]+/g, "").length === 0;
	};

	return {
		name: "skeleton-child-flag-css-only",
		apply: "build",
		generateBundle(_options, bundle) {
			for (const output of Object.values(bundle)) {
				if (output.type !== "chunk" || !output.isEntry) continue;
				if (isCssOnlyChunkCode(output.code ?? "")) {
					cssOnlyOutputFiles.add(output.fileName);
				}
			}
		},
		async closeBundle() {
			const manifestPath = path.resolve(root, "dist", "manifest.json");
			try {
				const raw = await fs.readFile(manifestPath, "utf8");
				const manifest = JSON.parse(raw) as Record<string, any>;
				for (const entry of Object.values(manifest)) {
					if (entry && typeof entry === "object" && typeof entry.file === "string") {
						if (cssOnlyOutputFiles.has(entry.file)) entry.cssOnly = true;
					}
				}
				await fs.writeFile(manifestPath, JSON.stringify(manifest, null, 2));
			} catch (err) {
				console.warn("[vite] Failed to flag css-only entries in dist/manifest.json", err);
			}
		},
	};
}

/** Injects import.meta.hot.accept into source scripts so the dev server
 *  reloads them (stylesheets are already self-accepting, see the original
 *  engine factory's longer note on why this only touches scripts). */
function autoHmr(): Plugin {
	const SCRIPT_RE = /\.[cm]?[jt]sx?$/;
	return {
		name: "skeleton-child-auto-hmr",
		apply: "serve",
		transform(code: string, id: string) {
			const file = id.split("?")[0];
			if (
				SCRIPT_RE.test(file) &&
				(id.includes("blocks/") || id.includes("views/") || id.includes("resources/"))
			) {
				if (!code.includes("import.meta.hot.accept")) {
					return {
						code: `${code}\n\nif (import.meta.hot) { import.meta.hot.accept(() => { console.log('HMR updated: ${id}'); }); }`,
						map: null,
					};
				}
			}
			return null;
		},
	};
}

/** Re-adds the unfiltered directory watch the refresh globs above narrow to
 *  twig/php only — an edit to a block's index.css needs to reach the dev
 *  server too. Must run after laravel-vite-plugin's own refresh plugin. */
function watchRoots(dirs: string[]): Plugin {
	return {
		name: "skeleton-child-watch-roots",
		apply: "serve",
		configureServer(server) {
			for (const dir of dirs) {
				if (existsSync(dir)) server.watcher.add(dir.split(path.sep).join("/"));
			}
		},
	};
}

export default defineConfig(() => ({
	base: "./",
	resolve: {
		alias: {
			"@shared": skeletonShared,
			"@use": skeletonHooks,
			"@app": appShared,
		},
	},
	plugins: [
		autoHmr(),
		tailwindcss(),
		flagCssOnlyEntries(),
		laravel({
			input: entries,
			refresh: REFRESH_GLOBS,
			publicDirectory: "dist",
			buildDirectory: ".",
		}),
		// Last on purpose — undoes the watcher narrowing laravel-vite-plugin's
		// refresh option causes. See watchRoots().
		watchRoots(REFRESH_DIRS.map((dir) => path.resolve(root, dir))),
	],
	css: {
		lightningcss: {
			targets: {
				chrome: 90 << 16,
				firefox: 103 << 16,
				safari: (15 << 16) | (4 << 8),
				edge: 90 << 16,
			},
		},
	},
	build: {
		minify: "terser",
		terserOptions: {
			compress: {
				drop_debugger: true,
			},
		},
		rollupOptions: {
			external: [/^@wordpress\/.*/],
			output: {
				manualChunks(id: string) {
					if (id.includes("node_modules/swiper")) return "swiper";
					if (id.startsWith(appSharedId)) return "app-shared";
					if (id.includes("resources/scripts/skeleton-shared")) return "shared-core";
					if (id.includes("resources/scripts/skeleton-hooks")) return "use-core";
				},
			},
		},
	},
	server: {
		port,
		strictPort: true,
		origin: `http://localhost:${port}`,
		hmr: { host: "localhost" },
		cors: true,
		fs: {
			allow: [root],
		},
	},
}));
