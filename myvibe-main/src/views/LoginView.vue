<template>
	<section class="auth">
		<!-- Blob decorations -->
		<div class="lg-blob lg-blob--1"></div>
		<div class="lg-blob lg-blob--2"></div>
		<div class="lg-blob lg-blob--3"></div>
		<div class="lg-blob lg-blob--4"></div>

		<!-- Back button -->
		<RouterLink to="/landing" class="back-floating">
			<fa icon="arrow-left" />
		</RouterLink>

		<div class="container">
			<!-- Logo & Header -->
			<div class="login-header">
				<div class="login-logo-ring">
					<img src="@/assets/logo.png" alt="MyVibe" />
				</div>
				<h1 class="title">Welcome <span class="highlight">Back!</span></h1>
				<p class="login-subtitle">Sign in to continue your vibe</p>
			</div>

			<!-- Glass Form Card -->
			<div class="login-glass-card">
				<form autocomplete="off" @submit.prevent="login" class="login-form">
					<div class="glass-input-group">
						<label class="glass-label">Email Address</label>
						<div class="glass-input">
							<fa icon="envelope" class="glass-input__icon" />
							<input type="email" v-model="email" placeholder="e.g. john@email.com" required />
						</div>
					</div>

					<div class="glass-input-group">
						<label class="glass-label">Password</label>
						<div class="glass-input">
							<fa icon="lock" class="glass-input__icon" />
							<input :type="showPassword ? 'text' : 'password'" v-model="password" placeholder="Enter your password" required />
							<button type="button" class="toggle-password" @click="showPassword = !showPassword">
								<fa :icon="showPassword ? 'eye-slash' : 'eye'" />
							</button>
						</div>
					</div>

					<div class="btn-center">
						<button type="submit" class="btn-action btn-login" :disabled="loading">
							<span v-if="!loading">
								Sign In
								<fa icon="arrow-right" class="ms-2" />
							</span>
							<span v-else>
								<fa icon="spinner" class="fa-spin" /> Signing in...
							</span>
						</button>
					</div>
				</form>
			</div>

			<!-- Bottom Links -->
			<div class="login-bottom">
				<div class="glass-divider">
					<span>need help?</span>
				</div>

				<div class="login-links">
					<RouterLink to="/forgot-password" class="login-link">
						<fa icon="key" class="login-link__icon" />
						Forgot Password?
					</RouterLink>
					<RouterLink to="/resend-verify" class="login-link">
						<fa icon="envelope-circle-check" class="login-link__icon" />
						Verify Email
					</RouterLink>
				</div>

				<p class="login-register-link">
					Don't have an account? <RouterLink :to="registerLink">Register Here!</RouterLink>
				</p>
			</div>
		</div>

		<!-- Notification Modal -->
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
import { requestPermission } from '@/services/firebaseMessaging';

export default {
	name: 'LoginView',
	data() {
		return {
			email: '',
			password: '',
			loading: false,
			showPassword: false,
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
	computed: {
    registerLink() {
        const redirect = this.$route.query.redirect;
        return redirect ? `/register?redirect=${encodeURIComponent(redirect)}` : '/register';
    }
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
					const redirect = this.$route.query.redirect;
					if (!response.data.user.mbti_id) {
						this.$router.push('/fill-vibe');
					} else if (redirect) {
						this.$router.push(redirect);
					} else {
						this.$router.push('/dashboard');
					}
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
		clickLink() {
			var backdrop = document.querySelector('.modal-backdrop');
			if (backdrop) { backdrop.remove(); }
		},
		async requestPushPermission() {
			const token = await requestPermission();
			if (token) {
				const formData = new FormData();
				formData.append('_method', 'PUT');
				formData.append('fcm_token', token);
				await authService.profile_put(formData);
			}
		}
	}
};
</script>

<style lang="scss" scoped>
@import '@/assets/scss/color.scss';

// === BLOBS - BIGGER ===
.lg-blob {
	position: absolute;
	filter: blur(50px);
	pointer-events: none;
	z-index: 0;
}

.lg-blob--1 {
	top: -10%;
	left: -15%;
	width: min(320px, 70vw);
	height: min(320px, 70vw);
	background: radial-gradient(circle, rgba($purple, 0.35) 0%, rgba(#6c5ce7, 0.12) 40%, transparent 70%);
	border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
	animation: lgBlobMorph1 12s ease-in-out infinite;
}

.lg-blob--2 {
	bottom: 2%;
	right: -12%;
	width: min(280px, 62vw);
	height: min(280px, 62vw);
	background: radial-gradient(circle, rgba(#4a3adf, 0.3) 0%, rgba($purple, 0.1) 40%, transparent 70%);
	border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
	animation: lgBlobMorph2 14s ease-in-out infinite;
}

.lg-blob--3 {
	top: 32%;
	left: 50%;
	transform: translateX(-50%);
	width: min(350px, 80vw);
	height: min(180px, 40vw);
	background: radial-gradient(ellipse, rgba($purple, 0.14) 0%, rgba(#7c6cf0, 0.06) 40%, transparent 70%);
	border-radius: 50%;
	animation: lgBlobFloat 9s ease-in-out infinite;
}

.lg-blob--4 {
	top: 60%;
	left: -8%;
	width: min(200px, 45vw);
	height: min(200px, 45vw);
	background: radial-gradient(circle, rgba(#9b8fff, 0.2) 0%, rgba($purple, 0.06) 50%, transparent 70%);
	border-radius: 50% 30% 40% 60% / 40% 60% 50% 50%;
	animation: lgBlobMorph3 16s ease-in-out infinite;
}

@keyframes lgBlobMorph1 {
	0%, 100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(0, 0) rotate(0deg); }
	50% { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; transform: translate(12px, 18px) rotate(40deg); }
}
@keyframes lgBlobMorph2 {
	0%, 100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; transform: translate(0, 0) rotate(0deg); }
	50% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(-15px, -12px) rotate(-40deg); }
}
@keyframes lgBlobFloat {
	0%, 100% { transform: translateX(-50%) translateY(0) scale(1); opacity: 0.14; }
	50% { transform: translateX(-50%) translateY(-18px) scale(1.06); opacity: 0.22; }
}
@keyframes lgBlobMorph3 {
	0%, 100% { border-radius: 50% 30% 40% 60% / 40% 60% 50% 50%; transform: translate(0, 0) rotate(0deg); }
	33% { border-radius: 40% 60% 50% 40% / 60% 40% 60% 50%; transform: translate(10px, -12px) rotate(30deg); }
	66% { border-radius: 60% 40% 60% 40% / 50% 50% 40% 60%; transform: translate(-8px, 10px) rotate(-20deg); }
}

// === BACK BUTTON ===
.back-floating {
	position: fixed;
	top: calc(env(safe-area-inset-top) + 20px);
	left: 20px;
	width: 44px;
	height: 44px;
	display: flex;
	align-items: center;
	justify-content: center;
	border-radius: 50%;
	color: $white;
	text-decoration: none;
	z-index: 100;
	font-size: 15px;
	cursor: pointer;
	background: rgba($white, 0.04);
	backdrop-filter: blur(20px);
	-webkit-backdrop-filter: blur(20px);
	border: 1px solid rgba($white, 0.08);
	box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba($white, 0.08);
	transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);

	&::before {
		content: '';
		position: absolute;
		top: 2px;
		left: 15%;
		right: 15%;
		height: 40%;
		background: linear-gradient(180deg, rgba($white, 0.1) 0%, transparent 100%);
		border-radius: 50%;
		pointer-events: none;
	}

	&:hover {
		transform: translateY(-2px) scale(1.08);
		background: rgba($purple, 0.15);
		border-color: rgba($purple, 0.25);
		color: $white;
		box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25), 0 0 16px rgba($purple, 0.1);
	}

	&:active { transform: scale(0.92); }
}

// === HEADER ===
.login-header {
	display: flex;
	flex-direction: column;
	align-items: center;
	text-align: center;
	padding-top: 56px;
	margin-bottom: 28px;
	animation: lgSlideIn 0.6s ease-out both;
}

.login-logo-ring {
	width: 80px;
	height: 80px;
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	margin-bottom: 18px;
	position: relative;
	background: rgba($white, 0.04);
	backdrop-filter: blur(16px);
	-webkit-backdrop-filter: blur(16px);
	border: 1px solid rgba($purple, 0.15);
	box-shadow: 0 6px 24px rgba($purple, 0.1), inset 0 1px 0 rgba($white, 0.06);

	img {
		width: 44px;
		height: 44px;
		object-fit: contain;
		filter: drop-shadow(0 0 8px rgba($purple, 0.3));
	}

	&::before {
		content: '';
		position: absolute;
		inset: -5px;
		border-radius: 50%;
		border: 1px solid rgba($purple, 0.08);
		animation: lgRingPulse 3s ease-in-out infinite;
	}

	&::after {
		content: '';
		position: absolute;
		top: 3px;
		left: 18%;
		right: 18%;
		height: 35%;
		background: linear-gradient(180deg, rgba($white, 0.1) 0%, transparent 100%);
		border-radius: 50%;
		pointer-events: none;
	}
}

@keyframes lgRingPulse {
	0%, 100% { opacity: 0.3; transform: scale(1); }
	50% { opacity: 0.7; transform: scale(1.05); }
}

.login-subtitle {
	color: rgba($white1, 0.38);
	font-size: 13px;
	margin-top: 4px;
}

// === GLASS CARD ===
.login-glass-card {
	background: rgba($white, 0.03);
	backdrop-filter: blur(20px);
	-webkit-backdrop-filter: blur(20px);
	border: 1px solid rgba($white, 0.07);
	border-radius: 22px;
	padding: 24px 20px;
	position: relative;
	z-index: 1;
	animation: lgSlideIn 0.6s ease-out 0.1s both;
	box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15), inset 0 1px 0 rgba($white, 0.05);

	&::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		height: 45%;
		background: linear-gradient(180deg, rgba($white, 0.04) 0%, transparent 100%);
		border-radius: 22px 22px 0 0;
		pointer-events: none;
	}
}

.login-form {
	position: relative;
	z-index: 1;
}

.glass-input-group {
	margin-bottom: 18px;
	animation: lgSlideIn 0.5s ease-out both;
	&:nth-child(1) { animation-delay: 0.15s; }
	&:nth-child(2) { animation-delay: 0.22s; }
}

.glass-label {
	display: block;
	font-size: 12px;
	font-weight: 500;
	color: rgba($white1, 0.5);
	margin-bottom: 6px;
	padding-left: 4px;
	letter-spacing: 0.3px;
}

.glass-input {
	display: flex;
	align-items: center;
	background: rgba($white, 0.035);
	backdrop-filter: blur(16px);
	-webkit-backdrop-filter: blur(16px);
	border: 1px solid rgba($white, 0.07);
	border-radius: 14px;
	padding: 0 16px;
	min-height: 52px;
	transition: all 0.35s cubic-bezier(0.23, 1, 0.32, 1);
	position: relative;
	box-shadow: inset 0 1px 0 rgba($white, 0.04), 0 2px 10px rgba(0, 0, 0, 0.08);

	&::before {
		content: '';
		position: absolute;
		top: 0; left: 0; right: 0;
		height: 50%;
		background: linear-gradient(180deg, rgba($white, 0.025) 0%, transparent 100%);
		border-radius: 14px 14px 0 0;
		pointer-events: none;
	}

	&:focus-within {
		border-color: rgba($purple, 0.35);
		background: rgba($white, 0.05);
		box-shadow: 0 0 0 3px rgba($purple, 0.06), inset 0 1px 0 rgba($white, 0.06), 0 4px 20px rgba(0, 0, 0, 0.12);
		transform: translateY(-1px);

		.glass-input__icon { color: $purple; }
	}

	&__icon {
		color: rgba($white, 0.28);
		font-size: 14px;
		margin-right: 12px;
		transition: all 0.3s ease;
		flex-shrink: 0;
	}

	input {
		flex: 1;
		background: transparent;
		border: none;
		outline: none;
		color: $white1;
		font-size: 15px;
		font-family: inherit;
		min-width: 0;
		padding: 0;

		&::placeholder {
			color: rgba($white, 0.22);
			font-size: 13px;
			font-weight: 300;
		}
	}
}

.toggle-password {
	background: none;
	border: none;
	color: rgba($white, 0.25);
	font-size: 14px;
	padding: 4px 0 4px 8px;
	cursor: pointer;
	transition: all 0.3s ease;
	flex-shrink: 0;
	position: relative;
	z-index: 1;

	&:hover { color: $purple; }
}

// === BUTTON CENTER ===
.btn-center {
	display: flex;
	justify-content: center;
	margin-top: 8px;
}

.btn-login {
	position: relative;
	overflow: hidden;
	width: 100%;
	background: linear-gradient(135deg, rgba($purple, 0.65) 0%, rgba($purple, 0.85) 50%, rgba(#5a4dd6, 0.75) 100%) !important;
	backdrop-filter: blur(20px);
	-webkit-backdrop-filter: blur(20px);
	border: 1px solid rgba($white, 0.12) !important;
	box-shadow: 0 8px 28px rgba($purple, 0.2), inset 0 1px 0 rgba($white, 0.18);
	transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);

	&::after {
		content: '';
		position: absolute;
		top: 0; left: 0; right: 0;
		height: 50%;
		background: linear-gradient(180deg, rgba($white, 0.12) 0%, transparent 100%);
		border-radius: 30px 30px 0 0;
		pointer-events: none;
	}

	&::before {
		content: '';
		position: absolute;
		top: 0;
		left: -100%;
		width: 100%;
		height: 100%;
		background: linear-gradient(90deg, transparent, rgba($white, 0.12), transparent);
		transition: left 0.6s ease;
		z-index: 1;
	}

	&:not(:disabled):hover {
		transform: translateY(-3px);
		box-shadow: 0 14px 40px rgba($purple, 0.3), inset 0 1px 0 rgba($white, 0.22), 0 0 25px rgba($purple, 0.12);
		&::before { left: 100%; }
	}

	&:not(:disabled):active { transform: translateY(-1px) scale(0.98); }
	&:disabled { opacity: 0.5; }
}

// === BOTTOM ===
.login-bottom {
	margin-top: 28px;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 16px;
	animation: lgSlideIn 0.6s ease-out 0.3s both;
}

.glass-divider {
	width: 100%;
	max-width: 300px;
	display: flex;
	align-items: center;
	gap: 12px;

	&::before, &::after {
		content: '';
		flex: 1;
		height: 1px;
		background: linear-gradient(90deg, transparent, rgba($white, 0.08), transparent);
	}

	span {
		color: rgba($white, 0.28);
		font-size: 11px;
		font-weight: 400;
		letter-spacing: 0.8px;
		text-transform: uppercase;
	}
}

.login-links {
	display: flex;
	gap: 10px;
	width: 100%;
}

.login-link {
	flex: 1;
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 8px;
	padding: 13px 12px;
	border-radius: 14px;
	font-size: 11px;
	font-weight: 500;
	color: rgba($white1, 0.5);
	text-decoration: none;
	background: rgba($white, 0.025);
	backdrop-filter: blur(12px);
	-webkit-backdrop-filter: blur(12px);
	border: 1px solid rgba($white, 0.05);
	box-shadow: inset 0 1px 0 rgba($white, 0.03), 0 2px 8px rgba(0, 0, 0, 0.08);
	transition: all 0.35s cubic-bezier(0.23, 1, 0.32, 1);
	position: relative;

	&::before {
		content: '';
		position: absolute;
		top: 0; left: 0; right: 0;
		height: 45%;
		background: linear-gradient(180deg, rgba($white, 0.02) 0%, transparent 100%);
		border-radius: 14px 14px 0 0;
		pointer-events: none;
	}

	&__icon {
		font-size: 13px;
		color: rgba($purple, 0.5);
		transition: color 0.3s ease;
	}

	&:hover {
		background: rgba($purple, 0.08);
		border-color: rgba($purple, 0.15);
		color: $white1;
		transform: translateY(-2px);
		box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12), 0 0 10px rgba($purple, 0.05);
		.login-link__icon { color: $purple; }
	}

	&:active { transform: translateY(-1px) scale(0.98); }
}

.login-register-link {
	font-size: 13px;
	color: rgba($white1, 0.4);
	margin: 4px 0 0;

	a {
		color: $purple;
		text-decoration: none;
		font-weight: 500;
		position: relative;

		&::after {
			content: '';
			position: absolute;
			bottom: -2px;
			left: 0;
			width: 0;
			height: 1px;
			background: $purple;
			transition: width 0.3s ease;
			box-shadow: 0 0 6px rgba($purple, 0.5);
		}

		&:hover::after { width: 100%; }
	}
}

@keyframes lgSlideIn {
	from { opacity: 0; transform: translateY(24px); }
	to { opacity: 1; transform: translateY(0); }
}
</style>