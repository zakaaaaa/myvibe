<template>
	<section class="dashboard" style="height: 100dvh" ref="sectionList" @scroll="handleScroll()">
		<!-- Morphing Blob Background -->
		<div class="blob-container">
			<div class="blob blob-1"></div>
			<div class="blob blob-2"></div>
			<div class="blob blob-3"></div>
		</div>

		<div v-if="!isLoading" class="container searchFriend">
			<!-- Back Button -->
			<div class="back-top">
				<RouterLink :to="back" class="glass-back-btn">
					<fa icon="arrow-left-long" class="text-white" />
				</RouterLink>
			</div>

			<h1 class="title text-center mb-5" style="margin-top: 10px">
				{{ this.title[0] }} <span class="highlight">{{ this.title[1] }}</span>
			</h1>

			<ul class="more_list">
				<li
					v-for="(section, index) in exploreResult"
					:key="section.id"
					class="mb-4 vibe-card-wrapper"
					:style="{ animationDelay: `${index * 0.08}s` }"
					@click="goDetail(section.user.username + '/' + section.id)"
					@touchstart.passive="startLongPress($event, section)"
					@touchend="endLongPress"
					@touchmove="cancelLongPress"
					@mousedown="startLongPress($event, section)"
					@mouseup="endLongPress"
					@mouseleave="cancelLongPress"
				>
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

			<div v-if="isLoadingMore" class="loading-more">
				<div class="loading-dots">
					<span></span><span></span><span></span>
				</div>
			</div>

			<div class="pt-5"></div>
		</div>

		<div v-else class="loading">
			<div class="loading-glass">
				<fa icon="spinner" class="fa-spin-pulse" />
			</div>
		</div>

		<!-- Long Press Popup Overlay -->
		<Transition name="popup-overlay">
			<div v-if="showPopup" class="popup-overlay" @click="closePopup" @touchstart="closePopup"></div>
		</Transition>

		<!-- Long Press Popup Menu -->
		<Transition name="popup-menu">
			<div v-if="showPopup" class="popup-container" :style="popupPosition">
				<div class="popup-preview">
					<div class="popup-preview-image" :style="{ backgroundImage: `url(${selectedVibe?.image_url})` }"></div>
					<div class="popup-preview-info">
						<h4>{{ selectedVibe?.title }}</h4>
						<p>{{ selectedVibe?.comment && selectedVibe.comment.length > 60 ? selectedVibe.comment.slice(0, 60) + '...' : selectedVibe?.comment }}</p>
					</div>
				</div>
				<div class="popup-actions">
					<button class="popup-action" @click.stop="handleAction('detail')">
						<fa :icon="['fas', 'eye']" />
						<span>View Detail</span>
					</button>
					<div class="popup-divider"></div>
					<button class="popup-action" @click.stop="handleAction('share')">
						<fa :icon="['fas', 'share']" />
						<span>Share Vibe</span>
					</button>
					<div class="popup-divider"></div>
					<button class="popup-action popup-action-danger" @click.stop="handleAction('report')">
						<fa :icon="['fas', 'flag']" />
						<span>Report</span>
					</button>
				</div>
			</div>
		</Transition>
	</section>
</template>

<script>
import dashboardService from '@/services/dashboardService';

export default {
	name: 'ShowMoreVibeView',
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
			exploreResult: [],
			showPopup: false,
			selectedVibe: null,
			longPressTimer: null,
			longPressDuration: 500,
			popupPosition: {},
			isLongPressing: false
		};
	},
	mounted() {
		this.getList();
	},
	beforeUnmount() {
		this.cancelLongPress();
	},
	methods: {
		splitString(data) {
			return data.split(' ');
		},

		startLongPress(event, section) {
			this.isLongPressing = false;
			this.longPressTimer = setTimeout(() => {
				this.isLongPressing = true;
				this.selectedVibe = section;

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
				let top = clientY - 200;

				if (top < 20) top = 20;
				if (top + 350 > viewportHeight) top = viewportHeight - 370;

				this.popupPosition = {
					top: `${top}px`,
					left: `${left}px`,
					width: `${popupWidth}px`
				};

				this.showPopup = true;
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
		closePopup() {
			this.showPopup = false;
			setTimeout(() => {
				this.selectedVibe = null;
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
				case 'share':
					if (navigator.share && vibe) {
						navigator.share({
							title: vibe.title,
							text: vibe.comment || '',
							url: window.location.origin + '/' + vibe.user.username + '/' + vibe.id
						}).catch(() => {});
					}
					break;
				case 'report':
					break;
			}
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
				const response = await dashboardService.getFriendsCategory(this.$route.params.username, this.$route.params.id, params);
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
					const response = await dashboardService.getFriendsCategory(this.$route.params.username, this.$route.params.id, params);
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
			if (this.isLongPressing) return;
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

<style scoped>
/* ========== OVERRIDE GLOBAL .back-top (black bar fix) ========== */
.back-top {
	background: transparent !important;
	height: auto !important;
	padding: 12px 0 0 !important;
	position: relative !important;
	z-index: 1017;
}

/* ========== CARD SIZE — reduce 15% ========== */
.more_list .content {
	width: 100% !important;
	margin: 0 auto;
	aspect-ratio: 4/5 !important;
}

/* ========== BLOB BACKGROUND ========== */
.blob-container {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	z-index: 0;
	pointer-events: none;
	overflow: hidden;
}

.blob {
	position: absolute;
	border-radius: 50%;
	filter: blur(80px);
	opacity: 0.35;
	animation: morphBlob 12s ease-in-out infinite alternate;
}

.blob-1 {
	width: 280px;
	height: 280px;
	background: linear-gradient(135deg, #6366f1, #8b5cf6);
	top: -80px;
	right: -60px;
}

.blob-2 {
	width: 220px;
	height: 220px;
	background: linear-gradient(135deg, #ec4899, #8b5cf6);
	bottom: 20%;
	left: -80px;
	animation-delay: -4s;
}

.blob-3 {
	width: 180px;
	height: 180px;
	background: linear-gradient(135deg, #3b82f6, #06b6d4);
	top: 40%;
	right: -40px;
	animation-delay: -8s;
}

@keyframes morphBlob {
	0% {
		border-radius: 40% 60% 60% 40% / 60% 30% 70% 40%;
		transform: translate(0, 0) scale(1);
	}
	33% {
		border-radius: 60% 40% 30% 70% / 50% 60% 40% 50%;
		transform: translate(30px, -30px) scale(1.1);
	}
	66% {
		border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%;
		transform: translate(-20px, 20px) scale(0.95);
	}
	100% {
		border-radius: 50% 40% 60% 50% / 40% 50% 60% 50%;
		transform: translate(10px, -10px) scale(1.05);
	}
}

/* ========== GLASS BACK BUTTON ========== */
.glass-back-btn {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	width: 42px;
	height: 42px;
	margin-left: 20px;
	border-radius: 14px;
	background: rgba(255, 255, 255, 0.08);
	backdrop-filter: blur(20px);
	-webkit-backdrop-filter: blur(20px);
	border: 1px solid rgba(255, 255, 255, 0.12);
	color: #fff;
	text-decoration: none;
	transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
	animation: fadeSlideDown 0.5s ease-out both;
}

.glass-back-btn:active {
	transform: scale(0.95);
}

/* ========== TITLE ========== */
.title {
	animation: fadeSlideDown 0.6s ease-out both;
	animation-delay: 0.1s;
}

.highlight {
	background: linear-gradient(135deg, #a78bfa, #818cf8);
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
	background-clip: text;
}

/* ========== CARD ENTRANCE ANIMATION ========== */
.vibe-card-wrapper {
	animation: cardEntrance 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
}

@keyframes cardEntrance {
	from {
		opacity: 0;
		transform: translateY(40px) scale(0.96);
		filter: blur(4px);
	}
	to {
		opacity: 1;
		transform: translateY(0) scale(1);
		filter: blur(0);
	}
}

@keyframes fadeSlideDown {
	from {
		opacity: 0;
		transform: translateY(-16px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

/* ========== LOADING ========== */
.loading {
	display: flex;
	align-items: center;
	justify-content: center;
	height: 100dvh;
	position: relative;
	z-index: 1;
}

.loading-glass {
	width: 64px;
	height: 64px;
	display: flex;
	align-items: center;
	justify-content: center;
	border-radius: 20px;
	background: rgba(255, 255, 255, 0.08);
	backdrop-filter: blur(20px);
	-webkit-backdrop-filter: blur(20px);
	border: 1px solid rgba(255, 255, 255, 0.1);
	color: #a78bfa;
	font-size: 24px;
	animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
	0%, 100% { opacity: 0.6; transform: scale(1); }
	50% { opacity: 1; transform: scale(1.05); }
}

.loading-more {
	display: flex;
	justify-content: center;
	padding: 20px 0;
}

.loading-dots {
	display: flex;
	gap: 6px;
}

.loading-dots span {
	width: 8px;
	height: 8px;
	border-radius: 50%;
	background: rgba(167, 139, 250, 0.6);
	animation: dotBounce 1.4s ease-in-out infinite;
}

.loading-dots span:nth-child(2) { animation-delay: 0.16s; }
.loading-dots span:nth-child(3) { animation-delay: 0.32s; }

@keyframes dotBounce {
	0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
	40% { transform: scale(1); opacity: 1; }
}

/* ========== POPUP OVERLAY ========== */
.popup-overlay {
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: rgba(0, 0, 0, 0.5);
	backdrop-filter: blur(20px);
	-webkit-backdrop-filter: blur(20px);
	z-index: 100;
}

/* ========== POPUP TRANSITIONS ========== */
.popup-overlay-enter-active {
	transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.popup-overlay-leave-active {
	transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.popup-overlay-enter-from,
.popup-overlay-leave-to {
	opacity: 0;
	backdrop-filter: blur(0);
	-webkit-backdrop-filter: blur(0);
}

.popup-menu-enter-active {
	transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.popup-menu-leave-active {
	transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.popup-menu-enter-from {
	opacity: 0;
	transform: scale(0.8);
	filter: blur(8px);
}
.popup-menu-leave-to {
	opacity: 0;
	transform: scale(0.9);
	filter: blur(4px);
}

/* ========== POPUP CONTAINER ========== */
.popup-container {
	position: fixed;
	z-index: 101;
	display: flex;
	flex-direction: column;
	gap: 10px;
}

/* ========== POPUP PREVIEW — LIQUID GLASS 60% ========== */
.popup-preview {
	border-radius: 20px;
	overflow: hidden;
	background: rgba(20, 20, 40, 0.6);
	backdrop-filter: blur(40px) saturate(1.5);
	-webkit-backdrop-filter: blur(40px) saturate(1.5);
	border: 1px solid rgba(255, 255, 255, 0.15);
	box-shadow:
		0 24px 80px rgba(0, 0, 0, 0.5),
		0 0 0 0.5px rgba(255, 255, 255, 0.06) inset,
		inset 0 2px 0 rgba(255, 255, 255, 0.08),
		inset 0 -1px 0 rgba(0, 0, 0, 0.2);
}

.popup-preview-image {
	width: 100%;
	height: 180px;
	background-size: cover;
	background-position: center;
}

.popup-preview-info {
	padding: 14px 16px;
}

.popup-preview-info h4 {
	font-size: 16px;
	font-weight: 700;
	color: #fff;
	margin: 0;
	line-height: 1.3;
}

.popup-preview-info p {
	font-size: 13px;
	color: rgba(255, 255, 255, 0.45);
	margin: 5px 0 0;
	line-height: 1.4;
	font-weight: 400;
}

/* ========== POPUP ACTIONS — LIQUID GLASS 60% ========== */
.popup-actions {
	display: flex;
	flex-direction: column;
	border-radius: 16px;
	overflow: hidden;
	background: rgba(20, 20, 40, 0.6);
	backdrop-filter: blur(40px) saturate(1.5);
	-webkit-backdrop-filter: blur(40px) saturate(1.5);
	border: 1px solid rgba(255, 255, 255, 0.15);
	box-shadow:
		0 16px 48px rgba(0, 0, 0, 0.4),
		0 0 0 0.5px rgba(255, 255, 255, 0.06) inset,
		inset 0 2px 0 rgba(255, 255, 255, 0.08),
		inset 0 -1px 0 rgba(0, 0, 0, 0.2);
}

.popup-action {
	display: flex;
	align-items: center;
	gap: 12px;
	padding: 14px 16px;
	background: none;
	border: none;
	color: #fff;
	font-size: 15px;
	font-weight: 500;
	cursor: pointer;
	transition: background 0.15s ease;
	text-align: left;
	width: 100%;
}

.popup-action svg {
	width: 18px;
	color: rgba(255, 255, 255, 0.5);
}

.popup-action:active {
	background: rgba(255, 255, 255, 0.1);
}

.popup-action-danger {
	color: #f87171;
}

.popup-action-danger svg {
	color: #f87171;
}

.popup-divider {
	height: 0.5px;
	background: rgba(255, 255, 255, 0.1);
	margin: 0 14px;
}
</style>