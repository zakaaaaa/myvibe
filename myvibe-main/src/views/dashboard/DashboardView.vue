<template>
	<section class="dashboard">
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
	</section>
</template>

<script>
import HomeComponent from './HomeView.vue';
import FriendsComponent from './FriendsView.vue';
import ChatComponent from './ChatView.vue';

import authService from '@/services/authService';
import dashboardService from '@/services/dashboardService';
import logo from '@/assets/avatar.png';

export default {
	name: 'DashboardView',
	components: {
		HomeComponent,
		FriendsComponent,
		ChatComponent
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
			others: []
		};
	},
	mounted() {
		this.getProfile();
		this.getHome();
		this.getOther();
	},
	methods: {
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
				this.$router.push({
					path: '/dashboard',
					query: { current: 'home' }
				});
			} else if (current == 'friend') {
				this.isHome = false;
				this.isFriend = true;
				this.isChat = false;
				this.$router.push({
					path: '/dashboard',
					query: { current: 'friend' }
				});
			} else if (current == 'chat') {
				this.isHome = false;
				this.isFriend = false;
				this.isChat = true;
				this.$router.push({
					path: '/dashboard',
					query: { current: 'chat' }
				});
			}
		}
	}
};
</script>
