import axios from 'axios';

const publicClient = axios.create({
    baseURL: process.env.VUE_APP_API_URL,
    withCredentials: false,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

// Attach token kalau ada (untuk enrich viewer.is_following dll),
// tapi endpoint tetap jalan tanpa token.
publicClient.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

const publicService = {
    // Profile + vibe_category + viewer flags (1 round-trip)
    // Match struktur FriendshipController::show()
    getUserProfile(username) {
        return publicClient.get(`/api/public/users/${username}`);
    },

    // Vibes per kategori untuk More page
    getUserVibesByCategory(username, categoryId, params = {}) {
        return publicClient.get(
            `/api/public/users/${username}/category/${categoryId}`,
            { params }
        );
    },

    // Vibe detail
    getVibe(username, vibeId) {
        return publicClient.get(`/api/public/vibes/${username}/${vibeId}`);
    },

    // Other vibe detail
    getOtherVibe(username, vibeId) {
        return publicClient.get(`/api/public/other-vibes/${username}/${vibeId}`);
    },

    // Other categories list (untuk render section "Other" di profile page)
    getOtherCategories(username) {
        return publicClient.get(`/api/public/users/${username}/categories-other`);
    },

    // Other vibes per kategori untuk More-Other page
    getOtherVibesByCategory(username, categoryId, params = {}) {
        return publicClient.get(
            `/api/public/users/${username}/category-other/${categoryId}`,
            { params }
        );
    },
};

export default publicService;