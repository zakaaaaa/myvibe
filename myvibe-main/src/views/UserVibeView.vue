<template>
	<section class="dashboard">
		<!-- Blobs -->
		<div class="user-blob user-blob--1"></div>
		<div class="user-blob user-blob--2"></div>
		<div class="user-blob user-blob--3"></div>

		<!-- Follow success popup -->
		<transition name="follow-pop">
			<div v-if="showFollowPopup" class="follow-popup">
				<div class="follow-popup__inner">
					<div class="follow-popup__icon">
						<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
							<polyline points="20 6 9 17 4 12"></polyline>
						</svg>
					</div>
					<h3>Following!</h3>
					<p>You're now following <strong>{{ profile.name }}</strong></p>
				</div>
			</div>
		</transition>

		<div v-if="!isLoading" class="container">
			<RouterLink to="/dashboard" class="back-floating anim-back">
				<fa icon="arrow-left" />
			</RouterLink>
			<div class="menu-floating anim-back" data-bs-toggle="modal" data-bs-target="#reportModal">
				<fa icon="ellipsis-vertical" />
			</div>
			<div class="vibe-profile mt-4 anim-profile">
				<div class="profile-preview">
					<div class="image"><img :src="profile.profile_picture" alt="" /></div>
					<div class="info">
						<h1>{{ profile.name }}</h1>
						<div class="count">
							<div class="followers">
								<span class="fw-bold">{{ profile.follower }}</span> Followers
							</div>
							<div class="following">
								<span class="fw-bold">{{ profile.following }}</span> Following
							</div>
						</div>
						<ul class="summary">
							<li v-if="profile.mbti"><span>{{ profile.mbti }}</span></li>
							<li v-if="profile.zodiac"><span>{{ profile.zodiac }}</span></li>
							<li v-if="profile.enthusiast"><span>{{ profile.enthusiast }}</span></li>
							<li v-if="profile.relationship"><span>{{ profile.relationship }}</span></li>
						</ul>
					</div>
					<div class="icon">
						<div class="btn-follow" v-if="!profile.isFollowing" @click="follow(profile.id)">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
								<g id="user_add_2_line" fill="none" fill-rule="evenodd">
									<path d="M24 0v24H0V0zM12.594 23.258l-.012.002-.071.035-.02.004-.014-.004-.071-.036c-.01-.003-.019 0-.024.006l-.004.01-.017.428.005.02.01.013.104.074.015.004.012-.004.104-.074.012-.016.004-.017-.017-.427c-.002-.01-.009-.017-.016-.018m.264-.113-.014.002-.184.093-.01.01-.003.011.018.43.005.012.008.008.201.092c.012.004.023 0 .029-.008l.004-.014-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014-.034.614c0 .012.007.02.017.024l.015-.002.201-.093.01-.008.003-.011.018-.43-.003-.012-.01-.01z" />
									<path fill="currentColor" d="M11 2a5 5 0 1 0 0 10 5 5 0 0 0 0-10M8 7a3 3 0 1 1 6 0 3 3 0 0 1-6 0M4 18.5c0-.18.09-.489.413-.899.316-.4.804-.828 1.451-1.222C7.157 15.589 8.977 15 11 15c.375 0 .744.02 1.105.059a1 1 0 1 0 .211-1.99A12.905 12.905 0 0 0 11 13c-2.395 0-4.575.694-6.178 1.672-.8.488-1.484 1.064-1.978 1.69C2.358 16.976 2 17.713 2 18.5c0 .845.411 1.511 1.003 1.986.56.45 1.299.748 2.084.956C6.665 21.859 8.771 22 11 22l.685-.005a1 1 0 1 0-.027-2L11 20c-2.19 0-4.083-.143-5.4-.492-.663-.175-1.096-.382-1.345-.582C4.037 18.751 4 18.622 4 18.5M18 14a1 1 0 0 1 1 1v2h2a1 1 0 1 1 0 2h-2v2a1 1 0 1 1-2 0v-2h-2a1 1 0 1 1 0-2h2v-2a1 1 0 0 1 1-1" />
								</g>
							</svg>
						</div>
						<div class="btn-follow" v-if="profile.isFollowing" @click="goDM(profile.username)">
							<fa :icon="['far', 'paper-plane']" />
						</div>
					</div>
				</div>
			</div>
			<div v-for="(section, sIndex) in vibe_category" :key="section.id" class="vibe-section-preview mb-1 anim-section" :style="{ animationDelay: (sIndex * 0.1 + 0.3) + 's' }">
				<div v-if="section.vibes.length > 0" class="content">
					<div class="head">
						<h1 class="title">
							{{ section.title[0] }} <span class="highlight">{{ section.title[1] }}</span>
						</h1>
						<RouterLink :to="'/' + this.profile.username + '/more/' + section.id" class="more-btn"><fa :icon="['fas', 'angle-right']" /></RouterLink>
					</div>
					<ul :class="[{ 'justify-content-start': section.vibes.length < 3 }, { 'justify-content-space-between m-0': section.vibes.length >= 3 }, { twothree: ['Film', 'Top', 'Reading'].includes(section.title[0]) }]">
						<li v-for="vibe in section.vibes" :key="vibe.id" :style="{ backgroundImage: `url(${vibe.image_url})` }" @click="goDetail(profile.username + '/' + vibe.id)">
							<div class="caption">
								<h3>{{ vibe.desc }}</h3>
								<h1>{{ vibe.title.slice(0, 20) }}</h1>
								<div class="stars">
									<fa v-for="star in Math.floor(Number(vibe.rating))" :key="`star-${star}`" :icon="['fas', 'star']" />
									<fa v-if="Number(vibe.rating) % 1 >= 0.5" :key="`half-star-${vibe.id}`" :icon="['fas', 'star-half-stroke']" />
									<fa v-for="emptyStar in 5 - (Math.floor(Number(vibe.rating)) + (Number(vibe.rating) % 1 >= 0.5 ? 1 : 0))" :key="`empty-star-${emptyStar}`" :icon="['far', 'star']" />
								</div>
							</div>
						</li>
					</ul>
				</div>
			</div>
			<div v-for="(section, oIndex) in others" :key="section.id" class="vibe-section-preview mb-1 anim-section" :style="{ animationDelay: ((vibe_category.length + oIndex) * 0.1 + 0.3) + 's' }">
				<div v-if="section.vibes.length > 0" class="content">
					<div class="head">
						<h1 class="title">{{ section.title }}</h1>
						<RouterLink :to="'/' + this.profile.username + '/more-other/' + section.id" class="more-btn"><fa :icon="['fas', 'angle-right']" /></RouterLink>
					</div>
					<ul :class="[{ 'justify-content-start': section.vibes.length < 3 }, { 'justify-content-space-between m-0': section.vibes.length >= 3 }]">
						<li v-for="vibe in section.vibes" :key="vibe.id" :style="{ backgroundImage: `url(${vibe.image_url})` }" @click="goDetail(profile.username + '/other/' + vibe.id)">
							<div class="caption">
								<h3>{{ vibe.desc }}</h3>
								<h1>{{ vibe.title.slice(0, 20) }}</h1>
								<div class="stars">
									<fa v-for="star in Math.floor(Number(vibe.rating))" :key="`star-${star}`" :icon="['fas', 'star']" />
									<fa v-if="Number(vibe.rating) % 1 >= 0.5" :key="`half-star-${vibe.id}`" :icon="['fas', 'star-half-stroke']" />
									<fa v-for="emptyStar in 5 - (Math.floor(Number(vibe.rating)) + (Number(vibe.rating) % 1 >= 0.5 ? 1 : 0))" :key="`empty-star-${emptyStar}`" :icon="['far', 'star']" />
								</div>
							</div>
						</li>
					</ul>
				</div>
			</div>
			<div class="pt-5"></div>
		</div>
		<div v-else class="loading">
			<fa icon="spinner" class="fa-spin-pulse" />
		</div>
		<ul class="floating_menu">
			<li @click="isActive('home', '/dashboard')"><fa :icon="['far', 'user']" /></li>
			<li @click="isActive('friend', '/dashboard')"><fa :icon="['fas', 'magnifying-glass']" /></li>
			<li @click="isActive('chat', '/dashboard')">
				<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" style="margin-top: -7px">
					<g id="chat_1_line" fill="none" transform="scale(1.2)">
						<path d="M24 0v24H0V0zM12.593 23.258l-.011.002-.071.035-.02.004-.014-.004-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01-.017.428.005.02.01.013.104.074.015.004.012-.004.104-.074.012-.016.004-.017-.017-.427c-.002-.01-.009-.017-.017-.018m.265-.113-.013.002-.185.093-.01.01-.003.011.018.43.005.012.008.007.201.093c.012.004.023 0 .029-.008l.004-.014-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014-.034.614c0 .012.007.02.017.024l.015-.002.201-.093.01-.008.004-.011.017-.43-.003-.012-.01-.01z" />
						<path fill="currentColor" d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10H4a2 2 0 0 1-2-2v-8C2 6.477 6.477 2 12 2m0 2a8 8 0 0 0-8 8v8h8a8 8 0 1 0 0-16m0 10a1 1 0 0 1 .117 1.993L12 16H9a1 1 0 0 1-.117-1.993L9 14zm3-4a1 1 0 1 1 0 2H9a1 1 0 1 1 0-2z" />
					</g>
				</svg>
			</li>
		</ul>
		<div class="modal pt-5 fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body notification">
						<div class="title"><h1>{{ profile.username }}</h1></div>
						<ul class="btn-list">
							<li><div class="form-check"><input class="form-check-input" type="radio" name="accountAction" id="blockAccount" value="block" v-model="selectedAction" /><label class="form-check-label" for="blockAccount">Block Account</label></div></li>
							<li><div class="form-check"><input class="form-check-input" type="radio" name="accountAction" id="reportAccount" value="report" v-model="selectedAction" /><label class="form-check-label" for="reportAccount">Report Account</label></div></li>
						</ul>
						<div v-if="selectedAction === 'report'">
							<p class="mt-2">If you want to report this account, please contact our support team:</p>
							<ul class="list-group">
								<li class="list-group-item"><strong>Email:</strong> <a href="mailto:lukersaintfeller@gmail.com">lukersaintfeller@gmail.com</a></li>
								<li class="list-group-item"><strong>Hotline:</strong> +6282278709681</li>
							</ul>
						</div>
						<p class="mt-2" v-if="selectedAction === 'block'">Are you sure you want to block this account? This action will prevent them from interacting with you.</p>
					</div>
					<div class="modal-button" v-if="selectedAction === 'block'"><button class="btn-block text-danger" @click="blockAccount()">Block</button></div>
					<div class="modal-button"><button type="button" class="btn-block" data-bs-dismiss="modal" aria-label="Close" ref="reportModalDismiss">Close</button></div>
				</div>
			</div>
		</div>
	</section>
</template>

<script>
import dashboardService from '@/services/dashboardService';
import logo from '@/assets/avatar.png';

export default {
	name: 'UserVibeView',
	data() {
		return {
			isLoading: false,
			selectedAction: 'block',
			showFollowPopup: false,
			profile: [],
			others: [],
			vibe_category: []
		};
	},
	mounted() {
		this.getUser();
		this.getOther();
	},
	methods: {
		splitString(data) {
			return data.split(' ');
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
				for (let i = 0; i < response.data.vibe_category.length; i++) {
					response.data.vibe_category[i].title = this.splitString(response.data.vibe_category[i].title);
				}
				this.profile = response.data.profile;
				this.profile.mbti = this.profile.mbti?.mbti_name ?? '';
				this.profile.zodiac = this.profile.zodiac?.zodiac_name ?? '';
				this.profile.relationship = this.profile.relationship?.relationship_name ?? '';
				this.vibe_category = response.data.vibe_category;
				this.isLoading = false;
			} catch (error) {
				console.error('Error fetching home:', error);
				this.isLoading = false;
				this.$router.push('/dashboard');
			}
		},
		goDetail(path) {
			this.$router.push('/' + path);
		},
		isActive(current, route) {
			this.$router.push({ path: route, query: { current } });
		},
		async follow() {
			try {
				await dashboardService.followFriends(this.profile.id);
				this.showFollowPopup = true;
				setTimeout(() => { this.showFollowPopup = false; }, 2200);
				const response = await dashboardService.getFriends(this.$route.params.username);
				response.data.profile.profile_picture = process.env.VUE_APP_API_URL + '/' + response.data.profile.profile_picture || logo;
				this.profile = response.data.profile ?? '';
				this.profile.mbti = this.profile.mbti?.mbti_name ?? '';
				this.profile.zodiac = this.profile.zodiac?.zodiac_name ?? '';
				this.profile.relationship = this.profile.relationship?.relationship_name ?? '';
				this.isLoading = false;
			} catch (error) {
				console.error('Error fetching home:', error);
			}
		},
		goDM(username) {
			this.$router.push('/message/' + username);
		},
		async getOther() {
			this.isLoading = true;
			try {
				const response = await dashboardService.getOtherUserVibe(this.$route.params.username);
				this.others = response.data.data;
				this.isLoading = false;
			} catch (error) {
				console.error('Error fetching others:', error);
			} finally {
				const current = this.$route.query.current;
				if (current && current != 'home') {
					this.isActive(current);
				}
			}
		},
		async blockAccount() {
			try {
				await dashboardService.unfollowFriends(this.profile.id);
				this.$router.push('/dashboard');
			} catch (error) {
				console.error('Error blocking account:', error);
			} finally {
				var backdrop = document.querySelector('.modal-backdrop');
				if (backdrop) { backdrop.remove(); }
			}
		}
	}
};
</script>

<style lang="scss" scoped>
@import '@/assets/scss/color.scss';

// === BLOBS ===
.user-blob {
	position: fixed;
	filter: blur(55px);
	pointer-events: none;
	z-index: 0;
}
.user-blob--1 {
	top: -8%;
	right: -14%;
	width: min(270px, 62vw);
	height: min(270px, 62vw);
	background: radial-gradient(circle, rgba($purple, 0.22) 0%, rgba(#6c5ce7, 0.07) 40%, transparent 70%);
	border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
	animation: uBm1 13s ease-in-out infinite;
}
.user-blob--2 {
	bottom: 6%;
	left: -12%;
	width: min(230px, 52vw);
	height: min(230px, 52vw);
	background: radial-gradient(circle, rgba(#4a3adf, 0.18) 0%, rgba($purple, 0.05) 40%, transparent 70%);
	border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
	animation: uBm2 16s ease-in-out infinite;
}
.user-blob--3 {
	top: 30%;
	left: 50%;
	transform: translateX(-50%);
	width: min(300px, 66vw);
	height: min(140px, 30vw);
	background: radial-gradient(ellipse, rgba($purple, 0.08) 0%, transparent 70%);
	border-radius: 50%;
	animation: uBf 10s ease-in-out infinite;
}
@keyframes uBm1 {
	0%, 100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(0,0) rotate(0deg); }
	50% { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; transform: translate(-12px,16px) rotate(30deg); }
}
@keyframes uBm2 {
	0%, 100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; transform: translate(0,0) rotate(0deg); }
	50% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(14px,-12px) rotate(-30deg); }
}
@keyframes uBf {
	0%, 100% { transform: translateX(-50%) translateY(0) scale(1); opacity: 0.08; }
	50% { transform: translateX(-50%) translateY(-16px) scale(1.05); opacity: 0.14; }
}

// === ENTRANCE ANIMATIONS ===
.anim-back { animation: uFd 0.4s cubic-bezier(0.23,1,0.32,1) both; }
.anim-profile { animation: uFu 0.55s cubic-bezier(0.23,1,0.32,1) 0.1s both; }
.anim-section { animation: uFu 0.5s cubic-bezier(0.23,1,0.32,1) both; }
@keyframes uFd { from { opacity:0; transform:translateY(-16px); } to { opacity:1; transform:translateY(0); } }
@keyframes uFu { from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:translateY(0); } }

// === BACK-TOP FIX ===
// (back-top div removed from template, buttons are now independent fixed elements)

// === GLASS BACK BUTTON (same as AddView) ===
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
		top: 2px;
		left: 15%;
		right: 15%;
		height: 40%;
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

// === GLASS MENU BUTTON (fixed right) ===
.menu-floating {
	position: fixed;
	top: calc(env(safe-area-inset-top) + 16px);
	right: 16px;
	width: 44px;
	height: 44px;
	display: flex;
	align-items: center;
	justify-content: center;
	border-radius: 50%;
	color: $white;
	font-size: 15px;
	cursor: pointer;
	z-index: 100;
	background: rgba($white, 0.04);
	backdrop-filter: blur(20px);
	-webkit-backdrop-filter: blur(20px);
	border: 1px solid rgba($white, 0.08);
	box-shadow: 0 4px 16px rgba(0,0,0,0.2), inset 0 1px 0 rgba($white,0.08);
	transition: all 0.4s cubic-bezier(0.23,1,0.32,1);

	&::before {
		content: '';
		position: absolute;
		top: 2px;
		left: 15%;
		right: 15%;
		height: 40%;
		background: linear-gradient(180deg, rgba($white,0.1) 0%, transparent 100%);
		border-radius: 50%;
		pointer-events: none;
	}

	&:hover {
		transform: translateY(-2px) scale(1.08);
		background: rgba($white, 0.08);
		border-color: rgba($white, 0.15);
	}
	&:active { transform: scale(0.92); }
}

// === FOLLOW POPUP ===
.follow-popup {
	position: fixed;
	top: 0; left: 0; right: 0; bottom: 0;
	z-index: 9999;
	display: flex;
	align-items: center;
	justify-content: center;
	background: rgba($black, 0.4);
	backdrop-filter: blur(8px);
	-webkit-backdrop-filter: blur(8px);

	&__inner {
		text-align: center;
		padding: 32px 40px;
		border-radius: 28px;
		background: rgba($background_second, 0.85);
		backdrop-filter: blur(24px);
		-webkit-backdrop-filter: blur(24px);
		border: 1px solid rgba($white, 0.1);
		box-shadow: 0 20px 60px rgba(0,0,0,0.4), inset 0 1px 0 rgba($white,0.1), 0 0 40px rgba($purple,0.1);
		position: relative;
		overflow: hidden;

		&::before {
			content: '';
			position: absolute;
			top: 0; left: 0; right: 0;
			height: 45%;
			background: linear-gradient(180deg, rgba($white,0.06) 0%, transparent 100%);
			border-radius: 28px 28px 0 0;
			pointer-events: none;
		}
		h3 { color: $white; font-size: 20px; font-weight: 700; margin: 12px 0 6px; }
		p { color: rgba($white,0.6); font-size: 14px; margin: 0; strong { color: $purple; } }
	}

	&__icon {
		width: 64px; height: 64px;
		border-radius: 50%;
		background: linear-gradient(135deg, rgba($purple,0.6), rgba($purple,0.9));
		display: flex; align-items: center; justify-content: center;
		margin: 0 auto;
		color: $white;
		border: 1px solid rgba($white,0.2);
		box-shadow: 0 8px 24px rgba($purple,0.35);
		animation: fIP 0.5s cubic-bezier(0.23,1,0.32,1) both;
		position: relative;
	}
}
@keyframes fIP { 0% { transform:scale(0); opacity:0; } 50% { transform:scale(1.2); } 100% { transform:scale(1); opacity:1; } }

.follow-pop-enter-active { transition: all 0.4s cubic-bezier(0.23,1,0.32,1); }
.follow-pop-leave-active { transition: all 0.3s ease-in; }
.follow-pop-enter-from { opacity: 0; }
.follow-pop-leave-to { opacity: 0; }
</style>