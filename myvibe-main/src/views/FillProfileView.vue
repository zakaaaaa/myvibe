<template>
	<section class="auth fill-profile">
		<!-- Blobs -->
		<div class="fp-blob fp-blob--1"></div>
		<div class="fp-blob fp-blob--2"></div>
		<div class="fp-blob fp-blob--3"></div>

		<div class="container">
			<!-- Header -->
			<div class="fp-header">
				<h1 class="title">Fill Your <span class="highlight">Vibe</span></h1>
				<p class="fp-subtitle">Tell us about yourself to get started</p>
			</div>

			<!-- Avatar Section -->
			<div class="fp-avatar-section" data-bs-toggle="modal" data-bs-target="#chooseMethodPictureModal">
				<div class="fp-avatar-ring">
					<div class="fp-avatar-img">
						<img :src="this.ava_picture" alt="Avatar" />
					</div>
					<div class="fp-avatar-badge">
						<fa icon="camera" />
					</div>
				</div>
				<p class="fp-avatar-hint">Tap to add photo</p>
			</div>

			<!-- Glass Form Card -->
			<div class="fp-glass-card">
				<form autocomplete="off" @submit.prevent="fillProfile" class="fp-form">
					<!-- Username & Fullname row -->
					<div class="fp-input-row">
						<div class="glass-input-group">
							<label class="glass-label">Username</label>
							<div class="glass-input">
								<fa icon="at" class="glass-input__icon" />
								<input type="text" v-model="username" placeholder="e.g. johndoe" minlength="3" maxlength="15" pattern="^[a-zA-Z0-9_]+$" required />
							</div>
						</div>
						<div class="glass-input-group">
							<label class="glass-label">Full Name</label>
							<div class="glass-input">
								<fa icon="user" class="glass-input__icon" />
								<input type="text" v-model="name" placeholder="e.g. John Doe" required />
							</div>
						</div>
					</div>

					<!-- MBTI & Zodiac row -->
					<div class="fp-input-row">
						<div class="glass-input-group">
							<label class="glass-label">MBTI <span class="optional">optional</span></label>
							<div class="glass-native-select">
								<select v-model="mbti_id">
									<option value="" disabled selected>Your MBTI</option>
									<option v-for="opt in optionsMBTI" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
								</select>
								<fa icon="chevron-down" class="glass-native-select__arrow" />
							</div>
						</div>
						<div class="glass-input-group">
							<label class="glass-label">Zodiac <span class="optional">optional</span></label>
							<div class="glass-native-select">
								<select v-model="zodiac_id">
									<option value="" disabled selected>Your sign</option>
									<option v-for="opt in optionsZodiac" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
								</select>
								<fa icon="chevron-down" class="glass-native-select__arrow" />
							</div>
						</div>
					</div>

					<!-- Enthusiast -->
					<div class="glass-input-group">
						<label class="glass-label">Enthusiast</label>
						<div class="glass-input">
							<fa icon="heart" class="glass-input__icon" />
							<input type="text" v-model="enthusiast" maxlength="10" placeholder="e.g. Music, Art, Film" required />
						</div>
					</div>

					<!-- Relationship -->
					<div class="glass-input-group">
						<label class="glass-label">Relationship <span class="optional">optional</span></label>
						<div class="glass-native-select">
							<select v-model="relationship_id">
								<option value="" disabled selected>Your status</option>
								<option v-for="opt in optionsRelationship" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
							</select>
							<fa icon="chevron-down" class="glass-native-select__arrow" />
						</div>
					</div>

					<!-- Actions -->
					<div class="fp-actions">
						<div class="glass-checkbox" data-bs-toggle="modal" data-bs-target="#termsModal" v-if="showTnc">
							<input class="form-check-input" type="checkbox" v-model="isChecked" :disabled="!isCheckboxEnabled" />
							<label>Agree to MyVibe's <span>Terms of Use</span></label>
						</div>

						<div class="btn-center">
							<button type="submit" class="btn-action btn-allset" :disabled="!isChecked">
								<span v-if="!loading">
									All Set!
									<fa icon="check" class="ms-2" />
								</span>
								<span v-else>
									<fa icon="spinner" class="fa-spin" /> Loading...
								</span>
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<!-- Choose Picture Modal -->
		<div class="modal pt-5 fade" id="chooseMethodPictureModal" tabindex="-1" aria-labelledby="chooseMethodPictureModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body notification">
						<div class="title">
							<h1><span>Set Profile Picture</span></h1>
						</div>
						<ul class="btn-list">
							<li>
								<button @click="chooseFromGallery">
									<span><fa icon="image" class="me-2" /> Choose From Gallery</span>
									<span class="icon"><fa icon="angle-right" /></span>
								</button>
							</li>
							<li>
								<button @click="takePhoto">
									<span><fa icon="camera-retro" class="me-2" /> Take a Photo</span>
									<span class="icon"><fa icon="angle-right" /></span>
								</button>
							</li>
						</ul>
					</div>
					<div class="modal-button">
						<button type="button" class="btn-block" data-bs-dismiss="modal" aria-label="Close" ref="closeModalPicture">Close</button>
					</div>
				</div>
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

		<!-- Terms Modal -->
		<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-body" style="height: 50vh" ref="termsContent" @scroll="checkScroll">
						<h2>General Terms</h2>
						<p>Updated at January 18th, 2025</p>
						<p>By accessing and placing an order with MyVibe, you confirm that you are in agreement with and bound by the terms of service contained in the Terms & Conditions outlined below.</p>
						<p>Under no circumstances shall MyVibe team be liable for any direct, indirect, special, incidental or consequential damages.</p>
						<h2>License</h2>
						<p>MyVibe grants you a revocable, non-exclusive, non-transferable, limited license to download, install and use the app strictly in accordance with the terms of this Agreement.</p>
						<h2>Restrictions</h2>
						<ul>
							<li>License, sell, rent, lease, assign, distribute, transmit, host, outsource, disclose or otherwise commercially exploit the app.</li>
							<li>Modify, make derivative works of, disassemble, decrypt, reverse compile or reverse engineer any part of the app.</li>
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
	</section>
</template>

<script>
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import authService from '@/services/authService';
import { Camera, CameraResultType, CameraSource } from '@capacitor/camera';
import avatar from '@/assets/avatar.png';

export default {
	name: 'FillProfileView',
	data() {
		return {
			optionsMBTI: [],
			optionsZodiac: [],
			optionsRelationship: [],
			ava_picture: avatar,
			profile_picture: '',
			username: '',
			name: '',
			mbti_id: '',
			zodiac_id: '',
			relationship_id: '',
			enthusiast: '',
			loading: false,
			statusNotif: 'failed',
			titleNotif: '',
			titleNotifSecond: '',
			message: '',
			showLink: false,
			linkTo: '/',
			linkText: 'Okay',
			showDismiss: false,
			isCheckboxEnabled: false,
			isChecked: false,
			showTnc: false
		};
	},
	mounted() {
		this.getProfile();
		this.getOptionsMBTI();
		this.getOptionsZodiac();
		this.getOptionsRelationship();
	},
	methods: {
		async getProfile() {
			try {
				const response = await authService.profile();
				this.username = response.data.username;
				this.name = response.data.name;
				if (response.data.username) {
					this.isChecked = true;
				} else {
					this.showTnc = true;
				}
			} catch (error) {
				console.error('Error fetching profile:', error);
			}
		},
		async getOptionsMBTI() {
			try {
				const response = await authService.getMBTI();
				const data = response.data.data;
				this.optionsMBTI = data.map((item) => ({
					label: item.mbti_name,
					value: item.id
				}));
			} catch (error) {
				this.message = error.response.data.message;
				this.showDismiss = true;
				this.showNotifModal();
			}
		},
		async getOptionsZodiac() {
			try {
				const response = await authService.getZodiac();
				const data = response.data.data;
				this.optionsZodiac = data.map((item) => ({
					label: item.zodiac_name,
					value: item.id
				}));
			} catch (error) {
				this.message = error.response.data.message;
				this.showDismiss = true;
				this.showNotifModal();
			}
		},
		async getOptionsRelationship() {
			try {
				const response = await authService.getRelationship();
				const data = response.data.data;
				this.optionsRelationship = data.map((item) => ({
					label: item.relationship_name,
					value: item.id
				}));
			} catch (error) {
				this.message = error.response.data.message;
				this.showDismiss = true;
				this.showNotifModal();
			}
		},
		async fillProfile() {
			if (!this.profile_picture || !this.name || !this.enthusiast || !this.username) {
				this.statusNotif = 'failed';
				this.titleNotif = 'Validation';
				this.titleNotifSecond = 'Error!';
				this.message = 'Please fill all form & picture';
				this.showLink = false;
				this.showDismiss = true;
				this.showNotifModal();
			} else {
				this.loading = true;
				try {
					const formData = new FormData();
					formData.append('_method', 'PUT');
					formData.append('profile_picture', this.profile_picture);
					formData.append('username', this.username);
					formData.append('name', this.name);
					formData.append('enthusiast', this.enthusiast);
					if (this.mbti_id) formData.append('mbti_id', this.mbti_id);
					if (this.zodiac_id) formData.append('zodiac_id', this.zodiac_id);
					if (this.relationship_id) formData.append('relationship_id', this.relationship_id);
					await authService.profile_put(formData);
					this.$router.push('/welcome');
				} catch (error) {
					this.statusNotif = 'failed';
					this.titleNotif = 'Validation';
					this.titleNotifSecond = 'Error!';
					if (error.response.data.errors) {
						let messages = [];
						for (const key in error.response.data.errors) {
							if (error.response.data.errors[key] && Array.isArray(error.response.data.errors[key])) {
								messages = messages.concat(error.response.data.errors[key]);
							}
						}
						this.message = messages.join(', ');
					} else {
						this.message = error.response.data.message;
					}
					this.showLink = false;
					this.showDismiss = true;
					this.showNotifModal();
				} finally {
					this.loading = false;
				}
			}
		},
		async takePhoto() {
			try {
				const photo = await Camera.getPhoto({
					resultType: CameraResultType.Uri,
					source: CameraSource.Camera,
					quality: 90
				});
				this.ava_picture = photo.webPath;
				const file = await this.convertUriToFile(photo.webPath, 'avatar.jpg');
				this.profile_picture = file;
			} catch (error) {
				console.error('Error Take Photo: ', error);
			} finally {
				this.$refs.closeModalPicture.click();
			}
		},
		async chooseFromGallery() {
			try {
				const photo = await Camera.getPhoto({
					resultType: CameraResultType.Uri,
					source: CameraSource.Photos,
					quality: 90
				});
				this.ava_picture = photo.webPath;
				const file = await this.convertUriToFile(photo.webPath, 'avatar.jpg');
				this.profile_picture = file;
			} catch (error) {
				console.error('Error Choose Photo: ', error);
			} finally {
				this.$refs.closeModalPicture.click();
			}
		},
		async convertUriToFile(uri, fileName) {
			const response = await fetch(uri);
			const blob = await response.blob();
			return new File([blob], fileName, { type: blob.type });
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

// === BLOBS ===
.fp-blob { position: absolute; filter: blur(50px); pointer-events: none; z-index: 0; }
.fp-blob--1 { top: -6%; left: -12%; width: min(300px, 68vw); height: min(300px, 68vw); background: radial-gradient(circle, rgba($purple, 0.3) 0%, rgba(#6c5ce7, 0.1) 40%, transparent 70%); border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; animation: fpBlobMorph1 12s ease-in-out infinite; }
.fp-blob--2 { bottom: 3%; right: -10%; width: min(260px, 58vw); height: min(260px, 58vw); background: radial-gradient(circle, rgba(#4a3adf, 0.25) 0%, rgba($purple, 0.08) 40%, transparent 70%); border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; animation: fpBlobMorph2 15s ease-in-out infinite; }
.fp-blob--3 { top: 40%; left: 50%; transform: translateX(-50%); width: min(320px, 72vw); height: min(160px, 36vw); background: radial-gradient(ellipse, rgba($purple, 0.12) 0%, transparent 70%); border-radius: 50%; animation: fpBlobFloat 9s ease-in-out infinite; }
@keyframes fpBlobMorph1 { 0%, 100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(0, 0) rotate(0deg); } 50% { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; transform: translate(12px, 16px) rotate(40deg); } }
@keyframes fpBlobMorph2 { 0%, 100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; transform: translate(0, 0) rotate(0deg); } 50% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(-14px, -12px) rotate(-40deg); } }
@keyframes fpBlobFloat { 0%, 100% { transform: translateX(-50%) translateY(0) scale(1); opacity: 0.12; } 50% { transform: translateX(-50%) translateY(-16px) scale(1.05); opacity: 0.2; } }

// === HEADER ===
.fp-header { text-align: center; margin-bottom: 20px; animation: fpSlideIn 0.6s ease-out both; }
.fp-subtitle { color: rgba($white1, 0.38); font-size: 13px; margin-top: 4px; }

// === AVATAR SECTION ===
.fp-avatar-section { display: flex; flex-direction: column; align-items: center; margin-bottom: 20px; cursor: pointer; animation: fpSlideIn 0.6s ease-out 0.08s both; }
.fp-avatar-ring { width: 96px; height: 96px; border-radius: 50%; position: relative; background: rgba($white, 0.04); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 2px solid rgba($purple, 0.2); box-shadow: 0 6px 24px rgba($purple, 0.1), inset 0 1px 0 rgba($white, 0.06); transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1); padding: 4px;
	&::before { content: ''; position: absolute; inset: -6px; border-radius: 50%; border: 1px solid rgba($purple, 0.1); animation: fpRingPulse 3s ease-in-out infinite; }
	&::after { content: ''; position: absolute; top: 3px; left: 18%; right: 18%; height: 30%; background: linear-gradient(180deg, rgba($white, 0.12) 0%, transparent 100%); border-radius: 50%; pointer-events: none; }
	&:hover { border-color: rgba($purple, 0.4); transform: scale(1.05); }
}
.fp-avatar-img { width: 100%; height: 100%; border-radius: 50%; overflow: hidden; background: rgba(#1e1e21, 0.8); img { width: 100%; height: 100%; object-fit: cover; } }
.fp-avatar-badge { position: absolute; bottom: 0; right: 0; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; color: $white; background: rgba($purple, 0.7); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 2px solid rgba($background, 0.8); box-shadow: 0 2px 8px rgba($purple, 0.3); }
.fp-avatar-hint { color: rgba($white1, 0.35); font-size: 11px; margin-top: 8px; }
@keyframes fpRingPulse { 0%, 100% { opacity: 0.3; transform: scale(1); } 50% { opacity: 0.7; transform: scale(1.04); } }

// === GLASS CARD ===
.fp-glass-card { background: rgba($white, 0.03); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba($white, 0.07); border-radius: 22px; padding: 22px 18px 18px; position: relative; z-index: 1; animation: fpSlideIn 0.6s ease-out 0.15s both; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15), inset 0 1px 0 rgba($white, 0.05);
	&::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 40%; background: linear-gradient(180deg, rgba($white, 0.04) 0%, transparent 100%); border-radius: 22px 22px 0 0; pointer-events: none; }
}
.fp-form { position: relative; z-index: 1; }

// === INPUT GROUPS ===
.glass-input-group { margin-bottom: 14px; animation: fpSlideIn 0.5s ease-out both;
	@for $i from 1 through 8 { &:nth-child(#{$i}) { animation-delay: #{0.05 * $i + 0.15}s; } }
}
.fp-input-row { display: flex; gap: 10px;
	.glass-input-group { flex: 1; min-width: 0; }
}
.glass-label { display: block; font-size: 12px; font-weight: 500; color: rgba($white1, 0.5); margin-bottom: 5px; padding-left: 4px; letter-spacing: 0.3px;
	.optional { font-size: 10px; font-weight: 400; color: rgba($white1, 0.3); font-style: italic; }
}
.glass-input { display: flex; align-items: center; background: rgba($white, 0.035); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba($white, 0.07); border-radius: 14px; padding: 0 14px; min-height: 46px; transition: all 0.35s cubic-bezier(0.23, 1, 0.32, 1); position: relative; box-shadow: inset 0 1px 0 rgba($white, 0.04), 0 2px 10px rgba(0, 0, 0, 0.08);
	&::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 50%; background: linear-gradient(180deg, rgba($white, 0.025) 0%, transparent 100%); border-radius: 14px 14px 0 0; pointer-events: none; }
	&:focus-within { border-color: rgba($purple, 0.35); background: rgba($white, 0.05); box-shadow: 0 0 0 3px rgba($purple, 0.06), inset 0 1px 0 rgba($white, 0.06), 0 4px 20px rgba(0, 0, 0, 0.12); transform: translateY(-1px);
		.glass-input__icon { color: $purple; }
	}
	&__icon { color: rgba($white, 0.28); font-size: 13px; margin-right: 10px; transition: all 0.3s ease; flex-shrink: 0; }
	input { flex: 1; background: transparent; border: none; outline: none; color: $white1; font-size: 14px; font-family: inherit; min-width: 0; padding: 0;
		&::placeholder { color: rgba($white, 0.22); font-size: 12px; font-weight: 300; }
	}
}

// === NATIVE SELECT (iOS picker) ===
.glass-native-select {
	position: relative;
	background: rgba($white, 0.035);
	backdrop-filter: blur(16px);
	-webkit-backdrop-filter: blur(16px);
	border: 1px solid rgba($white, 0.07);
	border-radius: 14px;
	min-height: 46px;
	display: flex;
	align-items: center;
	box-shadow: inset 0 1px 0 rgba($white, 0.04), 0 2px 10px rgba(0, 0, 0, 0.08);
	transition: all 0.35s cubic-bezier(0.23, 1, 0.32, 1);
	overflow: hidden;

	&::before {
		content: '';
		position: absolute;
		top: 0; left: 0; right: 0;
		height: 50%;
		background: linear-gradient(180deg, rgba($white, 0.025) 0%, transparent 100%);
		border-radius: 14px 14px 0 0;
		pointer-events: none;
	}

	select {
		width: 100%;
		height: 46px;
		background: transparent;
		border: none;
		outline: none;
		color: $white1;
		font-size: 13px;
		font-family: inherit;
		padding: 0 36px 0 14px;
		-webkit-appearance: none;
		-moz-appearance: none;
		appearance: none;
		cursor: pointer;
		position: relative;
		z-index: 1;

		option {
			background: #1e1e2e;
			color: $white1;
		}

		// Placeholder style (disabled selected option)
		&:invalid,
		& option[value=""][disabled] {
			color: rgba($white, 0.22);
		}
	}

	&__arrow {
		position: absolute;
		right: 14px;
		top: 50%;
		transform: translateY(-50%);
		color: rgba($white, 0.3);
		font-size: 11px;
		pointer-events: none;
		z-index: 2;
	}

	&:focus-within {
		border-color: rgba($purple, 0.35);
		background: rgba($white, 0.05);
		box-shadow: 0 0 0 3px rgba($purple, 0.06), inset 0 1px 0 rgba($white, 0.06), 0 4px 20px rgba(0, 0, 0, 0.12);
	}
}

// === ACTIONS ===
.fp-actions { margin-top: 18px; display: flex; flex-direction: column; align-items: center; gap: 14px; animation: fpSlideIn 0.6s ease-out 0.5s both; }
.glass-checkbox { display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 12px; color: rgba($white1, 0.55); padding: 8px 16px; border-radius: 12px; background: rgba($white, 0.02); border: 1px solid rgba($white, 0.05); transition: all 0.3s ease;
	&:hover { border-color: rgba($purple, 0.15); background: rgba($purple, 0.04); }
	.form-check-input { width: 18px; height: 18px; background: rgba($background_second, 0.6); border: 1px solid rgba($white, 0.15); border-radius: 5px; flex-shrink: 0; &:checked { background-color: $purple; border-color: $purple; } }
	label { cursor: pointer; }
	span { color: $purple; text-decoration: underline; font-weight: 500; }
}
.btn-center { display: flex; justify-content: center; width: 100%; }
.btn-allset { position: relative; overflow: hidden; width: 100%; max-width: 350px; background: linear-gradient(135deg, rgba($purple, 0.65) 0%, rgba($purple, 0.85) 50%, rgba(#5a4dd6, 0.75) 100%) !important; backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba($white, 0.12) !important; box-shadow: 0 8px 28px rgba($purple, 0.2), inset 0 1px 0 rgba($white, 0.18); transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
	&::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 50%; background: linear-gradient(180deg, rgba($white, 0.12) 0%, transparent 100%); border-radius: 30px 30px 0 0; pointer-events: none; }
	&::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba($white, 0.12), transparent); transition: left 0.6s ease; z-index: 1; }
	&:not(:disabled):hover { transform: translateY(-3px); box-shadow: 0 14px 40px rgba($purple, 0.3), inset 0 1px 0 rgba($white, 0.22), 0 0 25px rgba($purple, 0.12); &::before { left: 100%; } }
	&:not(:disabled):active { transform: translateY(-1px) scale(0.98); }
	&:disabled { opacity: 0.35; cursor: not-allowed; }
}

@keyframes fpSlideIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>