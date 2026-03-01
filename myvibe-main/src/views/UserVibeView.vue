<template>
	<section class="dashboard">
		<div v-if="!isLoading" class="container">
			<div class="back-top d-flex justify-content-between">
				<RouterLink to="/dashboard"> <fa icon="arrow-left-long" class="text-white" /></RouterLink>
				<div style="width: 24px; height: 24px; display: flex; justify-content: end; align-items: center" data-bs-toggle="modal" data-bs-target="#reportModal"><fa icon="ellipsis-vertical" class="text-white" /></div>
			</div>
			<div class="vibe-profile mt-4">
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
			<div v-for="section in vibe_category" :key="section.id" class="vibe-section-preview mb-1">
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
			<div v-for="section in others" :key="section.id" class="vibe-section-preview mb-1">
				<div v-if="section.vibes.length > 0" class="content">
					<div class="head">
						<h1 class="title">
							{{ section.title }}
						</h1>
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
			<li @click="isActive('home', '/dashboard')">
				<fa :icon="['far', 'user']" />
			</li>
			<li @click="isActive('friend', '/dashboard')">
				<fa :icon="['fas', 'magnifying-glass']" />
			</li>
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
						<div class="title">
							<h1>{{ profile.username }}</h1>
						</div>
						<ul class="btn-list">
							<li>
								<div class="form-check">
									<input class="form-check-input" type="radio" name="accountAction" id="blockAccount" value="block" v-model="selectedAction" />
									<label class="form-check-label" for="blockAccount">Block Account</label>
								</div>
							</li>
							<li>
								<div class="form-check">
									<input class="form-check-input" type="radio" name="accountAction" id="reportAccount" value="report" v-model="selectedAction" />
									<label class="form-check-label" for="reportAccount">Report Account</label>
								</div>
							</li>
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
					<div class="modal-button" v-if="selectedAction === 'block'">
						<button class="btn-block text-danger" @click="blockAccount()">Block</button>
					</div>
					<div class="modal-button">
						<button type="button" class="btn-block" data-bs-dismiss="modal" aria-label="Close" ref="reportModalDismiss">Close</button>
					</div>
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
			this.$router.push({
				path: route,
				query: { current }
			});
		},
		async follow() {
			try {
				dashboardService.followFriends(this.profile.id);
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
				if (backdrop) {
					backdrop.remove();
				}
			}
		}
	}
};
</script>
