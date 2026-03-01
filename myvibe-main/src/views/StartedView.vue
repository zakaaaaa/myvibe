<template>
	<section v-if="!isLoading" class="started">
		<div class="logo">
			<img src="@/assets/logo.png" alt="" />
			<h1>My <span>Vibe</span></h1>
		</div>
		<div class="bottom-float-action">
			<RouterLink to="/landing" class="btn-action">Get Started!</RouterLink>
		</div>
	</section>
	<div v-else>
		<div class="loading">
			<fa icon="spinner" class="fa-spin-pulse" />
		</div>
	</div>
</template>

<script>
import axios from 'axios';

export default {
	name: 'StartedView',
	data() {
		return {
			isLoading: true
		};
	},
	mounted() {
		this.getProfile();
	},
	methods: {
		async getProfile() {
			const url = process.env.VUE_APP_API_URL + '/api/user';
			const token = localStorage.getItem('token');
			try {
				const response = await axios.get(url, {
					withCredentials: false,
					headers: {
						'Content-Type': 'application/json',
						Accept: 'application/json',
						Authorization: `Bearer ${token}`
					}
				});
				if (!response.data.username) {
					this.$router.push('/fill-vibe');
				} else {
					this.$router.push('/dashboard');
				}
			} catch (error) {
				console.error('Not Login.');
				this.isLoading = false;
			}
		}
	}
};
</script>
