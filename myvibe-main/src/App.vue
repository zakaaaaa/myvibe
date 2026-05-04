<template>
    <main
        @touchstart="handleTouchStart"
        @touchmove="handleTouchMove"
        @touchend="handleTouchEnd"
    >
        <router-view v-slot="{ Component }">
            <transition :name="transitionName">
                <component :is="Component" />
            </transition>
        </router-view>
        <AuthGateModal />
    </main>
</template>

<script>
import { requestPermission, listenForMessages } from '@/services/firebaseMessaging';
import AuthGateModal from '@/components/AuthGateModal.vue';

export default {
    components: {
        AuthGateModal
    },
    data() {
        return {
            // Touch state
            touchStartX: 0,
            touchStartY: 0,
            touchDeltaX: 0,
            isEdgeSwipe: false,
            isSwipingBack: false,

            // Tuning constants
            EDGE_THRESHOLD: 30,
            TRIGGER_THRESHOLD: 80,
            VERTICAL_TOLERANCE: 60,

            transitionName: 'slide-left',

            // Routes yang TIDAK boleh swipe-back keluar
            rootRoutes: [
                '/',
                '/dashboard',
                '/landing',
                '/login',
                '/register',
                '/welcome',
                '/fill-vibe',
                '/forgot-password',
                '/resend-verify',
            ],
        };
    },
    watch: {
        $route(to, from) {
            if (this.isSwipingBack) {
                this.transitionName = 'slide-right';
                this.isSwipingBack = false;
                return;
            }
            const fromDepth = from.path.split('/').filter(Boolean).length;
            const toDepth = to.path.split('/').filter(Boolean).length;
            this.transitionName = toDepth < fromDepth ? 'slide-right' : 'slide-left';
        },
    },
    mounted() {
        requestPermission();
    },
    methods: {
        async requestPushPermission() {
            const token = await requestPermission();
            if (token) {
                localStorage.setItem('fcm_token', token);
                listenForMessages();
            } else {
                console.error('Notification Permission Denied.');
            }
        },

        handleTouchStart(e) {
            if (this.rootRoutes.includes(this.$route.path)) {
                this.isEdgeSwipe = false;
                return;
            }
            const touch = e.touches[0];
            if (touch.clientX < this.EDGE_THRESHOLD) {
                this.isEdgeSwipe = true;
                this.touchStartX = touch.clientX;
                this.touchStartY = touch.clientY;
                this.touchDeltaX = 0;
            } else {
                this.isEdgeSwipe = false;
            }
        },

        handleTouchMove(e) {
            if (!this.isEdgeSwipe) return;
            const touch = e.touches[0];
            const deltaY = Math.abs(touch.clientY - this.touchStartY);
            if (deltaY > this.VERTICAL_TOLERANCE) {
                this.isEdgeSwipe = false;
                return;
            }
            this.touchDeltaX = touch.clientX - this.touchStartX;
        },

        handleTouchEnd() {
            if (!this.isEdgeSwipe) return;
            if (this.touchDeltaX > this.TRIGGER_THRESHOLD) {
                this.isSwipingBack = true;
                this.$router.back();
            }
            this.isEdgeSwipe = false;
            this.touchDeltaX = 0;
        },
    }
};
</script>

<style lang="scss">
@import 'assets/scss/styles';

main {
    position: relative;
    width: 100%;
    min-height: 100vh;
    overflow-x: hidden;
}

/* Slide RIGHT (back navigation) */
.slide-right-enter-active,
.slide-right-leave-active {
    transition: transform 0.32s cubic-bezier(0.32, 0.72, 0, 1);
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    will-change: transform;
}
.slide-right-enter-from {
    transform: translateX(-30%);
}
.slide-right-leave-to {
    transform: translateX(100%);
}

/* Slide LEFT (forward navigation) */
.slide-left-enter-active,
.slide-left-leave-active {
    transition: transform 0.32s cubic-bezier(0.32, 0.72, 0, 1);
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    will-change: transform;
}
.slide-left-enter-from {
    transform: translateX(100%);
}
.slide-left-leave-to {
    transform: translateX(-30%);
}
</style>