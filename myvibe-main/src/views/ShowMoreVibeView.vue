<template>
	<section class="dashboard" ref="sectionList" @scroll="handleScroll()">
		<!-- Morphing Blob Background -->
		<div class="blob-container">
			<div class="blob blob-1"></div>
			<div class="blob blob-2"></div>
			<div class="blob blob-3"></div>
		</div>

		<div v-if="!isLoading" class="container searchFriend">
			<!-- Glass Back Button -->
			<div class="back-top">
				<RouterLink :to="back" class="glass-back-btn">
					<fa icon="arrow-left-long" class="text-white" />
				</RouterLink>
			</div>

			<h1 class="title text-center mt-4 mb-5" style="margin-top: 10px">
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
					<div class="glass-card">
						<div class="card-image" :style="{ backgroundImage: `url(${section.image_url})` }" :class="[{ twothree: ['Film', 'Top', 'Reading'].some((word) => section.category.title.includes(word)) }]">
						</div>
						<div class="card-bottom">
							<div class="text">
								<h1>{{ section.title }}</h1>
								<h3>{{ section.comment && section.comment.length > 47 ? section.comment.slice(0, 47) + '...' : section.comment }}</h3>
							</div>
							<div class="star-badge">
								<fa :icon="['fas', 'star']" />
								<span>{{ section.rating }}</span>
							</div>
						</div>
					</div>
				</li>
			</ul>

			<!-- Loading More Indicator -->
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
			<div v-if="showPopup" class="popup-overlay" @click="closePopup" @touchstart="closePopup">
			</div>
		</Transition>

		<!-- Long Press Popup Menu (WhatsApp iOS style) -->
		<Transition name="popup-menu">
			<div v-if="showPopup" class="popup-container" :style="popupPosition">
				<!-- Preview Card -->
				<div class="popup-preview glass-popup">
					<div class="popup-preview-image" :style="{ backgroundImage: `url(${selectedVibe?.image_url})` }"></div>
					<div class="popup-preview-info">
						<h4>{{ selectedVibe?.title }}</h4>
						<p>{{ selectedVibe?.comment && selectedVibe.comment.length > 60 ? selectedVibe.comment.slice(0, 60) + '...' : selectedVibe?.comment }}</p>
					</div>
				</div>
				<!-- Action Menu -->
				<div class="popup-actions glass-popup">
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
			// Long press popup
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

		// ---- Long Press Handlers ----
		startLongPress(event, section) {
			this.isLongPressing = false;
			this.longPressTimer = setTimeout(() => {
				this.isLongPressing = true;
				this.selectedVibe = section;

				// Haptic feedback (if supported)
				if (navigator.vibrate) {
					navigator.vibrate(10);
				}

				// Calculate popup position
				const viewportHeight = window.innerHeight;
				const viewportWidth = window.innerWidth;
				let clientY, clientX;

				if (event.touches) {
					clientY = event.touches[0].clientY;
					clientX = event.touches[0].clientX;
				} else {
					clientY = event.clientY;
					clientX = event.clientX;
				}

				// Position popup centered, avoid going off screen
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
			// If it was a long press, prevent the click navigation
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
					// Handle report
					break;
			}
		},

		// ---- Data Fetching ----
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
/* ========== BASE & BACKGROUND ========== */
.dashboard {
	height: 100dvh;
	overflow-y: auto;
	position: relative;
	background: #0a0a1a;
	-webkit-overflow-scrolling: touch;
}

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
	opacity: 0.4;
	animation: morphBlob 12s ease-in-out infinite alternate;
}

.blob-1 {
	width: 300px;
	height: 300px;
	background: linear-gradient(135deg, #6366f1, #8b5cf6);
	top: -80px;
	right: -60px;
	animation-delay: 0s;
}

.blob-2 {
	width: 250px;
	height: 250px;
	background: linear-gradient(135deg, #ec4899, #8b5cf6);
	bottom: 20%;
	left: -80px;
	animation-delay: -4s;
}

.blob-3 {
	width: 200px;
	height: 200px;
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

/* ========== CONTAINER & LAYOUT ========== */
.container.searchFriend {
	position: relative;
	z-index: 1;
	padding: 0 16px;
}

/* ========== GLASS BACK BUTTON ========== */
.back-top {
	padding-top: 16px;
}

.glass-back-btn {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	width: 42px;
	height: 42px;
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

.glass-back-btn:hover {
	background: rgba(255, 255, 255, 0.14);
	transform: scale(1.05);
}

.glass-back-btn:active {
	transform: scale(0.95);
}

/* ========== TITLE ========== */
.title {
	font-size: 26px;
	font-weight: 700;
	color: #fff;
	letter-spacing: -0.02em;
	animation: fadeSlideDown 0.6s ease-out both;
	animation-delay: 0.1s;
}

.highlight {
	background: linear-gradient(135deg, #a78bfa, #818cf8);
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
	background-clip: text;
}

/* ========== CARD LIST ========== */
.more_list {
	list-style: none;
	padding: 0;
	margin: 0;
}

/* ========== CARD ENTRANCE ANIMATION ========== */
.vibe-card-wrapper {
	animation: cardEntrance 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
}

@keyframes cardEntrance {
	from {
		opacity: 0;
		transform: translateY(40px) scale(0.95);
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

/* ========== GLASS CARD ========== */
.glass-card {
	border-radius: 20px;
	overflow: hidden;
	background: rgba(255, 255, 255, 0.06);
	backdrop-filter: blur(24px);
	-webkit-backdrop-filter: blur(24px);
	border: 1px solid rgba(255, 255, 255, 0.1);
	box-shadow:
		0 8px 32px rgba(0, 0, 0, 0.3),
		inset 0 1px 0 rgba(255, 255, 255, 0.1);
	transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
	cursor: pointer;
	position: relative;
}

.glass-card::before {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	height: 1px;
	background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
	z-index: 1;
}

.glass-card:hover {
	transform: translateY(-4px);
	border-color: rgba(255, 255, 255, 0.18);
	box-shadow:
		0 16px 48px rgba(0, 0, 0, 0.4),
		inset 0 1px 0 rgba(255, 255, 255, 0.15);
}

.glass-card:active {
	transform: scale(0.98);
	transition-duration: 0.15s;
}

/* ========== CARD IMAGE ========== */
.card-image {
	width: 100%;
	aspect-ratio: 1 / 1;
	background-size: cover;
	background-position: center;
	position: relative;
}

.card-image.twothree {
	aspect-ratio: 2 / 3;
}

.card-image::after {
	content: '';
	position: absolute;
	bottom: 0;
	left: 0;
	right: 0;
	height: 50%;
	background: linear-gradient(to top, rgba(10, 10, 26, 0.95), transparent);
	pointer-events: none;
}

/* ========== CARD BOTTOM ========== */
.card-bottom {
	display: flex;
	align-items: flex-end;
	justify-content: space-between;
	padding: 16px 18px;
	position: relative;
	margin-top: -60px;
	z-index: 2;
}

.card-bottom .text h1 {
	font-size: 17px;
	font-weight: 700;
	color: #fff;
	margin: 0;
	line-height: 1.3;
	letter-spacing: -0.01em;
	text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
}

.card-bottom .text h3 {
	font-size: 13px;
	color: rgba(255, 255, 255, 0.55);
	margin: 4px 0 0;
	font-weight: 400;
	line-height: 1.4;
}

/* ========== STAR BADGE ========== */
.star-badge {
	display: flex;
	align-items: center;
	gap: 5px;
	padding: 6px 12px;
	border-radius: 12px;
	background: rgba(139, 92, 246, 0.25);
	backdrop-filter: blur(12px);
	-webkit-backdrop-filter: blur(12px);
	border: 1px solid rgba(139, 92, 246, 0.3);
	flex-shrink: 0;
}

.star-badge svg {
	color: #a78bfa;
	font-size: 13px;
}

.star-badge span {
	color: #fff;
	font-size: 14px;
	font-weight: 700;
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
	background: rgba(0, 0, 0, 0.6);
	backdrop-filter: blur(12px);
	-webkit-backdrop-filter: blur(12px);
	z-index: 100;
}

/* ========== POPUP TRANSITIONS ========== */
.popup-overlay-enter-active {
	transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.popup-overlay-leave-active {
	transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
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
	transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.popup-menu-enter-from {
	opacity: 0;
	transform: scale(0.85);
	filter: blur(4px);
}
.popup-menu-leave-to {
	opacity: 0;
	transform: scale(0.92);
	filter: blur(2px);
}

/* ========== POPUP CONTAINER ========== */
.popup-container {
	position: fixed;
	z-index: 101;
	display: flex;
	flex-direction: column;
	gap: 10px;
}

.glass-popup {
	border-radius: 16px;
	background: rgba(30, 30, 50, 0.85);
	backdrop-filter: blur(40px);
	-webkit-backdrop-filter: blur(40px);
	border: 1px solid rgba(255, 255, 255, 0.12);
	box-shadow:
		0 24px 80px rgba(0, 0, 0, 0.5),
		inset 0 1px 0 rgba(255, 255, 255, 0.1);
	overflow: hidden;
}

/* ========== POPUP PREVIEW ========== */
.popup-preview-image {
	width: 100%;
	height: 160px;
	background-size: cover;
	background-position: center;
}

.popup-preview-info {
	padding: 12px 16px;
}

.popup-preview-info h4 {
	font-size: 15px;
	font-weight: 700;
	color: #fff;
	margin: 0;
	line-height: 1.3;
}

.popup-preview-info p {
	font-size: 12px;
	color: rgba(255, 255, 255, 0.5);
	margin: 4px 0 0;
	line-height: 1.4;
}

/* ========== POPUP ACTIONS ========== */
.popup-actions {
	display: flex;
	flex-direction: column;
}

.popup-action {
	display: flex;
	align-items: center;
	gap: 12px;
	padding: 13px 16px;
	background: none;
	border: none;
	color: #fff;
	font-size: 15px;
	font-weight: 500;
	cursor: pointer;
	transition: background 0.2s ease;
	text-align: left;
	width: 100%;
}

.popup-action svg {
	width: 18px;
	color: rgba(255, 255, 255, 0.6);
}

.popup-action:active {
	background: rgba(255, 255, 255, 0.08);
}

.popup-action-danger {
	color: #f87171;
}

.popup-action-danger svg {
	color: #f87171;
}

.popup-divider {
	height: 1px;
	background: rgba(255, 255, 255, 0.08);
	margin: 0 12px;
}
</style>