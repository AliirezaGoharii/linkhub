<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 v-if="title" class="text-lg font-medium text-gray-900 mb-4">{{ title }}</h3>
            <slot />
        </div>
    </div>
</template>
<script setup>
defineProps({ title: String });
</script>
