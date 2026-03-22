<template>
	<div class="container searchFriend" ref="sectionList" @scroll="handleScroll" style="height: 100dvh; overflow-y: auto">
		<!-- Blobs -->
		<div class="friend-blob friend-blob--1"></div>
		<div class="friend-blob friend-blob--2"></div>
		<div class="friend-blob friend-blob--3"></div>

		<h1 class="title text-center mt-4 mb-5 anim-title">Find Your <span class="highlight">Friends</span></h1>
		<div class="search-box anim-search">
			<input type="text" v-model="searchQuery" placeholder="Search" @click="onFocus()" @keyup="debounceSearch()" />
			<div class="icon" @click="searchFriend()">
				<fa icon="search" />
			</div>
		</div>

		<!-- SPOTLIGHT -->
		<transition name="friends-fade" mode="out-in">
			<div v-if="isExplore" key="explore" class="explore">
				<div class="section-title anim-section-title">
					<h3>Spotlight</h3>
				</div>
				<ul v-if="!isLoading" class="explore_list">
					<li
						v-for="(section, index) in exploreResult"
						:key="section.id"
						class="mb-4"
						:style="{ animationDelay: (index * 0.08) + 's' }"
						@touchstart.passive="startLongPress($event, section)"
						@touchend="endLongPress"
						@touchmove="cancelLongPress"
					>
						<div class="content glass-spotlight-card" :style="{ backgroundImage: `url(${section.image_url})` }" :class="[{ twothree: ['Film', 'Top', 'Reading'].some((word) => section.category.title.includes(word)) }]">
							<div class="badge" @click="goProfileUsername(section.user.username)">
								<img :src="section.user.profile_picture" alt="" />
								<h3>{{ section.user.name }}</h3>
							</div>
							<div class="report" @click.stop="openPopup($event, section)"><fa icon="ellipsis" class="text-white" /></div>
							<div class="bottom" @click="goDetail(section.user.username + '/' + section.id)">
								<div class="text">
									<h1>{{ section.title }}</h1>
									<h3>{{ section.comment }}</h3>
								</div>
								<div class="star">
									<fa :icon="['fas', 'star']" />
									<span>{{ section.rating }}</span>
								</div>
							</div>
						</div>
					</li>
				</ul>
				<div v-else>
					<div class="loading">
						<fa icon="spinner" class="fa-spin-pulse" />
					</div>
				</div>
			</div>
		</transition>

		<!-- FRIEND SEARCH RESULTS -->
		<transition name="friends-slide" mode="out-in">
			<div v-if="isFriendList" key="friendlist" class="explore">
				<ul v-if="!isLoading" class="friend_list" style="padding-top: 4px">
					<li v-for="(section, index) in searchResult" :key="section.id" class="mb-4" @click="goProfile(section)" :style="{ animationDelay: (index * 0.06) + 's' }">
						<img :src="section.profile_picture" alt="" />
						<div class="text">
							<h2>{{ section.name }}</h2>
							<p>@{{ section.username }}</p>
						</div>
					</li>
				</ul>
				<div v-else>
					<div class="loading">
						<fa icon="spinner" class="fa-spin-pulse" />
					</div>
				</div>
			</div>
		</transition>

		<!-- RECENTS -->
		<transition name="friends-slide" mode="out-in">
			<div v-if="isRecentFriendList" key="recents" class="explore">
				<div class="section-title anim-section-title">
					<h3>Recents</h3>
					<small @click="clearRecent()">Clear History</small>
				</div>
				<ul v-if="!isLoading" class="friend_list">
					<li v-for="(item, index) in recentProfiles" :key="index" class="mb-4" @click="goProfile(item)" :style="{ animationDelay: (index * 0.06) + 's' }">
						<img :src="item.profile_picture" alt="" />
						<div class="text">
							<h2>{{ item.name }}</h2>
							<p>@{{ item.username }}</p>
						</div>
					</li>
				</ul>
				<div v-else>
					<div class="loading">
						<fa icon="spinner" class="fa-spin-pulse" />
					</div>
				</div>
			</div>
		</transition>

		<!-- Long Press Popup Overlay -->
		<Transition name="popup-overlay">
			<div v-if="showPopup" class="popup-overlay" @click="closePopup" @touchstart.prevent="closePopup"></div>
		</Transition>

		<!-- Long Press Popup Menu -->
		<Transition name="popup-menu">
			<div v-if="showPopup" class="popup-container" :style="popupPosition">
				<!-- Preview Card -->
				<div class="popup-preview">
					<div class="popup-preview-image" :style="{ backgroundImage: `url(${selectedVibe?.image_url})` }"></div>
					<div class="popup-preview-info">
						<div class="popup-preview-user">
							<img :src="selectedVibe?.user?.profile_picture" alt="" />
							<span>{{ selectedVibe?.user?.name }}</span>
						</div>
						<h4>{{ selectedVibe?.title }}</h4>
						<p>{{ selectedVibe?.comment && selectedVibe.comment.length > 60 ? selectedVibe.comment.slice(0, 60) + '...' : selectedVibe?.comment }}</p>
					</div>
				</div>
				<!-- Action Menu -->
				<div class="popup-actions">
					<button class="popup-action" @click.stop="handleAction('detail')">
						<fa :icon="['fas', 'eye']" />
						<span>View Detail</span>
					</button>
					<div class="popup-divider"></div>
					<button class="popup-action" @click.stop="handleAction('profile')">
						<fa :icon="['fas', 'user']" />
						<span>Visit Profile</span>
					</button>
					<div class="popup-divider"></div>
					<button class="popup-action" @click.stop="handleAction('sendVibe')">
						<fa :icon="['fas', 'paper-plane']" />
						<span>Share This Vibe</span>
					</button>
					<div class="popup-divider"></div>
					<button class="popup-action popup-action-danger" @click.stop="handleAction('report')">
						<fa :icon="['fas', 'flag']" />
						<span>Report</span>
					</button>
				</div>
			</div>
		</Transition>

		<!-- Report Confirmation Popup -->
		<Transition name="popup-overlay">
			<div v-if="showReportPopup" class="popup-overlay" @click="closeReportPopup" @touchstart.prevent="closeReportPopup"></div>
		</Transition>
		<Transition name="popup-menu">
			<div v-if="showReportPopup" class="popup-container popup-container--center">
				<div class="popup-report">
					<h3>Report Content</h3>
					<div class="popup-report-options">
						<label class="popup-radio" :class="{ active: selectedAction === 'report' }">
							<input type="radio" name="action" value="report" v-model="selectedAction" />
							<span>Report Content</span>
						</label>
						<label class="popup-radio" :class="{ active: selectedAction === 'block' }">
							<input type="radio" name="action" value="block" v-model="selectedAction" />
							<span>Block Account</span>
						</label>
					</div>
					<div v-if="selectedAction === 'report'" class="popup-report-textarea">
						<textarea v-model="commentReport" placeholder="Tell us why..." maxlength="250" rows="3"></textarea>
					</div>
					<p v-if="selectedAction === 'block'" class="popup-report-warning">This will prevent them from interacting with you.</p>
					<div class="popup-report-buttons">
						<button class="popup-btn popup-btn--danger" @click="selectedAction === 'report' ? reportContent() : blockAccount()">
							{{ selectedAction === 'report' ? 'Report' : 'Block' }}
						</button>
						<button class="popup-btn" @click="closeReportPopup">Cancel</button>
					</div>
				</div>
			</div>
		</Transition>

		<!-- Success Toast -->
		<transition name="toast-pop">
			<div v-if="showToast" class="success-toast">{{ toastMessage }}</div>
		</transition>
	</div>
</template>

<script>
import dashboardService from '@/services/dashboardService';
import avatar from '@/assets/avatar.png';

export default {
	name: 'FriendsView',
	data() {
		return {
			isLoading: false,
			isLoadingMore: false,
			hasCalledLoopList: false,
			scrollTimeout: null,
			selectedAction: 'report',
			selectedContent: null,
			commentReport: '',
			searchQuery: '',
			searchResult: [],
			recentProfiles: [],
			exploreResult: [],
			debounceTimeout: null,
			isExplore: false,
			isFriendList: false,
			isRecentFriendList: false,
			next: false,
			nextPage: '',
			// Popup
			showPopup: false,
			selectedVibe: null,
			longPressTimer: null,
			longPressDuration: 500,
			popupPosition: {},
			isLongPressing: false,
			// Report popup
			showReportPopup: false,
			// Toast
			showToast: false,
			toastMessage: ''
		};
	},
	mounted() {
		this.getSpotlight();
		this.loadRecentProfiles();
	},
	beforeUnmount() {
		this.cancelLongPress();
	},
	methods: {
		// === LONG PRESS HANDLERS ===
		startLongPress(event, section) {
			this.isLongPressing = false;
			this.longPressTimer = setTimeout(() => {
				this.isLongPressing = true;
				this.openPopupAt(event, section);
			}, this.longPressDuration);
		},
		endLongPress() {
			if (this.longPressTimer) {
				clearTimeout(this.longPressTimer);
				this.longPressTimer = null;
			}
			if (this.isLongPressing) {
				setTimeout(() => {
					this.isLongPressing = false;
				}, 100);
			}
		},
		cancelLongPress() {
			if (this.longPressTimer) {
				clearTimeout(this.longPressTimer);
				this.longPressTimer = null;
			}
		},
		openPopup(event, section) {
			this.openPopupAt(event, section);
		},
		openPopupAt(event, section) {
			this.selectedVibe = section;
			this.selectedContent = section;

			if (navigator.vibrate) {
				navigator.vibrate(10);
			}

			const viewportHeight = window.innerHeight;
			const viewportWidth = window.innerWidth;
			let clientY;

			if (event.touches) {
				clientY = event.touches[0].clientY;
			} else {
				clientY = event.clientY;
			}

			const popupWidth = Math.min(viewportWidth - 40, 300);
			let left = (viewportWidth - popupWidth) / 2;
			let top = clientY - 220;

			if (top < 20) top = 20;
			if (top + 420 > viewportHeight) top = viewportHeight - 440;

			this.popupPosition = {
				top: `${top}px`,
				left: `${left}px`,
				width: `${popupWidth}px`
			};

			this.showPopup = true;
		},
		closePopup() {
			this.showPopup = false;
			setTimeout(() => {
				if (!this.showReportPopup) {
					this.selectedVibe = null;
				}
			}, 300);
		},
		handleAction(action) {
			const vibe = this.selectedVibe;
			this.closePopup();

			switch (action) {
				case 'detail':
					if (vibe) {
						this.$router.push('/' + vibe.user.username + '/' + vibe.id);
					}
					break;
				case 'profile':
					if (vibe) {
						this.goProfileUsername(vibe.user.username);
					}
					break;
				case 'sendVibe':
					if (vibe) {
						this.$router.push({
							path: '/message/' + vibe.user.username,
							query: {
								vibeId: vibe.id,
								vibeTitle: vibe.title,
								vibeImage: vibe.image_url,
								vibeUser: vibe.user.name,
								vibeUsername: vibe.user.username,
								vibeComment: vibe.comment || "",
								vibeRating: vibe.rating || ""
							}
						});
					}
					break;
				case 'report':
					this.selectedAction = 'report';
					this.commentReport = '';
					setTimeout(() => {
						this.showReportPopup = true;
					}, 350);
					break;
			}
		},
		closeReportPopup() {
			this.showReportPopup = false;
			this.selectedVibe = null;
			this.selectedContent = null;
		},
		showSuccessToast(message) {
			this.toastMessage = message;
			this.showToast = true;
			setTimeout(() => {
				this.showToast = false;
			}, 2000);
		},

		// === EXISTING METHODS ===
		debounceSearch() {
			if (this.debounceTimeout) {
				clearTimeout(this.debounceTimeout);
			}
			this.debounceTimeout = setTimeout(() => {
				this.searchFriend();
			}, 1000);
		},
		async searchFriend() {
			if (!this.searchQuery.trim()) {
				this.searchResult = [];
				this.isRecentFriendList = true;
				this.isFriendList = false;
				this.isLoading = false;
			} else {
				this.isRecentFriendList = false;
				this.isLoading = true;
				try {
					const params = {
						pagination: true,
						search: this.searchQuery
					};
					const response = await dashboardService.searchFriends(params);
					for (let i = 0; i < response.data.data.length; i++) {
						if (!response.data.data[i].profile_picture) {
							response.data.data[i].profile_picture = avatar;
						} else {
							response.data.data[i].profile_picture = process.env.VUE_APP_API_URL + '/' + response.data.data[i].profile_picture;
						}
					}
					this.searchResult = response.data.data;
					this.isFriendList = true;
					this.isLoading = false;
				} catch (error) {
					console.error('Error fetching data:', error);
					this.isLoading = false;
				}
			}
		},
		async getSpotlight() {
			this.isLoading = true;
			try {
				const params = {
					pagination: true,
					per_page: 10
				};
				const response = await dashboardService.getExplore(params);
				for (let i = 0; i < response.data.data.length; i++) {
					if (!response.data.data[i].user.profile_picture) {
						response.data.data[i].user.profile_picture = avatar;
					} else {
						response.data.data[i].user.profile_picture = process.env.VUE_APP_API_URL + '/' + response.data.data[i].user.profile_picture;
					}
					if (response.data.data[i].image_url.startsWith('http://')) {
						response.data.data[i].image_url = response.data.data[i].image_url.replace('http://', 'https://');
					}
				}
				if (response.data.next_page) {
					this.next = true;
					this.nextPage = response.data.next_page;
				}
				this.exploreResult = response.data.data;
				this.isExplore = true;
				this.isLoading = false;
			} catch (error) {
				console.error('Error fetching data:', error);
			}
		},
		async loopList() {
			if (this.next) {
				try {
					const params = {
						pagination: true,
						per_page: 10,
						page: this.nextPage
					};
					const response = await dashboardService.getExplore(params);
					for (let i = 0; i < response.data.data.length; i++) {
						if (!response.data.data[i].user.profile_picture) {
							response.data.data[i].user.profile_picture = avatar;
						} else {
							response.data.data[i].user.profile_picture = process.env.VUE_APP_API_URL + '/' + response.data.data[i].user.profile_picture;
						}
						if (response.data.data[i].image_url.startsWith('http://')) {
							response.data.data[i].image_url = response.data.data[i].image_url.replace('http://', 'https://');
						}
					}
					if (response.data.next_page) {
						this.next = true;
						this.nextPage = response.data.next_page;
					} else {
						this.next = false;
					}
					this.exploreResult.push(...response.data.data);
					this.isExplore = true;
					this.isLoading = false;
				} catch (error) {
					console.error('Error fetching list:', error);
				}
			}
		},
		goDetail(path) {
			if (this.isLongPressing) return;
			this.$router.push(path);
		},
		onFocus() {
			this.isExplore = false;
			this.isRecentFriendList = true;
		},
		goProfile(data) {
			const info = {
				profile_picture: data.profile_picture,
				name: data.name,
				username: data.username,
				clicked_at: new Date()
			};
			const existingProfiles = JSON.parse(localStorage.getItem('recentProfiles')) || [];
			const isExisting = existingProfiles.some((profile) => profile.username === info.username);
			if (!isExisting) {
				existingProfiles.unshift(info);
				if (existingProfiles.length > 10) {
					existingProfiles.pop();
				}
				localStorage.setItem('recentProfiles', JSON.stringify(existingProfiles));
			}
			this.recentProfiles = existingProfiles;
			this.$router.push('/' + data.username);
		},
		goProfileUsername(username) {
			this.$router.push('/' + username);
		},
		clearRecent() {
			localStorage.removeItem('recentProfiles');
			this.recentProfiles = [];
		},
		loadRecentProfiles() {
			const existingProfiles = JSON.parse(localStorage.getItem('recentProfiles')) || [];
			this.recentProfiles = existingProfiles;
		},
		async blockAccount() {
			try {
				await dashboardService.unfollowFriends(this.selectedContent.user.id);
			} catch (error) {
				console.error('Error blocking account:', error);
			} finally {
				this.closeReportPopup();
				this.showSuccessToast('Account blocked successfully');
			}
		},
		async reportContent() {
			try {
				const params = {
					vibe_id: this.selectedContent.id,
					desc: this.commentReport
				};
				await dashboardService.postReportVibe(params);
			} catch (error) {
				console.error('Error reporting content:', error);
			} finally {
				this.closeReportPopup();
				this.showSuccessToast('Report submitted. Thank you!');
			}
		},
		handleScroll() {
			const container = this.$refs.sectionList;
			const scrollTop = container.scrollTop;
			const scrollHeight = container.scrollHeight;
			const clientHeight = container.clientHeight;
			if (scrollTop + clientHeight >= scrollHeight - 10) {
				if (!this.hasCalledLoopList) {
					this.loopList();
					this.hasCalledLoopList = true;
				} else {
					if (!this.isLoadingMore) {
						this.isLoadingMore = true;
						setTimeout(() => {
							this.loopList();
							this.isLoadingMore = false;
						}, 1000);
					}
				}
			}
		}
	}
};
</script>

<style lang="scss" scoped>
@import '@/assets/scss/color.scss';

.friend-blob { position: fixed; filter: blur(55px); pointer-events: none; z-index: 0; }
.friend-blob--1 { top: -6%; right: -14%; width: min(260px, 60vw); height: min(260px, 60vw); background: radial-gradient(circle, rgba($purple, 0.22) 0%, rgba(#6c5ce7, 0.07) 40%, transparent 70%); border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; animation: friendBlobMorph1 13s ease-in-out infinite; }
.friend-blob--2 { bottom: 8%; left: -12%; width: min(220px, 50vw); height: min(220px, 50vw); background: radial-gradient(circle, rgba(#4a3adf, 0.18) 0%, rgba($purple, 0.05) 40%, transparent 70%); border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; animation: friendBlobMorph2 16s ease-in-out infinite; }
.friend-blob--3 { top: 40%; left: 50%; transform: translateX(-50%); width: min(280px, 64vw); height: min(140px, 32vw); background: radial-gradient(ellipse, rgba($purple, 0.08) 0%, transparent 70%); border-radius: 50%; animation: friendBlobFloat 10s ease-in-out infinite; }
@keyframes friendBlobMorph1 { 0%, 100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(0, 0) rotate(0deg); } 50% { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; transform: translate(-12px, 16px) rotate(30deg); } }
@keyframes friendBlobMorph2 { 0%, 100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; transform: translate(0, 0) rotate(0deg); } 50% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(14px, -12px) rotate(-30deg); } }
@keyframes friendBlobFloat { 0%, 100% { transform: translateX(-50%) translateY(0) scale(1); opacity: 0.08; } 50% { transform: translateX(-50%) translateY(-16px) scale(1.05); opacity: 0.14; } }

.friends-fade-enter-active, .friends-fade-leave-active { transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1); }
.friends-fade-enter-from { opacity: 0; transform: translateY(20px); }
.friends-fade-leave-to { opacity: 0; transform: translateY(-10px); }
.friends-slide-enter-active, .friends-slide-leave-active { transition: all 0.35s cubic-bezier(0.23, 1, 0.32, 1); }
.friends-slide-enter-from { opacity: 0; transform: translateY(16px); }
.friends-slide-leave-to { opacity: 0; transform: translateY(-8px); }

.anim-title { animation: friendEnterDown 0.6s cubic-bezier(0.23, 1, 0.32, 1) both; }
.anim-search { animation: friendEnterUp 0.5s cubic-bezier(0.23, 1, 0.32, 1) 0.1s both; }
.anim-section-title { animation: friendEnterUp 0.45s cubic-bezier(0.23, 1, 0.32, 1) 0.15s both; }
@keyframes friendEnterDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes friendEnterUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

.searchFriend { position: relative; z-index: 1;
	> h1.title { color: $white1; font-size: 32px; font-weight: 700; position: relative; z-index: 2;
		.highlight { color: $purple; text-shadow: 0 0 20px rgba($purple, 0.3); }
	}
}

.search-box { position: relative !important; z-index: 2; background: rgba($white, 0.04) !important; backdrop-filter: blur(20px) !important; -webkit-backdrop-filter: blur(20px) !important; border: 1px solid rgba($white, 0.08) !important; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12), inset 0 1px 0 rgba($white, 0.06) !important; transition: all 0.3s ease !important; overflow: hidden;
	&::before { content: ''; position: absolute; top: 0; left: 10%; right: 10%; height: 1px; background: linear-gradient(90deg, transparent, rgba($white, 0.12), transparent); pointer-events: none; z-index: 2; }
	&:focus-within { border-color: rgba($purple, 0.3) !important; box-shadow: 0 0 0 3px rgba($purple, 0.06), 0 8px 28px rgba(0, 0, 0, 0.18), inset 0 1px 0 rgba($white, 0.08) !important; background: rgba($white, 0.055) !important; }
	.icon { transition: all 0.3s ease; &:hover { color: $purple !important; filter: drop-shadow(0 0 6px rgba($purple, 0.4)); } }
}

.section-title { display: flex; justify-content: space-between; align-items: center; padding: 0 10px; margin-bottom: 15px; position: relative; z-index: 2;
	h3 { font-size: 18px; color: $white; font-weight: 700; margin: 0; }
	small { font-size: 9px; color: rgba($white, 0.5); font-weight: 300; cursor: pointer; transition: all 0.3s ease; padding: 4px 10px; border-radius: 12px; border: 1px solid transparent; &:hover { color: $white; border-color: rgba($white, 0.1); background: rgba($white, 0.04); } }
}

.explore { position: relative; z-index: 2; }

.glass-spotlight-card { position: relative; transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1) !important; border: 1px solid rgba($white, 0.08) !important; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25), inset 0 1px 0 rgba($white, 0.06) !important;
	&::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 40%; background: linear-gradient(180deg, rgba($white, 0.06) 0%, transparent 100%); border-radius: 25px 25px 0 0; pointer-events: none; z-index: 2; }
	&:active { transform: translateY(-1px) scale(0.99) !important; }
	.badge { backdrop-filter: blur(14px) !important; -webkit-backdrop-filter: blur(14px) !important; background: rgba($purple, 0.55) !important; border: 1px solid rgba($white, 0.12) !important; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important; transition: all 0.3s ease; z-index: 3; }
	.report { z-index: 3; svg { filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.4)) !important; } }
	.bottom .star { .fa-star { filter: drop-shadow(0 0 8px rgba($purple, 0.4)); } span { text-shadow: 0 2px 6px rgba(0, 0, 0, 0.4); } }
}

ul.explore_list > li { animation: friendCardSlideIn 0.5s ease-out both; }
ul.friend_list > li { animation: friendCardSlideIn 0.4s ease-out both; position: relative; overflow: hidden; background: rgba($white, 0.035) !important; backdrop-filter: blur(16px) !important; -webkit-backdrop-filter: blur(16px) !important; border: 1px solid rgba($white, 0.07) !important; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba($white, 0.05) !important; transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1) !important;
	&:active { transform: translateY(0) scale(0.98); }
	img { position: relative; z-index: 2; border: 1px solid rgba($white, 0.1) !important; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15); }
	.text { position: relative; z-index: 2; }
}
@keyframes friendCardSlideIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

.popup-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); z-index: 100; }
.popup-overlay-enter-active { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
.popup-overlay-leave-active { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.popup-overlay-enter-from, .popup-overlay-leave-to { opacity: 0; backdrop-filter: blur(0); -webkit-backdrop-filter: blur(0); }
.popup-menu-enter-active { transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1); }
.popup-menu-leave-active { transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
.popup-menu-enter-from { opacity: 0; transform: scale(0.8); filter: blur(8px); }
.popup-menu-leave-to { opacity: 0; transform: scale(0.9); filter: blur(4px); }

.popup-container { position: fixed; z-index: 101; display: flex; flex-direction: column; gap: 10px;
	&--center { top: 50% !important; left: 50% !important; transform: translate(-50%, -50%); width: min(320px, calc(100vw - 40px)) !important; }
}

.popup-preview { border-radius: 20px; overflow: hidden; background: rgba(20, 20, 40, 0.6); backdrop-filter: blur(40px) saturate(1.5); -webkit-backdrop-filter: blur(40px) saturate(1.5); border: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 24px 80px rgba(0, 0, 0, 0.5), 0 0 0 0.5px rgba(255, 255, 255, 0.06) inset, inset 0 2px 0 rgba(255, 255, 255, 0.08), inset 0 -1px 0 rgba(0, 0, 0, 0.2); }
.popup-preview-image { width: 100%; height: 160px; background-size: cover; background-position: center; }
.popup-preview-info { padding: 12px 16px; }
.popup-preview-user { display: flex; align-items: center; gap: 8px; margin-bottom: 6px;
	img { width: 22px; height: 22px; border-radius: 50%; border: 1px solid rgba(255, 255, 255, 0.15); }
	span { font-size: 12px; color: rgba(255, 255, 255, 0.6); font-weight: 500; }
}
.popup-preview-info h4 { font-size: 15px; font-weight: 700; color: #fff; margin: 0; line-height: 1.3; }
.popup-preview-info p { font-size: 12px; color: rgba(255, 255, 255, 0.4); margin: 4px 0 0; line-height: 1.4; }

.popup-actions { display: flex; flex-direction: column; border-radius: 16px; overflow: hidden; background: rgba(20, 20, 40, 0.6); backdrop-filter: blur(40px) saturate(1.5); -webkit-backdrop-filter: blur(40px) saturate(1.5); border: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 16px 48px rgba(0, 0, 0, 0.4), 0 0 0 0.5px rgba(255, 255, 255, 0.06) inset, inset 0 2px 0 rgba(255, 255, 255, 0.08), inset 0 -1px 0 rgba(0, 0, 0, 0.2); }
.popup-action { display: flex; align-items: center; gap: 12px; padding: 14px 16px; background: none; border: none; color: #fff; font-size: 15px; font-weight: 500; cursor: pointer; transition: background 0.15s ease; text-align: left; width: 100%;
	svg { width: 18px; color: rgba(255, 255, 255, 0.5); }
	&:active { background: rgba(255, 255, 255, 0.1); }
	&-danger { color: #f87171; svg { color: #f87171; } }
}
.popup-divider { height: 0.5px; background: rgba(255, 255, 255, 0.1); margin: 0 14px; }

.popup-report { border-radius: 20px; overflow: hidden; background: rgba(20, 20, 40, 0.75); backdrop-filter: blur(40px) saturate(1.5); -webkit-backdrop-filter: blur(40px) saturate(1.5); border: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 24px 80px rgba(0, 0, 0, 0.5), inset 0 2px 0 rgba(255, 255, 255, 0.08); padding: 20px;
	h3 { color: #fff; font-size: 18px; font-weight: 700; margin: 0 0 14px; text-align: center; }
}
.popup-report-options { display: flex; flex-direction: column; gap: 8px; margin-bottom: 12px; }
.popup-radio { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 12px; background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); cursor: pointer; transition: all 0.2s ease;
	input { display: none; } span { color: rgba(255, 255, 255, 0.7); font-size: 14px; font-weight: 500; }
	&.active { background: rgba($purple, 0.15); border-color: rgba($purple, 0.3); span { color: #fff; } }
	&::before { content: ''; width: 18px; height: 18px; border-radius: 50%; border: 2px solid rgba(255, 255, 255, 0.2); flex-shrink: 0; transition: all 0.2s ease; }
	&.active::before { border-color: $purple; background: $purple; box-shadow: inset 0 0 0 3px rgba(20, 20, 40, 0.75); }
}
.popup-report-textarea textarea { width: 100%; background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 10px 14px; color: #fff; font-size: 13px; outline: none; resize: none; transition: border-color 0.2s ease;
	&::placeholder { color: rgba(255, 255, 255, 0.3); } &:focus { border-color: rgba($purple, 0.3); }
}
.popup-report-warning { color: rgba(255, 255, 255, 0.5); font-size: 13px; text-align: center; margin: 0; }
.popup-report-buttons { display: flex; flex-direction: column; gap: 8px; margin-top: 16px; }
.popup-btn { width: 100%; padding: 12px; border: none; border-radius: 12px; font-size: 15px; font-weight: 600; cursor: pointer; background: rgba(255, 255, 255, 0.06); color: #fff; transition: all 0.2s ease;
	&:active { transform: scale(0.98); }
	&--danger { background: rgba(#f87171, 0.2); color: #f87171; border: 1px solid rgba(#f87171, 0.2); &:active { background: rgba(#f87171, 0.3); } }
}

.success-toast { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999; background: rgba(20, 20, 40, 0.9); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 14px; padding: 12px 28px; color: #fff; font-size: 14px; font-weight: 600; box-shadow: 0 8px 28px rgba(0, 0, 0, 0.3); }
.toast-pop-enter-active { transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1); }
.toast-pop-leave-active { transition: all 0.2s ease-in; }
.toast-pop-enter-from { opacity: 0; transform: translate(-50%, -50%) scale(0.85); }
.toast-pop-leave-to { opacity: 0; transform: translate(-50%, -50%) scale(0.9); }

.loading { display: flex; justify-content: center; align-items: center; height: 30vh; color: $purple; font-size: 28px; filter: drop-shadow(0 0 12px rgba($purple, 0.4)); }
</style>