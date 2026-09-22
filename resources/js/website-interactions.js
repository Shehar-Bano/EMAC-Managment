/**
 * EMAC Development Website - Visual Interaction & 3D Effects Engine
 * Lightweight, GPU-accelerated micro-interactions, scroll reveals, 3D card tilt & counters.
 */

(function () {
    'use strict';

    // Check if user prefers reduced motion
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isTouchDevice = () => ('ontouchstart' in window) || (navigator.maxTouchPoints > 0) || (window.innerWidth < 1024);

    /**
     * 1. 3D Card Tilt Engine
     */
    function init3DTilt() {
        if (prefersReducedMotion || isTouchDevice()) return;

        const tiltCards = document.querySelectorAll('[data-tilt], .tilt-card');
        tiltCards.forEach((card) => {
            const maxTilt = parseFloat(card.dataset.tiltMax || '6');
            let isHovered = false;

            card.addEventListener('mouseenter', () => {
                isHovered = true;
                card.style.transition = 'transform 0.15s ease-out, box-shadow 0.25s ease';
            });

            card.addEventListener('mousemove', (e) => {
                if (!isHovered) return;
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                const centerX = rect.width / 2;
                const centerY = rect.height / 2;

                const rotateX = ((y - centerY) / centerY) * -maxTilt;
                const rotateY = ((x - centerX) / centerX) * maxTilt;

                card.style.transform = `perspective(1000px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) translateY(-4px)`;
            });

            card.addEventListener('mouseleave', () => {
                isHovered = false;
                card.style.transition = 'transform 0.5s cubic-bezier(0.23, 1, 0.32, 1), box-shadow 0.5s cubic-bezier(0.23, 1, 0.32, 1)';
                card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0px)';
            });
        });
    }

    /**
     * 2. Scroll-Triggered Reveal Engine (IntersectionObserver)
     */
    function initScrollReveals() {
        if (prefersReducedMotion) {
            document.querySelectorAll('.reveal-init, .reveal-scale-init, .reveal-left-init, .reveal-right-init').forEach(el => {
                el.classList.add('revealed');
            });
            return;
        }

        const revealElements = document.querySelectorAll(
            '.reveal-init, .reveal-scale-init, .reveal-left-init, .reveal-right-init, [data-reveal], [data-reveal-stagger]'
        );

        if (!('IntersectionObserver' in window)) {
            revealElements.forEach(el => el.classList.add('revealed'));
            return;
        }

        const observerOptions = {
            root: null,
            rootMargin: '0px 0px -50px 0px',
            threshold: 0.12
        };

        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = entry.target;

                    if (target.hasAttribute('data-reveal-stagger')) {
                        const children = target.querySelectorAll(':scope > *');
                        children.forEach((child, index) => {
                            child.classList.add('reveal-init');
                            child.style.transitionDelay = `${index * 90}ms`;
                            setTimeout(() => {
                                child.classList.add('revealed');
                            }, 50);
                        });
                        target.classList.add('revealed');
                    } else {
                        target.classList.add('revealed');
                    }

                    observer.unobserve(target);
                }
            });
        }, observerOptions);

        revealElements.forEach(el => revealObserver.observe(el));
    }

    /**
     * 3. Numeric Statistics Counter Animation
     */
    function initNumericCounters() {
        const counters = document.querySelectorAll('[data-counter-target]');
        if (counters.length === 0) return;

        if (prefersReducedMotion || !('IntersectionObserver' in window)) {
            counters.forEach(c => {
                c.textContent = c.dataset.counterTarget + (c.dataset.counterSuffix || '');
            });
            return;
        }

        const counterObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseInt(el.dataset.counterTarget, 10) || 0;
                    const suffix = el.dataset.counterSuffix || '';
                    const duration = parseInt(el.dataset.counterDuration, 10) || 1200;
                    const startTime = performance.now();

                    const updateCount = (currentTime) => {
                        const elapsed = currentTime - startTime;
                        const progress = Math.min(elapsed / duration, 1);
                        // Ease out cubic
                        const easeProgress = 1 - Math.pow(1 - progress, 3);
                        const current = Math.floor(easeProgress * target);

                        el.textContent = current + (progress === 1 ? suffix : '');

                        if (progress < 1) {
                            requestAnimationFrame(updateCount);
                        } else {
                            el.textContent = target + suffix;
                        }
                    };

                    requestAnimationFrame(updateCount);
                    observer.unobserve(el);
                }
            });
        }, { threshold: 0.3 });

        counters.forEach(c => counterObserver.observe(c));
    }

    /**
     * 4. Sticky Navbar Scroll Dynamics
     */
    function initNavbarScroll() {
        const navbar = document.querySelector('header');
        if (!navbar) return;

        let ticking = false;

        const onScroll = () => {
            const scrollY = window.scrollY || window.pageYOffset;
            if (scrollY > 20) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
            ticking = false;
        };

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(onScroll);
                ticking = true;
            }
        }, { passive: true });

        onScroll();
    }

    /**
     * 5. Parallax Background Shapes
     */
    function initParallax() {
        if (prefersReducedMotion || isTouchDevice()) return;

        const parallaxItems = document.querySelectorAll('[data-parallax]');
        if (parallaxItems.length === 0) return;

        let ticking = false;

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    const scrollY = window.scrollY;
                    parallaxItems.forEach(item => {
                        const speed = parseFloat(item.dataset.parallax || '0.1');
                        item.style.transform = `translate3d(0, ${(scrollY * speed).toFixed(1)}px, 0)`;
                    });
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    }

    // Initialize all visual modules when DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        init3DTilt();
        initScrollReveals();
        initNumericCounters();
        initNavbarScroll();
        initParallax();
    });

})();
