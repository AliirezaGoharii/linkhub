<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ mustVerifyEmail: Boolean, status: String });
</script>

<template>
    <Head title="Profile" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-slate-200 leading-tight">Profile Settings</h2>
                 <Link href="/logout" method="post" as="button" class="animated-button bg-slate-700 hover:bg-slate-600 text-slate-200">
                    Log Out
                </Link>
            </div>
        </template>
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <div
                    v-motion
                    :initial="{ opacity: 0, y: 50 }"
                    :enter="{ opacity: 1, y: 0, transition: { duration: 500 } }"
                    class="p-4 sm:p-8 bg-slate-800/50 backdrop-blur-sm border-y sm:border-x border-slate-700/50 sm:rounded-2xl shadow-2xl"
                >
                    <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" />
                </div>
                <div
                    v-motion
                    :initial="{ opacity: 0, y: 50 }"
                    :enter="{ opacity: 1, y: 0, transition: { duration: 500, delay: 150 } }"
                    class="p-4 sm:p-8 bg-slate-800/50 backdrop-blur-sm border-y sm:border-x border-slate-700/50 sm:rounded-2xl shadow-2xl"
                >
                    <UpdatePasswordForm />
                </div>
                <div
                    v-motion
                    :initial="{ opacity: 0, y: 50 }"
                    :enter="{ opacity: 1, y: 0, transition: { duration: 500, delay: 300 } }"
                    class="p-4 sm:p-8 bg-slate-800/50 backdrop-blur-sm border-y sm:border-x border-slate-700/50 sm:rounded-2xl shadow-2xl"
                >
                    <DeleteUserForm />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
