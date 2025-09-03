<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({ name: '', username: '', email: '', password: '', password_confirmation: '' });
const submit = () => form.post('/register', { onFinish: () => form.reset('password', 'password_confirmation') });
</script>

<template>
    <GuestLayout>
        <Head title="Register" />
        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" class="text-slate-300" />
                <input id="name" type="text" class="mt-1 block w-full dark-input" v-model="form.name" required autofocus autocomplete="name" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div class="mt-4">
                <InputLabel for="username" value="Username" class="text-slate-300" />
                <input id="username" type="text" class="mt-1 block w-full dark-input" v-model="form.username" required autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.username" />
            </div>
            <div class="mt-4">
                <InputLabel for="email" value="Email" class="text-slate-300" />
                <input id="email" type="email" class="mt-1 block w-full dark-input" v-model="form.email" required autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div class="mt-4">
                <InputLabel for="password" value="Password" class="text-slate-300" />
                <input id="password" type="password" class="mt-1 block w-full dark-input" v-model="form.password" required autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" class="text-slate-300" />
                <input id="password_confirmation" type="password" class="mt-1 block w-full dark-input" v-model="form.password_confirmation" required autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>
            <div class="flex items-center justify-end mt-4">
                <Link href="/login" class="underline text-sm text-slate-400 hover:text-slate-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Already registered?
                </Link>
                <button class="ms-4 animated-button bg-indigo-600 hover:bg-indigo-500 text-indigo-50 shadow-indigo-500/30" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
