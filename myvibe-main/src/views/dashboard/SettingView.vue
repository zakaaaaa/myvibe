<template>
	<section class="setting">
		<div class="set-blob set-blob--1"></div>
		<div class="set-blob set-blob--2"></div>
		<div class="set-blob set-blob--3"></div>

		<div v-if="!isLoading" class="container">
			<!-- === MAIN PROFILE === -->
			<div class="mt-4" v-if="isMain">
				<RouterLink to="/dashboard" class="back-floating anim-back"><fa icon="arrow-left" /></RouterLink>

				<div class="profile-hero anim-profile-hero">
					<div class="profile-hero__avatar">
						<img :src="profile.profile_picture" alt="" />
						<div class="profile-hero__edit-badge" @click="goEdit()"><fa :icon="['fas', 'pencil']" /></div>
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

				<div class="menu-glass anim-menu">
					<h2 class="menu-glass__title">Account</h2>
					<ul class="menu-glass__list">
						<li @click="manageFollowers()" class="menu-glass__item">
							<div class="menu-glass__icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><g fill="none"><path d="M24 0v24H0V0z"/><path fill="currentColor" d="M11 4a3 3 0 1 0 0 6 3 3 0 0 0 0-6M6 7a5 5 0 1 1 10 0A5 5 0 0 1 6 7M4.413 17.601c-.323.41-.413.72-.413.899 0 .122.037.251.255.426.249.2.682.407 1.344.582C6.917 19.858 8.811 20 11 20c.222 0 .441-.002.658-.005a1 1 0 0 1 .027 2c-.226.003-.455.005-.685.005-2.229 0-4.335-.14-5.913-.558-.785-.208-1.524-.506-2.084-.956C2.41 20.01 2 19.345 2 18.5c0-.787.358-1.523.844-2.139.494-.625 1.177-1.2 1.978-1.69C6.425 13.695 8.605 13 11 13c.447 0 .887.024 1.316.07a1 1 0 0 1-.211 1.989C11.745 15.02 11.375 15 11 15c-2.023 0-3.843.59-5.136 1.379-.647.394-1.135.822-1.45 1.222Zm17.295-1.533a1 1 0 0 0-1.414-1.414l-3.182 3.182-1.414-1.414a1 1 0 0 0-1.414 1.414l2.05 2.05a1.1 1.1 0 0 0 1.556 0z"/></g></svg></div>
							<span>Manage Followers</span>
							<div class="menu-glass__arrow"><fa :icon="['fas', 'angle-right']" /></div>
						</li>
						<li @click="goSavedVibes()" class="menu-glass__item">
							<div class="menu-glass__icon"><fa :icon="['far', 'bookmark']" /></div>
							<span>Saved Vibes</span>
							<div class="menu-glass__arrow"><fa :icon="['fas', 'angle-right']" /></div>
						</li>
						<li @click="openQrShare()" class="menu-glass__item">
						<div class="menu-glass__icon">
							<!-- icon SVG existing tetap dipakai -->
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><g fill="none"><path d="M24 0v24H0V0z"/><path fill="currentColor" d="M18.5 2a3.5 3.5 0 1 1-2.506 5.943L11.67 10.21c.213.555.33 1.16.33 1.79a4.99 4.99 0 0 1-.33 1.79l4.324 2.267a3.5 3.5 0 1 1-.93 1.771l-4.475-2.346a5 5 0 1 1 0-6.963l4.475-2.347A3.5 3.5 0 0 1 18.5 2m0 15a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M7 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6m11.5-5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3"/></g></svg>
						</div>
						<span>Share Your Vibe!</span>
						</li>
						<li data-bs-toggle="modal" data-bs-target="#logOutModal" class="menu-glass__item">
							<div class="menu-glass__icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><g fill="none"><path d="M24 0v24H0V0h24Z"/><path fill="currentColor" d="M5 6a1 1 0 0 0-2 0v12a1 1 0 1 0 2 0V6Zm7.703 10.95a1 1 0 0 0 0-1.415L10.167 13H20a1 1 0 1 0 0-2h-9.833l2.536-2.536a1 1 0 0 0-1.415-1.414l-4.242 4.243a1 1 0 0 0 0 1.414l4.242 4.243a1 1 0 0 0 1.415 0Z"/></g></svg></div>
							<span>Logout</span>
						</li>
						<li @click="deleteAccount()" class="menu-glass__item menu-glass__item--danger">
							<div class="menu-glass__icon"><fa icon="trash" /></div>
							<span>Delete Account</span>
						</li>
					</ul>
				</div>
			</div>

			<!-- === SAVED VIBES === -->
			<transition name="set-slide" mode="out-in">
				<div class="mt-4" v-if="isSavedVibes" key="saved">
					<div class="back-floating anim-back" @click="goBack()"><fa icon="arrow-left" /></div>
					<h1 class="title text-center mb-4 anim-title" style="padding-top: 40px">Saved <span class="highlight">Vibes</span></h1>

					<div v-if="!isLoadingSaved" class="sv-section">
						<div v-if="savedVibes.length === 0" class="saved-empty">
							<fa :icon="['far', 'bookmark']" />
							<p>No saved vibes yet</p>
							<small>Long press on any vibe in Spotlight to save it here</small>
						</div>
						<ul v-else class="sv-card-list">
							<li
								v-for="(vibe, index) in savedVibes"
								:key="vibe.id"
								class="mb-4 anim-list-item"
								:style="{ animationDelay: (index * 0.08) + 's' }"
								@touchstart.passive="startSavedLongPress($event, vibe)"
								@touchend="endSavedLongPress"
								@touchmove="cancelSavedLongPress"
								@click="goVibeDetail(vibe)"
							>
								<div class="sv-card" :style="{ backgroundImage: `url(${vibe.image_url})` }" :class="[{ 'sv-card--tall': vibe.category && ['Film', 'Top', 'Reading'].some((w) => vibe.category.title.includes(w)) }]">
									<div class="sv-card__badge" v-if="vibe.user" @click.stop="goDetail(vibe.user.username)">
										<img :src="getProfilePic(vibe.user.profile_picture)" alt="" />
										<h3>{{ vibe.user.name }}</h3>
									</div>
									<div class="sv-card__gradient"></div>
									<div class="sv-card__bottom">
										<div class="sv-card__text">
											<h1>{{ vibe.title }}</h1>
										</div>
										<div class="sv-card__star">
											<fa :icon="['fas', 'star']" />
											<span>{{ vibe.rating }}</span>
										</div>
									</div>
								</div>
							</li>
						</ul>
					</div>
					<div v-else class="loading-small"><fa icon="spinner" class="fa-spin-pulse" /></div>

					<!-- Saved Vibe Long Press Popup -->
					<Transition name="sv-popup-overlay">
						<div v-if="showSavedPopup" class="sv-popup-overlay" @click="closeSavedPopup" @touchstart.prevent="closeSavedPopup"></div>
					</Transition>
					<Transition name="sv-popup-menu">
						<div v-if="showSavedPopup" class="sv-popup-container" :style="savedPopupPosition">
							<div class="sv-popup-preview">
								<div class="sv-popup-preview__image" :style="{ backgroundImage: `url(${selectedSavedVibe?.image_url})` }"></div>
								<div class="sv-popup-preview__info">
									<h4>{{ selectedSavedVibe?.title }}</h4>
									<p>{{ selectedSavedVibe?.comment && selectedSavedVibe.comment.length > 60 ? selectedSavedVibe.comment.slice(0, 60) + '...' : selectedSavedVibe?.comment }}</p>
								</div>
							</div>
							<div class="sv-popup-actions">
								<button class="sv-popup-action" @click.stop="handleSavedAction('detail')"><fa :icon="['fas', 'eye']" /><span>View Detail</span></button>
								<div class="sv-popup-divider"></div>
								<button class="sv-popup-action" @click.stop="handleSavedAction('share')"><fa :icon="['fas', 'paper-plane']" /><span>Share This Vibe</span></button>
								<div class="sv-popup-divider"></div>
								<button class="sv-popup-action sv-popup-action--danger" @click.stop="handleSavedAction('unsave')"><fa :icon="['fas', 'bookmark']" /><span>Unsave Vibe</span></button>
							</div>
						</div>
					</Transition>
				</div>
			</transition>

			<!-- === MANAGE FOLLOWERS === -->
			<transition name="set-slide" mode="out-in">
				<div class="mt-4" v-if="isManageFollowers" key="followers">
					<div class="back-floating anim-back" @click="goBack()"><fa icon="arrow-left" /></div>
					<h1 class="title text-center mb-4 anim-title" style="padding-top: 40px">Followers</h1>
					<ul class="followers_list">
						<li v-for="(item, index) in listFollowers" :key="index" class="mb-4 anim-list-item" :style="{ animationDelay: (index * 0.06) + 's' }">
							<img :src="item.profile_picture" alt="" @click="goDetail(item.username)" />
							<div class="text"><h2>{{ item.name }}</h2><p>@{{ item.username }}</p></div>
							<div class="icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" v-if="!item.is_following" @click="follow(index, item.id)"><g fill="none"><path d="M24 0v24H0V0z"/><path fill="currentColor" d="M11 2a5 5 0 1 0 0 10 5 5 0 0 0 0-10M8 7a3 3 0 1 1 6 0 3 3 0 0 1-6 0M4 18.5c0-.18.09-.489.413-.899.316-.4.804-.828 1.451-1.222C7.157 15.589 8.977 15 11 15c.375 0 .744.02 1.105.059a1 1 0 1 0 .211-1.99A12.905 12.905 0 0 0 11 13c-2.395 0-4.575.694-6.178 1.672-.8.488-1.484 1.064-1.978 1.69C2.358 16.976 2 17.713 2 18.5c0 .845.411 1.511 1.003 1.986.56.45 1.299.748 2.084.956C6.665 21.859 8.771 22 11 22l.685-.005a1 1 0 1 0-.027-2L11 20c-2.19 0-4.083-.143-5.4-.492-.663-.175-1.096-.382-1.345-.582C4.037 18.751 4 18.622 4 18.5M18 14a1 1 0 0 1 1 1v2h2a1 1 0 1 1 0 2h-2v2a1 1 0 1 1-2 0v-2h-2a1 1 0 1 1 0-2h2v-2a1 1 0 0 1 1-1"/></g></svg>
								<fa :icon="['fas', 'check']" v-if="item.is_following" @click="unfollow(index, item.id)" />
							</div>
						</li>
					</ul>
				</div>
			</transition>

			<!-- === EDIT PROFILE === -->
			<transition name="set-slide" mode="out-in">
				<div class="mt-4" v-if="isEditProfile" key="edit">
					<div class="back-floating anim-back" @click="goBack()"><fa icon="arrow-left" /></div>
					<h1 class="title text-center mb-4 anim-title" style="padding-top: 40px">Fill Your <span class="highlight">Vibe</span></h1>
					<form autocomplete="off" @submit.prevent="fillProfile" class="anim-form">
						<div class="mb-3"><label class="form-label mb-1">Profile Picture</label><div class="profile-picture-cage" data-bs-toggle="modal" data-bs-target="#chooseMethodPictureModal"><div class="image"><img :src="this.ava_picture" alt="" /></div><p>Put up a nice photo! Everyone can see it.</p></div></div>
						<div class="mb-3" v-if="showUsername"><label class="form-label">Username</label><input type="text" class="form-control" v-model="username" placeholder="Add your username here" minlength="3" maxlength="15" pattern="^[a-zA-Z0-9_]+$" required /></div>
						<div class="mb-3"><label class="form-label">Fullname</label><input type="text" class="form-control" v-model="name" placeholder="Add your fullname here" required /></div>
						<div class="mb-3"><label class="form-label">MBTI <span style="font-size: 10px">(optional)</span></label><select class="form-control" v-model="mbti_id"><option value="" disabled>Let me know what your MBTI</option><option v-for="opt in optionsMBTI" :key="opt.value" :value="opt.value">{{ opt.label }}</option></select></div>
						<div class="mb-3"><label class="form-label">Zodiac <span style="font-size: 10px">(optional)</span></label><select class="form-control" v-model="zodiac_id"><option value="" disabled>Choose your zodiac here!</option><option v-for="opt in optionsZodiac" :key="opt.value" :value="opt.value">{{ opt.label }} {{ opt.description }}</option></select></div>
						<div class="mb-3"><label class="form-label">Enthusiast</label><input type="text" class="form-control" v-model="enthusiast" maxlength="10" placeholder="Let anyone know your hobbies or interest" required /></div>
						<div class="mb-3"><label class="form-label">Relationship <span style="font-size: 10px">(optional)</span></label><select class="form-control" v-model="relationship_id"><option value="" disabled>What about your relationship?</option><option v-for="opt in optionsRelationship" :key="opt.value" :value="opt.value">{{ opt.label }} - {{ opt.description }}</option></select></div>
						<div class="d-flex justify-content-center align-items-center flex-column mt-4 gap-2"><button type="submit" class="btn-action glass-submit-btn"><span v-if="!loading" class="me-2">All Set!</span><span v-else><fa icon="spinner" class="fa-spin" /> Loading...</span></button></div>
						<div class="pt-5"></div>
					</form>
				</div>
			</transition>
		</div>
		<div v-else class="loading"><fa icon="spinner" class="fa-spin-pulse" /></div>
		<!-- === QR SHARE POPUP === -->
<Transition name="qr-overlay">
  <div v-if="showQrPopup" class="qr-overlay" @click.self="closeQrShare"></div>
</Transition>
<Transition name="qr-sheet">
  <div v-if="showQrPopup" class="qr-sheet" @click.stop>
    <div class="qr-sheet__handle"></div>

    <div class="qr-sheet__inner">
      <!-- Card untuk di-screenshot/download (ref="qrCard") -->
      <div class="qr-card" ref="qrCard">
        <div class="qr-card__brand">
          <span class="qr-card__logo">MyVibe</span>
          <small>Rate Your Favorites</small>
        </div>

        <div class="qr-card__qr-wrap">
          <div class="qr-card__qr">
            <QrcodeVue
              :value="profileUrl"
              :size="180"
              level="H"
              foreground="#1a1033"
              background="#ffffff"
              render-as="svg"
            />
          </div>
        </div>

        <div class="qr-card__user">
          <img v-if="profile.profile_picture" :src="profile.profile_picture" alt="" />
          <h3>{{ profile.name }}</h3>
          <p>@{{ profile.username }}</p>
        </div>

        <div class="qr-card__footer">
          <span>myvibeapp.co</span>
        </div>
      </div>

      <!-- Action buttons (TIDAK ikut di-screenshot, di luar qr-card) -->
      <div class="qr-actions">
        <button class="qr-action qr-action--primary" @click="shareProfileNative">
          <fa :icon="['fas', 'share-nodes']" />
          <span>Share Profile</span>
        </button>
        <div class="qr-actions-row">
          <button class="qr-action" @click="copyProfileLink">
            <fa :icon="['fas', 'link']" />
            <span>Copy Link</span>
          </button>
          <button class="qr-action" @click="downloadQrImage" :disabled="qrDownloading">
            <fa :icon="qrDownloading ? 'spinner' : 'download'" :class="{ 'fa-spin': qrDownloading }" />
            <span>{{ qrDownloading ? 'Saving...' : 'Download QR' }}</span>
          </button>
        </div>
      </div>

      <button class="qr-close" @click="closeQrShare" aria-label="Close">
        <fa :icon="['fas', 'xmark']" />
      </button>
    </div>
  </div>
</Transition>
		<transition name="toast-pop"><div v-if="showToast" class="sv-toast">{{ toastMessage }}</div></transition>

		<!-- Modals -->
		<div class="modal pt-5 fade" id="chooseMethodPictureModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-body notification"><div class="title"><h1><span>Set Profile Picture</span></h1></div><ul class="btn-list"><li><button @click="chooseFromGallery"><span><fa icon="image" class="me-2" /> Choose From Gallery</span> <span class="icon"><fa icon="angle-right" /></span></button></li><li><button @click="takePhoto"><span><fa icon="camera-retro" class="me-2" /> Take a Photo</span> <span class="icon"><fa icon="angle-right" /></span></button></li></ul></div><div class="modal-button"><button type="button" class="btn-block" data-bs-dismiss="modal" ref="closeModalPicture">Close</button></div></div></div></div>
		<div class="modal pt-5 fade" id="logOutModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-body notification"><div class="title"><h1>Logout <span>Alert!</span></h1></div><p class="m-0 text-center">Are you sure to logout?</p></div><div class="modal-button"><RouterLink to="/landing" class="btn-block purple" @click="logoutLink()">Yes</RouterLink></div><div class="modal-button"><button type="button" class="btn-block" data-bs-dismiss="modal">Close</button></div></div></div></div>
		<span data-bs-toggle="modal" data-bs-target="#notifModal" ref="notifModalBtn"></span>
		<div class="modal fade" id="notifModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-body notification"><div class="title"><h1>{{ titleNotif }} <span :class="{ success: statusNotif === 'success' }">{{ titleNotifSecond }}</span></h1></div><p class="m-0 text-center">{{ message }}</p></div><div class="modal-button" v-if="showLink"><RouterLink :to="linkTo" class="btn-block purple" @click="clickLink">{{ linkText }}</RouterLink></div><div class="modal-button" v-if="showDismiss"><button type="button" class="btn-block" data-bs-dismiss="modal">Close</button></div></div></div></div>
		<span data-bs-toggle="modal" data-bs-target="#deleteAccount" ref="deleteAccountBtn"></span>
		<div class="modal fade" id="deleteAccount" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-body notification"><div class="title"><h1>Delete Account <span>Alert!</span></h1></div><p class="m-0 text-center">Wait! Are you sure you want to delete your account? All your data will be permanently erased, and this action can't be undone.</p></div><div class="modal-button"><div class="btn-block purple" :class="countdown > 0 ? 'd-block' : 'd-none'">{{ countdown > 0 ? `Wait (${countdown}s)` : '' }}</div><RouterLink to="/landing" class="btn-block purple" :class="countdown > 0 ? 'd-none' : 'd-block'" @click.prevent="handleDeleteAccount()">Yes, Delete My Account</RouterLink></div><div class="modal-button"><button type="button" class="btn-block" data-bs-dismiss="modal">Close</button></div></div></div></div>
	</section>
</template>

<script>
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { Clipboard } from '@capacitor/clipboard';
import dashboardService from '@/services/dashboardService';
import logo from '@/assets/avatar.png';
import authService from '@/services/authService';
import { Camera, CameraResultType, CameraSource } from '@capacitor/camera';
import { Share } from '@capacitor/share';
import { Filesystem, Directory } from '@capacitor/filesystem';
import QrcodeVue from 'qrcode.vue';
export default {
	name: 'SettingView',
	components: { QrcodeVue },
	data() {
		return {
			optionsMBTI: [], optionsZodiac: [], optionsRelationship: [],
			old_picture: '', ava_picture: '', profile_picture: '',
			username: '', name: '', mbti_id: '', zodiac_id: '', relationship_id: '', enthusiast: '',
			isLoading: false, isMain: true, isManageFollowers: false, isEditProfile: false, isSavedVibes: false,
			profile: [], listFollowers: [],
			statusNotif: 'failed', titleNotif: '', titleNotifSecond: '', message: '',
			showLink: false, linkTo: '/', linkText: 'Okay', showDismiss: false,
			showUsername: true, countdown: 10, timer: null,
			savedVibes: [], isLoadingSaved: false,
			showSavedPopup: false, selectedSavedVibe: null, savedLongPressTimer: null, savedPopupPosition: {}, isSavedLongPressing: false,
			showToast: false, toastMessage: '',
			showQrPopup: false, qrDownloading: false
		};
	},
	mounted() {
		// Run all API calls in parallel for faster loading
		Promise.all([
			this.getOptionsMBTI(),
			this.getOptionsZodiac(),
			this.getOptionsRelationship(),
			this.getUser()
		]);
	},
	methods: {
		async goSavedVibes() {
			this.isMain = false; this.isSavedVibes = true; this.isLoadingSaved = true;
			try {
				const response = await dashboardService.getSavedVibes();
				this.savedVibes = response.data.map(v => {
					if (v.image_url && v.image_url.startsWith('http://')) v.image_url = v.image_url.replace('http://', 'https://');
					if (v.user && v.user.profile_picture) v.user.profile_picture = process.env.VUE_APP_API_URL + '/' + v.user.profile_picture;
					return v;
				});
			} catch (e) { console.error('Error fetching saved vibes:', e); }
			finally { this.isLoadingSaved = false; }
		},
		getProfilePic(pic) { if (!pic) return logo; if (pic.startsWith('http')) return pic; return process.env.VUE_APP_API_URL + '/' + pic; },
		goVibeDetail(vibe) { if (this.isSavedLongPressing) return; if (vibe && vibe.user) this.$router.push('/' + vibe.user.username + '/' + vibe.id); },
		startSavedLongPress(event, vibe) { this.isSavedLongPressing = false; this.savedLongPressTimer = setTimeout(() => { this.isSavedLongPressing = true; this.openSavedPopup(event, vibe); }, 500); },
		endSavedLongPress() { if (this.savedLongPressTimer) { clearTimeout(this.savedLongPressTimer); this.savedLongPressTimer = null; } if (this.isSavedLongPressing) setTimeout(() => { this.isSavedLongPressing = false; }, 100); },
		cancelSavedLongPress() { if (this.savedLongPressTimer) { clearTimeout(this.savedLongPressTimer); this.savedLongPressTimer = null; } },
		openSavedPopup(event, vibe) {
			this.selectedSavedVibe = vibe; if (navigator.vibrate) navigator.vibrate(10);
			const vh = window.innerHeight, vw = window.innerWidth;
			let clientY = event.touches ? event.touches[0].clientY : event.clientY;
			const pw = Math.min(vw - 40, 300); let left = (vw - pw) / 2; let top = clientY - 180;
			if (top < 20) top = 20; if (top + 360 > vh) top = vh - 380;
			this.savedPopupPosition = { top: `${top}px`, left: `${left}px`, width: `${pw}px` }; this.showSavedPopup = true;
		},
		closeSavedPopup() { this.showSavedPopup = false; setTimeout(() => { this.selectedSavedVibe = null; }, 300); },
		async handleSavedAction(action) {
			const vibe = this.selectedSavedVibe; this.closeSavedPopup(); if (!vibe) return;
			switch (action) {
				case 'detail': if (vibe.user) this.$router.push('/' + vibe.user.username + '/' + vibe.id); break;
				case 'share': if (vibe.user) this.$router.push({ path: '/message/' + vibe.user.username, query: { vibeId: vibe.id, vibeTitle: vibe.title, vibeImage: vibe.image_url, vibeUser: vibe.user.name, vibeUsername: vibe.user.username, vibeComment: vibe.comment || '', vibeRating: vibe.rating || '' } }); break;
				case 'unsave': try { await dashboardService.toggleSaveVibe({ vibe_id: vibe.id }); this.savedVibes = this.savedVibes.filter(v => v.id !== vibe.id); this.showSuccessToast('Vibe unsaved'); } catch (e) { this.showSuccessToast('Something went wrong'); } break;
			}
		},
		showSuccessToast(msg) { this.toastMessage = msg; this.showToast = true; setTimeout(() => { this.showToast = false; }, 2000); },
		startCountdown() { this.countdown = 10; this.timer = setInterval(() => { if (this.countdown > 0) this.countdown -= 1; else clearInterval(this.timer); }, 1000); },
		async getUser() {
			this.isLoading = true;
			try {
				const response = await dashboardService.getFriends(this.$route.params.username);
				if (response.data.profile.profile_picture) response.data.profile.profile_picture = process.env.VUE_APP_API_URL + '/' + response.data.profile.profile_picture;
				else response.data.profile.profile_picture = logo;
				this.profile = response.data.profile;
				this.profile.mbti = this.profile.mbti?.mbti_name ?? ''; this.profile.zodiac = this.profile.zodiac?.zodiac_name ?? ''; this.profile.relationship = this.profile.relationship?.relationship_name ?? '';
				this.ava_picture = this.profile.profile_picture; this.profile_picture = this.profile.profile_picture;
				this.username = this.profile.username; this.name = this.profile.name;
				this.mbti_id = this.profile.mbti_id ?? ''; this.zodiac_id = this.profile.zodiac_id ?? ''; this.relationship_id = this.profile.relationship_id ?? '';
				this.enthusiast = this.profile.enthusiast; this.old_picture = this.profile.profile_picture; this.isLoading = false;
			} catch (e) { console.error('Error fetching home:', e); }
		},
		goDetail(path) { this.$router.push('/' + path); },
// NEW: Buka QR Popup (replace fungsi copy lama)
openQrShare() {
  this.showQrPopup = true;
  if (navigator.vibrate) navigator.vibrate(8);
},
closeQrShare() { this.showQrPopup = false; },

get profileUrl() {
  return 'https://myvibeapp.co/' + this.$route.params.username;
},

async copyProfileLink() {
  try {
    await Clipboard.write({ string: this.profileUrl });
    this.showSuccessToast('Link copied to clipboard');
  } catch (e) {
    this.showSuccessToast('Failed to copy link');
  }
},

async shareProfileNative() {
  try {
    await Share.share({
      title: 'Check out my MyVibe profile',
      text: `Follow @${this.profile.username} on MyVibe — Rate Your Favorites`,
      url: this.profileUrl,
      dialogTitle: 'Share your vibe'
    });
  } catch (e) {
    // user cancel = OK, jangan alert
    if (e?.message && !/cancel/i.test(e.message)) {
      this.showSuccessToast('Failed to share');
    }
  }
},

async downloadQrImage() {
  if (this.qrDownloading) return;
  this.qrDownloading = true;
  try {
    // 1. Render canvas dari elemen .qr-card
    const html2canvas = (await import('html2canvas')).default;
    const target = this.$refs.qrCard;
    if (!target) throw new Error('QR card not found');

    const canvas = await html2canvas(target, {
      backgroundColor: null,
      scale: 3,
      useCORS: true,
      logging: false
    });
    const dataUrl = canvas.toDataURL('image/png');
    const base64 = dataUrl.split(',')[1];
    const fileName = `myvibe-${this.profile.username}-${Date.now()}.png`;

    // 2. Detect platform: native (Capacitor) vs web
    const isNative = window.Capacitor?.isNativePlatform?.();

    if (isNative) {
      // Save ke Documents folder
      const saved = await Filesystem.writeFile({
        path: fileName,
        data: base64,
        directory: Directory.Cache
      });
      // Native share dengan file URI
      await Share.share({
        title: 'My MyVibe QR',
        text: `Scan to follow @${this.profile.username}`,
        url: saved.uri,
        dialogTitle: 'Save or share QR'
      });
      this.showSuccessToast('QR ready to share');
    } else {
      // Web: trigger download langsung
      const a = document.createElement('a');
      a.href = dataUrl;
      a.download = fileName;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      this.showSuccessToast('QR downloaded');
    }
  } catch (e) {
    console.error('QR download error:', e);
    this.showSuccessToast('Failed to download QR');
  } finally {
    this.qrDownloading = false;
  }
},		async logoutLink() { try { await dashboardService.postLogout(); } catch (e) {} finally { var b = document.querySelector('.modal-backdrop'); if (b) b.remove(); } },
		deleteAccount() { this.$refs.deleteAccountBtn.click(); this.startCountdown(); },
		async handleDeleteAccount() { try { await dashboardService.postDeleteAccount(); } catch (e) {} finally { var b = document.querySelector('.modal-backdrop'); if (b) b.remove(); } },
		showNotifModal() { this.$refs.notifModalBtn.click(); },
		async manageFollowers() { this.isLoading = true; try { const response = await dashboardService.getFollowers(this.profile.id); for (let i = 0; i < response.data.followers.length; i++) { if (!response.data.followers[i].profile_picture) response.data.followers[i].profile_picture = logo; else response.data.followers[i].profile_picture = process.env.VUE_APP_API_URL + '/' + response.data.followers[i].profile_picture; } this.listFollowers = response.data.followers; this.isLoading = false; } catch (e) { console.error('Error:', e); } finally { this.isMain = false; this.isManageFollowers = true; } },
		async follow(index, id) { try { await dashboardService.followFriends(id); this.listFollowers[index].is_following = true; } catch (e) {} },
		async unfollow(index, id) { try { await dashboardService.followFriends(id); this.listFollowers[index].is_following = false; } catch (e) {} },
		async getOptionsMBTI() { try { const r = await authService.getMBTI(); this.optionsMBTI = r.data.data.map(i => ({ label: i.mbti_name + ' - ' + i.mbti_desc, value: i.id })); } catch (e) {} },
		async getOptionsZodiac() { try { const r = await authService.getZodiac(); this.optionsZodiac = r.data.data.map(i => ({ label: i.zodiac_name, value: i.id, description: i.zodiac_desc })); } catch (e) {} },
		async getOptionsRelationship() { try { const r = await authService.getRelationship(); this.optionsRelationship = r.data.data.map(i => ({ label: i.relationship_name, value: i.id, description: i.relationship_desc })); } catch (e) {} },
		async fillProfile() {
			if (!this.profile_picture || !this.name || !this.enthusiast || !this.username) { this.statusNotif = 'failed'; this.titleNotif = 'Validation'; this.titleNotifSecond = 'Error!'; this.message = 'Please fill all form & picture'; this.showLink = false; this.showDismiss = true; this.showNotifModal(); }
			else { this.loading = true; try { const fd = new FormData(); if (this.old_picture != this.profile_picture) fd.append('profile_picture', this.profile_picture); if (!this.mbti_id) this.mbti_id = ''; if (!this.zodiac_id) this.zodiac_id = ''; if (!this.relationship_id) this.relationship_id = ''; fd.append('_method', 'PUT'); fd.append('username', this.username); fd.append('name', this.name); fd.append('mbti_id', this.mbti_id); fd.append('zodiac_id', this.zodiac_id); fd.append('relationship_id', this.relationship_id); fd.append('enthusiast', this.enthusiast); await authService.profile_put(fd); this.loading = false; this.$router.push('/dashboard'); } catch (e) { this.statusNotif = 'failed'; this.titleNotif = 'Validation'; this.titleNotifSecond = 'Error!'; if (e.response?.data?.errors) { let m = []; for (const k in e.response.data.errors) { if (Array.isArray(e.response.data.errors[k])) m = m.concat(e.response.data.errors[k]); } this.message = m.join(', '); } else this.message = e.response?.data?.message || 'Error'; this.showLink = false; this.showDismiss = true; this.showNotifModal(); this.loading = false; } finally { this.loading = false; } }
		},
		async takePhoto() { try { const p = await Camera.getPhoto({ resultType: CameraResultType.Uri, source: CameraSource.Camera, quality: 90 }); this.ava_picture = p.webPath; this.profile_picture = await this.convertUriToFile(p.webPath, 'avatar.jpg'); } catch (e) {} finally { this.$refs.closeModalPicture.click(); } },
		async chooseFromGallery() { try { const p = await Camera.getPhoto({ resultType: CameraResultType.Uri, source: CameraSource.Photos, quality: 90 }); this.ava_picture = p.webPath; this.profile_picture = await this.convertUriToFile(p.webPath, 'avatar.jpg'); } catch (e) {} finally { this.$refs.closeModalPicture.click(); } },
		async convertUriToFile(uri, fileName) { const r = await fetch(uri); const b = await r.blob(); return new File([b], fileName, { type: b.type }); },
		clickLink() { var b = document.querySelector('.modal-backdrop'); if (b) b.remove(); },
		goEdit() { this.isMain = false; this.isManageFollowers = false; this.isEditProfile = true; this.isSavedVibes = false; },
		goBack() { this.isMain = true; this.isManageFollowers = false; this.isEditProfile = false; this.isSavedVibes = false; }
	}
};
</script>

<style lang="scss" scoped>
@import '@/assets/scss/color.scss';

// === BLOBS ===
.set-blob { position: fixed; filter: blur(55px); pointer-events: none; z-index: 0; }
.set-blob--1 { top: -8%; right: -14%; width: min(260px, 60vw); height: min(260px, 60vw); background: radial-gradient(circle, rgba($purple, 0.2) 0%, rgba(#6c5ce7, 0.06) 40%, transparent 70%); border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; animation: sBm1 14s ease-in-out infinite; }
.set-blob--2 { bottom: 8%; left: -12%; width: min(220px, 50vw); height: min(220px, 50vw); background: radial-gradient(circle, rgba(#4a3adf, 0.16) 0%, rgba($purple, 0.04) 40%, transparent 70%); border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; animation: sBm2 17s ease-in-out infinite; }
.set-blob--3 { top: 35%; left: 50%; transform: translateX(-50%); width: min(280px, 64vw); height: min(130px, 30vw); background: radial-gradient(ellipse, rgba($purple, 0.07) 0%, transparent 70%); border-radius: 50%; animation: sBf 11s ease-in-out infinite; }
@keyframes sBm1 { 0%, 100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(0,0) rotate(0deg); } 50% { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; transform: translate(-10px,14px) rotate(28deg); } }
@keyframes sBm2 { 0%, 100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; transform: translate(0,0) rotate(0deg); } 50% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(12px,-10px) rotate(-28deg); } }
@keyframes sBf { 0%, 100% { transform: translateX(-50%) translateY(0) scale(1); opacity: 0.07; } 50% { transform: translateX(-50%) translateY(-14px) scale(1.04); opacity: 0.12; } }

.set-slide-enter-active, .set-slide-leave-active { transition: all 0.4s cubic-bezier(0.23,1,0.32,1); }
.set-slide-enter-from { opacity: 0; transform: translateX(30px); } .set-slide-leave-to { opacity: 0; transform: translateX(-20px); }

.anim-back { animation: sDown 0.4s cubic-bezier(0.23,1,0.32,1) both; }
.anim-profile-hero { animation: sUp 0.6s cubic-bezier(0.23,1,0.32,1) 0.1s both; }
.anim-menu { animation: sUp 0.5s cubic-bezier(0.23,1,0.32,1) 0.25s both; }
.anim-title { animation: sDown 0.5s cubic-bezier(0.23,1,0.32,1) both; }
.anim-form { animation: sUp 0.5s cubic-bezier(0.23,1,0.32,1) 0.15s both; }
.anim-list-item { animation: sListIn 0.4s ease-out both; }
@keyframes sDown { from { opacity:0; transform:translateY(-18px); } to { opacity:1; transform:translateY(0); } }
@keyframes sUp { from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:translateY(0); } }
@keyframes sListIn { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }

.back-floating { position: fixed; top: calc(env(safe-area-inset-top) + 16px); left: 16px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: $white; text-decoration: none; z-index: 100; font-size: 15px; cursor: pointer; background: rgba($white, 0.04); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba($white, 0.08); box-shadow: 0 4px 16px rgba(0,0,0,0.2), inset 0 1px 0 rgba($white,0.08); transition: all 0.4s cubic-bezier(0.23,1,0.32,1);
	&::before { content: ''; position: absolute; top: 2px; left: 15%; right: 15%; height: 40%; background: linear-gradient(180deg, rgba($white,0.1) 0%, transparent 100%); border-radius: 50%; pointer-events: none; }
	&:hover { transform: translateY(-2px) scale(1.08); background: rgba($purple, 0.15); border-color: rgba($purple, 0.25); color: $white; }
	&:active { transform: scale(0.92); }
}

.profile-hero { text-align: center; padding: 56px 20px 28px; position: relative; z-index: 2;
	&__avatar { position: relative; display: inline-block; margin-bottom: 14px;
		img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid rgba($purple, 0.3); box-shadow: 0 8px 28px rgba(0,0,0,0.25); }
	}
	&__edit-badge { position: absolute; bottom: 2px; right: 2px; width: 32px; height: 32px; border-radius: 50%; background: rgba($purple, 0.7); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba($white, 0.15); display: flex; align-items: center; justify-content: center; color: $white; font-size: 12px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba($purple, 0.3); &:hover { transform: scale(1.1); } }
	&__name { color: $white; font-size: 22px; font-weight: 700; margin: 0 0 4px; }
	&__email { color: rgba($white, 0.45); font-size: 13px; margin: 0 0 12px; }
	&__tags { list-style: none; display: flex; justify-content: center; flex-wrap: wrap; gap: 6px; padding: 0; margin: 0;
		li span { background: rgba($purple, 0.6); padding: 3px 10px; border-radius: 8px; font-size: 11px; color: $white; border: 1px solid rgba($white, 0.1); }
	}
}

.menu-glass { position: relative; z-index: 2; padding: 0 4px;
	&__title { color: $white; font-size: 16px; font-weight: 700; margin-bottom: 10px; padding-left: 4px; }
	&__list { list-style: none; padding: 0; margin: 0; background: rgba($white, 0.025); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba($white, 0.06); border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1), inset 0 1px 0 rgba($white,0.04); }
	&__item { display: flex; align-items: center; padding: 16px 18px; color: $white; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid rgba($white, 0.04); &:last-child { border-bottom: none; } &:hover { background: rgba($white, 0.04); }
		&--danger { span { color: rgba(#E84C4C, 0.85); } .menu-glass__icon { color: rgba(#E84C4C, 0.85); } }
		span { font-size: 14px; font-weight: 600; flex: 1; }
	}
	&__icon { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 10px; background: rgba($white, 0.04); border: 1px solid rgba($white, 0.06); margin-right: 14px; color: $white; font-size: 16px; }
	&__arrow { color: rgba($white, 0.3); font-size: 16px; }
}

// ========================================
// SAVED VIBES — SELF-CONTAINED CARD STYLES
// (exact same as FriendsView Spotlight)
// ========================================
.sv-section { padding: 0 16px 80px; position: relative; z-index: 2; }

.saved-empty { text-align: center; padding: 60px 20px; color: rgba($white, 0.3);
	svg { font-size: 48px; margin-bottom: 16px; color: rgba($purple, 0.3); }
	p { font-size: 16px; font-weight: 600; margin: 0 0 6px; color: rgba($white, 0.5); }
	small { font-size: 12px; }
}

.sv-card-list { list-style: none; padding: 0; margin: 0; }

.sv-card {
	position: relative;
	background-color: $background;
	aspect-ratio: 1/1;
	width: 100%;
	background-position: center center;
	background-size: cover;
	background-repeat: no-repeat;
	overflow: hidden;
	border-radius: 35px;
	border: 1px solid rgba($white, 0.08);
	box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25), inset 0 1px 0 rgba($white, 0.06);
	transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
	cursor: pointer;

	&--tall { aspect-ratio: 2/3; }

	// Glass reflection top
	&::after {
		content: '';
		position: absolute;
		top: 0; left: 0; right: 0;
		height: 40%;
		background: linear-gradient(180deg, rgba($white, 0.06) 0%, transparent 100%);
		border-radius: 35px 35px 0 0;
		pointer-events: none;
		z-index: 2;
	}

	&:active { transform: translateY(-1px) scale(0.99); }

	// Badge (top-left user pill)
	&__badge {
		position: absolute;
		top: 20px;
		left: 20px;
		display: flex;
		border-radius: 15px;
		align-items: center;
		background: rgba($purple, 0.55);
		backdrop-filter: blur(14px);
		-webkit-backdrop-filter: blur(14px);
		padding: 4px 10px 4px 5px;
		border: 1px solid rgba($white, 0.12);
		box-shadow: 0 4px 12px rgba(0,0,0,0.2);
		z-index: 3;
		transition: all 0.3s ease;

		img { width: 20px; height: 20px; border-radius: 50%; margin-right: 5px; }
		h3 { font-weight: 600; font-size: 12px; color: $white; margin: 0; }
	}

	// Gradient overlay
	&__gradient {
		position: absolute;
		bottom: 0;
		left: 0;
		width: 100%;
		height: 55%;
		background: linear-gradient(to top, rgba($background, 1) 0%, rgba($background, 0.7) 40%, transparent 100%);
		z-index: 1;
	}

	// Bottom content
	&__bottom {
		position: absolute;
		bottom: 0;
		left: 0;
		width: 100%;
		height: calc(100% - 60px);
		display: flex;
		align-items: flex-end;
		padding-bottom: 20px;
		z-index: 2;
	}

	&__text {
		color: $white;
		padding: 5px 20px;
		width: calc(100% - 68px);
		h1 { font-weight: 700; font-size: 20px; margin-bottom: 3px; }
	}

	&__star {
		position: relative;
		width: 68px;
		padding-bottom: 15px;

		svg {
			font-size: 46px;
			color: $purple;
			filter: drop-shadow(0 0 8px rgba($purple, 0.4));
		}
		span {
			color: $white;
			position: absolute;
			left: 14px;
			top: 14px;
			font-weight: 700;
			text-shadow: 0 2px 6px rgba(0,0,0,0.4);
		}
	}
}

// === POPUP ===
.sv-popup-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); z-index: 200; }
.sv-popup-overlay-enter-active { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
.sv-popup-overlay-leave-active { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.sv-popup-overlay-enter-from, .sv-popup-overlay-leave-to { opacity: 0; }
.sv-popup-menu-enter-active { transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1); }
.sv-popup-menu-leave-active { transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
.sv-popup-menu-enter-from { opacity: 0; transform: scale(0.8); filter: blur(8px); }
.sv-popup-menu-leave-to { opacity: 0; transform: scale(0.9); filter: blur(4px); }

.sv-popup-container { position: fixed; z-index: 201; display: flex; flex-direction: column; gap: 10px; }
.sv-popup-preview { border-radius: 20px; overflow: hidden; background: rgba(20, 20, 40, 0.6); backdrop-filter: blur(40px) saturate(1.5); -webkit-backdrop-filter: blur(40px) saturate(1.5); border: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 24px 80px rgba(0, 0, 0, 0.5), inset 0 2px 0 rgba(255, 255, 255, 0.08);
	&__image { width: 100%; height: 140px; background-size: cover; background-position: center; }
	&__info { padding: 12px 16px;
		h4 { font-size: 15px; font-weight: 700; color: #fff; margin: 0; line-height: 1.3; }
		p { font-size: 12px; color: rgba(255, 255, 255, 0.4); margin: 4px 0 0; line-height: 1.4; }
	}
}
.sv-popup-actions { display: flex; flex-direction: column; border-radius: 16px; overflow: hidden; background: rgba(20, 20, 40, 0.6); backdrop-filter: blur(40px) saturate(1.5); -webkit-backdrop-filter: blur(40px) saturate(1.5); border: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 16px 48px rgba(0, 0, 0, 0.4), inset 0 2px 0 rgba(255, 255, 255, 0.08); }
.sv-popup-action { display: flex; align-items: center; gap: 12px; padding: 14px 16px; background: none; border: none; color: #fff; font-size: 15px; font-weight: 500; cursor: pointer; transition: background 0.15s ease; text-align: left; width: 100%;
	svg { width: 18px; color: rgba(255, 255, 255, 0.5); }
	&:active { background: rgba(255, 255, 255, 0.1); }
	&--danger { color: #f87171; svg { color: #f87171; } }
}
.sv-popup-divider { height: 0.5px; background: rgba(255, 255, 255, 0.1); margin: 0 14px; }

.sv-toast { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999; background: rgba(20, 20, 40, 0.9); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 14px; padding: 12px 28px; color: #fff; font-size: 14px; font-weight: 600; box-shadow: 0 8px 28px rgba(0,0,0,0.3); }
.toast-pop-enter-active { transition: all 0.3s cubic-bezier(0.23,1,0.32,1); }
.toast-pop-leave-active { transition: all 0.2s ease-in; }
.toast-pop-enter-from { opacity: 0; transform: translate(-50%, -50%) scale(0.85); }
.toast-pop-leave-to { opacity: 0; transform: translate(-50%, -50%) scale(0.9); }

.glass-submit-btn { background: linear-gradient(135deg, rgba($purple, 0.7), rgba($purple, 0.9)) !important; border: 1px solid rgba($white, 0.15) !important; box-shadow: 0 6px 24px rgba($purple, 0.3), inset 0 1px 0 rgba($white, 0.15) !important; position: relative; overflow: hidden; transition: all 0.4s cubic-bezier(0.23,1,0.32,1) !important;
	&::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 50%; background: linear-gradient(180deg, rgba($white,0.12) 0%, transparent 100%); border-radius: 30px 30px 0 0; pointer-events: none; }
	&:hover { transform: translateY(-3px); } &:active { transform: translateY(-1px) scale(0.97); }
}

ul.followers_list > li { background: rgba($white, 0.035) !important; backdrop-filter: blur(16px) !important; -webkit-backdrop-filter: blur(16px) !important; border: 1px solid rgba($white, 0.07) !important; box-shadow: 0 4px 16px rgba(0,0,0,0.1), inset 0 1px 0 rgba($white,0.05) !important; transition: all 0.4s cubic-bezier(0.23,1,0.32,1) !important;
	&:hover { border-color: rgba($purple, 0.2) !important; background: rgba($white, 0.06) !important; transform: translateY(-2px); }
	img { border: 1px solid rgba($white, 0.1) !important; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
}

.loading { display: flex; justify-content: center; align-items: center; height: 100dvh; color: $purple; font-size: 28px; filter: drop-shadow(0 0 12px rgba($purple, 0.4)); }
.loading-small { display: flex; justify-content: center; align-items: center; height: 30vh; color: $purple; font-size: 28px; filter: drop-shadow(0 0 12px rgba($purple, 0.4)); }
// =========================
// QR SHARE POPUP — Liquid Glass
// =========================
.qr-overlay {
  position: fixed; inset: 0;
  background: rgba(0, 0, 0, 0.55);
  backdrop-filter: blur(24px) saturate(1.4);
  -webkit-backdrop-filter: blur(24px) saturate(1.4);
  z-index: 300;
}
.qr-overlay-enter-active { transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1); }
.qr-overlay-leave-active { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.qr-overlay-enter-from, .qr-overlay-leave-to { opacity: 0; backdrop-filter: blur(0); -webkit-backdrop-filter: blur(0); }

.qr-sheet {
  position: fixed;
  bottom: 0; left: 0; right: 0;
  z-index: 301;
  padding: 0 14px calc(env(safe-area-inset-bottom) + 20px);
  pointer-events: none;
  display: flex;
  justify-content: center;

  &__inner {
    pointer-events: auto;
    width: 100%;
    max-width: 420px;
    position: relative;
    background: rgba(20, 20, 40, 0.62);
    backdrop-filter: blur(40px) saturate(1.6);
    -webkit-backdrop-filter: blur(40px) saturate(1.6);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 32px;
    box-shadow:
      0 -16px 60px rgba(0, 0, 0, 0.55),
      0 0 0 0.5px rgba(255, 255, 255, 0.06),
      inset 0 1.5px 0 rgba(255, 255, 255, 0.1);
    padding: 14px 18px 22px;
    overflow: hidden;

    // Glass reflection top
    &::before {
      content: '';
      position: absolute;
      top: 0; left: 5%; right: 5%;
      height: 30%;
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.08) 0%, transparent 100%);
      border-radius: 32px 32px 0 0;
      pointer-events: none;
    }
  }

  &__handle {
    width: 38px; height: 4px;
    border-radius: 2px;
    background: rgba(255, 255, 255, 0.22);
    margin: 4px auto 14px;
  }
}
.qr-sheet-enter-active { transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
.qr-sheet-leave-active { transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1); }
.qr-sheet-enter-from { opacity: 0; transform: translateY(60px) scale(0.96); }
.qr-sheet-leave-to { opacity: 0; transform: translateY(40px) scale(0.97); }

.qr-close {
  position: absolute;
  top: 14px; right: 14px;
  width: 32px; height: 32px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #fff;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  font-size: 13px;
  transition: all 0.25s ease;
  z-index: 5;

  &:active { transform: scale(0.9); background: rgba(255, 255, 255, 0.14); }
}

// === QR Card (yang di-screenshot) ===
.qr-card {
  position: relative;
  background: linear-gradient(155deg, #2d1b69 0%, #1a0d3d 60%, #0d0626 100%);
  border-radius: 26px;
  padding: 22px 20px 18px;
  margin: 0 auto 16px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  overflow: hidden;
  animation: qrCardIn 0.6s cubic-bezier(0.23, 1, 0.32, 1) 0.1s both;

  // Animated glow
  &::after {
    content: '';
    position: absolute;
    inset: -50%;
    background: radial-gradient(circle, rgba(108, 92, 231, 0.18) 0%, transparent 60%);
    animation: qrGlow 6s ease-in-out infinite;
    pointer-events: none;
  }

  &__brand {
    text-align: center;
    margin-bottom: 14px;
    position: relative;
    z-index: 2;
  }
  &__logo {
    display: block;
    color: #fff;
    font-weight: 800;
    font-size: 20px;
    letter-spacing: -0.5px;
  }
  &__brand small {
    color: rgba(255, 255, 255, 0.5);
    font-size: 10px;
    letter-spacing: 2px;
    text-transform: uppercase;
  }

  &__qr-wrap {
    display: flex;
    justify-content: center;
    margin-bottom: 16px;
    position: relative;
    z-index: 2;
  }
  &__qr {
    background: #fff;
    padding: 14px;
    border-radius: 18px;
    box-shadow:
      0 12px 32px rgba(108, 92, 231, 0.4),
      inset 0 0 0 1px rgba(255, 255, 255, 0.6);
    animation: qrPulse 3s ease-in-out infinite;

    svg, canvas { display: block; border-radius: 6px; }
  }

  &__user {
    text-align: center;
    position: relative;
    z-index: 2;

    img {
      width: 44px; height: 44px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid rgba(255, 255, 255, 0.2);
      margin-bottom: 6px;
    }
    h3 {
      color: #fff;
      font-size: 16px;
      font-weight: 700;
      margin: 0;
    }
    p {
      color: rgba(255, 255, 255, 0.55);
      font-size: 12px;
      margin: 2px 0 0;
    }
  }

  &__footer {
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px dashed rgba(255, 255, 255, 0.12);
    text-align: center;
    position: relative;
    z-index: 2;

    span {
      color: rgba(255, 255, 255, 0.4);
      font-size: 11px;
      letter-spacing: 1px;
    }
  }
}

@keyframes qrCardIn {
  from { opacity: 0; transform: translateY(20px) scale(0.95); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes qrGlow {
  0%, 100% { transform: rotate(0deg) scale(1); opacity: 0.6; }
  50% { transform: rotate(180deg) scale(1.1); opacity: 1; }
}
@keyframes qrPulse {
  0%, 100% { box-shadow: 0 12px 32px rgba(108, 92, 231, 0.4), inset 0 0 0 1px rgba(255, 255, 255, 0.6); }
  50% { box-shadow: 0 16px 44px rgba(108, 92, 231, 0.65), inset 0 0 0 1px rgba(255, 255, 255, 0.6); }
}

// === Action Buttons ===
.qr-actions {
  display: flex;
  flex-direction: column;
  gap: 10px;
  animation: qrActionsIn 0.5s cubic-bezier(0.23, 1, 0.32, 1) 0.25s both;
}
.qr-actions-row {
  display: flex;
  gap: 10px;

  .qr-action { flex: 1; }
}
.qr-action {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 14px 16px;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  color: #fff;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
  position: relative;
  overflow: hidden;

  // Glass reflection
  &::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 50%;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.08) 0%, transparent 100%);
    border-radius: 16px 16px 0 0;
    pointer-events: none;
  }

  svg { font-size: 14px; opacity: 0.85; }

  &:hover { background: rgba(255, 255, 255, 0.08); transform: translateY(-1px); }
  &:active { transform: scale(0.97); }
  &:disabled { opacity: 0.6; cursor: not-allowed; }

  &--primary {
    background: linear-gradient(135deg, rgba(108, 92, 231, 0.85), rgba(74, 58, 223, 0.95));
    border-color: rgba(255, 255, 255, 0.15);
    box-shadow: 0 6px 20px rgba(108, 92, 231, 0.3);

    &:hover { transform: translateY(-2px); box-shadow: 0 8px 26px rgba(108, 92, 231, 0.4); }
  }
}

@keyframes qrActionsIn {
  from { opacity: 0; transform: translateY(16px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>