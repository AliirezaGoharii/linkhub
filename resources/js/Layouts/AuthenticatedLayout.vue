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
    <div class="min-h-screen text-slate-300">
        <nav class="sticky top-0 z-40 bg-black/30 backdrop-blur-lg border-b border-slate-700/50 shadow-lg shadow-black/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="shrink-0 flex items-center">
                            <Link href="/">
                                <ApplicationLogo class="block h-9 w-auto fill-current text-slate-400" />
                            </Link>
                        </div>
                        <div class="space-x-8 ms-6 flex">
                           <Link href="/dashboard" :class="[$page.url.startsWith('/dashboard') ? 'text-slate-100 border-indigo-400' : 'text-slate-400 border-transparent hover:text-slate-200 hover:border-slate-600']" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                Dashboard
                            </Link>
                        </div>
                    </div>
                    <div class="flex items-center ms-6">
                         <Link href="/profile" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-slate-400 hover:text-slate-100 focus:outline-none transition ease-in-out duration-150">
                            {{ $page.props.auth.user.name }}
                        </Link>
                    </div>
                </div>
            </div>
        </nav>
        <header class="bg-black/20 shadow-lg" v-if="$slots.header">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>
        <main>
            <slot />
        </main>
    </div>
</template>
