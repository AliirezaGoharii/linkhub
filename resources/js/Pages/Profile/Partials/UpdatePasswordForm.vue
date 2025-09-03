<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);
const form = useForm({ current_password: '', password: '', password_confirmation: '' });
const updatePassword = () => {
    form.put('/password', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-slate-100">Update Password</h2>
            <p class="mt-1 text-sm text-slate-400">Ensure your account is using a long, random password to stay secure.</p>
        </header>
        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div>
                <InputLabel for="current_password" value="Current Password" class="text-slate-300" />
                <input id="current_password" ref="currentPasswordInput" v-model="form.current_password" type="password" class="mt-1 block w-full dark-input" autocomplete="current-password" />
                <InputError :message="form.errors.current_password" class="mt-2" />
            </div>
            <div>
                <InputLabel for="password" value="New Password" class="text-slate-300" />
                <input id="password" ref="passwordInput" v-model="form.password" type="password" class="mt-1 block w-full dark-input" autocomplete="new-password" />
                <InputError :message="form.errors.password" class="mt-2" />
            </div>
            <div>
                <InputLabel for="password_confirmation" value="Confirm Password" class="text-slate-300" />
                <input id="password_confirmation" v-model="form.password_confirmation" type="password" class="mt-1 block w-full dark-input" autocomplete="new-password" />
                <InputError :message="form.errors.password_confirmation" class="mt-2" />
            </div>
            <div class="flex items-center gap-4">
                <button :disabled="form.processing" class="animated-button bg-indigo-600 hover:bg-indigo-500 text-indigo-50 shadow-indigo-500/30">Save</button>
                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                    <p v-if="form.recentlySuccessful" class="text-sm text-slate-400">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
