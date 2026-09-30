---
name: design-tokens
description: Read before writing any CSS in this theme, and before starting a new block, page or section — the project's design tokens (colour, type, spacing, radius, shadow, container), the search-before-you-create rule that keeps a second copy of an existing value or block from appearing, and the traps that have actually cost this project time. Use when converting a Figma frame, adding a block, building a page section, or picking a colour, font size, gap, radius or shadow.
---

# Design tokens and the no-duplicates rule

Most of the mess in a theme is not wrong code — it is a second copy of something
that already existed, made by someone who did not know the first one was there.
This skill is the few minutes of looking that prevents it.

## 1. Read the inventory first

`docs/design/tokens.md` lists every token the project has, with values, what
each is called in Figma, and which values are still written out by hand. Read it
before the first line of CSS, not after the block is built.

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
colour → `--color-*`, font size → a rung (`--text-2xl`) or a pair (`--text-h2`),
gap or padding → the 4px grid, width → a `container-*` utility.

**The design's names are already ours.** Figma's type steps (`2xs`…`9xl`) and
its spacing ladder are the Tailwind scale — `text-3xl` in the markup is `3xl` in
the frame, and every spacing rung is a whole step of `--spacing` (12px is `3`,
20px is `5`, 40px is `10`). Never introduce a third name for a value that
already has two.

If a value sits between two rungs, take the nearer one — a 4px difference nobody
can see is not worth a second scale. If nothing fits, say so and ask, then
follow "Adding a token" in the inventory: `theme.json` first, mapped in
`plumbing.css`, documented in `docs/design/tokens.md`. Never a literal in a
block stylesheet.

Breakpoints are for layout changes only — column counts, stacking, visibility.
A size that changes between the frames is a clamp between two rungs.

## 4. Traps this project has already fallen into

Each of these cost a session. None is visible by reading the CSS alone.

- **A utility class can lose, or not exist.** WordPress injects `theme.json`
  styles outside `@layer`, and unlayered CSS beats everything layered: a
  `text-h4` class on an `<h3>` does nothing. And a class whose token was never
  mapped generates no utility at all — `text-large` silently inherited for
  months. After using a utility, confirm the element's computed value.
- **Tailwind drops a theme variable no utility uses.** A token read only by
  block stylesheets — which Tailwind never scans — must be declared
  `@theme static`, or it is missing at runtime.
- **`--spacing(n)` works only in stylesheets that reach `app.css`.** A block's
  `index.css` is compiled without the theme and the build fails outright.
- **Two clamps with the same ends are not the same ramp.** 24↔40 exists here
  both ascending and descending. Compare the whole expression.
- **Colours a digit apart are different colours.** `#06111A` is the panel
  gradient; `#06121A` is the table backing.
- **A component's states carry their own values.** The game tile's scrim differs
  across default, hover and Coming Soon. Pull the component set, not one frame,
  before writing a gradient or a shadow.
- **A fill or an effect in the node data may paint nothing.** The MCP lists
  them whether or not they are switched on, and it does not say which is on
  top. Both readings have gone wrong here in one day: a tile's fade was
  dismissed as hidden under the artwork when it is the thing that makes the
  name readable, and every panel was given a glow whose effect is switched off
  in Figma, which read as a white edge around every section. Export the node as
  a PNG and look at it — one call settles what no amount of reading the JSON
  will.
- **Figma variables are the authority, not the frame.** The MCP returns resolved
  styles; the variable collections (Font / Size, Spacing, Colour / *) are where
  the names live. Reading a number off a frame loses its name.

## 5. Verify by measuring

Never conclude from a screenshot that a size or spacing is right. Measure the
computed value in the browser at 390 and 1920 and compare with the frame. After
changing anything global — a scale, a token, a colour — sweep several pages, not
the one you were working on.

## 6. Leave the note behind

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
