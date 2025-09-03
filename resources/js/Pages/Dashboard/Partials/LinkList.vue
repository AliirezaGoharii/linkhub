<script setup>
import { ref, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { usePageStore } from '@/stores/pageStore';
import draggable from 'vuedraggable';
import LinkItem from './LinkItem.vue';
import InputError from '@/Components/InputError.vue';
import { debounce } from 'lodash';

const pageStore = usePageStore();
const links = ref(pageStore.userLinks);
watch(() => pageStore.userLinks, (newLinks) => { links.value = [...newLinks]; });

const form = useForm({ title: '', url: '' });
const store = () => form.post('/links', {
    preserveScroll: true,
    onSuccess: (page) => {
        pageStore.userLinks = page.props.auth.user.links;
        form.reset();
    }
});

const updateLinkOrder = debounce(() => {
    const linkIds = links.value.map(link => link.id);
    router.put('/links/order', { links: linkIds }, {
        preserveScroll: true,
        onSuccess: () => pageStore.updateLinkOrder(links.value),
    });
}, 500);

const onDragEnd = () => updateLinkOrder();
</script>

<template>
    <div
        v-motion
        :initial="{ opacity: 0, y: 100 }"
        :enter="{ opacity: 1, y: 0, transition: { duration: 500, delay: 200, type: 'spring', stiffness: 100, damping: 15 } }"
        class="bg-slate-800/50 backdrop-blur-sm border-y sm:border-x border-slate-700/50 sm:rounded-2xl shadow-2xl shadow-black/20 overflow-hidden"
    >
        <div class="p-6 sm:p-8">
            <div
                v-motion
                :initial="{ opacity: 0, y: 20 }"
                :enter="{ opacity: 1, y: 0, transition: { delay: 300 } }"
            >
                <h2 class="text-2xl font-bold text-slate-100 font-rubik tracking-tight">My Links</h2>
                <p class="mt-1 text-sm text-slate-400 font-inter">Add, edit, and reorder your links.</p>
            </div>

            <form @submit.prevent="store" class="mt-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-1">
                         <input v-model="form.title" type="text" placeholder="Link Title" class="dark-input" />
                         <InputError :message="form.errors.title" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <div class="flex gap-4">
                            <input v-model="form.url" type="url" placeholder="https://example.com" class="dark-input flex-grow" />
                            <button :disabled="form.processing" type="submit" class="animated-button bg-indigo-600 hover:bg-indigo-500 text-indigo-50 shadow-indigo-500/30 shrink-0">
                                Add
                            </button>
                        </div>
                        <InputError :message="form.errors.url" class="mt-2" />
                    </div>
                </div>
            </form>

            <div class="mt-6 border-t border-slate-700/50"></div>

            <draggable v-model="links" item-key="id" handle=".handle" @end="onDragEnd" class="mt-6 space-y-4" ghost-class="ghost-link">
                <template #item="{ element, index }">
                    <div
                        v-motion
                        :initial="{ opacity: 0, y: 20 }"
                        :enter="{ opacity: 1, y: 0, transition: { delay: 400 + index * 50 } }"
                    >
                        <LinkItem :link="element" />
                    </div>
                </template>
            </draggable>

            <div v-if="links.length === 0" class="text-center text-slate-500 mt-8 py-8 border-2 border-dashed border-slate-700 rounded-lg">
                <p class="font-medium">You have no links yet.</p>
                <p class="text-sm mt-1">Use the form above to add your first link!</p>
            </div>
        </div>
    </div>
</template>
