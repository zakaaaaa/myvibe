<template>
	<main>
		<router-view />
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
		}
	}
};
</script>
<style lang="scss">
@import 'assets/scss/styles';
</style>