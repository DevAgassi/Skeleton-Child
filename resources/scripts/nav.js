/**
 * Header behaviour — project level, and deliberately so.
 *
 * Every function here is coupled to this theme's header markup: the
 * [data-header] root, the .site-header--compact/--unpinned modifiers, the
 * .mobile-toggle buttons and their :scope > ul submenus. Rewriting the header
 * markup without rewriting this file gives dead behaviour and no error, which
 * is exactly why the engine ships neither: its header renders and navigates
 * with JavaScript disabled.
 *
 * Styles: resources/css/components/header.css
 * Markup: ui/layout/header.twig + ui/components/menu.twig
 */

/**
 * Sticky header — fixed + spacer pattern (headroom-style).
 *
 * Header is always position:fixed. A spacer div reserves its height
 * in the document flow — zero layout shift, zero feedback loops.
 *
 * States:
 *   default              — full-size header visible at top
 *   --compact            — reduced height (past OFFSET)
 *   --unpinned           — translateY(-100%), hidden above viewport
 *   pinned (scroll up)   — compact header slides back in
 */
export function initStickyHeader() {
  const header = document.querySelector('[data-header]');
  if (!header) return;

  const OFFSET = 100;
  const TOLERANCE_DOWN = 8;
  const TOLERANCE_UP = 3;
  let lastY = window.scrollY;
  let pinned = true;
  let ticking = false;

  function update() {
    const y = window.scrollY;

    if (y <= OFFSET) {
      if (!pinned) {
        header.classList.remove('site-header--unpinned');
        pinned = true;
      }
      header.classList.remove('site-header--compact');
    } else {
      header.classList.add('site-header--compact');

      if (y > lastY + TOLERANCE_DOWN && pinned) {
        header.classList.add('site-header--unpinned');
        pinned = false;
      } else if (y < lastY - TOLERANCE_UP && !pinned) {
        header.classList.remove('site-header--unpinned');
        pinned = true;
      }
    }

    lastY = y;
    ticking = false;
  }

  window.addEventListener('scroll', () => {
    if (!ticking) {
      ticking = true;
      requestAnimationFrame(update);
    }
  }, { passive: true });
}

/**
 * Scroll-to-top button — smooth scroll to page top on click.
 */
export function initScrollToTop() {
  const btn = document.getElementById('scroll-to-top');
  if (!btn) return;

  btn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}

/**
 * Mobile menu — arrow toggles submenu, text navigates.
 */
export function initMobileMenu() {
  document.querySelectorAll(".mobile-toggle").forEach((btn) => {
    btn.addEventListener("click", () => {
      const li = btn.closest("li");
      const ul = li.querySelector(":scope > ul");
      if (!ul) return;

      const isOpen = !ul.classList.contains("hidden");
      const parentUl = li.parentElement;

      if (!isOpen) {
        parentUl.querySelectorAll(":scope > li > div > .mobile-toggle").forEach((sibling) => {
          if (sibling === btn) return;
          const siblingUl = sibling.closest("li").querySelector(":scope > ul");
          if (!siblingUl || siblingUl.classList.contains("hidden")) return;
          siblingUl.style.height = siblingUl.scrollHeight + 'px';
          requestAnimationFrame(() => {
            siblingUl.style.height = '0';
            siblingUl.addEventListener('transitionend', () => {
              siblingUl.classList.add('hidden');
              siblingUl.style.height = '';
            }, { once: true });
          });
          sibling.setAttribute("aria-expanded", "false");
          sibling.querySelector("svg").classList.remove("rotate-180");
        });
      }

      if (isOpen) {
        ul.style.height = ul.scrollHeight + 'px';
        requestAnimationFrame(() => {
          ul.style.height = '0';
          ul.addEventListener('transitionend', () => {
            ul.classList.add('hidden');
            ul.style.height = '';
          }, { once: true });
        });
      } else {
        ul.classList.remove('hidden');
        ul.style.height = '0';
        requestAnimationFrame(() => {
          ul.style.height = ul.scrollHeight + 'px';
          ul.addEventListener('transitionend', () => {
            ul.style.height = '';
          }, { once: true });
        });
      }

      btn.setAttribute("aria-expanded", String(!isOpen));
      btn.querySelector("svg").classList.toggle("rotate-180", !isOpen);
    });
  });
}
