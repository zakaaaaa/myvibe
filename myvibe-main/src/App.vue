<template>
	<main>
		<router-view />
	</main>
</template>
<script>
import { requestPermission, listenForMessages } from '@/services/firebaseMessaging';
export default {
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
