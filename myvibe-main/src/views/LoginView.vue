<template>
	<section class="auth">
		<div class="container">
			<div class="back">
				<RouterLink to="/landing"> <fa icon="arrow-left-long" class="text-white" /></RouterLink>
			</div>
			<h1 class="title mb-4">Welcome Back!</h1>
			<form autocomplete="off" @submit.prevent="login">
				<div class="mb-3">
					<label class="form-label">Email</label>
					<input type="email" class="form-control" v-model="email" placeholder="Type your email address here" required />
				</div>
				<div class="mb-3">
					<label class="form-label">Password</label>
					<input type="password" class="form-control" v-model="password" placeholder="Type your password here" required />
				</div>
				<div class="d-flex justify-content-center align-items-center flex-column mt-4 gap-2">
					<button type="submit" class="btn-action">
						<span v-if="!loading" class="me-2">Sign In</span>
						<span v-else> <fa icon="spinner" class="fa-spin" /> Loading... </span>
					</button>
					<p class="mb-1 mt-3">Forgot your password? <RouterLink to="/forgot-password">Click Here</RouterLink></p>
					<p class="mb-1">Your email is not verified? <RouterLink to="/resend-verify">Verified Now</RouterLink></p>
				</div>
			</form>
		</div>
		<span data-bs-toggle="modal" data-bs-target="#notifModal" ref="notifModalBtn"></span>
		<div class="modal fade" id="notifModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="notifModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body notification">
						<div class="title">
							<h1>
								{{ titleNotif }} <span :class="{ success: statusNotif === 'success' }">{{ titleNotifSecond }}</span>
							</h1>
						</div>
						<p class="m-0 text-center">{{ message }}</p>
					</div>
					<div class="modal-button" v-if="showLink">
						<RouterLink :to="linkTo" class="btn-block purple" @click="clickLink"> {{ linkText }} </RouterLink>
					</div>
					<div class="modal-button" v-if="showDismiss">
						<button type="button" class="btn-block" data-bs-dismiss="modal" aria-label="Close">Close</button>
					</div>
				</div>
			</div>
		</div>
	</section>
</template>

<script>
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import authService from '@/services/authService';
import axios from 'axios';
import { googleTokenLogin } from 'vue3-google-login';
import { requestPermission } from '@/services/firebaseMessaging';
import { Capacitor } from '@capacitor/core';
import { GoogleAuth, User } from '@codetrix-studio/capacitor-google-auth';

export default {
	name: 'LoginView',
	data() {
		return {
			email: '',
			password: '',
			loading: false,
			statusNotif: 'failed',
			titleNotif: '',
			titleNotifSecond: '',
			message: '',
			showLink: false,
			linkTo: '/',
			linkText: '',
			showDismiss: false
		};
	},
	methods: {
		async login() {
			this.loading = true;
			try {
				const response = await authService.login({
					email: this.email,
					password: this.password
				});
				if (response.data && response.data.token) {
					const token = response.data.token;
					localStorage.setItem('token', token);
					this.requestPushPermission();
					if (!response.data.user.mbti_id) {
						this.$router.push('/fill-vibe');
					} else {
						this.$router.push('/dashboard');
					}
					this.loginGoogle = false;
				} else {
					this.loading = false;
					this.titleNotif = 'Login';
					this.titleNotifSecond = 'Failed';
					this.message = 'Looks like something went wrong when you tried to log in';
					this.showLink = false;
					this.showDismiss = true;
					this.showNotifModal();
				}
			} catch (error) {
				console.log(error);
				this.titleNotif = 'Login';
				this.titleNotifSecond = 'Failed';
				this.message = error.response.data.message;
				this.showLink = false;
				this.showDismiss = true;
				this.linkTo = '';
				this.showNotifModal();
			} finally {
				this.loading = false;
			}
		},

		showNotifModal() {
			this.$refs.notifModalBtn.click();
		},

		async requestPushPermission() {
			const token = await requestPermission();
			if (token) {
				const formData = new FormData();
				formData.append('_method', 'PUT');
				formData.append('fcm_token', token);
				const response = await authService.profile_put(formData);
			} else {
				console.error('Notification Permission Denied.');
			}
		}
	}
};
</script>
