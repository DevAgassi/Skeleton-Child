# CLAUDE.md — project (child theme)

This theme is a project built on the Skeleton engine (the parent theme,
`../Skeleton`). The engine's rules and architecture apply here in full:

@../../Skeleton/.claude/CLAUDE.md

## This theme vs the engine

- **Project code lives here, never in the engine.** Blocks, models, views,
  `functions/`, styles and anything true of this site only. The engine
  changes only for reusable behaviour every project needs.
- **The build is this theme's.** `npm run dev` / `npm run build` run here;
  `dist/` and `vite.config.ts` belong to this theme.
- **Docs have two layers.** This site's decisions: `docs/` here (see
  `docs/README.md` for which layer a fact belongs to). How the engine works:
  `../Skeleton/docs/`.
- **Before writing CSS or starting a block, page or section** — the
  `design-tokens` skill.

## Project-specific

_Add rules true of this site only._
