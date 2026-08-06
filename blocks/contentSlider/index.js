import Swiper from 'swiper';
import { Pagination, Autoplay, A11y } from 'swiper/modules';
import './index.css';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-cs-slider]').forEach(initSlider);
});

function initSlider(el) {
    const autoplay = el.dataset.autoplay === '1';
    const loop     = el.dataset.loop     === '1';
    const prevBtn  = el.querySelector('.cs-btn-prev');
    const nextBtn  = el.querySelector('.cs-btn-next');

    const swiper = new Swiper(el.querySelector('.cs-swiper'), {
        modules: [Pagination, Autoplay, A11y],
        loop,
        autoplay: autoplay ? { delay: 5000, disableOnInteraction: true } : false,
        pagination: {
            el:        el.querySelector('.cs-pagination'),
            clickable: true,
        },
        a11y: {
            prevSlideMessage: prevBtn?.getAttribute('aria-label') ?? 'Previous slide',
            nextSlideMessage: nextBtn?.getAttribute('aria-label') ?? 'Next slide',
        },
        on: {
            init:       (s) => syncNav(s, prevBtn, nextBtn),
            slideChange:(s) => syncNav(s, prevBtn, nextBtn),
        },
    });

    prevBtn?.addEventListener('click', () => swiper.slidePrev());
    nextBtn?.addEventListener('click', () => swiper.slideNext());
}

function syncNav(swiper, prevBtn, nextBtn) {
    if (!prevBtn || !nextBtn || swiper.params.loop) return;
    prevBtn.disabled = swiper.isBeginning;
    nextBtn.disabled = swiper.isEnd;
}
