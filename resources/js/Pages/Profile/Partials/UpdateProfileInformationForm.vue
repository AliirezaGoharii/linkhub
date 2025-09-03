<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({ mustVerifyEmail: Boolean, status: String });
const user = usePage().props.auth.user;
const form = useForm({ name: user.name, username: user.username, email: user.email });
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-slate-100">Profile Information</h2>
            <p class="mt-1 text-sm text-slate-400">Update your account's profile information and email address.</p>
        </header>
        <form @submit.prevent="form.patch('/profile')" class="mt-6 space-y-6">
            <div>
                <InputLabel for="name" value="Name" class="text-slate-300" />
                <input id="name" type="text" class="mt-1 block w-full dark-input" v-model="form.name" required autofocus autocomplete="name" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div>
                <InputLabel for="username" value="Username" class="text-slate-300" />
                <input id="username" type="text" class="mt-1 block w-full dark-input" v-model="form.username" required autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.username" />
            </div>
            <div>
                <InputLabel for="email" value="Email" class="text-slate-300" />
                <input id="email" type="email" class="mt-1 block w-full dark-input" v-model="form.email" required autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm mt-2 text-slate-200">
                    Your email address is unverified.
                    <Link href="/email/verification-notification" method="post" as="button" class="underline text-sm text-slate-400 hover:text-slate-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Click here to re-send the verification email.
                    </Link>
                </p>
                <div v-show="status === 'verification-link-sent'" class="mt-2 font-medium text-sm text-green-400">
                    A new verification link has been sent to your email address.
                </div>
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
