<template>
	<section class="conversation">
		<!-- Blobs -->
		<div class="cv-blob cv-blob--1"></div>
		<div class="cv-blob cv-blob--2"></div>

		<!-- Bubble action menu -->
		<transition name="bubble-menu">
			<div v-if="showBubbleMenu" class="bubble-menu-overlay" @click="closeBubbleMenu()">
				<div class="bubble-menu" :style="{ top: bubbleMenuY + 'px', left: bubbleMenuX + 'px' }" @click.stop>
					<div class="bubble-menu__item" @click="replyMessage()">
						<fa icon="reply" />
						<span>Reply</span>
					</div>
					<div class="bubble-menu__item" @click="forwardMessage()">
						<fa icon="share" />
						<span>Forward</span>
					</div>
					<div class="bubble-menu__item" @click="copyMessage()">
						<fa icon="copy" />
						<span>Copy</span>
					</div>
				</div>
			</div>
		</transition>

		<!-- Copied toast -->
		<transition name="toast-pop">
			<div v-if="showCopiedToast" class="copied-toast">Copied!</div>
		</transition>

		<div v-if="!isLoading" class="room">
			<div class="header-info anim-header">
				<div class="profile-preview">
					<div class="header-back" @click="backChat('chat', '/dashboard')"><fa icon="arrow-left" /></div>
					<div class="image mx-2" @click="goProfile(profile.username)"><img :src="profile.profile_picture" alt="" /></div>
					<div class="info" @click="goProfile(profile.username)">
						<h1>{{ profile.name }}</h1>
						<p>@{{ profile.username }}</p>
					</div>
					<div class="header-profile-btn" @click="goProfile(profile.username)">
						<fa :icon="['far', 'user']" />
					</div>
				</div>
			</div>
			<div class="chat-container" ref="chatContainer" @scroll="handleScroll">
				<div class="messages">
					<div
						v-for="(message, mIdx) in messages"
						:key="message.id"
						:class="['message', message.sender_id === profile.id ? 'left' : 'right', { 'msg-new': message.isNew }]"
						@touchstart="onBubbleTouchStart($event, message, mIdx)"
						@touchend="onBubbleTouchEnd($event)"
						@contextmenu.prevent="openBubbleMenu($event, message)"
					>
						<!-- Reply preview -->
						<div v-if="message.replyTo" class="reply-preview">
							<span>{{ message.replyTo }}</span>
						</div>
						<p class="text">{{ message.message_text }}</p>
						<span class="timestamp">{{ formatTime(message.created_at) }}</span>
					</div>
				</div>
			</div>

			<!-- Reply bar -->
			<transition name="reply-bar">
				<div v-if="replyingTo" class="reply-bar">
					<div class="reply-bar__content">
						<div class="reply-bar__line"></div>
						<div class="reply-bar__text">
							<small>Replying to</small>
							<p>{{ replyingTo.message_text }}</p>
						</div>
					</div>
					<div class="reply-bar__close" @click="cancelReply()">
						<fa icon="xmark" />
					</div>
				</div>
			</transition>

			<div class="comment-box anim-input">
				<input type="text" v-model="chatQuery" placeholder="Say something here!" @keyup.enter="sendChat(profile.id)" />
				<div class="send-btn" :class="{ 'send-btn--active': chatQuery.trim() }" @click="sendChat(profile.id)">
					<fa :icon="['far', 'paper-plane']" />
				</div>
			</div>
		</div>
		<div v-else class="loading">
			<fa icon="spinner" class="fa-spin-pulse" />
		</div>
	</section>
</template>

<script>
import { parseISO, format, isToday } from 'date-fns';
import dashboardService from '@/services/dashboardService';
import logo from '@/assets/avatar.png';
import { listenForMessages } from '@/services/firebaseMessaging';

export default {
	name: 'ChatUserView',
	data() {
		return {
			isLoading: false,
			chatQuery: '',
			profile: [],
			messages: [],
			conversationInterval: null,
			isScrollingUp: false,
			lastScrollTop: 0,
			scrollTimeout: null,
			isNearBottom: true,
			// Bubble menu
			showBubbleMenu: false,
			bubbleMenuX: 0,
			bubbleMenuY: 0,
			selectedMessage: null,
			longPressTimer: null,
			// Reply
			replyingTo: null,
			// Toast
			showCopiedToast: false
		};
	},
	mounted() {
		listenForMessages();
		this.getUser();
		this.startConversationPolling();
	},
	beforeUnmount() {
		if (this.conversationInterval) clearInterval(this.conversationInterval);
	},
	beforeDestroy() {
		if (this.conversationInterval) clearInterval(this.conversationInterval);
	},
	methods: {
		formatTime(dateString) {
			const parsedDate = parseISO(dateString);
			if (isToday(parsedDate)) return format(parsedDate, 'HH:mm');
			return format(parsedDate, 'dd MMM yyyy, HH:mm');
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
				this.profile = response.data.profile;
				this.profile.mbti = this.profile.mbti?.mbti_name ?? '';
				this.profile.zodiac = this.profile.zodiac?.zodiac_name ?? '';
				this.profile.relationship = this.profile.relationship?.relationship_name ?? '';
				this.getConversation(this.profile.id);
				this.isLoading = false;
			} catch (error) {
				console.error('Error fetching user:', error);
			}
		},
		async getConversation(id) {
			try {
				const response = await dashboardService.getDetailChatMessage(id);
				this.messages = response.data.map(m => ({ ...m, replyTo: m.reply_to_text || null }));
			} catch (error) {
				console.error('Error fetching conversation:', error);
			} finally {
				if (this.isNearBottom) this.scrollToLastMessage();
			}
		},
		scrollToLastMessage() {
			this.$nextTick(() => {
				const c = this.$refs.chatContainer;
				if (c) c.scrollTop = c.scrollHeight;
			});
		},
		async sendChat(id) {
			if (!this.chatQuery.trim()) return;
			const msgText = this.chatQuery;
			const replyText = this.replyingTo ? this.replyingTo.message_text : null;
			const replyId = this.replyingTo ? this.replyingTo.id : null;

			// Optimistic add
			const tempMsg = {
				id: 'temp-' + Date.now(),
				sender_id: null,
				message_text: msgText,
				replyTo: replyText,
				created_at: new Date().toISOString(),
				isNew: true
			};
			this.messages.push(tempMsg);
			this.chatQuery = '';
			this.replyingTo = null;
			this.scrollToLastMessage();

			try {
				const params = {
					receiver_id: id,
					message: msgText,
					reply_to_id: replyId,
					reply_to_text: replyText
				};
				await dashboardService.postMessage(params);
				this.getConversation(id);
			} catch (error) {
				console.error('Error sending:', error);
			}
		},

		// === BUBBLE LONG PRESS / CONTEXT MENU ===
		onBubbleTouchStart(e, message, idx) {
			this.longPressTimer = setTimeout(() => {
				this.openBubbleMenu(e, message);
			}, 500);
		},
		onBubbleTouchEnd() {
			clearTimeout(this.longPressTimer);
		},
		openBubbleMenu(e, message) {
			this.selectedMessage = message;
			const touch = e.touches ? e.touches[0] : e;
			const menuWidth = 160;
			const menuHeight = 140;
			let x = touch.clientX - menuWidth / 2;
			let y = touch.clientY - menuHeight - 10;
			// Keep in bounds
			if (x < 10) x = 10;
			if (x + menuWidth > window.innerWidth - 10) x = window.innerWidth - menuWidth - 10;
			if (y < 10) y = touch.clientY + 20;
			this.bubbleMenuX = x;
			this.bubbleMenuY = y;
			this.showBubbleMenu = true;
		},
		closeBubbleMenu() {
			this.showBubbleMenu = false;
			this.selectedMessage = null;
		},
		replyMessage() {
			this.replyingTo = this.selectedMessage;
			this.closeBubbleMenu();
			// Focus input
			this.$nextTick(() => {
				const input = this.$el.querySelector('.comment-box input');
				if (input) input.focus();
			});
		},
		forwardMessage() {
			// Copy text and go to chat list to pick recipient
			if (this.selectedMessage) {
				navigator.clipboard.writeText(this.selectedMessage.message_text).catch(() => {});
			}
			this.closeBubbleMenu();
			this.$router.push({ path: '/dashboard', query: { current: 'chat' } });
		},
		copyMessage() {
			if (this.selectedMessage) {
				navigator.clipboard.writeText(this.selectedMessage.message_text).then(() => {
					this.showCopiedToast = true;
					setTimeout(() => { this.showCopiedToast = false; }, 1500);
				}).catch(() => {});
			}
			this.closeBubbleMenu();
		},
		cancelReply() {
			this.replyingTo = null;
		},

		backChat(current, route) {
			this.$router.push({ path: route, query: { current } });
		},
		goProfile(username) {
			this.$router.push('/' + username);
		},
		handleScroll(event) {
			const c = event.target;
			const isAtBottom = c.scrollTop + c.clientHeight >= c.scrollHeight - 300;
			this.isNearBottom = isAtBottom;
			this.lastScrollTop = c.scrollTop;
			clearTimeout(this.scrollTimeout);
			this.scrollTimeout = setTimeout(() => { this.isScrollingUp = false; }, 1500);
			if (c.scrollTop < this.lastScrollTop) { this.isScrollingUp = true; } else { this.isScrollingUp = false; }
		},
		startConversationPolling() {
			this.conversationInterval = setInterval(() => {
				if (this.isNearBottom || (this.isScrollingUp && this.lastScrollTop < 50)) {
					this.getConversation(this.profile.id);
				}
			}, 5000);
		}
	}
};
</script>

<style lang="scss" scoped>
@import '@/assets/scss/color.scss';

// === BLOBS ===
.cv-blob {
	position: fixed;
	filter: blur(55px);
	pointer-events: none;
	z-index: 0;
}
.cv-blob--1 {
	top: -5%;
	right: -15%;
	width: min(220px, 50vw);
	height: min(220px, 50vw);
	background: radial-gradient(circle, rgba($purple, 0.15) 0%, transparent 70%);
	border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
	animation: cvB1 14s ease-in-out infinite;
}
.cv-blob--2 {
	bottom: 12%;
	left: -12%;
	width: min(200px, 46vw);
	height: min(200px, 46vw);
	background: radial-gradient(circle, rgba(#4a3adf, 0.12) 0%, transparent 70%);
	border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
	animation: cvB2 17s ease-in-out infinite;
}
@keyframes cvB1 {
	0%, 100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(0,0) rotate(0deg); }
	50% { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; transform: translate(-8px,12px) rotate(25deg); }
}
@keyframes cvB2 {
	0%, 100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; transform: translate(0,0) rotate(0deg); }
	50% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(10px,-8px) rotate(-25deg); }
}

// === ENTRANCE ANIMATIONS ===
.anim-header { animation: cvDown 0.4s cubic-bezier(0.23,1,0.32,1) both; }
.anim-input { animation: cvUp 0.4s cubic-bezier(0.23,1,0.32,1) 0.15s both; }
@keyframes cvDown { from { opacity:0; transform:translateY(-16px); } to { opacity:1; transform:translateY(0); } }
@keyframes cvUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }

// === NEW MESSAGE ANIMATION ===
.msg-new {
	animation: msgPopIn 0.35s cubic-bezier(0.23,1,0.32,1) both;
}
@keyframes msgPopIn {
	from { opacity: 0; transform: translateY(12px) scale(0.95); }
	to { opacity: 1; transform: translateY(0) scale(1); }
}

// === HEADER — GLASS BACK & PROFILE BTN ===
.header-back {
	width: 38px;
	height: 38px;
	display: flex;
	align-items: center;
	justify-content: center;
	border-radius: 50%;
	color: $white;
	font-size: 14px;
	cursor: pointer;
	background: rgba($white, 0.04);
	backdrop-filter: blur(16px);
	-webkit-backdrop-filter: blur(16px);
	border: 1px solid rgba($white, 0.08);
	box-shadow: 0 2px 10px rgba(0,0,0,0.15), inset 0 1px 0 rgba($white,0.06);
	transition: all 0.3s ease;
	flex-shrink: 0;

	&:hover {
		background: rgba($purple, 0.15);
		border-color: rgba($purple, 0.25);
	}
	&:active { transform: scale(0.9); }
}

.header-profile-btn {
	width: 38px;
	height: 38px;
	display: flex;
	align-items: center;
	justify-content: center;
	border-radius: 50%;
	color: $white;
	font-size: 16px;
	cursor: pointer;
	background: rgba($white, 0.04);
	backdrop-filter: blur(16px);
	-webkit-backdrop-filter: blur(16px);
	border: 1px solid rgba($white, 0.08);
	box-shadow: 0 2px 10px rgba(0,0,0,0.15), inset 0 1px 0 rgba($white,0.06);
	transition: all 0.3s ease;
	flex-shrink: 0;

	&:hover {
		background: rgba($white, 0.08);
	}
	&:active { transform: scale(0.9); }
}

// === MESSAGE BUBBLES — GLASS ENHANCED ===
.messages .message {
	position: relative;

	&.left {
		background: rgba($background_second, 0.7) !important;
		backdrop-filter: blur(12px) !important;
		-webkit-backdrop-filter: blur(12px) !important;
		border: 1px solid rgba($white, 0.06) !important;
		box-shadow: 0 2px 10px rgba(0,0,0,0.1), inset 0 1px 0 rgba($white,0.03);
	}

	&.right {
		background: linear-gradient(135deg, rgba($purple, 0.55), rgba($purple, 0.75)) !important;
		backdrop-filter: blur(12px) !important;
		-webkit-backdrop-filter: blur(12px) !important;
		border: 1px solid rgba($white, 0.1) !important;
		box-shadow: 0 4px 16px rgba($purple, 0.2), inset 0 1px 0 rgba($white,0.1);
	}
}

// === REPLY PREVIEW (inside bubble) ===
.reply-preview {
	background: rgba($white, 0.06);
	border-left: 2px solid rgba($purple, 0.6);
	border-radius: 4px;
	padding: 4px 8px;
	margin-bottom: 6px;
	font-size: 11px;
	color: rgba($white, 0.5);
	max-height: 36px;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}

// === REPLY BAR (above input) ===
.reply-bar {
	position: fixed;
	bottom: calc(3dvh + 58px);
	left: 50%;
	transform: translateX(-50%);
	width: calc(100% - 30px);
	max-width: 460px;
	background: rgba($background_second, 0.85);
	backdrop-filter: blur(16px);
	-webkit-backdrop-filter: blur(16px);
	border: 1px solid rgba($white, 0.08);
	border-radius: 16px 16px 0 0;
	padding: 10px 14px;
	display: flex;
	align-items: center;
	justify-content: space-between;
	z-index: 10;

	&__content {
		display: flex;
		align-items: center;
		gap: 10px;
		overflow: hidden;
		flex: 1;
	}

	&__line {
		width: 3px;
		height: 28px;
		background: $purple;
		border-radius: 2px;
		flex-shrink: 0;
	}

	&__text {
		overflow: hidden;
		small { font-size: 10px; color: $purple; font-weight: 600; display: block; }
		p {
			margin: 0; font-size: 12px; color: rgba($white, 0.5);
			white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
			max-width: 260px;
		}
	}

	&__close {
		width: 28px;
		height: 28px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 50%;
		background: rgba($white, 0.06);
		border: 1px solid rgba($white, 0.08);
		color: rgba($white, 0.5);
		font-size: 12px;
		cursor: pointer;
		flex-shrink: 0;
		transition: all 0.3s ease;

		&:hover { background: rgba($white, 0.1); color: $white; }
	}
}

.reply-bar-enter-active { transition: all 0.3s cubic-bezier(0.23,1,0.32,1); }
.reply-bar-leave-active { transition: all 0.2s ease-in; }
.reply-bar-enter-from { opacity: 0; transform: translateX(-50%) translateY(10px); }
.reply-bar-leave-to { opacity: 0; transform: translateX(-50%) translateY(10px); }

// === SEND BUTTON GLASS ===
.comment-box {
	.send-btn {
		width: 36px;
		height: 36px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 50%;
		color: rgba($white, 0.3);
		font-size: 16px;
		cursor: pointer;
		background: rgba($white, 0.04);
		border: 1px solid rgba($white, 0.06);
		transition: all 0.4s cubic-bezier(0.23,1,0.32,1);
		flex-shrink: 0;

		&--active {
			color: $white;
			background: rgba($purple, 0.6);
			border-color: rgba($purple, 0.3);
			box-shadow: 0 4px 14px rgba($purple, 0.25);

			&:hover {
				background: rgba($purple, 0.75);
				transform: scale(1.08);
				box-shadow: 0 6px 20px rgba($purple, 0.35);
			}
		}

		&:active { transform: scale(0.88); }
	}
}

// === BUBBLE ACTION MENU ===
.bubble-menu-overlay {
	position: fixed;
	top: 0; left: 0; right: 0; bottom: 0;
	z-index: 999;
	background: rgba($black, 0.25);
	backdrop-filter: blur(4px);
	-webkit-backdrop-filter: blur(4px);
}

.bubble-menu {
	position: fixed;
	z-index: 1000;
	background: rgba($background_second, 0.92);
	backdrop-filter: blur(24px);
	-webkit-backdrop-filter: blur(24px);
	border: 1px solid rgba($white, 0.1);
	border-radius: 16px;
	padding: 6px;
	box-shadow: 0 12px 40px rgba(0,0,0,0.4), inset 0 1px 0 rgba($white,0.08);
	min-width: 150px;
	overflow: hidden;

	// Glass reflection
	&::before {
		content: '';
		position: absolute;
		top: 0; left: 8%; right: 8%; height: 1px;
		background: linear-gradient(90deg, transparent, rgba($white,0.15), transparent);
		pointer-events: none;
	}

	&__item {
		display: flex;
		align-items: center;
		gap: 10px;
		padding: 11px 14px;
		color: $white;
		font-size: 13px;
		font-weight: 500;
		cursor: pointer;
		border-radius: 10px;
		transition: all 0.2s ease;

		svg { font-size: 14px; color: rgba($white, 0.5); width: 18px; }

		&:hover {
			background: rgba($white, 0.06);
		}

		&:active {
			background: rgba($purple, 0.15);
		}
	}
}

.bubble-menu-enter-active { transition: all 0.25s cubic-bezier(0.23,1,0.32,1); }
.bubble-menu-leave-active { transition: all 0.15s ease-in; }
.bubble-menu-enter-from { opacity: 0; .bubble-menu { transform: scale(0.9); } }
.bubble-menu-leave-to { opacity: 0; }

// === COPIED TOAST ===
.copied-toast {
	position: fixed;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	z-index: 9999;
	background: rgba($background_second, 0.9);
	backdrop-filter: blur(20px);
	-webkit-backdrop-filter: blur(20px);
	border: 1px solid rgba($white, 0.1);
	border-radius: 14px;
	padding: 12px 28px;
	color: $white;
	font-size: 14px;
	font-weight: 600;
	box-shadow: 0 8px 28px rgba(0,0,0,0.3);
}
.toast-pop-enter-active { transition: all 0.3s cubic-bezier(0.23,1,0.32,1); }
.toast-pop-leave-active { transition: all 0.2s ease-in; }
.toast-pop-enter-from { opacity: 0; transform: translate(-50%, -50%) scale(0.85); }
.toast-pop-leave-to { opacity: 0; transform: translate(-50%, -50%) scale(0.9); }

// === LOADING ===
.loading {
	display: flex;
	justify-content: center;
	align-items: center;
	height: 100dvh;
	color: $purple;
	font-size: 28px;
	filter: drop-shadow(0 0 12px rgba($purple, 0.4));
}
</style>