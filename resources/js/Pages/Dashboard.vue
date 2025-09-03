<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-6">
                        <AppearanceEditor />
                        <LinkList />
                    </div>
                    <div class="lg:col-span-1">
                       <LivePreview />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import LinkList from './Dashboard/Partials/LinkList.vue';
import AppearanceEditor from './Dashboard/Partials/AppearanceEditor.vue';
import LivePreview from './Dashboard/Partials/LivePreview.vue';
</script>
