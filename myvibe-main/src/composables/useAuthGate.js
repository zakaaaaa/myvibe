import { ref } from 'vue';

/**
 * Auth Gate — reusable logic untuk gate action yang butuh login.
 *
 * Pakai pattern singleton (state di luar function) supaya semua component
 * share state modal yang sama. Kalau guest klik tombol Follow di
 * UserVibeView, modal-nya muncul dari AuthGateModal.vue di App.vue.
 *
 * Usage di component:
 *   import { useAuthGate } from '@/composables/useAuthGate';
 *   const { requireAuth } = useAuthGate();
 *
 *   methods: {
 *     handleFollow() {
 *       if (!this.requireAuth('follow')) return;
 *       // ... existing follow logic
 *     }
 *   }
 */

// State global (singleton) — share across components
const showAuthModal = ref(false);
const authModalAction = ref('');

export function useAuthGate() {
    /**
     * Cek apakah user adalah guest (belum login).
     */
    const isGuest = () => {
        return !localStorage.getItem('token');
    };

    /**
     * Gate action yang butuh login.
     * Return true kalau user authenticated (lanjut action).
     * Return false kalau guest (modal muncul, action di-block).
     *
     * @param {string} actionLabel - label action: 'follow', 'message', 'save', 'comment', 'report'
     * @returns {boolean}
     */
    const requireAuth = (actionLabel = '') => {
        if (isGuest()) {
            authModalAction.value = actionLabel;
            showAuthModal.value = true;
            return false;
        }
        return true;
    };

    /**
     * Tutup modal auth gate.
     */
    const closeAuthModal = () => {
        showAuthModal.value = false;
        authModalAction.value = '';
    };

    /**
     * Buka modal manual (kalau perlu trigger dari tempat lain).
     */
    const openAuthModal = (actionLabel = '') => {
        authModalAction.value = actionLabel;
        showAuthModal.value = true;
    };

    return {
        isGuest,
        requireAuth,
        showAuthModal,
        authModalAction,
        closeAuthModal,
        openAuthModal,
    };
}