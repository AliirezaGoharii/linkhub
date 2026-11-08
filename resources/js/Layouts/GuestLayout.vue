<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import VantaBackground from '@/Components/VantaBackground.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <VantaBackground />
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
