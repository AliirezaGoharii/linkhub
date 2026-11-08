import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

/**
 * Client-side mirror of the user's public page.
 *
 * Initialized once from the shared Inertia props and then kept in sync
 * optimistically by the dashboard partials so the live preview reacts
 * instantly while Inertia visits round-trip in the background.
 */
export const usePageStore = defineStore('page', () => {
    const authUser = computed(() => usePage().props.auth.user);

    const name = ref(authUser.value.name);
    const username = ref(authUser.value.username);
    const bio = ref(authUser.value.bio);
    const profilePhotoPath = ref(authUser.value.profile_photo_path);
    const bannerPath = ref(authUser.value.banner_path);
    const themeColor = ref(authUser.value.theme_color);

    // Links are passed as an explicit prop by the dashboard (they are
    // no longer serialized into the shared auth.user on every request)
    // and hydrated here by the dashboard page itself.
    const userLinks = ref([]);

    // What visitors see on the public page: hidden links are excluded.
    const publicLinks = computed(() => userLinks.value.filter((link) => link.is_active !== false));

    const publicUrl = computed(() => `${window.location.origin}/${username.value}`);
    const profilePhotoUrl = computed(() => profilePhotoPath.value ? `/storage/${profilePhotoPath.value}` : null);
    const bannerUrl = computed(() => bannerPath.value ? `/storage/${bannerPath.value}` : null);

    function updateProfilePhotoPath(newPath) { profilePhotoPath.value = newPath; }
    function updateBannerPath(newPath) { bannerPath.value = newPath; }
    function updateThemeColor(newColor) { themeColor.value = newColor; }
    function updateBio(newBio) { bio.value = newBio; }
    function updateLinkOrder(newOrder) { userLinks.value = newOrder; }

    function addLink(link) {
        userLinks.value = [...userLinks.value, link];
    }

    function updateLink(link) {
        const index = userLinks.value.findIndex((item) => item.id === link.id);
        if (index === -1) return;
        userLinks.value = userLinks.value.map((item, i) => (i === index ? { ...item, ...link } : item));
    }

    function removeLink(linkId) {
        userLinks.value = userLinks.value.filter((item) => item.id !== linkId);
    }

    function syncLinks(links) {
        userLinks.value = [...(links || [])];
    }

    return {
        name, username, bio, profilePhotoPath, bannerPath, themeColor, userLinks, publicLinks,
        publicUrl, profilePhotoUrl, bannerUrl,
        updateProfilePhotoPath, updateBannerPath, updateThemeColor, updateBio,
        updateLinkOrder, addLink, updateLink, removeLink, syncLinks,
    };
});
