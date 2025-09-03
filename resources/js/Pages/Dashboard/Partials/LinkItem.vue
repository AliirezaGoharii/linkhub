<script setup>
import { useForm } from '@inertiajs/vue3';
import { usePageStore } from '@/stores/pageStore';
import InputError from '@/Components/InputError.vue';

const props = defineProps({ link: Object });
const pageStore = usePageStore();
const form = useForm({ title: props.link.title, url: props.link.url });

const update = () => form.put(`/links/${props.link.id}`, {
    preserveScroll: true,
    onSuccess: () => pageStore.updateLink({ id: props.link.id, ...form.data() }),
});
const destroy = () => {
    if (confirm('Are you sure?')) {
        useForm({}).delete(`/links/${props.link.id}`, {
            preserveScroll: true,
            onSuccess: () => pageStore.removeLink(props.link.id),
        });
    }
};
</script>

<template>
    <div class="bg-slate-900/50 border border-slate-700/50 rounded-lg shadow-md p-4 flex items-center gap-4 transition-all duration-300 hover:border-slate-600 hover:bg-slate-900">
        <div @click.prevent class="cursor-move text-slate-500 hover:text-slate-300 handle">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </div>
        <div class="flex-grow">
            <form @submit.prevent="update" class="space-y-2">
                <input v-model="form.title" placeholder="Title" class="dark-input !p-1 !text-base font-semibold text-slate-200" />
                <InputError :message="form.errors.title" />
                <input v-model="form.url" placeholder="https://example.com" class="dark-input !p-1 text-slate-400" />
                <InputError :message="form.errors.url" />
            </form>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <button @click="update" :disabled="form.processing" class="action-button bg-slate-700 hover:bg-slate-600 text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
            </button>
            <button @click="destroy" class="action-button bg-red-900/50 hover:bg-red-900/80 text-red-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" /></svg>
            </button>
        </div>
    </div>
</template>
<style>
.action-button { display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.375rem; transition: all 0.2s ease-in-out; }
.action-button:hover { transform: scale(1.1); }
</style>
