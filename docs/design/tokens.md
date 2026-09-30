# Design tokens

> Project layer. How they reach CSS: `../../Skeleton/docs/architecture/design-tokens.md`.
> Rules for using them: `.claude/skills/design-tokens/SKILL.md`.

Defined in `theme.json`, renamed in `resources/css/plumbing.css`, used as
`var(--token)` in CSS or as the matching utility class in Twig.

**Fill this in at project start, from the design file, before the first block.**
Empty rows below are the ones that need a value; a token added after ten blocks
is a token that ten blocks are not using.

## Colour

| Token | Value | Use |
|---|---|---|
| `--color-primary` | | brand |
| `--color-secondary` | | accents |
| `--color-background` | | the page |
| `--color-surface` | | panels, cards |
| `--color-text` | | body copy |
| `--color-text-secondary` | | muted copy |
| `--color-border` | | panel border |
| `--color-primary-content` | | text on primary fill |

Derived, already mapped in `plumbing.css`, no value of their own:
`--color-primary-dark`, `--color-text-muted`, `--color-border-subtle`,
`--color-border-strong`, `--color-btn-ghost-hover`.

## Type

| Token | Utility | Clamp |
|---|---|---|
| `--text-h1` | `text-h1` | → |
| `--text-h2` | `text-h2` | → |
| `--text-h3` | `text-h3` | → |
| `--text-h4` | `text-h4` | → |
| `--text-h5` | `text-h5` | → |
| `--text-h6` | `text-h6` | → |
| — | `text-large` / `text-normal` / `text-small` | |

Each clamp is the mobile and desktop frame value, so no font size ever needs a
breakpoint. Line height: `var(--text-h2--line-height)`. Family: `--font-primary`.

## Spacing

| Token | Value | Utilities |
|---|---|---|
| `--spacing-fluid-xs` | | `p-fluid-xs`, `gap-fluid-xs`, `mt-fluid-xs`, … |
| `--spacing-fluid-sm` | | |
| `--spacing-fluid-md` | | |
| `--spacing-fluid-lg` | | |
| `--spacing-fluid-xl` | | |
| `--spacing-fluid-2xl` | | |

Set these in `theme.json` → `settings.spacing.spacingSizes` as `clamp()`s taken
off both frames. Left unset, the engine's static defaults apply and every block
ends up writing its own `clamp()` for gaps.

Section rhythm — one value, owned by every section block on its own root:

```css
margin-block: clamp( … );   /* mobile → desktop gap between sections */
```

## Layout

| Token / utility | Value | Use |
|---|---|---|
| `container-content` | | standard section width |
| `container` | | wide sections |
| `--container-px` | | page gutter |
| `--header-gutter` / `--footer-gutter` | | chrome only |
| `--nav-h` | | header height |

An ACF block gets no gutter automatically. Each one sets its own width:

```css
width: min(calc(100% - 2 * var(--container-px)), var(--wp--style--global--content-size));
margin-inline: auto;
```

## Shape and depth

| Token | Value |
|---|---|
| `--radius-field` | |
| `--radius-base` / `--radius-box` | |
| `--shadow-sm` / `--shadow` / `--shadow-md` / `--shadow-lg` | |

## Not tokens yet

Every design has one repeating surface — the card or panel most sections sit on.
Name it here the first time it appears, and have blocks reference the tokens:

```css
border: 1px solid var(--color-border);
background: var(--surface-panel);
box-shadow: var(--shadow-glow);
```

Pasted literally instead, it reaches a dozen stylesheets before anyone notices,
and changing the design later means editing every one of them.
