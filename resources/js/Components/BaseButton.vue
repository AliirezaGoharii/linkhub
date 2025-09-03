<template>
    <button
        :type="type"
        :class="[baseClasses, variantClasses]"
        :disabled="disabled"
    >
        <slot />
    </button>
</template>
<script setup>
import { computed } from 'vue';
const props = defineProps({
    type: { type: String, default: 'button' },
    variant: { type: String, default: 'primary' },
    disabled: { type: Boolean, default: false },
});
const baseClasses = 'inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150';
const variantClasses = computed(() => {
    const disabledClass = 'opacity-50 cursor-not-allowed';
    if (props.disabled) return disabledClass;
    switch (props.variant) {
        case 'secondary': return 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 focus:ring-indigo-500';
        case 'danger': return 'bg-red-600 text-white border-red-600 hover:bg-red-500 focus:ring-red-500';
        case 'primary':
        default: return 'bg-gray-800 text-white border-gray-800 hover:bg-gray-700 focus:ring-gray-500';
    }
});
</script>
