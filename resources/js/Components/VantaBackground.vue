<script setup>
/**
 * Lazy, code-split "Cosmic Fog" background.
 *
 * - three.js + vanta are only fetched when a page actually uses this
 *   component, and only when the user has not requested reduced motion.
 * - A static CSS gradient always sits underneath, so the page looks
 *   right even before (or without) the WebGL effect.
 * - Versions are pinned through npm instead of a CDN "latest" script.
 */
import { onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    highlightColor: { type: Number, default: 0x5252b4 },
    midtoneColor: { type: Number, default: 0x3f3182 },
    lowlightColor: { type: Number, default: 0x1e1b4b },
    baseColor: { type: Number, default: 0x000000 },
    blurFactor: { type: Number, default: 0.4 },
    speed: { type: Number, default: 0.5 },
    zoom: { type: Number, default: 0.8 },
});

const containerRef = ref(null);

let effect = null;
let cancelled = false;

onMounted(async () => {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (prefersReducedMotion) {
        return; // keep the static gradient fallback
    }

    try {
        // The vanta bundle expects a global `THREE`; the ESM namespace
        // exposes the same named exports and is never written to.
        window.THREE = await import('three');

        const vantaModule = await import('vanta/dist/vanta.fog.min.js');
        const VantaFog = vantaModule.default ?? vantaModule;

        if (cancelled) {
            return;
        }

        effect = VantaFog({
            el: containerRef.value,
            mouseControls: true,
            touchControls: true,
            gyroControls: false,
            minHeight: 200.0,
            minWidth: 200.0,
            highlightColor: props.highlightColor,
            midtoneColor: props.midtoneColor,
            lowlightColor: props.lowlightColor,
            baseColor: props.baseColor,
            blurFactor: props.blurFactor,
            speed: props.speed,
            zoom: props.zoom,
        });
    } catch (error) {
        if (import.meta.env.DEV) {
            console.warn('[VantaBackground] effect failed to load, using static gradient.', error);
        }
    }
});

onBeforeUnmount(() => {
    cancelled = true;

    if (effect?.destroy) {
        effect.destroy();
    }
});
</script>

<template>
    <div ref="containerRef" class="vanta-bg" aria-hidden="true"></div>
</template>

<style scoped>
.vanta-bg {
    position: fixed;
    inset: 0;
    z-index: -1;
    background-color: #020617;
    background-image: radial-gradient(ellipse at 50% -20%, #3f3182 0%, #1e1b4b 35%, #020617 80%);
}
</style>
