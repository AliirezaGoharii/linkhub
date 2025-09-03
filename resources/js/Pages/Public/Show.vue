<script setup>
import { Head } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps({ user: Object });
const profilePhotoUrl = computed(() => props.user.profile_photo_path ? `/storage/${props.user.profile_photo_path}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(props.user.name)}&color=A8B2FF&background=1E293B&size=128`);
const bannerUrl = computed(() => props.user.banner_path ? `/storage/${props.user.banner_path}` : null);
const getTextColor = (hex) => { if (!hex) return '#fff'; const r = parseInt(hex.substr(1,2),16), g = parseInt(hex.substr(3,2),16), b = parseInt(hex.substr(5,2),16); return (((r*299)+(g*587)+(b*114))/1000 >= 128) ? '#1E293B' : '#FFF'; };
const linkStyle = (hex) => ({ backgroundColor: hex, color: getTextColor(hex), 'box-shadow': `0 0 20px 0px ${hex}33` });

watch(profilePhotoUrl, (newUrl) => {
  let link = document.querySelector("link[rel~='icon']");
  if (!link) {
    link = document.createElement('link');
    link.rel = 'icon';
    document.getElementsByTagName('head')[0].appendChild(link);
  }
  link.href = newUrl;
}, { immediate: true });
</script>

<template>
    <Head>
        <title>{{ user.name }} (@{{ user.username }})</title>
        <meta name="description" :content="`Check out the links from ${user.name}.`" />
    </Head>
    <div class="public-page-bg min-h-screen font-sans text-slate-200">
        <div class="container mx-auto max-w-lg p-4 pt-8 sm:pt-12">
            <div class="relative">
                <div v-if="bannerUrl" class="h-48 rounded-2xl overflow-hidden shadow-2xl">
                    <img :src="bannerUrl" class="w-full h-full object-cover" />
                </div>
                <div class="text-center" :class="user.banner_path ? '-mt-16' : ''">
                    <div v-motion-pop class="relative inline-block">
                        <img :src="profilePhotoUrl" alt="Profile Photo" class="w-28 h-28 rounded-full object-cover shadow-2xl ring-4 ring-slate-800/80">
                    </div>
                    <div v-motion-pop :delay="100">
                        <h1 class="text-3xl font-bold mt-5 text-slate-100 font-rubik tracking-tight">{{ user.name }}</h1>
                        <p class="text-md text-slate-400 font-inter">@{{ user.username }}</p>
                    </div>
                </div>
            </div>
            <div class="mt-10 space-y-5">
                 <a v-for="(link, index) in user.links" :key="link.id" :href="link.url" target="_blank" rel="noopener noreferrer"
                    v-motion :initial="{ opacity: 0, y: 50 }" :enter="{ opacity: 1, y: 0, transition: { delay: 300 + index * 100, type: 'spring', stiffness: 100, damping: 12 } }"
                    class="block w-full text-center p-4 rounded-xl font-semibold transition-all duration-300 ease-in-out transform hover:scale-105 shadow-lg hover:shadow-2xl"
                    :style="linkStyle(user.theme_color)">
                    {{ link.title }}
                </a>
            </div>
            <footer v-motion :initial="{ opacity: 0 }" :enter="{ opacity: 1, transition: { delay: 800 } }" class="text-center mt-16 text-slate-500">
                <a href="/" class="text-lg font-bold font-rubik text-slate-400 hover:text-slate-200 transition-colors">LinkHub</a>
            </footer>
        </div>
    </div>
</template>
<style>
.font-rubik { font-family: 'Rubik', sans-serif; }
.font-inter { font-family: 'Inter', sans-serif; }
.public-page-bg {
    background-color: #020617;
    background-image: url('https://www.transparenttextures.com/patterns/cubes.png'), linear-gradient(135deg, #020617 25%, #1e293b 100%);
    background-size: auto, cover;
}
</style>
