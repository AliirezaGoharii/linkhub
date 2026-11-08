<script setup>
import { computed } from 'vue';

const props = defineProps({
    stats: { type: Object, required: true },
});

const cards = computed(() => [
    {
        key: 'views',
        label: 'Page Views',
        value: props.stats.profile_views,
        hint: 'Total visits to your public page',
        icon: 'M2.032 8.642a48.036 48.036 0 011.068-3.557C4.256 4.73 5.512 4 6.877 4c1.284 0 2.388.68 3.062 1.463.126.148.37.148.497 0C14.293 4.68 15.397 4 16.68 4c1.366 0 2.621.73 3.152 2.085.398 1.015.783 2.23 1.068 3.557.215 1.01-.344 2.014-1.284 2.385l-2.266.912a.48.48 0 00-.293.442v3.797a2.25 2.25 0 01-1.98 2.233 47.94 47.94 0 01-4.157.34v-2.48a.75.75 0 00-1.5 0v2.48a47.94 47.94 0 01-4.157-.34 2.25 2.25 0 01-1.98-2.233v-3.797a.48.48 0 00-.293-.442l-2.266-.912C1.706 10.656 1.15 9.652 1.366 8.642z',
    },
    {
        key: 'clicks',
        label: 'Link Clicks',
        value: props.stats.total_clicks,
        hint: 'Clicks across all of your links',
        icon: 'M15.042 5.023a.75.75 0 011.06 0l2.895 2.895a.75.75 0 010 1.06l-2.895 2.895a.75.75 0 11-1.06-1.06l1.616-1.617H10.75a.75.75 0 010-1.5h5.908l-1.616-1.617a.75.75 0 010-1.06zM4.5 12.75a.75.75 0 01.75.75v.75a2.25 2.25 0 002.25 2.25h5.908l-1.617-1.617a.75.75 0 111.06-1.06l2.895 2.895a.75.75 0 010 1.06l-2.895 2.895a.75.75 0 11-1.06-1.06l1.616-1.616H7.5A3.75 3.75 0 013.75 14.25v-.75a.75.75 0 01.75-.75z',
    },
    {
        key: 'links',
        label: 'Active Links',
        value: props.stats.link_count,
        hint: 'Links currently on your page',
        icon: 'M13.19 8.688a4.5 4.5 0 01-1.941.842l-3.48.652a.75.75 0 00-.577.577l-.652 3.48a4.5 4.5 0 01-.842 1.94L1.5 10.63V15a3 3 0 003 3h9a3 3 0 003-3V7.5L13.19 8.688z M7.5 1.5h9a3 3 0 013 3V15a3 3 0 01-3 3h-9a3 3 0 01-3-3V4.5a3 3 0 013-3z',
    },
]);

function formatNumber(value) {
    return new Intl.NumberFormat().format(Number(value ?? 0));
}
</script>

<template>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div
            v-for="card in cards"
            :key="card.key"
            v-motion-pop
            class="card !rounded-2xl p-5 flex items-center gap-4"
        >
            <div class="shrink-0 inline-flex items-center justify-center h-12 w-12 rounded-xl bg-indigo-500/10 ring-1 ring-indigo-500/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" :d="card.icon" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-2xl font-bold text-slate-100 font-rubik leading-tight">{{ formatNumber(card.value) }}</p>
                <p class="text-xs uppercase tracking-widest text-slate-500 font-semibold mt-0.5">{{ card.label }}</p>
                <p class="text-xs text-slate-500 mt-1 truncate">{{ card.hint }}</p>
            </div>
        </div>

        <div
            v-if="stats.top_link"
            class="card !rounded-2xl p-5 sm:col-span-3 flex items-center gap-4"
        >
            <div class="shrink-0 inline-flex items-center justify-center h-12 w-12 rounded-xl bg-emerald-500/10 ring-1 ring-emerald-500/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-xs uppercase tracking-widest text-slate-500 font-semibold">Top Link</p>
                <p class="text-lg font-semibold text-slate-100 font-rubik truncate mt-0.5">
                    {{ stats.top_link.title }}
                    <span class="text-slate-500 font-normal">·</span>
                    <span class="text-emerald-400">{{ formatNumber(stats.top_link.click_count) }} clicks</span>
                </p>
            </div>
        </div>
    </div>
</template>
