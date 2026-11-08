<script setup>
import { computed, watch, ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { usePageStore } from '@/stores/pageStore';
import InputError from '@/Components/InputError.vue';

const pageStore = usePageStore();
const defaultAvatar = computed(() => `https://ui-avatars.com/api/?name=${encodeURIComponent(pageStore.name)}&color=A8B2FF&background=1E293B&size=128`);

const photoInput = ref(null);
const bannerInput = ref(null);
const isUploadingBanner = ref(false);

const appearanceForm = useForm({
    theme_color: pageStore.themeColor,
    bio: pageStore.bio ?? '',
});
const photoForm = useForm({ photo: null });
const bannerForm = useForm({ banner: null });

const bioMaxLength = 280;

watch(() => appearanceForm.theme_color, (newColor) => {
    // Only feed valid 6-digit hex into the live preview; 3-digit values
    // would otherwise render a wrong text contrast color.
    if (/^#[0-9A-F]{6}$/i.test(newColor)) {
        pageStore.updateThemeColor(newColor);
    }
});

watch(() => appearanceForm.bio, (newBio) => pageStore.updateBio(newBio ?? ''));

const handleBannerSelect = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    isUploadingBanner.value = true;
    const reader = new FileReader();

    reader.onload = (e) => {
        const image = new Image();
        image.src = e.target.result;

        image.onload = () => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const sourceWidth = image.width;
            const sourceHeight = image.height;
            const targetAspectRatio = 4 / 1;

            let cropWidth, cropHeight, sourceX, sourceY;

            if (sourceWidth / sourceHeight > targetAspectRatio) {
                cropHeight = sourceHeight;
                cropWidth = cropHeight * targetAspectRatio;
                sourceX = (sourceWidth - cropWidth) / 2;
                sourceY = 0;
            } else {
                cropWidth = sourceWidth;
                cropHeight = cropWidth / targetAspectRatio;
                sourceX = 0;
                sourceY = (sourceHeight - cropHeight) / 2;
            }

            canvas.width = 1200;
            canvas.height = 300;

            ctx.drawImage(image, sourceX, sourceY, cropWidth, cropHeight, 0, 0, canvas.width, canvas.height);

            canvas.toBlob((blob) => {
                bannerForm.banner = blob;
                bannerForm.post('/page-settings/banner', {
                    preserveScroll: true,
                    onSuccess: (page) => {
                        pageStore.updateBannerPath(page.props.auth.user.banner_path);
                    },
                    onFinish: () => {
                        isUploadingBanner.value = false;
                        if (bannerInput.value) bannerInput.value.value = '';
                    }
                });
            }, 'image/jpeg', 0.9);
        };
    };
    reader.readAsDataURL(file);
};

const updateAppearance = () => appearanceForm.put('/page-settings', {
    preserveScroll: true,
    onSuccess: () => {
        pageStore.updateThemeColor(appearanceForm.theme_color);
        pageStore.updateBio(appearanceForm.bio ?? '');
    },
});

const updatePhoto = () => photoForm.post('/page-settings/photo', {
    preserveScroll: true,
    onSuccess: (page) => {
        pageStore.updateProfilePhotoPath(page.props.auth.user.profile_photo_path);
        if (photoInput.value) photoInput.value.value = null;
    }
});

watch(() => photoForm.photo, (newPhoto) => { if (newPhoto) updatePhoto(); });

const deletePhoto = () => {
    if (confirm('Remove your profile photo?')) {
        router.delete('/page-settings/photo', {
            preserveScroll: true,
            onSuccess: (page) => pageStore.updateProfilePhotoPath(page.props.auth.user.profile_photo_path)
        });
    }
};

const deleteBanner = () => {
    if (confirm('Remove your banner?')) {
        router.delete('/page-settings/banner', {
            preserveScroll: true,
            onSuccess: (page) => pageStore.updateBannerPath(page.props.auth.user.banner_path)
        });
    }
};
</script>

<template>
    <div v-motion-pop class="card">
        <div class="p-6 sm:p-8">
            <div>
                <h2 class="card-title">Appearance</h2>
                <p class="card-subtitle">Customize your public page's look and feel.</p>
            </div>

            <div class="my-6 border-t border-slate-700/50"></div>

            <div class="space-y-8">
                <div>
                    <h3 class="text-lg font-semibold text-slate-300 font-rubik">Profile Photo</h3>
                    <div class="mt-4 flex items-center gap-6">
                        <img class="h-24 w-24 object-cover rounded-full ring-4 ring-slate-700/50 shadow-lg" :src="pageStore.profilePhotoUrl || defaultAvatar" alt="Profile photo" />
                        <div class="flex items-center gap-3">
                            <form @submit.prevent class="hidden"><input type="file" @input="photoForm.photo = $event.target.files[0]" ref="photoInput" accept="image/*"></form>
                            <button @click.prevent="$refs.photoInput.click()" class="animated-button bg-slate-700 hover:bg-slate-600 text-slate-200">Change</button>
                            <button v-if="pageStore.profilePhotoPath" @click="deletePhoto" class="animated-button bg-red-900/50 hover:bg-red-900/80 text-red-300">Remove</button>
                        </div>
                    </div>
                     <InputError :message="photoForm.errors.photo" class="mt-2" />
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-slate-300 font-rubik">Page Banner</h3>
                    <div class="mt-4 flex items-center gap-6">
                        <div class="w-32 h-16 rounded-lg ring-4 ring-slate-700/50 shadow-lg bg-slate-900/50 overflow-hidden">
                           <img v-if="pageStore.bannerUrl" :src="pageStore.bannerUrl" class="w-full h-full object-cover"/>
                        </div>
                        <div class="flex items-center gap-3">
                            <form @submit.prevent class="hidden"><input type="file" @change="handleBannerSelect" ref="bannerInput" accept="image/*"></form>
                            <button @click.prevent="$refs.bannerInput.click()" class="animated-button bg-slate-700 hover:bg-slate-600 text-slate-200" :disabled="isUploadingBanner">
                                {{ isUploadingBanner ? 'Uploading...' : 'Change' }}
                            </button>
                            <button v-if="pageStore.bannerPath" @click="deleteBanner" class="animated-button bg-red-900/50 hover:bg-red-900/80 text-red-300">Remove</button>
                        </div>
                    </div>
                     <InputError :message="bannerForm.errors.banner" class="mt-2" />
                </div>
            </div>

            <div class="my-8 border-t border-slate-700/50"></div>

            <form @submit.prevent="updateAppearance">
                <div>
                    <h3 class="text-lg font-semibold text-slate-300 font-rubik">Bio</h3>
                    <p class="mt-1 text-sm text-slate-400 font-inter">A short description shown under your name.</p>
                    <div class="mt-3">
                        <textarea
                            v-model="appearanceForm.bio"
                            rows="3"
                            maxlength="280"
                            placeholder="Tell the world who you are in one sentence."
                            class="dark-input resize-none"
                        ></textarea>
                        <div class="flex justify-between mt-1">
                            <InputError :message="appearanceForm.errors.bio" />
                            <span class="text-xs text-slate-500 ml-auto font-mono">{{ (appearanceForm.bio || '').length }}/{{ bioMaxLength }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-slate-300 font-rubik">Theme Color</h3>
                    <div class="mt-4 flex items-center gap-4">
                        <div class="relative w-12 h-12 rounded-full overflow-hidden border-2 border-slate-700 shadow-sm cursor-pointer group">
                            <div class="w-full h-full transition-all duration-300 group-hover:scale-110" :style="{ backgroundColor: appearanceForm.theme_color }"></div>
                            <input type="color" v-model="appearanceForm.theme_color" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        </div>
                        <div class="relative flex-grow">
                             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-slate-500 font-mono">#</span></div>
                            <input v-model="appearanceForm.theme_color" @input="appearanceForm.theme_color = '#' + $event.target.value.replace(/#/g, '')" class="w-full dark-input font-mono pl-7" />
                        </div>
                    </div>
                    <InputError :message="appearanceForm.errors.theme_color" class="mt-2" />
                </div>
                <div class="mt-8 pt-6 border-t border-slate-700/50 flex items-center justify-end gap-4">
                    <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 translate-x-4" leave-active-class="transition-all duration-200 ease-in" leave-to-class="opacity-0 -translate-x-4">
                        <p v-if="appearanceForm.recentlySuccessful" class="text-sm text-emerald-400 font-semibold">Saved!</p>
                    </Transition>
                    <button :disabled="appearanceForm.processing" type="submit" class="animated-button bg-indigo-600 hover:bg-indigo-500 text-indigo-50 shadow-indigo-500/30">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</template>
