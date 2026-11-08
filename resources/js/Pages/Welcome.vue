<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import VantaBackground from '@/Components/VantaBackground.vue';

defineProps({ canLogin: Boolean, canRegister: Boolean });
</script>

<template>
    <Head title="Welcome" />
    <VantaBackground />
    <div class="min-h-screen flex flex-col items-center justify-center text-slate-300 font-sans">
        <div
            v-motion
            :initial="{ opacity: 0, y: -50, scale: 0.8 }"
            :enter="{ opacity: 1, y: 0, scale: 1, transition: { type: 'spring', stiffness: 100, damping: 10 } }"
            class="text-center"
        >
            <ApplicationLogo class="w-24 h-24 mx-auto fill-current text-slate-400" />
            <h1 class="mt-6 text-5xl font-bold font-rubik tracking-tighter text-slate-100">
                Welcome to LinkHub
            </h1>
            <p class="mt-2 text-lg text-slate-400 font-inter">
                Your one link for everything.
            </p>
            <p class="mt-6 max-w-xl text-slate-400 font-inter">
                Build a beautiful page for all of your links, customize how it looks,
                and see how many people view it — all in one place.
            </p>
        </div>
        <div
            v-if="canLogin"
            v-motion
            :initial="{ opacity: 0, y: 50 }"
            :enter="{ opacity: 1, y: 0, transition: { delay: 300, type: 'spring', stiffness: 100, damping: 12 } }"
            class="mt-12 flex flex-col sm:flex-row items-center gap-4"
        >
             <Link v-if="$page.props.auth.user" href="/dashboard" class="welcome-button bg-indigo-600 hover:bg-indigo-500 text-indigo-50 shadow-indigo-500/30">
                Go to Dashboard
            </Link>
            <template v-else>
                <Link :href="'/login'" class="welcome-button bg-slate-700 hover:bg-slate-600 text-slate-200">
                    Log In
                </Link>
                <Link v-if="canRegister" :href="'/register'" class="welcome-button bg-indigo-600 hover:bg-indigo-500 text-indigo-50 shadow-indigo-500/30">
                    Get Started
                </Link>
            </template>
        </div>
    </div>
</template>
<style>
.welcome-button { display: inline-flex; align-items: center; justify-content: center; width: 12rem; padding: 0.75rem 1.5rem; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.75rem; font-size: 1rem; font-weight: 600; transition: all 0.2s ease-in-out; box-shadow: 0 4px 6px rgba(0,0,0,0.1), 0 0 0 rgba(0,0,0,0); text-align: center; }
.welcome-button:hover { transform: translateY(-3px) scale(1.05); box-shadow: 0 10px 20px rgba(0,0,0,0.2), 0 6px 6px rgba(0,0,0,0.1); }
</style>
