<template>
	<section class="setting">
		<div v-if="!isLoading" class="container">
			<div class="mt-4" v-if="isMain">
				<div class="back-top">
					<RouterLink to="/dashboard"> <fa icon="arrow-left-long" class="text-white" /></RouterLink>
				</div>
				<div class="vibe-profile">
					<div class="profile-preview">
						<div class="image"><img :src="profile.profile_picture" alt="" /></div>
						<div class="info">
							<h1>{{ profile.name }}</h1>
							<div class="count">
								{{ profile.email }}
							</div>
							<ul class="summary">
								<li v-if="profile.mbti">
									<span>{{ profile.mbti }}</span>
								</li>
								<li v-if="profile.zodiac">
									<span>{{ profile.zodiac }}</span>
								</li>
								<li v-if="profile.enthusiast">
									<span>{{ profile.enthusiast }}</span>
								</li>
								<li v-if="profile.relationship">
									<span>{{ profile.relationship }}</span>
								</li>
							</ul>
						</div>
						<div class="icon" @click="goEdit()">
							<fa :icon="['fas', 'pencil']" />
						</div>
					</div>
				</div>
				<div class="menu-list">
					<h1>Account</h1>
					<ul>
						<li @click="manageFollowers()">
							<div class="icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
									<g id="user_follow_2_line" fill="none" fill-rule="evenodd">
										<path d="M24 0v24H0V0zM12.594 23.258l-.012.002-.071.035-.02.004-.014-.004-.071-.036c-.01-.003-.019 0-.024.006l-.004.01-.017.428.005.02.01.013.104.074.015.004.012-.004.104-.074.012-.016.004-.017-.017-.427c-.002-.01-.009-.017-.016-.018m.264-.113-.014.002-.184.093-.01.01-.003.011.018.43.005.012.008.008.201.092c.012.004.023 0 .029-.008l.004-.014-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014-.034.614c0 .012.007.02.017.024l.015-.002.201-.093.01-.008.003-.011.018-.43-.003-.012-.01-.01z" />
										<path fill="currentColor" d="M11 4a3 3 0 1 0 0 6 3 3 0 0 0 0-6M6 7a5 5 0 1 1 10 0A5 5 0 0 1 6 7M4.413 17.601c-.323.41-.413.72-.413.899 0 .122.037.251.255.426.249.2.682.407 1.344.582C6.917 19.858 8.811 20 11 20c.222 0 .441-.002.658-.005a1 1 0 0 1 .027 2c-.226.003-.455.005-.685.005-2.229 0-4.335-.14-5.913-.558-.785-.208-1.524-.506-2.084-.956C2.41 20.01 2 19.345 2 18.5c0-.787.358-1.523.844-2.139.494-.625 1.177-1.2 1.978-1.69C6.425 13.695 8.605 13 11 13c.447 0 .887.024 1.316.07a1 1 0 0 1-.211 1.989C11.745 15.02 11.375 15 11 15c-2.023 0-3.843.59-5.136 1.379-.647.394-1.135.822-1.45 1.222Zm17.295-1.533a1 1 0 0 0-1.414-1.414l-3.182 3.182-1.414-1.414a1 1 0 0 0-1.414 1.414l2.05 2.05a1.1 1.1 0 0 0 1.556 0z" />
									</g>
								</svg>
							</div>
							<span>Manage Followers</span>
							<div class="icon-right">
								<fa :icon="['fas', 'angle-right']" />
							</div>
						</li>
						<li @click="copyMyVibe()">
							<div class="icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
									<title>share_2_line</title>
									<g id="share_2_line" fill="none" fill-rule="evenodd">
										<path d="M24 0v24H0V0zM12.593 23.258l-.011.002-.071.035-.02.004-.014-.004-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01-.017.428.005.02.01.013.104.074.015.004.012-.004.104-.074.012-.016.004-.017-.017-.427c-.002-.01-.009-.017-.017-.018m.265-.113-.013.002-.185.093-.01.01-.003.011.018.43.005.012.008.007.201.093c.012.004.023 0 .029-.008l.004-.014-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014-.034.614c0 .012.007.02.017.024l.015-.002.201-.093.01-.008.004-.011.017-.43-.003-.012-.01-.01z" />
										<path fill="currentColor" d="M18.5 2a3.5 3.5 0 1 1-2.506 5.943L11.67 10.21c.213.555.33 1.16.33 1.79a4.99 4.99 0 0 1-.33 1.79l4.324 2.267a3.5 3.5 0 1 1-.93 1.771l-4.475-2.346a5 5 0 1 1 0-6.963l4.475-2.347A3.5 3.5 0 0 1 18.5 2m0 15a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M7 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6m11.5-5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3" />
									</g>
								</svg>
							</div>
							<span>Share Your Vibe!</span>
						</li>
						<li data-bs-toggle="modal" data-bs-target="#logOutModal">
							<div class="icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
									<title>arrow_to_left_line</title>
									<g id="arrow_to_left_line" fill="none" fill-rule="nonzero">
										<path d="M24 0v24H0V0h24ZM12.594 23.258l-.012.002-.071.035-.02.004-.014-.004-.071-.036c-.01-.003-.019 0-.024.006l-.004.01-.017.428.005.02.01.013.104.074.015.004.012-.004.104-.074.012-.016.004-.017-.017-.427c-.002-.01-.009-.017-.016-.018Zm.264-.113-.014.002-.184.093-.01.01-.003.011.018.43.005.012.008.008.201.092c.012.004.023 0 .029-.008l.004-.014-.034-.614c-.003-.012-.01-.02-.02-.022Zm-.715.002a.023.023 0 0 0-.027.006l-.006.014-.034.614c0 .012.007.02.017.024l.015-.002.201-.093.01-.008.003-.011.018-.43-.003-.012-.01-.01-.184-.092Z" />
										<path fill="currentColor" d="M5 6a1 1 0 0 0-2 0v12a1 1 0 1 0 2 0V6Zm7.703 10.95a1 1 0 0 0 0-1.415L10.167 13H20a1 1 0 1 0 0-2h-9.833l2.536-2.536a1 1 0 0 0-1.415-1.414l-4.242 4.243a1 1 0 0 0 0 1.414l4.242 4.243a1 1 0 0 0 1.415 0Z" />
									</g>
								</svg>
							</div>
							<span>Logout</span>
						</li>
						<li @click="deleteAccount()">
							<div class="icon">
								<fa icon="trash" class="text-white" />
							</div>
							<span>Delete Account</span>
						</li>
					</ul>
				</div>
			</div>
			<div class="mt-4" v-if="isManageFollowers">
				<div class="back-top" @click="goBack()">
					<div><fa icon="arrow-left-long" class="text-white" /></div>
				</div>
				<h1 class="title text-center mb-4">Followers</h1>
				<ul class="followers_list">
					<li v-for="(item, index) in listFollowers" :key="index" class="mb-4">
						<img :src="item.profile_picture" alt="" @click="goDetail(item.username)" />
						<div class="text">
							<h2>{{ item.name }}</h2>
							<p>@{{ item.username }}</p>
						</div>
						<div class="icon">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" v-if="!item.is_following" @click="follow(index, item.id)">
								<g id="user_add_2_line" fill="none" fill-rule="evenodd">
									<path d="M24 0v24H0V0zM12.594 23.258l-.012.002-.071.035-.02.004-.014-.004-.071-.036c-.01-.003-.019 0-.024.006l-.004.01-.017.428.005.02.01.013.104.074.015.004.012-.004.104-.074.012-.016.004-.017-.017-.427c-.002-.01-.009-.017-.016-.018m.264-.113-.014.002-.184.093-.01.01-.003.011.018.43.005.012.008.008.201.092c.012.004.023 0 .029-.008l.004-.014-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014-.034.614c0 .012.007.02.017.024l.015-.002.201-.093.01-.008.003-.011.018-.43-.003-.012-.01-.01z" />
									<path fill="currentColor" d="M11 2a5 5 0 1 0 0 10 5 5 0 0 0 0-10M8 7a3 3 0 1 1 6 0 3 3 0 0 1-6 0M4 18.5c0-.18.09-.489.413-.899.316-.4.804-.828 1.451-1.222C7.157 15.589 8.977 15 11 15c.375 0 .744.02 1.105.059a1 1 0 1 0 .211-1.99A12.905 12.905 0 0 0 11 13c-2.395 0-4.575.694-6.178 1.672-.8.488-1.484 1.064-1.978 1.69C2.358 16.976 2 17.713 2 18.5c0 .845.411 1.511 1.003 1.986.56.45 1.299.748 2.084.956C6.665 21.859 8.771 22 11 22l.685-.005a1 1 0 1 0-.027-2L11 20c-2.19 0-4.083-.143-5.4-.492-.663-.175-1.096-.382-1.345-.582C4.037 18.751 4 18.622 4 18.5M18 14a1 1 0 0 1 1 1v2h2a1 1 0 1 1 0 2h-2v2a1 1 0 1 1-2 0v-2h-2a1 1 0 1 1 0-2h2v-2a1 1 0 0 1 1-1" />
								</g>
							</svg>
							<fa :icon="['fas', 'check']" v-if="item.is_following" @click="unfollow(index, item.id)" />
						</div>
					</li>
				</ul>
			</div>
			<div class="mt-4" v-if="isEditProfile">
				<div class="back-top" @click="goBack()">
					<div><fa icon="arrow-left-long" class="text-white" /></div>
				</div>
				<h1 class="title text-center mb-4">Fill Your <span class="highlight">Vibe</span></h1>
				<form autocomplete="off" @submit.prevent="fillProfile">
					<div class="mb-3">
						<label class="form-label mb-1">Profile Picture</label>
						<div class="profile-picture-cage" data-bs-toggle="modal" data-bs-target="#chooseMethodPictureModal">
							<div class="image"><img :src="this.ava_picture" alt="" /></div>
							<p>Put up a nice photo! Everyone can see it.</p>
						</div>
					</div>
					<div class="mb-3" v-if="showUsername">
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
						<button type="submit" class="btn-action">
							<span v-if="!loading" class="me-2">All Set!</span>
							<span v-else> <fa icon="spinner" class="fa-spin" /> Loading... </span>
						</button>
					</div>
					<div class="pt-5"></div>
				</form>
			</div>
		</div>
		<div v-else class="loading">
			<fa icon="spinner" class="fa-spin-pulse" />
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
		<div class="modal pt-5 fade" id="logOutModal" tabindex="-1" aria-labelledby="logOutModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body notification">
						<div class="title">
							<h1>Logout <span>Alert!</span></h1>
						</div>
						<p class="m-0 text-center">Are you sure to logout?</p>
					</div>
					<div class="modal-button">
						<RouterLink to="/landing" class="btn-block purple" @click="logoutLink()">Yes</RouterLink>
					</div>
					<div class="modal-button">
						<button type="button" class="btn-block" data-bs-dismiss="modal" aria-label="Close">Close</button>
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
		<span data-bs-toggle="modal" data-bs-target="#deleteAccount" ref="deleteAccountBtn"></span>
		<div class="modal fade" id="deleteAccount" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteAccountLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body notification">
						<div class="title">
							<h1>Delete Account <span>Alert!</span></h1>
						</div>
						<p class="m-0 text-center">Wait! Are you sure you want to delete your account? All your data will be permanently erased, and this action can't be undone.</p>
					</div>
					<div class="modal-button">
						<div class="btn-block purple" :class="countdown > 0 ? 'd-block' : 'd-none'">{{ countdown > 0 ? `Wait (${countdown}s)` : '' }}</div>
						<RouterLink to="/landing" class="btn-block purple" :class="countdown > 0 ? 'd-none' : 'd-block'" @click.prevent="handleDeleteAccount()">Yes, Delete My Account</RouterLink>
					</div>
					<div class="modal-button">
						<button type="button" class="btn-block" data-bs-dismiss="modal" aria-label="Close">Close</button>
					</div>
				</div>
			</div>
		</div>
	</section>
</template>

<script>
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { Clipboard } from '@capacitor/clipboard';
import dashboardService from '@/services/dashboardService';
import logo from '@/assets/avatar.png';
import VueSelect from 'vue3-select-component';
import authService from '@/services/authService';
import { Camera, CameraResultType, CameraSource } from '@capacitor/camera';

export default {
	name: 'SettingView',
	components: {
		VueSelect
	},
	data() {
		return {
			optionsMBTI: [],
			optionsZodiac: [],
			optionsRelationship: [],
			old_picture: '',
			ava_picture: '',
			profile_picture: '',
			username: '',
			name: '',
			mbti_id: '',
			zodiac_id: '',
			relationship_id: '',
			enthusiast: '',
			isLoading: false,
			isMain: true,
			isManageFollowers: false,
			isEditProfile: false,
			profile: [],
			listFollowers: [],
			statusNotif: 'failed',
			titleNotif: '',
			titleNotifSecond: '',
			message: '',
			showLink: false,
			linkTo: '/',
			linkText: 'Okay',
			showDismiss: false,
			showUsername: true,
			countdown: 10,
			timer: null
		};
	},
	mounted() {
		this.getOptionsMBTI();
		this.getOptionsZodiac();
		this.getOptionsRelationship();
		this.getUser();
	},
	methods: {
		startCountdown() {
			this.countdown = 10;
			this.timer = setInterval(() => {
				if (this.countdown > 0) {
					this.countdown -= 1;
				} else {
					clearInterval(this.timer);
				}
			}, 1000);
		},
		async getUser() {
			this.isLoading = true;
			try {
				const response = await dashboardService.getFriends(this.$route.params.username);
				if (response.data.profile.profile_picture) {
					response.data.profile.profile_picture = process.env.VUE_APP_API_URL + '/' + response.data.profile.profile_picture;
				} else {
					response.data.profile.profile_picture = logo;
				}
				this.profile = response.data.profile;
				this.profile.mbti = this.profile.mbti?.mbti_name ?? '';
				this.profile.zodiac = this.profile.zodiac?.zodiac_name ?? '';
				this.profile.relationship = this.profile.relationship?.relationship_name ?? '';
				this.ava_picture = this.profile.profile_picture;
				this.profile_picture = this.profile.profile_picture;
				this.username = this.profile.username;
				this.name = this.profile.name;
				this.mbti_id = this.profile.mbti_id ?? '';
				this.zodiac_id = this.profile.zodiac_id ?? '';
				this.relationship_id = this.profile.relationship_id ?? '';
				this.enthusiast = this.profile.enthusiast;
				this.old_picture = this.profile.profile_picture;
				this.isLoading = false;
			} catch (error) {
				console.error('Error fetching home:', error);
			}
		},
		goDetail(path) {
			this.$router.push('/' + path);
		},
		async copyMyVibe() {
			try {
				await Clipboard.write({
					string: 'https://myvibeapp.site/' + this.$route.params.username
				});
				this.statusNotif = 'success';
				this.titleNotif = 'Share Your';
				this.titleNotifSecond = 'Vibe!';
				this.message = 'Copied to clipboard!';
				this.showLink = false;
				this.showDismiss = true;
				this.linkTo = '';
				this.showNotifModal();
			} catch (error) {
				this.statusNotif = 'failed';
				this.titleNotif = 'Logout';
				this.titleNotifSecond = 'Alert!';
				this.message = 'Failed to copy vibe';
				this.showLink = false;
				this.showDismiss = true;
				this.linkTo = '';
				this.showNotifModal();
			}
		},
		async logoutLink() {
			try {
				await dashboardService.postLogout();
			} catch (error) {
				this.statusNotif = 'failed';
				this.titleNotif = 'Logout';
				this.titleNotifSecond = 'Alert!';
				this.message = 'Failed to logout';
				this.showLink = false;
				this.showDismiss = true;
				this.linkTo = '';
				this.showNotifModal();
			} finally {
				var backdrop = document.querySelector('.modal-backdrop');
				if (backdrop) {
					backdrop.remove();
				}
			}
		},
		deleteAccount() {
			this.$refs.deleteAccountBtn.click();
			this.startCountdown();
		},
		async handleDeleteAccount() {
			try {
				await dashboardService.postDeleteAccount();
			} catch (error) {
				this.statusNotif = 'failed';
				this.titleNotif = 'Delete Account';
				this.titleNotifSecond = 'Alert!';
				this.message = 'Failed to delete account';
				this.showLink = false;
				this.showDismiss = true;
				this.linkTo = '';
				this.showNotifModal();
			} finally {
				var backdrop = document.querySelector('.modal-backdrop');
				if (backdrop) {
					backdrop.remove();
				}
			}
		},
		showNotifModal() {
			this.$refs.notifModalBtn.click();
		},
		async manageFollowers() {
			this.isLoading = true;
			try {
				const response = await dashboardService.getFollowers(this.profile.id);
				for (let i = 0; i < response.data.followers.length; i++) {
					if (!response.data.followers[i].profile_picture) {
						response.data.followers[i].profile_picture = logo;
					} else {
						response.data.followers[i].profile_picture = process.env.VUE_APP_API_URL + '/' + response.data.followers[i].profile_picture;
					}
				}
				this.listFollowers = response.data.followers;
				this.isLoading = false;
			} catch (error) {
				console.error('Error fetching followers:', error);
			} finally {
				this.isMain = false;
				this.isManageFollowers = true;
			}
		},
		async follow(index, id) {
			try {
				const response = await dashboardService.followFriends(id);
				this.listFollowers[index].is_following = true;
			} catch (error) {
				console.error('Error follow:', error);
			}
		},
		async unfollow(index, id) {
			try {
				const response = await dashboardService.followFriends(id);
				this.listFollowers[index].is_following = false;
			} catch (error) {
				console.error('Error follow:', error);
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
					if (this.old_picture != this.profile_picture) {
						formData.append('profile_picture', this.profile_picture);
					}
					if (!this.mbti_id) {
						this.mbti_id = '';
					}
					if (!this.zodiac_id) {
						this.zodiac_id = '';
					}
					if (!this.relationship_id) {
						this.relationship_id = '';
					}
					formData.append('_method', 'PUT');
					formData.append('username', this.username);
					formData.append('name', this.name);
					formData.append('mbti_id', this.mbti_id);
					formData.append('zodiac_id', this.zodiac_id);
					formData.append('relationship_id', this.relationship_id);
					formData.append('enthusiast', this.enthusiast);
					const response = await authService.profile_put(formData);
					this.loading = false;
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
					this.loading = false;
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
		clickLink() {
			var backdrop = document.querySelector('.modal-backdrop');
			if (backdrop) {
				backdrop.remove();
			}
		},
		goEdit() {
			this.isMain = false;
			this.isManageFollowers = false;
			this.isEditProfile = true;
		},
		goBack() {
			this.isMain = true;
			this.isManageFollowers = false;
			this.isEditProfile = false;
		}
	}
};
</script>
