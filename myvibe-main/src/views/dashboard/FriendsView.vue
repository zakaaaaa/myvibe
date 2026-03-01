<template>
	<div class="container searchFriend" ref="sectionList" @scroll="handleScroll" style="height: 100dvh; overflow-y: auto">
		<h1 class="title text-center mt-4 mb-5">Find Your <span class="highlight">Friends</span></h1>
		<div class="search-box">
			<input type="text" v-model="searchQuery" placeholder="Search" @click="onFocus()" @keyup="debounceSearch()" />
			<div class="icon" @click="searchFriend()">
				<fa icon="search" />
			</div>
		</div>
		<div v-if="isExplore" class="explore">
			<div class="title">
				<h3>Spotlight</h3>
			</div>
			<ul v-if="!isLoading" class="explore_list">
				<li v-for="section in exploreResult" :key="section.id" class="mb-4">
					<div class="content" :style="{ backgroundImage: `url(${section.image_url})` }" :class="[{ twothree: ['Film', 'Top', 'Reading'].some((word) => section.category.title.includes(word)) }]">
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
		<div v-if="isFriendList" class="explore">
			<ul v-if="!isLoading" class="friend_list" style="padding-top: 4px">
				<li v-for="section in searchResult" :key="section.id" class="mb-4" @click="goProfile(section)">
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
		<div v-if="isRecentFriendList" class="explore">
			<div class="title">
				<h3>Recents</h3>
				<small @click="clearRecent()">Clear History</small>
			</div>
			<ul v-if="!isLoading" class="friend_list">
				<li v-for="(item, index) in recentProfiles" :key="index" class="mb-4" @click="goProfile(item)">
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
