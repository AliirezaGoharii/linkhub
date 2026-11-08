<script setup>
import { ref, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { usePageStore } from '@/stores/pageStore';
import InputError from '@/Components/InputError.vue';

const props = defineProps({ link: Object });
const emit = defineEmits(['delete-requested']);

const pageStore = usePageStore();
const form = useForm({ title: props.link.title, url: props.link.url });
const justSaved = ref(false);
const toggling = ref(false);

// Keep the form in sync when a server round-trip delivers fresh data.
watch(() => [props.link.title, props.link.url], ([title, url]) => {
    form.title = title;
    form.url = url;
});

const update = () => form.put(`/links/${props.link.id}`, {
    preserveScroll: true,
    onSuccess: () => {
        pageStore.updateLink({ id: props.link.id, ...form.data() });
        justSaved.value = true;
        setTimeout(() => (justSaved.value = false), 1500);
    },
});

// Hide or show the link on the public page. Optimistically mirrored
// into the store so the live preview reacts instantly; the redirect's
// fresh props reconcile everything afterwards.
const toggle = () => {
    if (toggling.value) return;
    toggling.value = true;
    const previousValue = props.link.is_active;
    pageStore.updateLink({ id: props.link.id, is_active: !previousValue });
    router.patch(`/links/${props.link.id}/toggle`, {}, {
        preserveScroll: true,
        onError: () => pageStore.updateLink({ id: props.link.id, is_active: previousValue }),
        onFinish: () => { toggling.value = false; },
    });
};
</script>

<template>
    <div
        class="bg-slate-900/50 border border-slate-700/50 rounded-lg shadow-md p-4 flex items-center gap-4 transition-all duration-300 hover:border-slate-600 hover:bg-slate-900"
        :class="link.is_active === false ? 'opacity-50' : ''"
    >
        <div @click.prevent class="cursor-move text-slate-500 hover:text-slate-300 handle" title="Drag to reorder">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </div>
        <div class="flex-grow min-w-0">
            <form @submit.prevent="update" class="space-y-2">
                <div class="flex items-center gap-2">
                    <input v-model="form.title" placeholder="Title" class="dark-input !p-1.5 !text-base font-semibold text-slate-200" />
                    <span
                        v-if="link.is_active === false"
                        class="shrink-0 inline-flex items-center gap-1 text-xs font-semibold text-amber-300 bg-amber-500/10 border border-amber-500/30 rounded-full px-2 py-0.5"
                        title="Hidden from your public page"
                    >
                        Hidden
                    </span>
                    <span
                        v-if="link.click_count > 0"
                        class="shrink-0 inline-flex items-center gap-1 text-xs font-semibold text-indigo-300 bg-indigo-500/10 border border-indigo-500/30 rounded-full px-2 py-0.5"
                        title="Times this link was clicked"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M1.5 4a.5.5 0 01.5-.5h4.586a.5.5 0 01.354.146l.853.854H13.5a.5.5 0 01.5.5v1H2v-1.5a.5.5 0 01.146-.354l.708-.708A.5.5 0 013 4h-.5zM2 7.5v6.5a2 2 0 002 2h8a2 2 0 002-2V7.5H2z" transform="translate(2 -2)" /><path fill-rule="evenodd" d="M15.854 8.646a.5.5 0 010 .708l-1.5 1.5a.5.5 0 01-.708 0l-.5-.5a.5.5 0 01.708-.708l.146.147 1.146-1.147a.5.5 0 01.708 0z" clip-rule="evenodd" transform="translate(3 -2)" /></svg>
                        {{ new Intl.NumberFormat().format(link.click_count) }}
                    </span>
                </div>
                <InputError :message="form.errors.title" />
                <input v-model="form.url" placeholder="https://example.com" class="dark-input !p-1.5 text-slate-400" />
                <InputError :message="form.errors.url" />
            </form>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <Transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0" leave-active-class="transition-opacity duration-300" leave-to-class="opacity-0">
                <p v-if="justSaved" class="text-xs text-emerald-400 font-semibold self-center">Saved</p>
            </Transition>
            <button @click="toggle" :disabled="toggling" class="action-button bg-slate-700 hover:bg-slate-600 text-slate-300" :title="link.is_active === false ? 'Show on public page' : 'Hide from public page'">
                <svg v-if="link.is_active !== false" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243" />
                </svg>
            </button>
            <button @click="update" :disabled="form.processing" class="action-button bg-slate-700 hover:bg-slate-600 text-slate-300" title="Save changes">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
            </button>
            <button @click="emit('delete-requested', link)" class="action-button bg-red-900/50 hover:bg-red-900/80 text-red-300" title="Delete link">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" /></svg>
            </button>
        </div>
    </div>
</template>
<style>
.action-button { display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 0.375rem; transition: all 0.2s ease-in-out; }
.action-button:hover { transform: scale(1.1); }
</style>
