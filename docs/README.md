# Project documentation

_Rename the heading to the project's name once this theme is cloned._

**This is the project layer.** Everything here is true of *this site only*: its
palette, its type scale, its sections, the decisions taken while building it.

The engine is documented separately, in the parent theme:
`../Skeleton/docs/` — how blocks, models, abilities, the build and the token
plumbing work, for every project built on Skeleton.

## Which layer does it belong to

> Would it still be true on the next project built with this engine?
> **Yes** → the engine's docs. **No** → here.

A concrete pair: *"tokens come from `theme.json`, are mapped in `plumbing.css`
and never redefined"* is a mechanism — the engine's. *"the brand colour is
`#xxxxxx` and the panel border already has a token"* is this site — ours.

Nothing here edits the engine. Engine files are never changed from project work,
so a gap in the engine's own docs is reported, not patched from this side.

## What is here

- [design/tokens.md](design/tokens.md) — every colour, size, gap, radius and
  shadow this project has, and where it is defined. **Fill it in at project
  start, before the first block.** Read before writing CSS.

## What to add as the project grows

- `inventory.md` — design section → block → pages using it. The list that stops
  a second block being built for a section that already has one. Start it while
  reading the design file, not after the tenth block.
- `decisions.md` — dated log: what was decided, why, and what it rules out. So a
  settled question is not reopened three weeks later.
- `content-model.md` — which copy lives in a block field, which in an options
  page, and which is fixed in a template. Decide the rule once; every later
  "move this text to the admin" request is that rule not having existed.

## Working rules

The short, always-loaded rules live in `CLAUDE.md` at the theme root, not here.
This folder is the reference behind them: `CLAUDE.md` says *what to do*, these
files say *what exists*.

`.claude/skills/` holds the skills that make an agent read the right file at the
right moment — `design-tokens` fires on new blocks, new sections and any CSS.
