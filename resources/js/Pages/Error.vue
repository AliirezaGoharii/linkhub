<script setup>
import { computed, onMounted, onBeforeUnmount, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const props = defineProps({ status: Number });

const title = computed(() => {
  return {
    503: '503: Service Unavailable',
    500: '500: Server Error',
    404: '404: Page Not Found',
    403: '403: Forbidden',
  }[props.status] || 'Error';
});

const description = computed(() => {
  return {
    503: 'Sorry, we are doing some maintenance. Please check back soon.',
    500: 'Whoops, something went wrong on our servers.',
    404: 'Sorry, the page you are looking for could not be found.',
    403: 'Sorry, you are forbidden from accessing this page.',
  }[props.status] || 'An unexpected error occurred.';
});


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
    <div class="min-h-screen flex flex-col items-center justify-center text-slate-300 font-sans">
        <div
            v-motion
            :initial="{ opacity: 0, y: -50, scale: 0.8 }"
            :enter="{ opacity: 1, y: 0, scale: 1, transition: { type: 'spring', stiffness: 100, damping: 10 } }"
            class="text-center"
        >
            <ApplicationLogo class="w-24 h-24 mx-auto fill-current text-slate-400" />
            <h1 class="mt-6 text-5xl font-bold font-rubik tracking-tighter text-slate-100">
                {{ title }}
            </h1>
            <p class="mt-2 text-lg text-slate-400 font-inter">
                {{ description }}
            </p>
        </div>
        <div
            v-motion
            :initial="{ opacity: 0, y: 50 }"
            :enter="{ opacity: 1, y: 0, transition: { delay: 300, type: 'spring', stiffness: 100, damping: 12 } }"
            class="mt-12"
        >
             <Link href="/" class="welcome-button bg-slate-700 hover:bg-slate-600 text-slate-200">
                Go Home
            </Link>
        </div>
    </div>
</template>
<style>
.font-rubik { font-family: 'Rubik', sans-serif; }
.font-inter { font-family: 'Inter', sans-serif; }
.welcome-button { display: inline-flex; align-items: center; justify-content: center; width: 12rem; padding: 0.75rem 1.5rem; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.75rem; font-size: 1rem; font-weight: 600; transition: all 0.2s ease-in-out; box-shadow: 0 4px 6px rgba(0,0,0,0.1), 0 0 0 rgba(0,0,0,0); text-align: center; }
.welcome-button:hover { transform: translateY(-3px) scale(1.05); box-shadow: 0 10px 20px rgba(0,0,0,0.2), 0 6px 6px rgba(0,0,0,0.1); }
</style>
