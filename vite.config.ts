import { defineSkeletonConfig } from "../Skeleton/vite/config";
import tailwindcss from "@tailwindcss/vite";
import laravel from "laravel-vite-plugin";
import { glob } from "glob";

// The project's build — the only one: the engine never builds. It collects
// this root's entries and the engine's into one manifest.
//
// tailwindcss/laravel/glob are imported here (not inside vite/config.ts) and
// passed in — this file's own directory is where they resolve from, see the
// comment in vite/config.ts. The factory itself is imported by path: this file
// runs before the @skeleton alias exists.
export default defineSkeletonConfig({
  root: __dirname,
  engine: "../Skeleton",
  port: 3001,
  tailwindcss,
  laravel,
  globSync: glob.sync,
});
