/**
 * EMAC Development Website - Clean UI Interactions
 */
(function () {
    'use strict';

    // Header scroll elevation
    const header = document.querySelector('header');
    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                header.classList.add('shadow-sm');
            } else {
                header.classList.remove('shadow-sm');
            }
        }, { passive: true });
    }
})();
