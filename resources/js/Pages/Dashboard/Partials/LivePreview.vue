<script setup>
import { usePageStore } from '@/stores/pageStore';
import { computed } from 'vue';

const pageStore = usePageStore();
const defaultAvatar = computed(() => `https://ui-avatars.com/api/?name=${pageStore.name}&color=A8B2FF&background=1E293B&size=128`);
const getTextColor = (hex) => { if (!hex) return '#fff'; const r = parseInt(hex.substr(1,2),16), g = parseInt(hex.substr(3,2),16), b = parseInt(hex.substr(5,2),16); return (((r*299)+(g*587)+(b*114))/1000 >= 128) ? '#1E293B' : '#FFF'; };
const linkStyle = (hex) => ({ backgroundColor: hex, color: getTextColor(hex), 'box-shadow': `0 0 20px 0px ${hex}33` });
</script>

<template>
    <div class="sticky top-24">
        <div class="relative mx-auto border-gray-800 bg-gray-800 border-[14px] rounded-[2.5rem] h-[600px] w-[300px] shadow-2xl shadow-black/50">
            <div class="w-[140px] h-[18px] bg-gray-800 top-0 rounded-b-[1rem] left-1/2 -translate-x-1/2 absolute"></div>
            <div class="h-[46px] w-[3px] bg-gray-800 absolute -left-[17px] top-[72px] rounded-l-lg"></div>
            <div class="h-[46px] w-[3px] bg-gray-800 absolute -left-[17px] top-[124px] rounded-l-lg"></div>
            <div class="h-[64px] w-[3px] bg-gray-800 absolute -right-[17px] top-[142px] rounded-r-lg"></div>
            <div class="rounded-[2rem] overflow-hidden w-full h-full bg-gradient-to-br from-black to-slate-900">
                <div class="w-full h-full overflow-y-auto p-4 text-slate-200">
                    <div class="relative">
                        <div v-if="pageStore.bannerUrl" class="h-24 -mx-4 -mt-4 rounded-t-3xl overflow-hidden">
                            <img :src="pageStore.bannerUrl" class="w-full h-full object-cover" />
                            <div class="absolute inset-0 bg-black/50"></div>
                        </div>
                        <div class="text-center" :class="pageStore.bannerUrl ? '-mt-10' : 'mt-4'">
                            <div class="relative inline-block">
                                <img :src="pageStore.profilePhotoUrl || defaultAvatar" alt="Profile Photo" class="w-20 h-20 rounded-full object-cover shadow-2xl ring-4 ring-slate-800/80">
                            </div>
                            <div>
                                <h1 class="text-xl font-bold mt-2 text-slate-100 font-rubik tracking-tight">{{ pageStore.name }}</h1>
                                <p class="text-xs text-slate-400 font-inter">@{{ pageStore.username }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 space-y-3">
                        <a v-for="link in pageStore.userLinks" :key="link.id" href="#" @click.prevent
                           class="block w-full text-center p-3 rounded-lg font-semibold transition-transform duration-200 ease-in-out hover:scale-105 shadow-lg"
                           :style="linkStyle(pageStore.themeColor)">
                            {{ link.title }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a :href="pageStore.publicUrl" target="_blank" class="text-sm text-indigo-400 hover:text-indigo-300 font-semibold inline-flex items-center gap-1 transition-colors">
                {{ pageStore.publicUrl }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" /><path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" /></svg>
            </a>
        </div>
    </div>
</template>
