<template>
	<section class="auth fill-profile">
		<div class="container">
			<h1 class="title text-center mb-4">Fill Your <span class="highlight">Vibe</span></h1>
			<form autocomplete="off" @submit.prevent="fillProfile">
				<div class="mb-3">
					<label class="form-label mb-1">Profile Picture</label>
					<div class="profile-picture-cage" data-bs-toggle="modal" data-bs-target="#chooseMethodPictureModal">
						<div class="image"><img :src="this.ava_picture" alt="" /></div>
						<p>Put up a nice photo! Everyone can see it.</p>
					</div>
				</div>
				<div class="mb-3">
					<label class="form-label">Username</label>
					<input type="text" class="form-control" v-model="username" placeholder="Add your username here" minlength="3" maxlength="15" pattern="^[a-zA-Z0-9_]+$" required />
				</div>
				<div class="mb-3">
					<label class="form-label">Fullname</label>
					<input type="text" class="form-control" v-model="name" placeholder="Add your fullname here" required />
				</div>
				<div class="mb-3">
					<label class="form-label">MBTI <span style="font-size: 10px">(optional)</span></label>
					<VueSelect v-model="mbti_id" :options="optionsMBTI" :isClearable="false" placeholder="Let me know what your MBTI" />
				</div>
				<div class="mb-3">
					<label class="form-label">Zodiac <span style="font-size: 10px">(optional)</span></label>
					<VueSelect v-model="zodiac_id" :options="optionsZodiac" :isClearable="false" placeholder="Choose your zodiac here!">
						<template #option="{ option }">
							{{ option.label }} <small>{{ option.description }}</small>
						</template>
					</VueSelect>
				</div>
				<div class="mb-3">
					<label class="form-label">Enthusiast</label>
					<input type="text" class="form-control" v-model="enthusiast" maxlength="10" placeholder="Let anyone know your hobbies or interest" required />
				</div>
				<div class="mb-3">
					<label class="form-label">Relationship <span style="font-size: 10px">(optional)</span></label>
					<VueSelect v-model="relationship_id" :options="optionsRelationship" :isClearable="false" placeholder="What about your relationship?">
						<template #option="{ option }">
							{{ option.label }} <small>- {{ option.description }}</small>
						</template>
					</VueSelect>
				</div>
				<div class="d-flex justify-content-center align-items-center flex-column mt-4 gap-2">
					<div class="form-check" data-bs-toggle="modal" data-bs-target="#termsModal" v-if="showTnc">
						<input class="form-check-input" type="checkbox" v-model="isChecked" :disabled="!isCheckboxEnabled" />
						<label class="form-check-label" for="flexCheckDefault">Agree to MyVibe Planner’s Terms of Use</label>
					</div>
					<button type="submit" class="btn-action" :disabled="!isChecked">
						<span v-if="!loading" class="me-2">All Set!</span>
						<span v-else> <fa icon="spinner" class="fa-spin" /> Loading... </span>
					</button>
				</div>
			</form>
		</div>
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
									<span><fa icon="image" class="me-2" /> Choose From Gallery</span> <span class="icon"><fa icon="angle-right" /></span>
								</button>
							</li>
							<li>
								<button @click="takePhoto">
									<span><fa icon="camera-retro" class="me-2" /> Take a Photo</span> <span class="icon"><fa icon="angle-right" /></span>
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
		<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-body" style="height: 50vh" ref="termsContent" @scroll="checkScroll">
						<h2>General Terms</h2>
						<p>Updated at January 18th, 2025</p>
						<p>By accessing and placing an order with MyVibe, you confirm that you are in agreement with and bound by the terms of service contained in the Terms & Conditions outlined below. These terms apply to the entire website and any email or other type of communication between you and MyVibe.</p>
						<p>Under no circumstances shall MyVibe team be liable for any direct, indirect, special, incidental or consequential damages, including, but not limited to, loss of data or profit, arising out of the use, or the inability to use, the materials on this site, even if MyVibe team or an authorized representative has been advised of the possibility of such damages. If your use of materials from this site results in the need for servicing, repair or correction of equipment or data, you assume any costs thereof.</p>
						<p>MyVibe will not be responsible for any outcome that may occur during the course of usage of our resources. We reserve the rights to change prices and revise the resources usage policy in any moment.</p>

						<h2>License</h2>
						<p>MyVibe grants you a revocable, non-exclusive, non-transferable, limited license to download, install and use the app strictly in accordance with the terms of this Agreement.</p>
						<p>These Terms & Conditions are a contract between you and MyVibe (referred to in these Terms & Conditions as "MyVibe", "us", "we" or "our"), the provider of the MyVibe website and the services accessible from the MyVibe website (which are collectively referred to in these Terms & Conditions as the "MyVibe Service").</p>
						<p>You are agreeing to be bound by these Terms & Conditions. If you do not agree to these Terms & Conditions, please do not use the MyVibe Service. In these Terms & Conditions, "you" refers both to you as an individual and to the entity you represent. If you violate any of these Terms & Conditions, we reserve the right to cancel your account or block access to your account without notice.</p>

						<h2>Definitions and Key Terms</h2>
						<p>To help explain things as clearly as possible in this Terms & Conditions, every time any of these terms are referenced, are strictly defined as:</p>
						<ul>
							<li><strong>Cookie:</strong> A small amount of data generated by a website and saved by your web browser. It is used to identify your browser, provide analytics, remember information about you such as your language preference or login information.</li>
							<li><strong>Company:</strong> When this terms mention “Company,” “we,” “us,” or “our,” it refers to MyVibe, that is responsible for your information under this Terms & Conditions.</li>
							<li><strong>Country:</strong> Where MyVibe or the owners/founders of MyVibe are based, in this case is Indonesia</li>
							<li><strong>Device:</strong> Any internet connected device such as a phone, tablet, computer or any other device that can be used to visit MyVibe and use the services.</li>
							<li><strong>Service:</strong> Refers to the service provided by MyVibe as described in the relative terms (if available) and on this platform.</li>
							<li><strong>Third-party service:</strong> Refers to advertisers, contest sponsors, promotional and marketing partners, and others who provide our content or whose products or services we think may interest you.</li>
							<li><strong>App/Application:</strong> MyVibe app, refers to the SOFTWARE PRODUCT identified above.</li>
							<li><strong>You:</strong> A person or entity that is registered with MyVibe to use the Services.</li>
						</ul>

						<h2>Restrictions</h2>
						<p>You agree not to, and will not permit others to:</p>
						<ul>
							<li>License, sell, rent, lease, assign, distribute, transmit, host, outsource, disclose or otherwise commercially exploit the app or make the platform available to any third party.</li>
							<li>Modify, make derivative works of, disassemble, decrypt, reverse compile or reverse engineer any part of the app.</li>
							<li>Remove, alter or obscure any proprietary notice (including any notice of copyright or trademark) of MyVibe or its affiliates, partners, suppliers or the licensors of the app.</li>
						</ul>

						<h2>Return and Refund Policy</h2>
						<p>If you are not satisfied with any product or service, contact us to discuss your concerns.</p>

						<h2>Your Suggestions</h2>
						<p>Suggestions provided to MyVibe remain the sole property of MyVibe.</p>

						<h2>Contact Us</h2>
						<p>If you have any questions:</p>
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

import VueSelect from 'vue3-select-component';

import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import authService from '@/services/authService';

import { Camera, CameraResultType, CameraSource } from '@capacitor/camera';

import avatar from '@/assets/avatar.png';

export default {
	name: 'FillProfileView',
	components: {
		VueSelect
	},
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
					label: item.mbti_name + ' - ' + item.mbti_desc,
					value: item.id
				}));
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
		async getOptionsZodiac() {
			try {
				const response = await authService.getZodiac();
				const data = response.data.data;
				this.optionsZodiac = data.map((item) => ({
					label: item.zodiac_name,
					value: item.id,
					description: item.zodiac_desc
				}));
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
		async getOptionsRelationship() {
			try {
				const response = await authService.getRelationship();
				const data = response.data.data;
				this.optionsRelationship = data.map((item) => ({
					label: item.relationship_name,
					value: item.id,
					description: item.relationship_desc
				}));
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
		async fillProfile() {
			if (!this.profile_picture || !this.name || !this.enthusiast || !this.username) {
				this.statusNotif = 'failed';
				this.titleNotif = 'Validation';
				this.titleNotifSecond = 'Error!';
				this.message = 'Please fill all form & picture';
				this.showLink = false;
				this.showDismiss = true;
				this.linkTo = '';
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
					if (this.mbti_id) {
						formData.append('mbti_id', this.mbti_id);
					}
					if (this.zodiac_id) {
						formData.append('zodiac_id', this.zodiac_id);
					}
					if (this.relationship_id) {
						formData.append('relationship_id', this.relationship_id);
					}
					await authService.profile_put(formData);
					this.$router.push('/dashboard');
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
					this.linkTo = '';
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
			const file = new File([blob], fileName, { type: blob.type });
			return file;
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
