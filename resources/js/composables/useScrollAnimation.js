import { onMounted, onUnmounted, nextTick } from 'vue';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export function useScrollAnimation() {
    onMounted(() => {
        // Wait for DOM to be fully ready
        nextTick(() => {
            // Small delay to ensure all components are rendered
            requestAnimationFrame(() => {
                initAnimations();
                // Refresh ScrollTrigger after everything is set up
                ScrollTrigger.refresh();
            });
        });
    });

    function initAnimations() {
        gsap.utils.toArray('.animate-fade-up').forEach(el => {
            gsap.fromTo(el,
                { y: 60, opacity: 0 },
                {
                    y: 0, opacity: 1, duration: 0.8,
                    scrollTrigger: {
                        trigger: el,
                        start: 'top 90%',
                        toggleActions: 'play none none none',
                    },
                }
            );
        });

        gsap.utils.toArray('.animate-stagger').forEach(parent => {
            gsap.fromTo(parent.children,
                { y: 40, opacity: 0 },
                {
                    y: 0, opacity: 1, duration: 0.6, stagger: 0.15,
                    scrollTrigger: {
                        trigger: parent,
                        start: 'top 90%',
                        toggleActions: 'play none none none',
                    },
                }
            );
        });

        gsap.utils.toArray('.animate-scale-in').forEach(el => {
            gsap.fromTo(el,
                { scale: 0.8, opacity: 0 },
                {
                    scale: 1, opacity: 1, duration: 0.6,
                    scrollTrigger: {
                        trigger: el,
                        start: 'top 90%',
                        toggleActions: 'play none none none',
                    },
                }
            );
        });

        gsap.utils.toArray('.animate-slide-right').forEach(el => {
            gsap.fromTo(el,
                { x: -80, opacity: 0 },
                {
                    x: 0, opacity: 1, duration: 0.8,
                    scrollTrigger: {
                        trigger: el,
                        start: 'top 90%',
                        toggleActions: 'play none none none',
                    },
                }
            );
        });

        gsap.utils.toArray('.animate-slide-left').forEach(el => {
            gsap.fromTo(el,
                { x: 80, opacity: 0 },
                {
                    x: 0, opacity: 1, duration: 0.8,
                    scrollTrigger: {
                        trigger: el,
                        start: 'top 90%',
                        toggleActions: 'play none none none',
                    },
                }
            );
        });
    }

    onUnmounted(() => {
        ScrollTrigger.getAll().forEach(t => t.kill());
    });
}
