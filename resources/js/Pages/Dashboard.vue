<script setup>
import { watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import LinkList from './Dashboard/Partials/LinkList.vue';
import AppearanceEditor from './Dashboard/Partials/AppearanceEditor.vue';
import LivePreview from './Dashboard/Partials/LivePreview.vue';
import StatsCards from './Dashboard/Partials/StatsCards.vue';
import QrCodeCard from './Dashboard/Partials/QrCodeCard.vue';
import AnalyticsChart from './Dashboard/Partials/AnalyticsChart.vue';
import { usePageStore } from '@/stores/pageStore';

const props = defineProps({
    stats: { type: Object, required: true },
    links: { type: Array, required: true },
    analytics: { type: Array, required: true },
});

// The store's link list is hydrated from the explicit dashboard prop
// (instead of the shared auth.user payload, which no longer carries it)
// and refreshed whenever a round-trip delivers fresh props.
const pageStore = usePageStore();
pageStore.syncLinks(props.links);
watch(() => props.links, (links) => pageStore.syncLinks(links));
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <StatsCards :stats="stats" />
                <AnalyticsChart :series="analytics" />

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-8">
                        <AppearanceEditor />
                        <LinkList />
                    </div>
                    <div class="lg:col-span-1 space-y-8">
                       <LivePreview />
                       <QrCodeCard />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
