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
					<li v-for="(section, index) in exploreResult" :key="section.id" class="mb-4" :style="{ animationDelay: (index * 0.08) + 's' }">
						<div class="content glass-spotlight-card" :style="{ backgroundImage: `url(${section.image_url})` }" :class="[{ twothree: ['Film', 'Top', 'Reading'].some((word) => section.category.title.includes(word)) }]">
							<div class="badge" @click="goProfileUsername(section.user.username)">
								<img :src="section.user.profile_picture" alt="" />
								<h3>{{ section.user.name }}</h3>
							</div>
							<div class="report" data-bs-toggle="modal" data-bs-target="#reportModal" @click="getContent(section)"><fa icon="ellipsis" class="text-white" /></div>
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

		<!-- REPORT MODAL -->
		<div class="modal pt-5 fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body notification">
						<div class="title">
							<h1>User Actions</h1>
						</div>
						<ul class="btn-list">
							<li>
								<div class="form-check">
									<input class="form-check-input" type="radio" name="accountAction" id="reportAccount" value="report" v-model="selectedAction" />
									<label class="form-check-label" for="reportAccount">Report Content</label>
								</div>
							</li>
							<li>
								<div class="form-check">
									<input class="form-check-input" type="radio" name="accountAction" id="blockAccount" value="block" v-model="selectedAction" />
									<label class="form-check-label" for="blockAccount">Block Account</label>
								</div>
							</li>
						</ul>
						<div v-if="selectedAction === 'report'">
							<p class="mt-2">Please provide a reason for reporting this content :</p>
							<textarea type="text" style="width: 100%; background: #1b1d27; outline: none; box-shadow: none; color: #fff; border-radius: 5px; padding: 5px 10px; border: none" v-model="commentReport" placeholder="Write here" maxlength="250" rows="3"></textarea>
						</div>
						<p class="mt-2" v-if="selectedAction === 'block'">Are you sure you want to block this account? This action will prevent them from interacting with you.</p>
					</div>
					<div class="modal-button" v-if="selectedAction === 'block'">
						<button class="btn-block text-danger" @click="blockAccount()">Block</button>
					</div>
					<div class="modal-button" v-if="selectedAction === 'report'">
						<button class="btn-block text-danger" @click="reportContent()">Report</button>
					</div>
					<div class="modal-button">
						<button type="button" class="btn-block" data-bs-dismiss="modal" aria-label="Close" ref="reportModalDismiss">Close</button>
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
							<h1>Success</h1>
						</div>
						<p class="m-0 text-center">You have successfully submitted your feedback. Thank you for your input! 🚀</p>
					</div>
					<div class="modal-button">
						<button type="button" class="btn-block" data-bs-dismiss="modal" aria-label="Close">Close</button>
					</div>
				</div>
			</div>
		</div>
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
			nextPage: ''
		};
	},
	mounted() {
		this.getSpotlight();
		this.loadRecentProfiles();
	},
	methods: {
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
		getContent(content) {
			this.selectedContent = content;
		},
		async blockAccount() {
			try {
				await dashboardService.unfollowFriends(this.selectedAction.user_id);
				this.$router.push({
					path: '/dashboard',
					query: { current: 'friend' }
				});
			} catch (error) {
				console.error('Error blocking account:', error);
			} finally {
				this.$refs.reportModalDismiss.click();
				var backdrop = document.querySelector('.modal-backdrop');
				if (backdrop) {
					backdrop.remove();
				}
				this.$refs.notifModalBtn.click();
			}
		},
		async reportContent() {
			try {
				const params = {
					vibe_id: this.selectedContent.id,
					desc: this.commentReport
				};
				await dashboardService.postReportVibe(params);
				this.$router.push({
					path: '/dashboard',
					query: { current: 'friend' }
				});
			} catch (error) {
				console.error('Error blocking account:', error);
			} finally {
				this.$refs.reportModalDismiss.click();
				var backdrop = document.querySelector('.modal-backdrop');
				if (backdrop) {
					backdrop.remove();
				}
				this.$refs.notifModalBtn.click();
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

// =============================================
// MYVIBE FRIENDSVIEW — LIQUID GLASS REDESIGN
// =============================================

// === MORPHING BLOBS ===
.friend-blob {
	position: fixed;
	filter: blur(55px);
	pointer-events: none;
	z-index: 0;
}

.friend-blob--1 {
	top: -6%;
	right: -14%;
	width: min(260px, 60vw);
	height: min(260px, 60vw);
	background: radial-gradient(circle, rgba($purple, 0.22) 0%, rgba(#6c5ce7, 0.07) 40%, transparent 70%);
	border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
	animation: friendBlobMorph1 13s ease-in-out infinite;
}

.friend-blob--2 {
	bottom: 8%;
	left: -12%;
	width: min(220px, 50vw);
	height: min(220px, 50vw);
	background: radial-gradient(circle, rgba(#4a3adf, 0.18) 0%, rgba($purple, 0.05) 40%, transparent 70%);
	border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
	animation: friendBlobMorph2 16s ease-in-out infinite;
}

.friend-blob--3 {
	top: 40%;
	left: 50%;
	transform: translateX(-50%);
	width: min(280px, 64vw);
	height: min(140px, 32vw);
	background: radial-gradient(ellipse, rgba($purple, 0.08) 0%, transparent 70%);
	border-radius: 50%;
	animation: friendBlobFloat 10s ease-in-out infinite;
}

@keyframes friendBlobMorph1 {
	0%, 100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(0, 0) rotate(0deg); }
	50% { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; transform: translate(-12px, 16px) rotate(30deg); }
}
@keyframes friendBlobMorph2 {
	0%, 100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; transform: translate(0, 0) rotate(0deg); }
	50% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(14px, -12px) rotate(-30deg); }
}
@keyframes friendBlobFloat {
	0%, 100% { transform: translateX(-50%) translateY(0) scale(1); opacity: 0.08; }
	50% { transform: translateX(-50%) translateY(-16px) scale(1.05); opacity: 0.14; }
}

// === PAGE TRANSITIONS ===
.friends-fade-enter-active,
.friends-fade-leave-active {
	transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
}
.friends-fade-enter-from {
	opacity: 0;
	transform: translateY(20px);
}
.friends-fade-leave-to {
	opacity: 0;
	transform: translateY(-10px);
}

.friends-slide-enter-active,
.friends-slide-leave-active {
	transition: all 0.35s cubic-bezier(0.23, 1, 0.32, 1);
}
.friends-slide-enter-from {
	opacity: 0;
	transform: translateY(16px);
}
.friends-slide-leave-to {
	opacity: 0;
	transform: translateY(-8px);
}

// === ENTRANCE ANIMATIONS ===
.anim-title {
	animation: friendEnterDown 0.6s cubic-bezier(0.23, 1, 0.32, 1) both;
}

.anim-search {
	animation: friendEnterUp 0.5s cubic-bezier(0.23, 1, 0.32, 1) 0.1s both;
}

.anim-section-title {
	animation: friendEnterUp 0.45s cubic-bezier(0.23, 1, 0.32, 1) 0.15s both;
}

@keyframes friendEnterDown {
	from { opacity: 0; transform: translateY(-20px); }
	to { opacity: 1; transform: translateY(0); }
}

@keyframes friendEnterUp {
	from { opacity: 0; transform: translateY(20px); }
	to { opacity: 1; transform: translateY(0); }
}

// === TITLE ===
.searchFriend {
	position: relative;
	z-index: 1;

	> h1.title {
		color: $white1;
		font-size: 32px;
		font-weight: 700;
		position: relative;
		z-index: 2;

		.highlight {
			color: $purple;
			position: relative;
			text-shadow: 0 0 20px rgba($purple, 0.3);

			&::after {
				content: attr(data-text);
				position: absolute;
				top: 0;
				left: 0;
				background: linear-gradient(90deg, transparent 0%, rgba($white, 0.2) 50%, transparent 100%);
				background-size: 200% 100%;
				-webkit-background-clip: text;
				background-clip: text;
				-webkit-text-fill-color: transparent;
				animation: friendShimmer 3s ease-in-out infinite;
			}
		}
	}
}

@keyframes friendShimmer {
	0% { background-position: -200% center; }
	100% { background-position: 200% center; }
}

// === SEARCH BOX — GLASS OVERRIDE ===
.search-box {
	position: relative !important;
	z-index: 2;
	background: rgba($white, 0.04) !important;
	backdrop-filter: blur(20px) !important;
	-webkit-backdrop-filter: blur(20px) !important;
	border: 1px solid rgba($white, 0.08) !important;
	box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12), inset 0 1px 0 rgba($white, 0.06) !important;
	transition: all 0.3s ease !important;
	overflow: hidden;

	// Top edge light
	&::before {
		content: '';
		position: absolute;
		top: 0;
		left: 10%;
		right: 10%;
		height: 1px;
		background: linear-gradient(90deg, transparent, rgba($white, 0.12), transparent);
		pointer-events: none;
		z-index: 2;
	}

	&:focus-within {
		border-color: rgba($purple, 0.3) !important;
		box-shadow:
			0 0 0 3px rgba($purple, 0.06),
			0 8px 28px rgba(0, 0, 0, 0.18),
			inset 0 1px 0 rgba($white, 0.08) !important;
		background: rgba($white, 0.055) !important;
	}

	.icon {
		transition: all 0.3s ease;
		&:hover {
			color: $purple !important;
			filter: drop-shadow(0 0 6px rgba($purple, 0.4));
		}
	}
}

// === SECTION TITLE (Spotlight / Recents) ===
.section-title {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 0 10px;
	margin-bottom: 15px;
	position: relative;
	z-index: 2;

	h3 {
		font-size: 18px;
		color: $white;
		font-weight: 700;
		margin: 0;
		text-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
	}

	small {
		font-size: 9px;
		color: rgba($white, 0.5);
		font-weight: 300;
		cursor: pointer;
		transition: all 0.3s ease;
		padding: 4px 10px;
		border-radius: 12px;
		border: 1px solid transparent;

		&:hover {
			color: $white;
			border-color: rgba($white, 0.1);
			background: rgba($white, 0.04);
		}
	}
}

// === EXPLORE SECTION ===
.explore {
	position: relative;
	z-index: 2;
}

// === SPOTLIGHT CARDS — GLASS ===
.glass-spotlight-card {
	position: relative;
	transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1) !important;
	border: 1px solid rgba($white, 0.08) !important;
	box-shadow:
		0 8px 32px rgba(0, 0, 0, 0.25),
		inset 0 1px 0 rgba($white, 0.06) !important;

	// Glass reflection top
	&::after {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		height: 40%;
		background: linear-gradient(180deg, rgba($white, 0.06) 0%, transparent 100%);
		border-radius: 25px 25px 0 0;
		pointer-events: none;
		z-index: 2;
	}

	&:hover {
		transform: translateY(-4px) !important;
		border-color: rgba($purple, 0.2) !important;
		box-shadow:
			0 16px 48px rgba(0, 0, 0, 0.35),
			0 0 24px rgba($purple, 0.08),
			inset 0 1px 0 rgba($white, 0.08) !important;
	}

	&:active {
		transform: translateY(-1px) scale(0.99) !important;
	}

	// Badge glass enhancement
	.badge {
		backdrop-filter: blur(14px) !important;
		-webkit-backdrop-filter: blur(14px) !important;
		background: rgba($purple, 0.55) !important;
		border: 1px solid rgba($white, 0.12) !important;
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
		transition: all 0.3s ease;

		&:hover {
			background: rgba($purple, 0.7) !important;
			transform: translateY(-1px);
			box-shadow: 0 6px 16px rgba($purple, 0.25) !important;
		}
	}

	// Report icon glass
	.report {
		z-index: 3;
		svg {
			filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.4)) !important;
			transition: all 0.3s ease;
		}
		&:hover svg {
			filter: drop-shadow(0 0 8px rgba($white, 0.5)) !important;
		}
	}

	// Star rating glow
	.bottom .star {
		.fa-star {
			filter: drop-shadow(0 0 8px rgba($purple, 0.4));
		}
		span {
			text-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
		}
	}
}

// === SPOTLIGHT LIST STAGGERED ENTRANCE ===
ul.explore_list > li {
	animation: friendCardSlideIn 0.5s ease-out both;
}

// === FRIEND LIST — GLASS ROWS ===
ul.friend_list > li {
	animation: friendCardSlideIn 0.4s ease-out both;
	position: relative;
	overflow: hidden;
	background: rgba($white, 0.035) !important;
	backdrop-filter: blur(16px) !important;
	-webkit-backdrop-filter: blur(16px) !important;
	border: 1px solid rgba($white, 0.07) !important;
	box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba($white, 0.05) !important;
	transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1) !important;

	// Glass shine sweep
	&::after {
		content: '';
		position: absolute;
		top: 0;
		left: -100%;
		width: 100%;
		height: 100%;
		background: linear-gradient(90deg, transparent, rgba($white, 0.06), transparent);
		transition: left 0.6s ease;
		z-index: 1;
		pointer-events: none;
	}

	&:hover {
		border-color: rgba($purple, 0.2) !important;
		background: rgba($white, 0.06) !important;
		transform: translateY(-2px);
		box-shadow:
			0 8px 24px rgba(0, 0, 0, 0.15),
			0 0 12px rgba($purple, 0.06),
			inset 0 1px 0 rgba($white, 0.08) !important;

		&::after { left: 100%; }
	}

	&:active {
		transform: translateY(0) scale(0.98);
	}

	img {
		position: relative;
		z-index: 2;
		border: 1px solid rgba($white, 0.1) !important;
		box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
	}

	.text {
		position: relative;
		z-index: 2;
	}
}

@keyframes friendCardSlideIn {
	from { opacity: 0; transform: translateY(20px); }
	to { opacity: 1; transform: translateY(0); }
}

// === LOADING ===
.loading {
	display: flex;
	justify-content: center;
	align-items: center;
	height: 30vh;
	color: $purple;
	font-size: 28px;
	filter: drop-shadow(0 0 12px rgba($purple, 0.4));
}
</style>