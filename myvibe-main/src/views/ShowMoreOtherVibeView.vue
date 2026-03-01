<template>
	<section class="dashboard" style="height: 100dvh" ref="sectionList" @scroll="handleScroll()">
		<div v-if="!isLoading" class="container searchFriend">
			<div class="back-top">
				<RouterLink :to="back" style="margin-left: 20px"> <fa icon="arrow-left-long" class="text-white" /></RouterLink>
			</div>
			<h1 class="title text-center mt-4 mb-5" style="margin-top: 10px">
				{{ this.title[0] }} <span class="highlight">{{ this.title[1] }}</span>
			</h1>
			<ul class="more_list">
				<li v-for="section in exploreResult" :key="section.id" class="mb-4" @click="goDetail(section.user.username + '/other/' + section.id)">
					<div class="content" :style="{ backgroundImage: `url(${section.image_url})` }" :class="[{ twothree: ['Film', 'Top', 'Reading'].some((word) => section.category.title.includes(word)) }]">
						<div class="bottom">
							<div class="text">
								<h1>{{ section.title }}</h1>
								<h3>{{ section.comment && section.comment.length > '47' ? section.comment.slice(0, 47) + '...' : section.comment }}</h3>
							</div>
							<div class="star">
								<fa :icon="['fas', 'star']" />
								<span>{{ section.rating }}</span>
							</div>
						</div>
					</div>
				</li>
			</ul>
			<div class="pt-5"></div>
		</div>
		<div v-else class="loading">
			<fa icon="spinner" class="fa-spin-pulse" />
		</div>
	</section>
</template>

<script>
import dashboardService from '@/services/dashboardService';

export default {
	name: 'ShowMoreOtherVibeView',
	data() {
		return {
			isLoading: false,
			isLoadingMore: false,
			hasCalledLoopList: false,
			scrollTimeout: null,
			title: [],
			next: false,
			nextPage: '',
			back: '',
			exploreResult: []
		};
	},
	mounted() {
		this.getList();
	},
	methods: {
		splitString(data) {
			return data.split(' ');
		},
		async getList() {
			this.isLoading = true;
			try {
				const params = {
					pagination: true,
					sort_order: 'desc',
					sort_column: 'rating',
					per_page: 10
				};
				this.back = '/' + this.$route.params.username;
				const response = await dashboardService.getMoreOtherUserVibe(this.$route.params.username, this.$route.params.id, params);
				this.title = this.splitString(response.data.data[0].category.title);
				if (response.data.next_page) {
					this.next = true;
					this.nextPage = response.data.next_page;
				}
				this.exploreResult = response.data.data;
				this.isLoading = false;
			} catch (error) {
				console.error('Error fetching list:', error);
			}
		},
		async loopList() {
			if (this.next) {
				try {
					const params = {
						pagination: true,
						sort_order: 'desc',
						sort_column: 'rating',
						per_page: 10,
						page: this.nextPage
					};
					const response = await dashboardService.getMoreOtherUserVibe(this.$route.params.username, this.$route.params.id, params);
					if (response.data.next_page) {
						this.next = true;
						this.nextPage = response.data.next_page;
					} else {
						this.next = false;
					}
					this.exploreResult.push(response.data);
				} catch (error) {
					console.error('Error fetching list:', error);
				}
			}
		},
		goDetail(path) {
			this.$router.push('/' + path);
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
