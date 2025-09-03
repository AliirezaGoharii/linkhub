<script setup>
import { onMounted, onBeforeUnmount, ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';

let vantaEffect = null;
const vantaRef = ref(null);

onMounted(() => {
  if (window.VANTA) {
    vantaEffect = window.VANTA.FOG({
      el: vantaRef.value,
      mouseControls: true,
      touchControls: true,
      gyroControls: false,
      minHeight: 200.00,
      minWidth: 200.00,
      highlightColor: 0x5252b4,
      midtoneColor: 0x3f3182,
      lowlightColor: 0x1e1b4b,
      baseColor: 0x000000,
      blurFactor: 0.40,
      speed: 0.50,
      zoom: 0.80
    });
  }
});

onBeforeUnmount(() => {
  if (vantaEffect) {
    vantaEffect.destroy();
  }
});
</script>

<template>
    <div ref="vantaRef" class="fixed inset-0 z-[-1]"></div>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div v-motion-pop>
            <Link href="/">
                <ApplicationLogo class="w-20 h-20 fill-current text-slate-400" />
            </Link>
        </div>
        <div
            v-motion
            :initial="{ opacity: 0, y: 50 }"
            :enter="{ opacity: 1, y: 0, transition: { duration: 500 } }"
            class="w-full sm:max-w-md mt-6 px-6 py-8 bg-slate-800/50 backdrop-blur-sm border-y sm:border-x border-slate-700/50 shadow-2xl overflow-hidden sm:rounded-2xl"
        >
            <slot />
        </div>
    </div>
</template>
