<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);
const form = useForm({ password: '' });
const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    setTimeout(() => passwordInput.value.focus(), 250);
};
const deleteUser = () => {
    form.delete('/profile', {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};
const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-slate-100">Delete Account</h2>
            <p class="mt-1 text-sm text-slate-400">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
        </header>
        <button @click="confirmUserDeletion" class="animated-button bg-red-900/50 hover:bg-red-900/80 text-red-300">Delete Account</button>
        <div v-if="confirmingUserDeletion" class="fixed inset-0 z-50 bg-black bg-opacity-75 backdrop-blur-sm transition-opacity">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="bg-slate-800 border border-slate-700 p-6 rounded-2xl shadow-2xl w-full max-w-lg">
                    <h2 class="text-lg font-medium text-slate-100">Are you sure you want to delete your account?</h2>
                    <p class="mt-1 text-sm text-slate-400">Please enter your password to confirm you would like to permanently delete your account.</p>
                    <div class="mt-6">
                        <InputLabel for="password" value="Password" class="sr-only" />
                        <input id="password" ref="passwordInput" v-model="form.password" type="password" class="mt-1 block w-full dark-input" placeholder="Password" @keyup.enter="deleteUser" />
                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button @click="closeModal" class="animated-button bg-slate-700 hover:bg-slate-600 text-slate-200">Cancel</button>
                        <button class="animated-button bg-red-900/50 hover:bg-red-900/80 text-red-300" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="deleteUser">Delete Account</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
