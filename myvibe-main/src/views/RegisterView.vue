<template>
	<section class="auth">
		<!-- Blob decorations -->
		<div class="rg-blob rg-blob--1"></div>
		<div class="rg-blob rg-blob--2"></div>
		<div class="rg-blob rg-blob--3"></div>
		<div class="rg-blob rg-blob--4"></div>

		<!-- Back button -->
		<RouterLink to="/landing" class="back-floating">
			<fa icon="arrow-left" />
		</RouterLink>

		<div class="container">
			<!-- Logo & Header -->
			<div class="register-header">
				<div class="register-logo-ring">
					<img src="@/assets/logo.png" alt="MyVibe" />
				</div>
				<h1 class="title">Create an <span class="highlight">Account</span></h1>
				<p class="register-subtitle">Join MyVibe and share your taste with the world</p>
			</div>

			<!-- Glass Form Card -->
			<div class="register-glass-card">
				<form autocomplete="off" @submit.prevent="register" class="register-form">
					<div class="glass-input-group">
						<label class="glass-label">Username</label>
						<div class="glass-input">
							<fa icon="at" class="glass-input__icon" />
							<input type="text" v-model="username" placeholder="e.g. johndoe" required />
						</div>
					</div>

					<div class="glass-input-group">
						<label class="glass-label">Full Name</label>
						<div class="glass-input">
							<fa icon="user" class="glass-input__icon" />
							<input type="text" v-model="name" placeholder="e.g. John Doe" required />
						</div>
					</div>

					<div class="glass-input-group">
						<label class="glass-label">Email Address</label>
						<div class="glass-input">
							<fa icon="envelope" class="glass-input__icon" />
							<input type="email" v-model="email" placeholder="e.g. john@email.com" required />
						</div>
					</div>

					<div class="glass-input-row">
						<div class="glass-input-group">
							<label class="glass-label">Password</label>
							<div class="glass-input">
								<fa icon="lock" class="glass-input__icon" />
								<input :type="showPassword ? 'text' : 'password'" v-model="password" placeholder="Min. 6 chars" required />
							</div>
						</div>
						<div class="glass-input-group">
							<label class="glass-label">Confirm</label>
							<div class="glass-input">
								<fa icon="shield-halved" class="glass-input__icon" />
								<input :type="showPassword ? 'text' : 'password'" v-model="repassword" placeholder="Re-type" required />
							</div>
						</div>
					</div>

					<div class="register-actions">
						<div class="glass-checkbox" data-bs-toggle="modal" data-bs-target="#termsModal">
							<input class="form-check-input" type="checkbox" v-model="isChecked" :disabled="!isCheckboxEnabled" />
							<label>Agree to MyVibe's <span>Terms of Use</span></label>
						</div>

						<div class="btn-center">
							<button type="submit" class="btn-action btn-register" :disabled="!isChecked">
								<span v-if="!loading">
									Get Started
									<fa icon="arrow-right" class="ms-2" />
								</span>
								<span v-else>
									<fa icon="spinner" class="fa-spin" /> Loading...
								</span>
							</button>
						</div>
					</div>
				</form>
			</div>

			<!-- Bottom -->
			<div class="register-bottom">
				<p class="register-login-link">
					Already a member? <RouterLink to="/login">Sign in Here!</RouterLink>
				</p>
			</div>
		</div>

		<!-- Terms Modal -->
		<span data-bs-toggle="modal" data-bs-target="#notifModal" ref="notifModalBtn"></span>
		<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-body" style="height: 50vh" ref="termsContent" @scroll="checkScroll">
						<h2>General Terms</h2>
						<p>Updated at January 18th, 2025</p>
						<p>By accessing and placing an order with MyVibe, you confirm that you are in agreement with and bound by the terms of service contained in the Terms & Conditions outlined below.</p>
						<p>Under no circumstances shall MyVibe team be liable for any direct, indirect, special, incidental or consequential damages, including, but not limited to, loss of data or profit, arising out of the use, or the inability to use, the materials on this site.</p>
						<p>MyVibe will not be responsible for any outcome that may occur during the course of usage of our resources.</p>
						<h2>License</h2>
						<p>MyVibe grants you a revocable, non-exclusive, non-transferable, limited license to download, install and use the app strictly in accordance with the terms of this Agreement.</p>
						<p>You are agreeing to be bound by these Terms & Conditions. If you do not agree, please do not use the MyVibe Service.</p>
						<h2>Restrictions</h2>
						<p>You agree not to, and will not permit others to:</p>
						<ul>
							<li>License, sell, rent, lease, assign, distribute, transmit, host, outsource, disclose or otherwise commercially exploit the app.</li>
							<li>Modify, make derivative works of, disassemble, decrypt, reverse compile or reverse engineer any part of the app.</li>
							<li>Remove, alter or obscure any proprietary notice of MyVibe or its affiliates.</li>
						</ul>
						<h2>Contact Us</h2>
						<ul>
							<li>Email: <a href="mailto:lukersaintfeller@gmail.com">lukersaintfeller@gmail.com</a></li>
							<li>Phone: +6282278709681</li>
						</ul>
					</div>
					<div class="modal-button">
						<button type="button" class="btn-block" data-bs-dismiss="modal" @click="confirmClick" aria-label="Close">Close</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Notification Modal -->
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

export default {
	name: 'RegisterView',
	data() {
		return {
			username: '',
			name: '',
			email: '',
			password: '',
			repassword: '',
			loading: false,
			showPassword: false,
			statusNotif: 'failed',
			titleNotif: '',
			titleNotifSecond: '',
			message: '',
			showLink: false,
			linkTo: '/',
			linkText: 'Okay',
			showDismiss: false,
			isCheckboxEnabled: false,
			isChecked: false
		};
	},
	methods: {
		async register() {
			this.loading = true;
			try {
				const response = await authService.register({
					username: this.username,
					name: this.name,
					email: this.email,
					password: this.password,
					password_confirmation: this.repassword
				});
				this.statusNotif = 'success';
				this.titleNotif = 'Registration';
				this.titleNotifSecond = 'Success!';
				this.message = response.data.message;
				this.showLink = true;
				this.showDismiss = false;
				this.linkTo = '/login';
				this.showNotifModal();
			} catch (error) {
				this.statusNotif = 'failed';
				this.titleNotif = 'Registration';
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
		checkScroll() {
			const termsContent = this.$refs.termsContent;
			if (termsContent.scrollTop + termsContent.clientHeight >= termsContent.scrollHeight) {
				this.isCheckboxEnabled = true;
				this.isChecked = true;
			}
		},
		confirmClick() {
			this.isCheckboxEnabled = true;
			this.isChecked = true;
		},
		clickLink() {
			var backdrop = document.querySelector('.modal-backdrop');
			if (backdrop) { backdrop.remove(); }
		},
		showNotifModal() {
			this.$refs.notifModalBtn.click();
		}
	}
};
</script>

<style lang="scss" scoped>
@import '@/assets/scss/color.scss';

// === BLOBS - BIGGER ===
.rg-blob {
	position: absolute;
	filter: blur(50px);
	pointer-events: none;
	z-index: 0;
}

.rg-blob--1 {
	top: -8%;
	right: -12%;
	width: min(300px, 68vw);
	height: min(300px, 68vw);
	background: radial-gradient(circle, rgba($purple, 0.32) 0%, rgba(#6c5ce7, 0.1) 40%, transparent 70%);
	border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
	animation: rgBlobMorph1 12s ease-in-out infinite;
}

.rg-blob--2 {
	bottom: 2%;
	left: -12%;
	width: min(260px, 58vw);
	height: min(260px, 58vw);
	background: radial-gradient(circle, rgba(#4a3adf, 0.28) 0%, rgba($purple, 0.08) 40%, transparent 70%);
	border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
	animation: rgBlobMorph2 15s ease-in-out infinite;
}

.rg-blob--3 {
	top: 38%;
	left: 50%;
	transform: translateX(-50%);
	width: min(340px, 78vw);
	height: min(170px, 38vw);
	background: radial-gradient(ellipse, rgba($purple, 0.12) 0%, rgba(#7c6cf0, 0.05) 40%, transparent 70%);
	border-radius: 50%;
	animation: rgBlobFloat 9s ease-in-out infinite;
}

.rg-blob--4 {
	bottom: 25%;
	right: -6%;
	width: min(180px, 40vw);
	height: min(180px, 40vw);
	background: radial-gradient(circle, rgba(#9b8fff, 0.18) 0%, rgba($purple, 0.05) 50%, transparent 70%);
	border-radius: 50% 30% 40% 60% / 40% 60% 50% 50%;
	animation: rgBlobMorph3 16s ease-in-out infinite;
}

@keyframes rgBlobMorph1 {
	0%, 100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(0, 0) rotate(0deg); }
	50% { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; transform: translate(-12px, 16px) rotate(40deg); }
}
@keyframes rgBlobMorph2 {
	0%, 100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; transform: translate(0, 0) rotate(0deg); }
	50% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(14px, -12px) rotate(-40deg); }
}
@keyframes rgBlobFloat {
	0%, 100% { transform: translateX(-50%) translateY(0) scale(1); opacity: 0.12; }
	50% { transform: translateX(-50%) translateY(-16px) scale(1.05); opacity: 0.2; }
}
@keyframes rgBlobMorph3 {
	0%, 100% { border-radius: 50% 30% 40% 60% / 40% 60% 50% 50%; transform: translate(0, 0) rotate(0deg); }
	33% { border-radius: 40% 60% 50% 40% / 60% 40% 60% 50%; transform: translate(8px, -10px) rotate(25deg); }
	66% { border-radius: 60% 40% 60% 40% / 50% 50% 40% 60%; transform: translate(-6px, 8px) rotate(-18deg); }
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
.register-header {
	display: flex;
	flex-direction: column;
	align-items: center;
	text-align: center;
	padding-top: 56px;
	margin-bottom: 24px;
	animation: rgSlideIn 0.6s ease-out both;
}

.register-logo-ring {
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
		animation: rgRingPulse 3s ease-in-out infinite;
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

@keyframes rgRingPulse {
	0%, 100% { opacity: 0.3; transform: scale(1); }
	50% { opacity: 0.7; transform: scale(1.05); }
}

.register-subtitle {
	color: rgba($white1, 0.38);
	font-size: 13px;
	margin-top: 4px;
}

// === GLASS CARD ===
.register-glass-card {
	background: rgba($white, 0.03);
	backdrop-filter: blur(20px);
	-webkit-backdrop-filter: blur(20px);
	border: 1px solid rgba($white, 0.07);
	border-radius: 22px;
	padding: 24px 20px 20px;
	position: relative;
	z-index: 1;
	animation: rgSlideIn 0.6s ease-out 0.1s both;
	box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15), inset 0 1px 0 rgba($white, 0.05);

	&::before {
		content: '';
		position: absolute;
		top: 0; left: 0; right: 0;
		height: 45%;
		background: linear-gradient(180deg, rgba($white, 0.04) 0%, transparent 100%);
		border-radius: 22px 22px 0 0;
		pointer-events: none;
	}
}

.register-form {
	position: relative;
	z-index: 1;
}

.glass-input-group {
	margin-bottom: 14px;
	animation: rgSlideIn 0.5s ease-out both;

	@for $i from 1 through 5 {
		&:nth-child(#{$i}) {
			animation-delay: #{0.06 * $i + 0.1}s;
		}
	}
}

.glass-input-row {
	display: flex;
	gap: 10px;

	.glass-input-group {
		flex: 1;
		min-width: 0;
	}
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
	min-height: 48px;
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
		font-size: 14px;
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

// === ACTIONS ===
.register-actions {
	margin-top: 16px;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 14px;
	animation: rgSlideIn 0.6s ease-out 0.4s both;
}

.glass-checkbox {
	display: flex;
	align-items: center;
	gap: 10px;
	cursor: pointer;
	font-size: 12px;
	color: rgba($white1, 0.55);
	padding: 8px 16px;
	border-radius: 12px;
	background: rgba($white, 0.02);
	border: 1px solid rgba($white, 0.05);
	transition: all 0.3s ease;

	&:hover {
		border-color: rgba($purple, 0.15);
		background: rgba($purple, 0.04);
	}

	.form-check-input {
		width: 18px;
		height: 18px;
		background: rgba($background_second, 0.6);
		border: 1px solid rgba($white, 0.15);
		border-radius: 5px;
		flex-shrink: 0;

		&:checked {
			background-color: $purple;
			border-color: $purple;
		}
	}

	label { cursor: pointer; }
	span {
		color: $purple;
		text-decoration: underline;
		font-weight: 500;
	}
}

.btn-center {
	display: flex;
	justify-content: center;
	width: 100%;
}

.btn-register {
	position: relative;
	overflow: hidden;
	width: 100%;
	max-width: 350px;
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
	&:disabled { opacity: 0.35; cursor: not-allowed; }
}

// === BOTTOM ===
.register-bottom {
	margin-top: 24px;
	text-align: center;
	animation: rgSlideIn 0.6s ease-out 0.5s both;
}

.register-login-link {
	font-size: 13px;
	color: rgba($white1, 0.4);
	margin: 0;

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

@keyframes rgSlideIn {
	from { opacity: 0; transform: translateY(24px); }
	to { opacity: 1; transform: translateY(0); }
}
</style>