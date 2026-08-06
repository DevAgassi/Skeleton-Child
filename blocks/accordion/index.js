import './index.css';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-accordion]').forEach(initAccordion);
});

function initAccordion(accordion) {
    accordion.querySelectorAll('[data-accordion-item]').forEach(item => {
        const trigger = item.querySelector('[data-accordion-trigger]');

        trigger.addEventListener('click', () => {
            const isOpen = item.hasAttribute('data-open');

            // Close all open items in this accordion
            accordion.querySelectorAll('[data-accordion-item][data-open]').forEach(openItem => {
                closeItem(openItem);
            });

            // Open clicked item if it was closed
            if (!isOpen) openItem(item);
        });
    });
}

function openItem(item) {
    const content = item.querySelector('[data-accordion-content]');
    const trigger = item.querySelector('[data-accordion-trigger]');

    item.setAttribute('data-open', '');
    trigger.setAttribute('aria-expanded', 'true');
    content.setAttribute('aria-hidden', 'false');
    content.style.maxHeight = content.scrollHeight + 'px';
}

function closeItem(item) {
    const content = item.querySelector('[data-accordion-content]');
    const trigger = item.querySelector('[data-accordion-trigger]');

    item.removeAttribute('data-open');
    trigger.setAttribute('aria-expanded', 'false');
    content.setAttribute('aria-hidden', 'true');
    content.style.maxHeight = '0';
}
