export function initSandbox() {
    const dialog = document.getElementById('sandbox-dialog');
    if (!dialog) return;

    const media    = document.getElementById('sandbox-media');
    const closeBtn = document.getElementById('sandbox-close');

    // ── Zoom / pan state ──────────────────────────────────────────────────────
    let zoomed     = false;
    let tx         = 0;   // current translate X (px)
    let ty         = 0;   // current translate Y (px)
    let dragActive = false;
    let didDrag    = false;
    let startX     = 0;
    let startY     = 0;

    function getImg() {
        return media.querySelector('.sandbox__img');
    }

    function applyTransform(img) {
        img.style.transform = zoomed ? `scale(2) translate(${tx / 2}px, ${ty / 2}px)` : '';
    }

    function resetZoom() {
        const img = getImg();
        zoomed = false;
        tx = 0;
        ty = 0;
        dragActive = false;
        didDrag    = false;
        if (img) {
            img.style.transform      = '';
            img.style.transformOrigin = '';
            img.style.cursor         = 'zoom-in';
        }
    }

    function onImgClick(e) {
        if (didDrag) { didDrag = false; return; } // absorb post-drag click, reset for next

        const img = getImg();
        if (!img) return;

        if (zoomed) {
            resetZoom();
            return;
        }

        // Zoom in centred at click point
        const rect = img.getBoundingClientRect();
        const ox   = (((e.clientX - rect.left) / rect.width)  * 100).toFixed(1);
        const oy   = (((e.clientY - rect.top)  / rect.height) * 100).toFixed(1);

        img.style.transformOrigin = `${ox}% ${oy}%`;
        zoomed = true;
        tx = 0;
        ty = 0;
        applyTransform(img);
        img.style.cursor = 'grab';
    }

    function onPointerDown(e) {
        if (!zoomed) return;
        const img = getImg();
        if (!img || e.button !== 0) return;

        dragActive = true;
        didDrag    = false;
        startX     = e.clientX - tx;
        startY     = e.clientY - ty;
        img.classList.add('is-dragging');
        img.style.cursor = 'grabbing';
        img.setPointerCapture(e.pointerId);
        e.preventDefault();
    }

    function onPointerMove(e) {
        if (!dragActive) return;
        e.preventDefault();
        const img = getImg();
        if (!img) return;

        const dx = e.clientX - startX;
        const dy = e.clientY - startY;

        // Only start tracking drag after a few px to avoid click mis-fires
        if (!didDrag && Math.hypot(dx - tx, dy - ty) > 4) {
            didDrag = true;
        }

        tx = dx;
        ty = dy;
        applyTransform(img);
    }

    function onPointerUp() {
        if (!dragActive) return;
        dragActive = false;
        const img = getImg();
        if (img) {
            img.classList.remove('is-dragging');
            img.style.cursor = 'grab';
        }
    }

    // ── Open / close ──────────────────────────────────────────────────────────
    function open(trigger) {
        const { src, alt, width, height } = trigger.dataset;
        const ratio = width && height ? `${width} / ${height}` : 'auto';

        media.innerHTML = `<img
            class="sandbox__img"
            src="${src}"
            alt="${alt ?? ''}"
            width="${width ?? ''}"
            height="${height ?? ''}"
            style="aspect-ratio:${ratio}"
        >`;

        const img = getImg();
        img.style.cursor = 'zoom-in';
        img.addEventListener('click',         onImgClick);
        img.addEventListener('pointerdown',   onPointerDown,  { passive: false });
        img.addEventListener('pointermove',   onPointerMove,  { passive: false });
        img.addEventListener('pointerup',     onPointerUp);
        img.addEventListener('pointercancel', onPointerUp);

        dialog.showModal();
    }

    function close() {
        resetZoom();
        dialog.close();
    }

    // ── Event wiring ──────────────────────────────────────────────────────────
    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-sandbox-trigger]');
        if (trigger) open(trigger);
    });

    closeBtn.addEventListener('click', close);

    dialog.addEventListener('click', (e) => {
        if (e.target === dialog) close();
    });

    dialog.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
    });
}
