<template>
	<div class="container searchFriend" ref="chatContainer">
		<!-- Blobs -->
		<div class="chat-blob chat-blob--1"></div>
		<div class="chat-blob chat-blob--2"></div>
		<div class="chat-blob chat-blob--3"></div>

		<h1 class="title ps-3 mt-4 mb-5 anim-title"><span class="highlight">Message</span></h1>

		<div v-if="!isLoading" class="explore chat">
			<ul class="friend_list" style="padding-top: 4px">
				<li
					v-for="(section, index) in searchResult"
					:key="section.friend_id"
					class="mb-4 anim-chat-item chat-swipe-wrapper"
					:style="{ animationDelay: (index * 0.06) + 's' }"
				>
					<!-- Swipe delete background -->
					<div class="swipe-delete-bg" :class="{ 'swipe-delete-bg--visible': section.swipeX < -10 }">
						<div class="swipe-delete-icon">
							<fa icon="trash" />
							<span>Delete</span>
						</div>
					</div>

					<!-- Chat item (swipeable) -->
					<div
						class="chat-item-content"
						@touchstart="onTouchStart($event, index)"
						@touchmove="onTouchMove($event, index)"
						@touchend="onTouchEnd($event, index)"
						:style="{ transform: `translateX(${section.swipeX || 0}px)`, transition: section.isSwiping ? 'none' : 'transform 0.3s cubic-bezier(0.23,1,0.32,1)' }"
						@click="handleItemClick(section)"
					>
						<img :src="section.profile_picture" alt="" />
						<div class="text">
							<h2>{{ section.friend.name }}</h2>
							<p>
								<small class="text">{{ section.last_message }}</small> <span>{{ formatTimeAgo(section.last_message_at) }}</span>
							</p>
						</div>
					</div>

					<!-- Delete confirm overlay -->
					<transition name="delete-confirm">
						<div v-if="section.showDeleteConfirm" class="delete-confirm-overlay" @click.stop>
							<p>Delete conversation?</p>
							<div class="delete-confirm-actions">
								<button class="btn-cancel" @click="cancelDelete(index)">Cancel</button>
								<button class="btn-delete" @click="confirmDelete(index, section.friend_id)">Delete</button>
							</div>
						</div>
					</transition>
				</li>
			</ul>

			<!-- Empty state -->
			<div v-if="searchResult.length === 0" class="empty-state anim-empty">
				<div class="empty-state__icon">
					<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none">
						<path d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10H4a2 2 0 0 1-2-2v-8C2 6.477 6.477 2 12 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
						<path d="M8 10h8M8 14h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
					</svg>
				</div>
				<p>No conversations yet</p>
			</div>
		</div>
		<div v-else>
			<div class="loading">
				<fa icon="spinner" class="fa-spin-pulse" />
			</div>
		</div>
	</div>
</template>

<script>
import { formatDistanceToNowStrict, parseISO } from 'date-fns';
import dashboardService from '@/services/dashboardService';
import avatar from '@/assets/avatar.png';

export default {
	name: 'ChatView',
	data() {
		return {
			isLoading: false,
			searchResult: [],
			touchStartX: 0,
			touchStartY: 0,
			swipeThreshold: 80,
			deleteThreshold: 120
		};
	},
	mounted() {
		this.getList();
	},
	methods: {
		async getList() {
			this.isLoading = true;
			try {
				const response = await dashboardService.getListChatMessage();
				for (let i = 0; i < response.data.length; i++) {
					if (response.data[i].friend.profile_picture) {
						response.data[i].profile_picture = process.env.VUE_APP_API_URL + '/' + response.data[i].friend.profile_picture;
					} else {
						response.data[i].profile_picture = avatar;
					}
					// Add swipe state
					response.data[i].swipeX = 0;
					response.data[i].isSwiping = false;
					response.data[i].showDeleteConfirm = false;
				}
				this.searchResult = response.data;
				this.isLoading = false;
			} catch (error) {
				console.error('Error fetching list:', error);
				this.isLoading = false;
			}
		},

		// === SWIPE HANDLERS ===
		onTouchStart(e, index) {
			this.touchStartX = e.touches[0].clientX;
			this.touchStartY = e.touches[0].clientY;
			this.searchResult[index].isSwiping = true;
		},
		onTouchMove(e, index) {
			const deltaX = e.touches[0].clientX - this.touchStartX;
			const deltaY = e.touches[0].clientY - this.touchStartY;

			// Only swipe left, ignore vertical scroll
			if (Math.abs(deltaY) > Math.abs(deltaX)) return;

			if (deltaX < 0) {
				// Clamp to max -140px
				const clampedX = Math.max(deltaX, -140);
				this.searchResult[index].swipeX = clampedX;
			} else if (this.searchResult[index].swipeX < 0) {
				// Allow swiping back right
				const newX = this.searchResult[index].swipeX + (e.touches[0].clientX - this.touchStartX);
				this.searchResult[index].swipeX = Math.min(0, newX);
			}
		},
		onTouchEnd(e, index) {
			this.searchResult[index].isSwiping = false;

			if (this.searchResult[index].swipeX < -this.deleteThreshold) {
				// Show delete confirm
				this.searchResult[index].swipeX = -140;
				this.searchResult[index].showDeleteConfirm = true;
			} else if (this.searchResult[index].swipeX < -this.swipeThreshold) {
				// Snap to show delete button
				this.searchResult[index].swipeX = -100;
			} else {
				// Snap back
				this.searchResult[index].swipeX = 0;
			}
		},
		handleItemClick(section) {
			if (section.swipeX < -20) {
				// If swiped, snap back instead of navigating
				section.swipeX = 0;
				section.showDeleteConfirm = false;
				return;
			}
			this.goDM(section.friend.username);
		},
		cancelDelete(index) {
			this.searchResult[index].swipeX = 0;
			this.searchResult[index].showDeleteConfirm = false;
		},
		async confirmDelete(index, friendId) {
			try {
				await dashboardService.deleteChatConversation(friendId);
			} catch (error) {
				console.error('Error deleting conversation:', error);
			}
			// Animate out
			this.searchResult[index].swipeX = -500;
			setTimeout(() => {
				this.searchResult.splice(index, 1);
			}, 300);
		},

		formatTimeAgo(dateString) {
			const parsedDate = parseISO(dateString);
			const distance = formatDistanceToNowStrict(parsedDate, { addSuffix: false });
			const [value, unit] = distance.split(' ');
			if (unit === 'second' || unit === 'seconds') return `${value}s`;
			if (unit === 'minute' || unit === 'minutes') return `${value}m`;
			if (unit === 'hour' || unit === 'hours') return `${value}h`;
			if (unit === 'day' || unit === 'days') return `${value}d`;
			if (unit === 'month' || unit === 'months') return `${value}mo`;
			if (unit === 'year' || unit === 'years') return `${value}y`;
			return distance;
		},
		goDM(username) {
			this.$router.push('/message/' + username);
		}
	}
};
</script>

<style lang="scss" scoped>
@import '@/assets/scss/color.scss';

// === BLOBS ===
.chat-blob {
	position: fixed;
	filter: blur(55px);
	pointer-events: none;
	z-index: 0;
}
.chat-blob--1 {
	top: -6%;
	right: -14%;
	width: min(250px, 58vw);
	height: min(250px, 58vw);
	background: radial-gradient(circle, rgba($purple, 0.2) 0%, rgba(#6c5ce7, 0.06) 40%, transparent 70%);
	border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
	animation: cBm1 14s ease-in-out infinite;
}
.chat-blob--2 {
	bottom: 10%;
	left: -12%;
	width: min(210px, 48vw);
	height: min(210px, 48vw);
	background: radial-gradient(circle, rgba(#4a3adf, 0.16) 0%, rgba($purple, 0.04) 40%, transparent 70%);
	border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
	animation: cBm2 17s ease-in-out infinite;
}
.chat-blob--3 {
	top: 40%;
	left: 50%;
	transform: translateX(-50%);
	width: min(260px, 60vw);
	height: min(120px, 28vw);
	background: radial-gradient(ellipse, rgba($purple, 0.06) 0%, transparent 70%);
	border-radius: 50%;
	animation: cBf 11s ease-in-out infinite;
}
@keyframes cBm1 {
	0%, 100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(0,0) rotate(0deg); }
	50% { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; transform: translate(-10px,14px) rotate(28deg); }
}
@keyframes cBm2 {
	0%, 100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; transform: translate(0,0) rotate(0deg); }
	50% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(12px,-10px) rotate(-28deg); }
}
@keyframes cBf {
	0%, 100% { transform: translateX(-50%) translateY(0) scale(1); opacity: 0.06; }
	50% { transform: translateX(-50%) translateY(-14px) scale(1.04); opacity: 0.11; }
}

// === ENTRANCE ANIMATIONS ===
.anim-title {
	animation: cDown 0.5s cubic-bezier(0.23,1,0.32,1) both;
}
.anim-chat-item {
	animation: cSlideIn 0.45s ease-out both;
}
.anim-empty {
	animation: cFadeUp 0.6s cubic-bezier(0.23,1,0.32,1) 0.2s both;
}
@keyframes cDown { from { opacity:0; transform:translateY(-18px); } to { opacity:1; transform:translateY(0); } }
@keyframes cSlideIn { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
@keyframes cFadeUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }

// === TITLE ===
.title {
	position: relative;
	z-index: 2;
	.highlight {
		color: $purple;
		text-shadow: 0 0 20px rgba($purple, 0.25);
	}
}

// === SWIPE CHAT ITEM ===
.chat-swipe-wrapper {
	position: relative !important;
	overflow: hidden !important;
	border-radius: 60px !important;
}

.swipe-delete-bg {
	position: absolute;
	top: 0;
	right: 0;
	bottom: 0;
	width: 140px;
	background: linear-gradient(90deg, rgba($red, 0.6), rgba($red, 0.8));
	backdrop-filter: blur(12px);
	-webkit-backdrop-filter: blur(12px);
	display: flex;
	align-items: center;
	justify-content: center;
	border-radius: 0 60px 60px 0;
	z-index: 1;
	opacity: 0;
	transition: opacity 0.2s ease;

	&--visible {
		opacity: 1;
	}
}

.swipe-delete-icon {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 4px;
	color: $white;
	font-size: 16px;

	span {
		font-size: 10px;
		font-weight: 600;
	}
}

.chat-item-content {
	position: relative;
	z-index: 2;
	display: flex;
	align-items: center;
	width: 100%;
	background: rgba($white, 0.035);
	backdrop-filter: blur(16px);
	-webkit-backdrop-filter: blur(16px);
	border: 1px solid rgba($white, 0.07);
	padding: 12px 15px;
	border-radius: 60px;
	box-shadow: 0 4px 16px rgba(0,0,0,0.1), inset 0 1px 0 rgba($white,0.05);
	cursor: pointer;
	will-change: transform;

	img {
		width: 50px;
		height: 50px;
		border-radius: 50%;
		overflow: hidden;
		margin-right: 20px;
		border: 1px solid rgba($white, 0.1);
		box-shadow: 0 2px 8px rgba(0,0,0,0.15);
		flex-shrink: 0;
	}

	.text {
		max-width: calc(100% - 75px);
		color: $white1;
		h2 { margin: 0; font-size: 18px; font-weight: 700; }
		p {
			margin: 0; font-size: 13px; font-weight: 300;
			display: flex; align-items: baseline;

			small.text {
				display: -webkit-box;
				-webkit-line-clamp: 1;
				-webkit-box-orient: vertical;
				overflow: hidden;
				width: auto;
				max-width: calc(100% - 25px);
				margin-right: 5px;
			}

			span {
				color: $grey1;
				position: relative;
				padding-left: 10px;
				font-size: 10px;
				flex-shrink: 0;

				&::before {
					position: absolute;
					top: 50%;
					left: 2px;
					transform: translateY(-50%);
					width: 5px;
					height: 5px;
					background: $grey;
					border-radius: 50%;
					content: '';
				}
			}
		}
	}
}

// === DELETE CONFIRM OVERLAY ===
.delete-confirm-overlay {
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	z-index: 10;
	background: rgba($background, 0.85);
	backdrop-filter: blur(16px);
	-webkit-backdrop-filter: blur(16px);
	border: 1px solid rgba($red, 0.2);
	border-radius: 60px;
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 12px;
	padding: 0 20px;

	p {
		color: $white;
		font-size: 13px;
		font-weight: 600;
		margin: 0;
		white-space: nowrap;
	}
}

.delete-confirm-actions {
	display: flex;
	gap: 8px;

	button {
		padding: 6px 16px;
		border-radius: 20px;
		font-size: 12px;
		font-weight: 700;
		border: none;
		cursor: pointer;
		transition: all 0.3s ease;
	}

	.btn-cancel {
		background: rgba($white, 0.06);
		color: $white;
		border: 1px solid rgba($white, 0.1);

		&:hover {
			background: rgba($white, 0.1);
		}
	}

	.btn-delete {
		background: rgba($red, 0.7);
		color: $white;
		border: 1px solid rgba($red, 0.3);
		box-shadow: 0 4px 12px rgba($red, 0.2);

		&:hover {
			background: rgba($red, 0.9);
			box-shadow: 0 6px 16px rgba($red, 0.3);
		}
	}
}

.delete-confirm-enter-active { transition: all 0.3s cubic-bezier(0.23,1,0.32,1); }
.delete-confirm-leave-active { transition: all 0.2s ease-in; }
.delete-confirm-enter-from { opacity: 0; }
.delete-confirm-leave-to { opacity: 0; }

// === EMPTY STATE ===
.empty-state {
	text-align: center;
	padding: 60px 20px;
	position: relative;
	z-index: 2;

	&__icon {
		width: 80px;
		height: 80px;
		border-radius: 50%;
		background: rgba($white, 0.03);
		backdrop-filter: blur(16px);
		-webkit-backdrop-filter: blur(16px);
		border: 1px solid rgba($white, 0.06);
		display: flex;
		align-items: center;
		justify-content: center;
		margin: 0 auto 16px;
		color: rgba($white, 0.25);
	}

	p {
		color: rgba($white, 0.35);
		font-size: 14px;
		margin: 0;
	}
}

// === LOADING ===
.loading {
	display: flex;
	justify-content: center;
	align-items: center;
	height: 40vh;
	color: $purple;
	font-size: 28px;
	filter: drop-shadow(0 0 12px rgba($purple, 0.4));
}
</style>