<template>
	<section class="dashboard">
		<!-- Dashboard Blobs -->
		<div class="dash-blob dash-blob--1"></div>
		<div class="dash-blob dash-blob--2"></div>
		<div class="dash-blob dash-blob--3"></div>
		<div class="dash-blob dash-blob--4"></div>
		<div class="dash-blob dash-blob--5"></div>

		<div v-if="!isLoading">
			<div v-if="isHome">
				<HomeComponent :profile="profile" :home="home" :others="others"></HomeComponent>
			</div>
			<div v-if="isFriend">
				<FriendsComponent></FriendsComponent>
			</div>
			<div v-if="isChat">
				<ChatComponent></ChatComponent>
			</div>
		</div>
		<div v-else>
			<div class="loading">
				<fa icon="spinner" class="fa-spin-pulse" />
			</div>
		</div>
		<ul class="floating_menu">
			<li @click="isActive('home')" :class="{ active: isHome }">
				<fa :icon="['far', 'user']" />
			</li>
			<li @click="isActive('friend')" :class="{ active: isFriend }">
				<fa :icon="['fas', 'magnifying-glass']" />
			</li>
			<li @click="isActive('chat')" :class="{ active: isChat }">
				<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" style="margin-top: -7px">
					<g id="chat_1_line" fill="none" transform="scale(1.2)">
						<path d="M24 0v24H0V0zM12.593 23.258l-.011.002-.071.035-.02.004-.014-.004-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01-.017.428.005.02.01.013.104.074.015.004.012-.004.104-.074.012-.016.004-.017-.017-.427c-.002-.01-.009-.017-.017-.018m.265-.113-.013.002-.185.093-.01.01-.003.011.018.43.005.012.008.007.201.093c.012.004.023 0 .029-.008l.004-.014-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014-.034.614c0 .012.007.02.017.024l.015-.002.201-.093.01-.008.004-.011.017-.43-.003-.012-.01-.01z" />
						<path fill="currentColor" d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10H4a2 2 0 0 1-2-2v-8C2 6.477 6.477 2 12 2m0 2a8 8 0 0 0-8 8v8h8a8 8 0 1 0 0-16m0 10a1 1 0 0 1 .117 1.993L12 16H9a1 1 0 0 1-.117-1.993L9 14zm3-4a1 1 0 1 1 0 2H9a1 1 0 1 1 0-2z" />
					</g>
				</svg>
			</li>
		</ul>

		<!-- Onboarding Tour -->
		<OnboardingTour :show="showOnboarding" @complete="onOnboardingComplete" />
	</section>
</template>

<script>
import HomeComponent from './HomeView.vue';
import FriendsComponent from './FriendsView.vue';
import ChatComponent from './ChatView.vue';
import OnboardingTour from '@/components/OnboardingTour.vue';

import authService from '@/services/authService';
import dashboardService from '@/services/dashboardService';
import logo from '@/assets/avatar.png';

export default {
	name: 'DashboardView',
	components: {
		HomeComponent,
		FriendsComponent,
		ChatComponent,
		OnboardingTour
	},
	data() {
		return {
			currentPosition: 'home',
			isLoading: false,
			isHome: false,
			isFriend: false,
			isChat: false,
			profile: [],
			home: [],
			others: [],
			showOnboarding: false
		};
	},
	mounted() {
		this.getProfile();
		this.getHome();
		this.getOther();
	},
	methods: {
		checkOnboarding() {
			const done = localStorage.getItem('onboarding_done');
			const fromWelcome = this.$route.query.from === 'welcome';
			if (!done || fromWelcome) {
				setTimeout(() => {
					this.showOnboarding = true;
				}, 800);
			}
		},
		onOnboardingComplete() {
			this.showOnboarding = false;
		},
		splitString(data) {
			return data.split(' ');
		},
		async getProfile() {
			this.isLoading = true;
			try {
				const response = await authService.profile();
				this.profile = response.data;
				this.profile.profile_picture = process.env.VUE_APP_API_URL + '/' + this.profile.profile_picture || logo;
				this.isHome = true;
				this.isLoading = false;
				this.checkOnboarding();
			} catch (error) {
				console.error('Error fetching profile:', error);
			} finally {
				const current = this.$route.query.current;
				if (current && current != 'home') {
					this.isActive(current);
				}
			}
		},
		async getHome() {
			this.isLoading = true;
			try {
				const response = await dashboardService.home();
				for (let i = 0; i < response.data.length; i++) {
					response.data[i].title = this.splitString(response.data[i].title);
					if (response.data[i].vibes.length > 0) {
						for (let x = 0; x < response.data[i].vibes.length; x++) {
							if (response.data[i].vibes[x].image_url) {
								if (response.data[i].vibes[x].image_url.startsWith('http://')) {
									response.data[i].vibes[x].image_url = response.data[i].vibes[x].image_url.replace('http://', 'https://');
								}
							}
						}
					}
				}
				this.home = response.data;
				this.isLoading = false;
			} catch (error) {
				console.error('Error fetching home:', error);
			} finally {
				const current = this.$route.query.current;
				if (current && current != 'home') {
					this.isActive(current);
				}
			}
		},
		async getOther() {
			this.isLoading = true;
			try {
				const response = await dashboardService.getOtherVibe();
				const clusteredData = response.data.data.reduce((acc, item) => {
					const categoryId = item.category.id;
					if (!acc[categoryId]) {
						acc[categoryId] = {
							category_id: categoryId,
							category_title: item.category.title,
							vibes: []
						};
					}
					if (item.image_url) {
						if (item.image_url.startsWith('http://')) {
							item.image_url = item.image_url.replace('http://', 'https://');
						}
					}
					acc[categoryId].vibes.push({
						id: item.id,
						title: item.title,
						desc: item.desc,
						image_url: item.image_url,
						rating: item.rating,
						comment: item.comment,
						created_at: item.created_at
					});
					acc[categoryId].vibes.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
					return acc;
				}, {});
				this.others = clusteredData;
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
		async isActive(current) {
			if (current == 'home') {
				this.isHome = true;
				this.isFriend = false;
				this.isChat = false;
				this.$router.push({ path: '/dashboard', query: { current: 'home' } });
			} else if (current == 'friend') {
				this.isHome = false;
				this.isFriend = true;
				this.isChat = false;
				this.$router.push({ path: '/dashboard', query: { current: 'friend' } });
			} else if (current == 'chat') {
				this.isHome = false;
				this.isFriend = false;
				this.isChat = true;
				this.$router.push({ path: '/dashboard', query: { current: 'chat' } });
			}
		}
	}
};
</script>
<style lang="scss" scoped>
@import '@/assets/scss/color.scss';

// === DASHBOARD BLOBS - BIGGER & MORE VISIBLE ===
.dash-blob {
	position: fixed;
	filter: blur(50px);
	pointer-events: none;
	z-index: 0;
}

.dash-blob--1 {
	top: -6%;
	left: -10%;
	width: min(360px, 80vw);
	height: min(360px, 80vw);
	background: radial-gradient(circle, rgba($purple, 0.35) 0%, rgba(#6c5ce7, 0.12) 40%, transparent 70%);
	border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
	animation: dashBlobMorph1 12s ease-in-out infinite;
}

.dash-blob--2 {
	bottom: 4%;
	right: -8%;
	width: min(320px, 72vw);
	height: min(320px, 72vw);
	background: radial-gradient(circle, rgba(#4a3adf, 0.3) 0%, rgba($purple, 0.1) 40%, transparent 70%);
	border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
	animation: dashBlobMorph2 14s ease-in-out infinite;
}

.dash-blob--3 {
	top: 35%;
	left: 50%;
	transform: translateX(-50%);
	width: min(380px, 85vw);
	height: min(200px, 45vw);
	background: radial-gradient(ellipse, rgba($purple, 0.18) 0%, rgba(#7c6cf0, 0.06) 40%, transparent 70%);
	border-radius: 50%;
	animation: dashBlobFloat 10s ease-in-out infinite;
}

.dash-blob--4 {
	top: 60%;
	left: -10%;
	width: min(260px, 58vw);
	height: min(260px, 58vw);
	background: radial-gradient(circle, rgba(#9b8fff, 0.25) 0%, rgba($purple, 0.08) 50%, transparent 70%);
	border-radius: 50% 30% 40% 60% / 40% 60% 50% 50%;
	animation: dashBlobMorph3 16s ease-in-out infinite;
}

.dash-blob--5 {
	bottom: 25%;
	right: -5%;
	width: min(220px, 50vw);
	height: min(220px, 50vw);
	background: radial-gradient(circle, rgba($purple, 0.2) 0%, transparent 70%);
	border-radius: 60% 40% 60% 40% / 50% 50% 50% 50%;
	animation: dashBlobMorph1 18s ease-in-out infinite reverse;
}

@keyframes dashBlobMorph1 {
	0%, 100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(0, 0) rotate(0deg); }
	50% { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; transform: translate(14px, 18px) rotate(40deg); }
}
@keyframes dashBlobMorph2 {
	0%, 100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; transform: translate(0, 0) rotate(0deg); }
	50% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(-16px, -12px) rotate(-40deg); }
}
@keyframes dashBlobFloat {
	0%, 100% { transform: translateX(-50%) translateY(0) scale(1); opacity: 0.18; }
	50% { transform: translateX(-50%) translateY(-16px) scale(1.06); opacity: 0.28; }
}
@keyframes dashBlobMorph3 {
	0%, 100% { border-radius: 50% 30% 40% 60% / 40% 60% 50% 50%; transform: translate(0, 0) rotate(0deg); }
	33% { border-radius: 40% 60% 50% 40% / 60% 40% 60% 50%; transform: translate(12px, -14px) rotate(30deg); }
	66% { border-radius: 60% 40% 60% 40% / 50% 50% 40% 60%; transform: translate(-10px, 12px) rotate(-25deg); }
}

// === SLIDE DOWN ENTRANCE ===
:deep(.vibe-profile) {
	animation: dashSlideDown 0.6s ease-out both;
}

:deep(.vibe-section-preview) {
	animation: dashSlideDown 0.6s ease-out both;

	&:nth-child(1) { animation-delay: 0.1s; }
	&:nth-child(2) { animation-delay: 0.2s; }
	&:nth-child(3) { animation-delay: 0.3s; }
	&:nth-child(4) { animation-delay: 0.4s; }
	&:nth-child(5) { animation-delay: 0.5s; }
	&:nth-child(6) { animation-delay: 0.6s; }
}

:deep(.floating-add-btn) {
	animation: dashFadeUp 0.5s ease-out 0.4s both;
}

:deep(.floating_menu) {
	animation: dashSlideUp 0.5s ease-out 0.3s both;
}

@keyframes dashSlideDown {
	from {
		opacity: 0;
		transform: translateY(-30px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

@keyframes dashFadeUp {
	from {
		opacity: 0;
		transform: translateX(-50%) translateY(20px) scale(0.8);
	}
	to {
		opacity: 1;
		transform: translateX(-50%) translateY(0) scale(1);
	}
}

@keyframes dashSlideUp {
	from {
		opacity: 0;
		transform: translateX(-50%) translateY(40px);
	}
	to {
		opacity: 1;
		transform: translateX(-50%) translateY(0);
	}
}
</style>