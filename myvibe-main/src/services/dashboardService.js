import apiClient from '@/services/axios';


const dashboardService = {
    home() {
        return apiClient.get('/api/home');
    },
    home_detail(data) {
        return apiClient.get('/api/home_detail', {
            params: data,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
        });
    },
    categories() {
        return apiClient.get('/api/category');
    },
    postVibe(data) {
        return apiClient.post('/api/vibe', data, {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });
    },
    getVibe(slug) {
        return apiClient.get('/api/vibe/' + slug);
    },
    getExplore(data) {
        return apiClient.get('/api/vibe_explore', {
            params: data
        });
    },
    searchFriends(data) {
        return apiClient.get('/api/friendship_search', {
            params: data,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
        });
    },
    getFriends(slug) {
        return apiClient.get('/api/friendship/' + slug);
    },
    getFriendsCategory(username, id, data) {
        return apiClient.get('/api/friendship/' + username + '/' + id, {
            params: data,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
        });
    },
    followFriends(id) {
        return apiClient.post('/api/follow/' + id);
    },
    unfollowFriends(id) {
        return apiClient.post('/api/unfollow/' + id);
    },
    getFollowers(id) {
        return apiClient.get('/api/followers/' + id);
    },
    getListChatMessage() {
        return apiClient.get('api/messages/conversations');
    },
    getDetailChatMessage(id) {
        return apiClient.get('api/messages/conversation/' + id);
    },
    postMessage(data) {
        return apiClient.post('/api/messages', data, {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });
    },
    postLogout() {
        return apiClient.post('/api/logout');
    },
    postDeleteAccount() {
        return apiClient.post('/api/hapus_akun');
    },
    postCategoryOther(data) {
        return apiClient.post('/api/category_other', data, {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });
    },
    getCategoryOther() {
        return apiClient.get('/api/category_other');
    },
    deleteCategoryOther(id) {
        return apiClient.delete('/api/category_other/' + id);
    },
    postOtherVibe(data) {
        return apiClient.post('/api/vibe_other', data, {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });
    },
    getOtherVibe() {
        return apiClient.get('/api/vibe_other');
    },
    getOtherUserVibe(username) {
        return apiClient.get('/api/category_other_user/' + username);
    },
    getMoreOtherUserVibe(username, id, data) {
        return apiClient.get('/api/category_other_user_detail/' + username + '/' + id, {
            params: data,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
        });
    },
    getOtherVibeShow(slug) {
        return apiClient.get('/api/vibe_other/' + slug);
    },
    getOtherMoreList(data, id) {
        return apiClient.get('/api/category_other/' + id, {
            params: data,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
        });
    },
    getCors() {
        return apiClient.get('/api/cors');
    },
    postReportVibe(data) {
        return apiClient.post('/api/report_vibe', data, {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });
    },
    // === GIPHY ===
    searchGiphy(query, limit = 24, offset = 0) {
        return apiClient.get('/api/giphy/search', {
            params: { q: query, limit, offset }
        });
    },
    trendingGiphy(limit = 24, offset = 0) {
        return apiClient.get('/api/giphy/trending', {
            params: { limit, offset }
        });
    },

// === Update sendMessage untuk support attachment ===
sendMessage(payload) {
  // payload: { receiver_id, message_text?, reply_to_id?, reply_to_text?,
  //            attachment_type?, attachment_payload? }
  return apiClient.post('/messages', payload);
},
    // Saved Vibes
    toggleSaveVibe(data) {
        return apiClient.post('/api/saved-vibes', data);
    },
    getSavedVibes() {
        return apiClient.get('/api/saved-vibes');
    },
    checkSavedVibe(vibeId) {
        return apiClient.get('/api/saved-vibes/check/' + vibeId);
    },
};

export default dashboardService;