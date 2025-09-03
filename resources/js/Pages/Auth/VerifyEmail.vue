<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post('/email/verification-notification');
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />
        <div class="mb-4 text-sm text-slate-400">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link
            we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </div>
        <div v-if="verificationLinkSent" class="mb-4 font-medium text-sm text-green-400">
            A new verification link has been sent to the email address you provided during registration.
        </div>
        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <button
                    class="animated-button bg-indigo-600 hover:bg-indigo-500 text-indigo-50 shadow-indigo-500/30"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Resend Verification Email
                </button>
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="underline text-sm text-slate-400 hover:text-slate-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >Log Out</Link
                >
            </div>
        </form>
    </GuestLayout>
</template>
