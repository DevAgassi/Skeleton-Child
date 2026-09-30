---
name: design-tokens
description: Read before writing any CSS in this theme, and before starting a new block, page or section — the project's design tokens (colour, type, spacing, radius, shadow, container) and the search-before-you-create rule that keeps a second copy of an existing value or block from appearing. Use when converting a Figma frame, adding a block, building a page section, or picking a colour, font size, gap, radius or shadow.
---

# Design tokens and the no-duplicates rule

Most of the mess in a theme is not wrong code — it is a second copy of something
that already existed, made by someone who did not know the first one was there.
This skill is the two minutes of looking that prevents it.

## 1. Read the inventory first

`docs/design/tokens.md` lists every token the project has, with values and what
each is for. Read it before the first line of CSS, not after the block is built.

## 2. Search before creating

Three greps, each answering a question that is expensive to get wrong.

**Does this value already have a name?** Take the Figma value and look for it in
the token layer and in existing CSS:

```bash
grep -rn "2ED6FF\|#d4d4d4" theme.json resources/css blocks views
```

A hit in `theme.json` or `plumbing.css` means a token exists — use it. A hit in
two or more block stylesheets means the value is being duplicated right now, and
this is the moment to give it a name rather than add a third copy.

**Does this section already exist as a block?** Two Figma frames on different
pages are often the same component with different content:

```bash
ls blocks
grep -rln "<keyword from the design>" blocks/*/view.twig
```

Prefer, in order: use the existing block as-is; add a field to it for the
variation; only then write a new one. A new block is justified when the markup
differs, not when the copy differs.

**Is this field group already defined?** ACF field groups live in `acf-json/`
and nowhere else. The `group_*.json` file inside a block folder is ACF's own
export, read-only, and editing it does nothing:

```bash
ls acf-json/group_*.json
```

## 3. Map, do not invent

Take each value off the Figma frame and name its token before writing it:
colour → `--color-*`, font size → `--text-*` or a `text-h*` utility, gap or
padding → `--spacing-fluid-*`, width → a `container-*` utility.

If a value sits between two tokens, take the nearer token — a 4px difference
nobody can see is not worth a second scale. If nothing fits, say so and ask,
then follow "Adding a token" in the inventory: `theme.json` first, mapped in
`plumbing.css`, documented in `docs/design/tokens.md`. Never a literal in a
block stylesheet.

Breakpoints are for layout changes only — column counts, stacking, visibility.
Anything that is a size gets a fluid token, so it scales smoothly between the
390 and 1920 frames instead of jumping.

## 4. Leave the note behind

Finishing a block is not finishing the work. If it introduced a value that a
second block will want — a surface, a shadow, a grid gap, a card ratio — add it
to `docs/design/tokens.md` in the same change. The next person starts from the
document, so anything that is not in it does not exist.

The same applies to a new block: its row belongs in the section inventory, so
the next page reuses it instead of rebuilding it.

## Where the rest is written

Two layers, kept apart on purpose: `docs/` in this theme is **this project** —
its values, its sections, its decisions. `../Skeleton/docs/` is the **engine** —
mechanisms shared by every project built on it. Engine files are never edited
from project work.

- `docs/README.md` — what the project layer holds, and what belongs where.
- `docs/design/tokens.md` — this project's values and rules.
- `../Skeleton/docs/guides/styling.md` — the engine's CSS conventions.
- `../Skeleton/docs/guides/creating-blocks.md` — the block scaffold itself.
- `../Skeleton/docs/architecture/design-tokens.md` — how theme.json reaches CSS.
