<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({ canResetPassword: Boolean, status: String });
const form = useForm({ email: '', password: '', remember: false });
const submit = () => form.post('/login', { onFinish: () => form.reset('password') });
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />
        <div v-if="status" class="mb-4 font-medium text-sm text-green-400">
            {{ status }}
        </div>
        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" class="text-slate-300" />
                <input id="email" type="email" class="mt-1 block w-full dark-input" v-model="form.email" required autofocus autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div class="mt-4">
                <InputLabel for="password" value="Password" class="text-slate-300" />
                <input id="password" type="password" class="mt-1 block w-full dark-input" v-model="form.password" required autocomplete="current-password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-slate-400">Remember me</span>
                </label>
            </div>
            <div class="flex items-center justify-end mt-4">
                <Link v-if="canResetPassword" href="/forgot-password" class="underline text-sm text-slate-400 hover:text-slate-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Forgot your password?
                </Link>
                <button class="ms-4 animated-button bg-indigo-600 hover:bg-indigo-500 text-indigo-50 shadow-indigo-500/30" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Log In
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
