<template>
	<section class="dashboard" style="height: 100dvh" ref="sectionList" @scroll="handleScroll()">
		<div v-if="!isLoading" class="container searchFriend">
			<div class="back-top">
				<RouterLink to="/dashboard" style="margin-left: 20px"> <fa icon="arrow-left-long" class="text-white" /></RouterLink>
			</div>
			<h1 class="title text-center mt-4 mb-5" style="margin-top: 10px">
				{{ this.title }}
			</h1>
			<ul class="more_list">
				<li v-for="section in exploreResult" :key="section.id" class="mb-4" @click="goDetail(section.user.username + '/' + section.id)">
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
			<RouterLink to="/add" class="floating-add-btn" style="bottom: 40px">
				<svg xmlns="http://www.w3.org/2000/svg" width="50px" height="50px" viewBox="0 0 24 24">
					<title>add_circle_fill</title>
					<g id="add_circle_fill" fill="none">
						<path d="M24 0v24H0V0zM12.593 23.258l-.011.002-.071.035-.02.004-.014-.004-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01-.017.428.005.02.01.013.104.074.015.004.012-.004.104-.074.012-.016.004-.017-.017-.427c-.002-.01-.009-.017-.017-.018m.265-.113-.013.002-.185.093-.01.01-.003.011.018.43.005.012.008.007.201.093c.012.004.023 0 .029-.008l.004-.014-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014-.034.614c0 .012.007.02.017.024l.015-.002.201-.093.01-.008.004-.011.017-.43-.003-.012-.01-.01z" />
						<path fill="currentColor" d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2m0 5a1 1 0 0 0-.993.883L11 8v3H8a1 1 0 0 0-.117 1.993L8 13h3v3a1 1 0 0 0 1.993.117L13 16v-3h3a1 1 0 0 0 .117-1.993L16 11h-3V8a1 1 0 0 0-1-1" />
					</g>
				</svg>
			</RouterLink>
		</div>
		<div v-else class="loading">
			<fa icon="spinner" class="fa-spin-pulse" />
		</div>
	</section>
</template>

<script>
import dashboardService from '@/services/dashboardService';

export default {
	name: 'HomeMoreView',
	data() {
		return {
			isLoading: false,
			isLoadingMore: false,
			hasCalledLoopList: false,
			scrollTimeout: null,
			title: [],
			next: false,
			nextPage: '',
			exploreResult: []
		};
	},
	mounted() {
		this.getList();
	},
	methods: {
		async getList() {
			this.isLoading = true;
			try {
				const params = {
					pagination: true,
					sort_column: 'rating',
					sort_order: 'desc',
					per_page: 10
				};
				const response = await dashboardService.getOtherMoreList(params, this.$route.params.id);
				this.title = response.data.data[0].category.title;
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
			if (this.next && this.nextPage) {
				this.next = false;
				try {
					const params = {
						pagination: true,
						sort_column: 'rating',
						sort_order: 'desc',
						per_page: 10,
						page: this.nextPage
					};
					const response = await dashboardService.getOtherMoreList(params, this.$route.params.id);
					if (response.data.next_page) {
						this.next = true;
						this.nextPage = response.data.next_page;
					} else {
						this.next = false;
					}
					this.exploreResult.push(...response.data.data);
				} catch (error) {
					console.error('Error fetching list:', error);
				}
			}
		},
		goDetail(path) {
			const segment = path.split('/').filter(Boolean);
			this.$router.push('/' + segment[0] + '/other/' + segment[1]);
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
