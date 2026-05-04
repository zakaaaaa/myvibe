<template>
    <!-- ============ BOTTOM BAR (persistent, dismissible) ============ -->
    <transition name="bar-slide">
        <div
            v-if="isGuest() && barVisible && !showAuthModal"
            class="auth-gate-bar"
        >
            <div class="auth-gate-bar__inner">
                <div class="brand">
                    <div class="brand__logo">
                        <fa icon="star" />
                    </div>
                    <div class="brand__text">
                        <h4>Join MyVibe</h4>
                        <p>Rate your favorites</p>
                    </div>
                </div>

                <div class="actions">
                    <button class="btn-bar btn-bar--ghost" @click="goLogin">Log in</button>
                    <button class="btn-bar btn-bar--primary" @click="goRegister">Sign up</button>
                    <button
                        class="btn-bar btn-bar--icon"
                        @click="openStoreModal"
                        aria-label="Get the app"
                    >
                        <fa :icon="['fas', 'mobile-screen']" />
                    </button>
                </div>

                <button
                    class="bar-close"
                    @click="dismissBar"
                    aria-label="Dismiss"
                >
                    <fa icon="xmark" />
                </button>
            </div>
        </div>
    </transition>

    <!-- ============ MODAL (action-triggered + Get App) ============ -->
    <transition name="fade">
        <div
            v-if="showAuthModal"
            class="auth-gate-overlay"
            @click.self="handleClose"
        >
            <transition name="slide-up" appear>
                <div v-if="showAuthModal" class="auth-gate-modal">
                    <button class="close-btn" @click="handleClose" aria-label="Close">
                        <fa icon="xmark" />
                    </button>

                    <div class="modal-icon">
                        <div class="logo-circle">
                            <fa icon="star" />
                        </div>
                    </div>

                    <h2 class="modal-title">{{ modalTitle }}</h2>
                    <p class="modal-subtitle">{{ contextMessage }}</p>

                    <!-- Auth CTAs (hidden kalau mode === 'store-only') -->
                    <template v-if="modalMode !== 'store-only'">
                        <div class="cta-stack">
                            <button class="btn-primary" @click="goLogin">Log In</button>
                            <button class="btn-secondary" @click="goRegister">Create Account</button>
                        </div>

                        <div class="divider"><span>or get the app</span></div>
                    </template>

                    <div class="store-buttons">
                        <a
                            :href="appStoreUrl"
                            target="_blank"
                            rel="noopener"
                            class="store-btn"
                            aria-label="Download on the App Store"
                        >
                            <img
                                src="https://tools.applemediaservices.com/api/badges/download-on-the-app-store/black/en-us?size=250x83"
                                alt="Download on the App Store"
                            />
                        </a>
                        <a
                            :href="playStoreUrl"
                            target="_blank"
                            rel="noopener"
                            class="store-btn"
                            aria-label="Get it on Google Play"
                        >
                            <img
                                src="https://play.google.com/intl/en_us/badges/static/images/badges/en_badge_web_generic.png"
                                alt="Get it on Google Play"
                            />
                        </a>
                    </div>
                </div>
            </transition>
        </div>
    </transition>
</template>

<script>
import { useAuthGate } from '@/composables/useAuthGate';
import { computed, ref, watch } from 'vue';

export default {
    name: 'AuthGateModal',
    setup() {
        const { showAuthModal, authModalAction, closeAuthModal, isGuest, openAuthModal } = useAuthGate();

        // === Bar visibility state (dismissible, reappear saat aksi) ===
        const barVisible = ref(true);

        // Saat user tap aksi (follow/save/dll), modal terbuka.
        // Setelah modal close, bar harus reappear lagi.
        watch(showAuthModal, (newVal, oldVal) => {
            if (oldVal === true && newVal === false) {
                // Modal baru di-close → reappear bar
                barVisible.value = true;
            }
        });

        // === Modal mode: 'action' (follow/save/dll) atau 'store-only' (klik Get App) ===
        const modalMode = ref('action');

        // === Context messages per action ===
        const messages = {
            follow: 'Log in to follow and see updates from your favorite people.',
            message: 'Log in to send vibes and connect with the community.',
            save: 'Log in to save vibes to your collection.',
            comment: 'Log in to join the conversation.',
            report: 'Log in to report content.',
            share: 'Log in to share vibes with friends.',
            'store-only': 'Download MyVibe to rate your favorites and connect with friends.',
            default: 'Log in or sign up to interact with vibes from the community.',
        };

        const contextMessage = computed(() => {
            if (modalMode.value === 'store-only') return messages['store-only'];
            return messages[authModalAction.value] || messages.default;
        });

        const modalTitle = computed(() => {
            return modalMode.value === 'store-only' ? 'Get MyVibe' : 'Join MyVibe';
        });

        const appStoreUrl = 'https://apps.apple.com/app/id6742460190';
        const playStoreUrl = 'https://play.google.com/store/apps/details?id=com.myvibecommunity.app';

        return {
            showAuthModal,
            authModalAction,
            barVisible,
            modalMode,
            contextMessage,
            modalTitle,
            appStoreUrl,
            playStoreUrl,
            closeAuthModal,
            isGuest,
            openAuthModal,
        };
    },
    methods: {
        handleClose() {
            this.closeAuthModal();
            this.modalMode = 'action'; // reset ke default
        },
        dismissBar() {
            this.barVisible = false;
        },
        openStoreModal() {
            this.modalMode = 'store-only';
            // Trigger modal terbuka via composable (action key 'store-only')
            this.openAuthModal('store-only');
        },
        goLogin() {
            const returnPath = window.location.pathname + window.location.search;
            this.closeAuthModal();
            this.modalMode = 'action';
            this.$router.push({
                path: '/login',
                query: { redirect: returnPath },
            });
        },
        goRegister() {
            const returnPath = window.location.pathname + window.location.search;
            this.closeAuthModal();
            this.modalMode = 'action';
            this.$router.push({
                path: '/register',
                query: { redirect: returnPath },
            });
        },
    },
};
</script>

<style scoped lang="scss">
@import '@/assets/scss/color.scss';

/* ============================================ */
/* ============== BOTTOM BAR ================== */
/* ============================================ */
.auth-gate-bar {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 150;
    padding: 10px 12px calc(env(safe-area-inset-bottom, 0px) + 10px);
    background: rgba(10, 10, 10, 0.82);
    backdrop-filter: blur(28px) saturate(1.4);
    -webkit-backdrop-filter: blur(28px) saturate(1.4);
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 -8px 32px rgba(0, 0, 0, 0.45);

    &__inner {
        display: flex;
        align-items: center;
        gap: 10px;
        max-width: 720px;
        margin: 0 auto;
        position: relative;
    }
}

.brand {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
    padding-right: 4px;

    &__logo {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #8b5cf6, #6366f1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 14px;
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.35);
        flex-shrink: 0;
    }

    &__text {
        h4 {
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }
        p {
            color: rgba(255, 255, 255, 0.55);
            font-size: 11px;
            margin: 1px 0 0;
            line-height: 1.2;
        }
    }
}

.actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex: 1;
    justify-content: flex-end;
    margin-right: 28px; /* space untuk close button */
}

.btn-bar {
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 12.5px;
    cursor: pointer;
    transition: all 0.18s ease;
    white-space: nowrap;
    padding: 9px 14px;
    flex-shrink: 0;

    &--ghost {
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.12);

        &:hover {
            background: rgba(255, 255, 255, 0.14);
        }
        &:active {
            transform: scale(0.96);
        }
    }

    &--primary {
        background: linear-gradient(135deg, #8b5cf6, #6366f1);
        color: #fff;
        box-shadow: 0 4px 14px rgba(139, 92, 246, 0.32);

        &:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(139, 92, 246, 0.45);
        }
        &:active {
            transform: scale(0.96);
        }
    }

    &--icon {
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.12);
        width: 38px;
        height: 38px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;

        &:hover {
            background: rgba(255, 255, 255, 0.14);
        }
        &:active {
            transform: scale(0.94);
        }
    }
}

.bar-close {
    position: absolute;
    top: 50%;
    right: 0;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.06);
    border: none;
    color: rgba(255, 255, 255, 0.6);
    width: 22px;
    height: 22px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    transition: all 0.2s ease;

    &:hover {
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
    }
}

/* ============================================ */
/* ================== MODAL =================== */
/* ============================================ */
.auth-gate-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.auth-gate-modal {
    background: linear-gradient(145deg, rgba(30, 30, 30, 0.98), rgba(10, 10, 10, 0.98));
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 24px;
    padding: 32px 24px 28px;
    max-width: 400px;
    width: 100%;
    text-align: center;
    position: relative;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
}

.close-btn {
    position: absolute;
    top: 14px;
    right: 14px;
    background: rgba(255, 255, 255, 0.08);
    border: none;
    color: #fff;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    transition: background 0.2s ease;

    &:hover {
        background: rgba(255, 255, 255, 0.15);
    }
}

.modal-icon {
    margin-bottom: 18px;
}

.logo-circle {
    width: 64px;
    height: 64px;
    border-radius: 18px;
    background: linear-gradient(135deg, #8b5cf6, #6366f1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 28px;
    box-shadow: 0 8px 24px rgba(139, 92, 246, 0.4);
}

.modal-title {
    color: #fff;
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 8px;
}

.modal-subtitle {
    color: rgba(255, 255, 255, 0.65);
    font-size: 14px;
    line-height: 1.5;
    margin: 0 0 24px;
    padding: 0 8px;
}

.cta-stack {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 22px;
}

.btn-primary {
    background: linear-gradient(135deg, #8b5cf6, #6366f1);
    color: #fff;
    border: none;
    padding: 14px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.2s ease;

    &:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(139, 92, 246, 0.35);
    }
    &:active {
        transform: translateY(0);
    }
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.08);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.15);
    padding: 14px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: background 0.2s ease;

    &:hover {
        background: rgba(255, 255, 255, 0.12);
    }
}

.divider {
    position: relative;
    margin: 18px 0 16px;
    color: rgba(255, 255, 255, 0.4);
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;

    &::before,
    &::after {
        content: '';
        position: absolute;
        top: 50%;
        width: 30%;
        height: 1px;
        background: rgba(255, 255, 255, 0.1);
    }
    &::before { left: 0; }
    &::after { right: 0; }

    span {
        background: transparent;
        padding: 0 8px;
    }
}

.store-buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
}

.store-btn {
    display: inline-block;
    transition: transform 0.15s ease, opacity 0.2s ease;

    &:hover {
        transform: translateY(-2px);
        opacity: 0.9;
    }

    img {
        height: 44px;
        width: auto;
        display: block;
    }
}

/* ============================================ */
/* ================ ANIMATIONS ================ */
/* ============================================ */

/* Bottom bar slide-up entrance + slide-down exit */
.bar-slide-enter-active {
    transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
}
.bar-slide-leave-active {
    transition: transform 0.25s ease, opacity 0.2s ease;
}
.bar-slide-enter-from {
    transform: translateY(100%);
    opacity: 0;
}
.bar-slide-leave-to {
    transform: translateY(100%);
    opacity: 0;
}

/* Modal fade overlay */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.25s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Modal slide-up content */
.slide-up-enter-active {
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
}
.slide-up-leave-active {
    transition: transform 0.2s ease, opacity 0.2s ease;
}
.slide-up-enter-from {
    transform: translateY(20px);
    opacity: 0;
}
.slide-up-leave-to {
    transform: translateY(10px);
    opacity: 0;
}
</style>