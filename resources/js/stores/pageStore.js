import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export const usePageStore = defineStore('page', () => {
    const authUser = computed(() => usePage().props.auth.user);

    const name = ref(authUser.value.name);
    const username = ref(authUser.value.username);
    const profilePhotoPath = ref(authUser.value.profile_photo_path);
    const bannerPath = ref(authUser.value.banner_path);
    const themeColor = ref(authUser.value.theme_color);
    const userLinks = ref(authUser.value.links || []);

    const publicUrl = computed(() => `${window.location.origin}/${username.value}`);
    const profilePhotoUrl = computed(() => profilePhotoPath.value ? `/storage/${profilePhotoPath.value}` : null);
    const bannerUrl = computed(() => bannerPath.value ? `/storage/${bannerPath.value}` : null);

    function updateProfilePhotoPath(newPath) { profilePhotoPath.value = newPath; }
    function updateBannerPath(newPath) { bannerPath.value = newPath; }
    function updateThemeColor(newColor) { themeColor.value = newColor; }
    function updateLinkOrder(newOrder) { userLinks.value = newOrder; }

    return {
        name, username, profilePhotoPath, bannerPath, themeColor, userLinks,
        publicUrl, profilePhotoUrl, bannerUrl,
        updateProfilePhotoPath, updateBannerPath, updateThemeColor, updateLinkOrder
    };
});
