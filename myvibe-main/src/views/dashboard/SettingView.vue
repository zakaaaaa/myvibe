<template>
	<section class="setting">
		<!-- Blobs -->
		<div class="set-blob set-blob--1"></div>
		<div class="set-blob set-blob--2"></div>
		<div class="set-blob set-blob--3"></div>

		<div v-if="!isLoading" class="container">
			<!-- === MAIN PROFILE === -->
			<div class="mt-4" v-if="isMain">
				<RouterLink to="/dashboard" class="back-floating anim-back">
					<fa icon="arrow-left" />
				</RouterLink>

				<!-- Profile hero section -->
				<div class="profile-hero anim-profile-hero">
					<div class="profile-hero__avatar">
						<img :src="profile.profile_picture" alt="" />
						<div class="profile-hero__edit-badge" @click="goEdit()">
							<fa :icon="['fas', 'pencil']" />
						</div>
					</div>
					<h1 class="profile-hero__name">{{ profile.name }}</h1>
					<p class="profile-hero__email">{{ profile.email }}</p>
					<ul class="profile-hero__tags">
						<li v-if="profile.mbti"><span>{{ profile.mbti }}</span></li>
						<li v-if="profile.zodiac"><span>{{ profile.zodiac }}</span></li>
						<li v-if="profile.enthusiast"><span>{{ profile.enthusiast }}</span></li>
						<li v-if="profile.relationship"><span>{{ profile.relationship }}</span></li>
					</ul>
				</div>

				<!-- Menu list -->
				<div class="menu-glass anim-menu">
					<h2 class="menu-glass__title">Account</h2>
					<ul class="menu-glass__list">
						<li @click="manageFollowers()" class="menu-glass__item">
							<div class="menu-glass__icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
									<g id="user_follow_2_line" fill="none" fill-rule="evenodd">
										<path d="M24 0v24H0V0zM12.594 23.258l-.012.002-.071.035-.02.004-.014-.004-.071-.036c-.01-.003-.019 0-.024.006l-.004.01-.017.428.005.02.01.013.104.074.015.004.012-.004.104-.074.012-.016.004-.017-.017-.427c-.002-.01-.009-.017-.016-.018m.264-.113-.014.002-.184.093-.01.01-.003.011.018.43.005.012.008.008.201.092c.012.004.023 0 .029-.008l.004-.014-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014-.034.614c0 .012.007.02.017.024l.015-.002.201-.093.01-.008.003-.011.018-.43-.003-.012-.01-.01z" />
										<path fill="currentColor" d="M11 4a3 3 0 1 0 0 6 3 3 0 0 0 0-6M6 7a5 5 0 1 1 10 0A5 5 0 0 1 6 7M4.413 17.601c-.323.41-.413.72-.413.899 0 .122.037.251.255.426.249.2.682.407 1.344.582C6.917 19.858 8.811 20 11 20c.222 0 .441-.002.658-.005a1 1 0 0 1 .027 2c-.226.003-.455.005-.685.005-2.229 0-4.335-.14-5.913-.558-.785-.208-1.524-.506-2.084-.956C2.41 20.01 2 19.345 2 18.5c0-.787.358-1.523.844-2.139.494-.625 1.177-1.2 1.978-1.69C6.425 13.695 8.605 13 11 13c.447 0 .887.024 1.316.07a1 1 0 0 1-.211 1.989C11.745 15.02 11.375 15 11 15c-2.023 0-3.843.59-5.136 1.379-.647.394-1.135.822-1.45 1.222Zm17.295-1.533a1 1 0 0 0-1.414-1.414l-3.182 3.182-1.414-1.414a1 1 0 0 0-1.414 1.414l2.05 2.05a1.1 1.1 0 0 0 1.556 0z" />
									</g>
								</svg>
							</div>
							<span>Manage Followers</span>
							<div class="menu-glass__arrow"><fa :icon="['fas', 'angle-right']" /></div>
						</li>
						<li @click="copyMyVibe()" class="menu-glass__item">
							<div class="menu-glass__icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
									<g id="share_2_line" fill="none" fill-rule="evenodd">
										<path d="M24 0v24H0V0zM12.593 23.258l-.011.002-.071.035-.02.004-.014-.004-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01-.017.428.005.02.01.013.104.074.015.004.012-.004.104-.074.012-.016.004-.017-.017-.427c-.002-.01-.009-.017-.017-.018m.265-.113-.013.002-.185.093-.01.01-.003.011.018.43.005.012.008.007.201.093c.012.004.023 0 .029-.008l.004-.014-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014-.034.614c0 .012.007.02.017.024l.015-.002.201-.093.01-.008.004-.011.017-.43-.003-.012-.01-.01z" />
										<path fill="currentColor" d="M18.5 2a3.5 3.5 0 1 1-2.506 5.943L11.67 10.21c.213.555.33 1.16.33 1.79a4.99 4.99 0 0 1-.33 1.79l4.324 2.267a3.5 3.5 0 1 1-.93 1.771l-4.475-2.346a5 5 0 1 1 0-6.963l4.475-2.347A3.5 3.5 0 0 1 18.5 2m0 15a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M7 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6m11.5-5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3" />
									</g>
								</svg>
							</div>
							<span>Share Your Vibe!</span>
						</li>
						<li data-bs-toggle="modal" data-bs-target="#logOutModal" class="menu-glass__item">
							<div class="menu-glass__icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
									<g id="arrow_to_left_line" fill="none" fill-rule="nonzero">
										<path d="M24 0v24H0V0h24ZM12.594 23.258l-.012.002-.071.035-.02.004-.014-.004-.071-.036c-.01-.003-.019 0-.024.006l-.004.01-.017.428.005.02.01.013.104.074.015.004.012-.004.104-.074.012-.016.004-.017-.017-.427c-.002-.01-.009-.017-.016-.018Zm.264-.113-.014.002-.184.093-.01.01-.003.011.018.43.005.012.008.008.201.092c.012.004.023 0 .029-.008l.004-.014-.034-.614c-.003-.012-.01-.02-.02-.022Zm-.715.002a.023.023 0 0 0-.027.006l-.006.014-.034.614c0 .012.007.02.017.024l.015-.002.201-.093.01-.008.003-.011.018-.43-.003-.012-.01-.01-.184-.092Z" />
										<path fill="currentColor" d="M5 6a1 1 0 0 0-2 0v12a1 1 0 1 0 2 0V6Zm7.703 10.95a1 1 0 0 0 0-1.415L10.167 13H20a1 1 0 1 0 0-2h-9.833l2.536-2.536a1 1 0 0 0-1.415-1.414l-4.242 4.243a1 1 0 0 0 0 1.414l4.242 4.243a1 1 0 0 0 1.415 0Z" />
									</g>
								</svg>
							</div>
							<span>Logout</span>
						</li>
						<li @click="deleteAccount()" class="menu-glass__item menu-glass__item--danger">
							<div class="menu-glass__icon">
								<fa icon="trash" />
							</div>
							<span>Delete Account</span>
						</li>
					</ul>
				</div>
			</div>

			<!-- === MANAGE FOLLOWERS === -->
			<transition name="set-slide" mode="out-in">
				<div class="mt-4" v-if="isManageFollowers" key="followers">
					<div class="back-floating anim-back" @click="goBack()">
						<fa icon="arrow-left" />
					</div>
					<h1 class="title text-center mb-4 anim-title" style="padding-top: 40px">Followers</h1>
					<ul class="followers_list">
						<li v-for="(item, index) in listFollowers" :key="index" class="mb-4 anim-list-item" :style="{ animationDelay: (index * 0.06) + 's' }">
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
			</transition>

			<!-- === EDIT PROFILE === -->
			<transition name="set-slide" mode="out-in">
				<div class="mt-4" v-if="isEditProfile" key="edit">
					<div class="back-floating anim-back" @click="goBack()">
						<fa icon="arrow-left" />
					</div>
					<h1 class="title text-center mb-4 anim-title" style="padding-top: 40px">Fill Your <span class="highlight">Vibe</span></h1>
					<form autocomplete="off" @submit.prevent="fillProfile" class="anim-form">
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
							<button type="submit" class="btn-action glass-submit-btn">
								<span v-if="!loading" class="me-2">All Set!</span>
								<span v-else> <fa icon="spinner" class="fa-spin" /> Loading... </span>
							</button>
						</div>
						<div class="pt-5"></div>
					</form>
				</div>
			</transition>
		</div>
		<div v-else class="loading">
			<fa icon="spinner" class="fa-spin-pulse" />
		</div>

		<!-- Modals (unchanged) -->
		<div class="modal pt-5 fade" id="chooseMethodPictureModal" tabindex="-1" aria-labelledby="chooseMethodPictureModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body notification">
						<div class="title"><h1><span>Set Profile Picture</span></h1></div>
						<ul class="btn-list">
							<li><button @click="chooseFromGallery"><span><fa icon="image" class="me-2" /> Choose From Gallery</span> <span class="icon"><fa icon="angle-right" /></span></button></li>
							<li><button @click="takePhoto"><span><fa icon="camera-retro" class="me-2" /> Take a Photo</span> <span class="icon"><fa icon="angle-right" /></span></button></li>
						</ul>
					</div>
					<div class="modal-button"><button type="button" class="btn-block" data-bs-dismiss="modal" aria-label="Close" ref="closeModalPicture">Close</button></div>
				</div>
			</div>
		</div>
		<div class="modal pt-5 fade" id="logOutModal" tabindex="-1" aria-labelledby="logOutModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body notification">
						<div class="title"><h1>Logout <span>Alert!</span></h1></div>
						<p class="m-0 text-center">Are you sure to logout?</p>
					</div>
					<div class="modal-button"><RouterLink to="/landing" class="btn-block purple" @click="logoutLink()">Yes</RouterLink></div>
					<div class="modal-button"><button type="button" class="btn-block" data-bs-dismiss="modal" aria-label="Close">Close</button></div>
				</div>
			</div>
		</div>
		<span data-bs-toggle="modal" data-bs-target="#notifModal" ref="notifModalBtn"></span>
		<div class="modal fade" id="notifModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="notifModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body notification">
						<div class="title"><h1>{{ titleNotif }} <span :class="{ success: statusNotif === 'success' }">{{ titleNotifSecond }}</span></h1></div>
						<p class="m-0 text-center">{{ message }}</p>
					</div>
					<div class="modal-button" v-if="showLink"><RouterLink :to="linkTo" class="btn-block purple" @click="clickLink"> {{ linkText }} </RouterLink></div>
					<div class="modal-button" v-if="showDismiss"><button type="button" class="btn-block" data-bs-dismiss="modal" aria-label="Close">Close</button></div>
				</div>
			</div>
		</div>
		<span data-bs-toggle="modal" data-bs-target="#deleteAccount" ref="deleteAccountBtn"></span>
		<div class="modal fade" id="deleteAccount" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteAccountLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body notification">
						<div class="title"><h1>Delete Account <span>Alert!</span></h1></div>
						<p class="m-0 text-center">Wait! Are you sure you want to delete your account? All your data will be permanently erased, and this action can't be undone.</p>
					</div>
					<div class="modal-button">
						<div class="btn-block purple" :class="countdown > 0 ? 'd-block' : 'd-none'">{{ countdown > 0 ? `Wait (${countdown}s)` : '' }}</div>
						<RouterLink to="/landing" class="btn-block purple" :class="countdown > 0 ? 'd-none' : 'd-block'" @click.prevent="handleDeleteAccount()">Yes, Delete My Account</RouterLink>
					</div>
					<div class="modal-button"><button type="button" class="btn-block" data-bs-dismiss="modal" aria-label="Close">Close</button></div>
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
	components: { VueSelect },
	data() {
		return {
			optionsMBTI: [], optionsZodiac: [], optionsRelationship: [],
			old_picture: '', ava_picture: '', profile_picture: '',
			username: '', name: '', mbti_id: '', zodiac_id: '', relationship_id: '', enthusiast: '',
			isLoading: false, isMain: true, isManageFollowers: false, isEditProfile: false,
			profile: [], listFollowers: [],
			statusNotif: 'failed', titleNotif: '', titleNotifSecond: '', message: '',
			showLink: false, linkTo: '/', linkText: 'Okay', showDismiss: false,
			showUsername: true, countdown: 10, timer: null
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
				if (this.countdown > 0) { this.countdown -= 1; } else { clearInterval(this.timer); }
			}, 1000);
		},
		async getUser() {
			this.isLoading = true;
			try {
				const response = await dashboardService.getFriends(this.$route.params.username);
				if (response.data.profile.profile_picture) {
					response.data.profile.profile_picture = process.env.VUE_APP_API_URL + '/' + response.data.profile.profile_picture;
				} else { response.data.profile.profile_picture = logo; }
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
			} catch (error) { console.error('Error fetching home:', error); }
		},
		goDetail(path) { this.$router.push('/' + path); },
		async copyMyVibe() {
			try {
				await Clipboard.write({ string: 'https://myvibeapp.site/' + this.$route.params.username });
				this.statusNotif = 'success'; this.titleNotif = 'Share Your'; this.titleNotifSecond = 'Vibe!';
				this.message = 'Copied to clipboard!'; this.showLink = false; this.showDismiss = true; this.linkTo = ''; this.showNotifModal();
			} catch (error) {
				this.statusNotif = 'failed'; this.titleNotif = 'Logout'; this.titleNotifSecond = 'Alert!';
				this.message = 'Failed to copy vibe'; this.showLink = false; this.showDismiss = true; this.linkTo = ''; this.showNotifModal();
			}
		},
		async logoutLink() {
			try { await dashboardService.postLogout(); } catch (error) {
				this.statusNotif = 'failed'; this.titleNotif = 'Logout'; this.titleNotifSecond = 'Alert!';
				this.message = 'Failed to logout'; this.showLink = false; this.showDismiss = true; this.linkTo = ''; this.showNotifModal();
			} finally { var backdrop = document.querySelector('.modal-backdrop'); if (backdrop) { backdrop.remove(); } }
		},
		deleteAccount() { this.$refs.deleteAccountBtn.click(); this.startCountdown(); },
		async handleDeleteAccount() {
			try { await dashboardService.postDeleteAccount(); } catch (error) {
				this.statusNotif = 'failed'; this.titleNotif = 'Delete Account'; this.titleNotifSecond = 'Alert!';
				this.message = 'Failed to delete account'; this.showLink = false; this.showDismiss = true; this.linkTo = ''; this.showNotifModal();
			} finally { var backdrop = document.querySelector('.modal-backdrop'); if (backdrop) { backdrop.remove(); } }
		},
		showNotifModal() { this.$refs.notifModalBtn.click(); },
		async manageFollowers() {
			this.isLoading = true;
			try {
				const response = await dashboardService.getFollowers(this.profile.id);
				for (let i = 0; i < response.data.followers.length; i++) {
					if (!response.data.followers[i].profile_picture) { response.data.followers[i].profile_picture = logo; }
					else { response.data.followers[i].profile_picture = process.env.VUE_APP_API_URL + '/' + response.data.followers[i].profile_picture; }
				}
				this.listFollowers = response.data.followers; this.isLoading = false;
			} catch (error) { console.error('Error fetching followers:', error); }
			finally { this.isMain = false; this.isManageFollowers = true; }
		},
		async follow(index, id) { try { await dashboardService.followFriends(id); this.listFollowers[index].is_following = true; } catch (error) { console.error('Error follow:', error); } },
		async unfollow(index, id) { try { await dashboardService.followFriends(id); this.listFollowers[index].is_following = false; } catch (error) { console.error('Error follow:', error); } },
		async getOptionsMBTI() {
			try { const response = await authService.getMBTI(); this.optionsMBTI = response.data.data.map((item) => ({ label: item.mbti_name + ' - ' + item.mbti_desc, value: item.id })); }
			catch (error) { this.message = error.response.data.message; this.showLink = false; this.showDismiss = true; this.linkTo = ''; this.showNotifModal(); }
			finally { this.loading = false; }
		},
		async getOptionsZodiac() {
			try { const response = await authService.getZodiac(); this.optionsZodiac = response.data.data.map((item) => ({ label: item.zodiac_name, value: item.id, description: item.zodiac_desc })); }
			catch (error) { this.message = error.response.data.message; this.showLink = false; this.showDismiss = true; this.linkTo = ''; this.showNotifModal(); }
			finally { this.loading = false; }
		},
		async getOptionsRelationship() {
			try { const response = await authService.getRelationship(); this.optionsRelationship = response.data.data.map((item) => ({ label: item.relationship_name, value: item.id, description: item.relationship_desc })); }
			catch (error) { this.message = error.response.data.message; this.showLink = false; this.showDismiss = true; this.linkTo = ''; this.showNotifModal(); }
			finally { this.loading = false; }
		},
		async fillProfile() {
			if (!this.profile_picture || !this.name || !this.enthusiast || !this.username) {
				this.statusNotif = 'failed'; this.titleNotif = 'Validation'; this.titleNotifSecond = 'Error!';
				this.message = 'Please fill all form & picture'; this.showLink = false; this.showDismiss = true; this.linkTo = ''; this.showNotifModal();
			} else {
				this.loading = true;
				try {
					const formData = new FormData();
					if (this.old_picture != this.profile_picture) { formData.append('profile_picture', this.profile_picture); }
					if (!this.mbti_id) { this.mbti_id = ''; } if (!this.zodiac_id) { this.zodiac_id = ''; } if (!this.relationship_id) { this.relationship_id = ''; }
					formData.append('_method', 'PUT'); formData.append('username', this.username); formData.append('name', this.name);
					formData.append('mbti_id', this.mbti_id); formData.append('zodiac_id', this.zodiac_id);
					formData.append('relationship_id', this.relationship_id); formData.append('enthusiast', this.enthusiast);
					await authService.profile_put(formData); this.loading = false; this.$router.push('/dashboard');
				} catch (error) {
					this.statusNotif = 'failed'; this.titleNotif = 'Validation'; this.titleNotifSecond = 'Error!';
					if (error.response.data.errors) { let messages = []; for (const key in error.response.data.errors) { if (error.response.data.errors[key] && Array.isArray(error.response.data.errors[key])) { messages = messages.concat(error.response.data.errors[key]); } } this.message = messages.join(', '); }
					else { this.message = error.response.data.message; }
					this.showLink = false; this.showDismiss = true; this.linkTo = ''; this.showNotifModal(); this.loading = false;
				} finally { this.loading = false; }
			}
		},
		async takePhoto() {
			try { const photo = await Camera.getPhoto({ resultType: CameraResultType.Uri, source: CameraSource.Camera, quality: 90 }); this.ava_picture = photo.webPath; const file = await this.convertUriToFile(photo.webPath, 'avatar.jpg'); this.profile_picture = file; }
			catch (error) { console.error('Error Take Photo: ', error); } finally { this.$refs.closeModalPicture.click(); }
		},
		async chooseFromGallery() {
			try { const photo = await Camera.getPhoto({ resultType: CameraResultType.Uri, source: CameraSource.Photos, quality: 90 }); this.ava_picture = photo.webPath; const file = await this.convertUriToFile(photo.webPath, 'avatar.jpg'); this.profile_picture = file; }
			catch (error) { console.error('Error Choose Photo: ', error); } finally { this.$refs.closeModalPicture.click(); }
		},
		async convertUriToFile(uri, fileName) { const response = await fetch(uri); const blob = await response.blob(); return new File([blob], fileName, { type: blob.type }); },
		clickLink() { var backdrop = document.querySelector('.modal-backdrop'); if (backdrop) { backdrop.remove(); } },
		goEdit() { this.isMain = false; this.isManageFollowers = false; this.isEditProfile = true; },
		goBack() { this.isMain = true; this.isManageFollowers = false; this.isEditProfile = false; }
	}
};
</script>

<style lang="scss" scoped>
@import '@/assets/scss/color.scss';

// === BLOBS ===
.set-blob {
	position: fixed;
	filter: blur(55px);
	pointer-events: none;
	z-index: 0;
}
.set-blob--1 {
	top: -8%;
	right: -14%;
	width: min(260px, 60vw);
	height: min(260px, 60vw);
	background: radial-gradient(circle, rgba($purple, 0.2) 0%, rgba(#6c5ce7, 0.06) 40%, transparent 70%);
	border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
	animation: sBm1 14s ease-in-out infinite;
}
.set-blob--2 {
	bottom: 8%;
	left: -12%;
	width: min(220px, 50vw);
	height: min(220px, 50vw);
	background: radial-gradient(circle, rgba(#4a3adf, 0.16) 0%, rgba($purple, 0.04) 40%, transparent 70%);
	border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
	animation: sBm2 17s ease-in-out infinite;
}
.set-blob--3 {
	top: 35%;
	left: 50%;
	transform: translateX(-50%);
	width: min(280px, 64vw);
	height: min(130px, 30vw);
	background: radial-gradient(ellipse, rgba($purple, 0.07) 0%, transparent 70%);
	border-radius: 50%;
	animation: sBf 11s ease-in-out infinite;
}
@keyframes sBm1 {
	0%, 100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(0,0) rotate(0deg); }
	50% { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; transform: translate(-10px,14px) rotate(28deg); }
}
@keyframes sBm2 {
	0%, 100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; transform: translate(0,0) rotate(0deg); }
	50% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(12px,-10px) rotate(-28deg); }
}
@keyframes sBf {
	0%, 100% { transform: translateX(-50%) translateY(0) scale(1); opacity: 0.07; }
	50% { transform: translateX(-50%) translateY(-14px) scale(1.04); opacity: 0.12; }
}

// === TRANSITIONS ===
.set-slide-enter-active, .set-slide-leave-active { transition: all 0.4s cubic-bezier(0.23,1,0.32,1); }
.set-slide-enter-from { opacity: 0; transform: translateX(30px); }
.set-slide-leave-to { opacity: 0; transform: translateX(-20px); }

// === ENTRANCE ANIMATIONS ===
.anim-back { animation: sDown 0.4s cubic-bezier(0.23,1,0.32,1) both; }
.anim-profile-hero { animation: sUp 0.6s cubic-bezier(0.23,1,0.32,1) 0.1s both; }
.anim-menu { animation: sUp 0.5s cubic-bezier(0.23,1,0.32,1) 0.25s both; }
.anim-title { animation: sDown 0.5s cubic-bezier(0.23,1,0.32,1) both; }
.anim-form { animation: sUp 0.5s cubic-bezier(0.23,1,0.32,1) 0.15s both; }
.anim-list-item { animation: sListIn 0.4s ease-out both; }
@keyframes sDown { from { opacity:0; transform:translateY(-18px); } to { opacity:1; transform:translateY(0); } }
@keyframes sUp { from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:translateY(0); } }
@keyframes sListIn { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }

// === GLASS BACK BUTTON ===
.back-floating {
	position: fixed;
	top: calc(env(safe-area-inset-top) + 16px);
	left: 16px;
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
	box-shadow: 0 4px 16px rgba(0,0,0,0.2), inset 0 1px 0 rgba($white,0.08);
	transition: all 0.4s cubic-bezier(0.23,1,0.32,1);

	&::before {
		content: '';
		position: absolute;
		top: 2px; left: 15%; right: 15%; height: 40%;
		background: linear-gradient(180deg, rgba($white,0.1) 0%, transparent 100%);
		border-radius: 50%;
		pointer-events: none;
	}
	&:hover {
		transform: translateY(-2px) scale(1.08);
		background: rgba($purple, 0.15);
		border-color: rgba($purple, 0.25);
		color: $white;
		box-shadow: 0 8px 24px rgba(0,0,0,0.25), 0 0 16px rgba($purple,0.1);
	}
	&:active { transform: scale(0.92); }
}

// === PROFILE HERO (NEW LAYOUT) ===
.profile-hero {
	text-align: center;
	padding: 56px 20px 28px;
	position: relative;
	z-index: 2;

	&__avatar {
		position: relative;
		display: inline-block;
		margin-bottom: 14px;

		img {
			width: 100px;
			height: 100px;
			border-radius: 50%;
			object-fit: cover;
			border: 3px solid rgba($purple, 0.3);
			box-shadow: 0 8px 28px rgba(0,0,0,0.25), 0 0 20px rgba($purple, 0.1);
		}
	}

	&__edit-badge {
		position: absolute;
		bottom: 2px;
		right: 2px;
		width: 32px;
		height: 32px;
		border-radius: 50%;
		background: rgba($purple, 0.7);
		backdrop-filter: blur(12px);
		-webkit-backdrop-filter: blur(12px);
		border: 1px solid rgba($white, 0.15);
		display: flex;
		align-items: center;
		justify-content: center;
		color: $white;
		font-size: 12px;
		cursor: pointer;
		transition: all 0.3s ease;
		box-shadow: 0 4px 12px rgba($purple, 0.3);

		&:hover {
			transform: scale(1.1);
			background: rgba($purple, 0.9);
		}
	}

	&__name {
		color: $white;
		font-size: 22px;
		font-weight: 700;
		margin: 0 0 4px;
	}

	&__email {
		color: rgba($white, 0.45);
		font-size: 13px;
		margin: 0 0 12px;
	}

	&__tags {
		list-style: none;
		display: flex;
		justify-content: center;
		flex-wrap: wrap;
		gap: 6px;
		padding: 0;
		margin: 0;

		li span {
			background: rgba($purple, 0.6);
			backdrop-filter: blur(8px);
			-webkit-backdrop-filter: blur(8px);
			padding: 3px 10px;
			border-radius: 8px;
			font-size: 11px;
			color: $white;
			border: 1px solid rgba($white, 0.1);
			box-shadow: 0 2px 6px rgba($purple, 0.15);
		}
	}
}

// === MENU GLASS ===
.menu-glass {
	position: relative;
	z-index: 2;
	padding: 0 4px;

	&__title {
		color: $white;
		font-size: 16px;
		font-weight: 700;
		margin-bottom: 10px;
		padding-left: 4px;
	}

	&__list {
		list-style: none;
		padding: 0;
		margin: 0;
		background: rgba($white, 0.025);
		backdrop-filter: blur(16px);
		-webkit-backdrop-filter: blur(16px);
		border: 1px solid rgba($white, 0.06);
		border-radius: 20px;
		overflow: hidden;
		box-shadow: 0 4px 20px rgba(0,0,0,0.1), inset 0 1px 0 rgba($white,0.04);
	}

	&__item {
		display: flex;
		align-items: center;
		padding: 16px 18px;
		color: $white;
		cursor: pointer;
		transition: all 0.3s ease;
		position: relative;
		border-bottom: 1px solid rgba($white, 0.04);

		&:last-child { border-bottom: none; }

		&:hover {
			background: rgba($white, 0.04);
		}

		&--danger {
			span { color: rgba(#E84C4C, 0.85); }
			.menu-glass__icon { color: rgba(#E84C4C, 0.85); }
			&:hover { background: rgba(#E84C4C, 0.05); }
		}

		span {
			font-size: 14px;
			font-weight: 600;
			flex: 1;
		}
	}

	&__icon {
		width: 36px;
		height: 36px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 10px;
		background: rgba($white, 0.04);
		border: 1px solid rgba($white, 0.06);
		margin-right: 14px;
		color: $white;
		font-size: 16px;
	}

	&__arrow {
		color: rgba($white, 0.3);
		font-size: 16px;
	}
}

// === GLASS SUBMIT BUTTON ===
.glass-submit-btn {
	background: linear-gradient(135deg, rgba($purple, 0.7), rgba($purple, 0.9)) !important;
	backdrop-filter: blur(16px) !important;
	-webkit-backdrop-filter: blur(16px) !important;
	border: 1px solid rgba($white, 0.15) !important;
	box-shadow: 0 6px 24px rgba($purple, 0.3), inset 0 1px 0 rgba($white, 0.15) !important;
	position: relative;
	overflow: hidden;
	transition: all 0.4s cubic-bezier(0.23,1,0.32,1) !important;

	&::after {
		content: '';
		position: absolute;
		top: 0; left: 0; right: 0; height: 50%;
		background: linear-gradient(180deg, rgba($white,0.12) 0%, transparent 100%);
		border-radius: 30px 30px 0 0;
		pointer-events: none;
	}

	&:hover {
		transform: translateY(-3px);
		box-shadow: 0 12px 36px rgba($purple, 0.4), 0 0 20px rgba($purple, 0.15) !important;
	}
	&:active { transform: translateY(-1px) scale(0.97); }
}

// === FOLLOWERS LIST GLASS OVERRIDE ===
ul.followers_list > li {
	background: rgba($white, 0.035) !important;
	backdrop-filter: blur(16px) !important;
	-webkit-backdrop-filter: blur(16px) !important;
	border: 1px solid rgba($white, 0.07) !important;
	box-shadow: 0 4px 16px rgba(0,0,0,0.1), inset 0 1px 0 rgba($white,0.05) !important;
	transition: all 0.4s cubic-bezier(0.23,1,0.32,1) !important;

	&:hover {
		border-color: rgba($purple, 0.2) !important;
		background: rgba($white, 0.06) !important;
		transform: translateY(-2px);
	}

	img {
		border: 1px solid rgba($white, 0.1) !important;
		box-shadow: 0 2px 8px rgba(0,0,0,0.15);
	}
}

// === LOADING ===
.loading {
	display: flex;
	justify-content: center;
	align-items: center;
	height: 100dvh;
	color: $purple;
	font-size: 28px;
	filter: drop-shadow(0 0 12px rgba($purple, 0.4));
}
</style>