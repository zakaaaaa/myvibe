<template>
	<section class="auth">
		<div class="container">
			<div class="back">
				<RouterLink to="/login">
					<fa icon="arrow-left-long" class="text-white" />
				</RouterLink>
			</div>
			<h1 class="title mb-4">Recovery Your Account!</h1>
			<form autocomplete="off" @submit.prevent="forgotPassword">
				<div class="mb-3">
					<label class="form-label">Email</label>
					<input type="email" class="form-control" v-model="email" placeholder="Type your email address here" required />
				</div>
				<div class="d-flex justify-content-center align-items-center flex-column mt-4 gap-2">
					<button type="submit" class="btn-action">
						<span v-if="!loading" class="me-2">Send Now</span>
						<span v-else> <fa icon="spinner" class="fa-spin" /> Loading... </span>
					</button>
				</div>
			</form>
		</div>
		<span data-bs-toggle="modal" data-bs-target="#notifModal" ref="notifModalBtn"></span>
		<div class="modal fade" id="notifModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="notifModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body notification">
						<div class="logo">
							<img src="@/assets/logo.png" alt="" />
							<h1>My <span>Vibe</span></h1>
						</div>
						<h2>{{ message }}</h2>
						<div class="btn-group-action">
							<button type="button" class="btn btn-sm btn-dismiss" data-bs-dismiss="modal" aria-label="Close" v-if="showDismiss" ref="dismissNotifModal"><fa icon="circle-xmark" /> Close</button>
							<RouterLink :to="linkTo" class="btn btn-sm btn-link" v-if="showLink" @click="clickLink"> <fa icon="circle-check" /> {{ linkText }} </RouterLink>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</template>

<script>
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import authService from '@/services/authService';

export default {
	name: 'ForgotPasswordView',
	data() {
		return {
			email: '',
			loading: false,
			message: '',
			showLink: false,
			linkTo: '/',
			linkText: 'Okay',
			showDismiss: false
		};
	},
	methods: {
		async forgotPassword() {
			this.loading = true;
			try {
				const response = await authService.forgotPassword({
					email: this.email
				});
				this.message = response.data.message;
				this.showLink = true;
				this.showDismiss = false;
				this.linkTo = '/login';
				this.showNotifModal();
			} catch (error) {
				this.message = error.response.data.message;
				this.showLink = false;
				this.showDismiss = true;
				this.linkTo = '';
				this.showNotifModal();
			} finally {
				this.loading = false;
			}
		},
		clickLink() {
			var backdrop = document.querySelector('.modal-backdrop');
			if (backdrop) {
				backdrop.remove();
			}
		},
		showNotifModal() {
			this.$refs.notifModalBtn.click();
		}
	}
};
</script>
