<template>
	<section class="conversation">
		<div v-if="!isLoading" class="room">
			<div class="header-info">
				<div class="profile-preview">
					<div class="mx-2" @click="backChat('chat', '/dashboard')"><fa icon="arrow-left-long" class="text-white" /></div>
					<div class="image mx-2" @click="goProfile(profile.username)"><img :src="profile.profile_picture" alt="" /></div>
					<div class="info" @click="goProfile(profile.username)">
						<h1>{{ profile.name }}</h1>
						<p>@{{ profile.username }}</p>
					</div>
					<div class="icon" @click="backChat('chat', '/dashboard')">
						<fa :icon="['far', 'user']" />
					</div>
				</div>
			</div>
			<div class="chat-container" ref="chatContainer" @scroll="handleScroll">
				<div class="messages">
					<div v-for="message in messages" :key="message.id" :class="['message', message.sender_id === profile.id ? 'left' : 'right']">
						<p class="text">{{ message.message_text }}</p>
						<span class="timestamp">{{ formatTime(message.created_at) }}</span>
					</div>
				</div>
			</div>
			<div class="comment-box">
				<input type="text" v-model="chatQuery" placeholder="Say something here!" />
				<div class="icon" @click="sendChat(profile.id)">
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
			isNearBottom: true // Penanda untuk mengecek apakah pengguna berada di bagian bawah
		};
	},
	mounted() {
		listenForMessages();
		this.getUser();
		this.startConversationPolling();
	},
	beforeUnmount() {
		if (this.conversationInterval) {
			clearInterval(this.conversationInterval);
		}
	},
	beforeDestroy() {
		if (this.conversationInterval) {
			clearInterval(this.conversationInterval);
		}
	},
	methods: {
		formatTime(dateString) {
			const parsedDate = parseISO(dateString);
			if (isToday(parsedDate)) {
				return format(parsedDate, 'HH:mm');
			}
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
				this.profile.mbti = this.profile.mbti.mbti_name;
				this.profile.zodiac = this.profile.zodiac.zodiac_name;
				this.profile.relationship = this.profile.relationship.relationship_name;
				this.getConversation(this.profile.id);
				this.isLoading = false;
			} catch (error) {
				console.error('Error fetching user:', error);
			}
		},
		async getConversation(id) {
			try {
				const response = await dashboardService.getDetailChatMessage(id);
				this.messages = response.data;
			} catch (error) {
				console.error('Error fetching user:', error);
			} finally {
				this.scrollToLastMessage();
			}
		},
		scrollToLastMessage() {
			this.$nextTick(() => {
				const chatContainer = this.$refs.chatContainer;
				if (chatContainer) {
					chatContainer.scrollTop = chatContainer.scrollHeight;
				}
			});
		},
		async sendChat(id) {
			if (!this.chatQuery.trim()) return;
			try {
				const params = {
					receiver_id: id,
					message: this.chatQuery
				};
				const response = await dashboardService.postMessage(params);
				this.chatQuery = '';
				this.getConversation(id);
			} catch (error) {
				console.error('Error fetching user:', error);
			}
		},
		backChat(current, route) {
			this.$router.push({
				path: route,
				query: { current }
			});
		},
		goProfile(username) {
			this.$router.push('/' + username);
		},
		handleScroll(event) {
			const chatContainer = event.target;
			const isAtBottom = chatContainer.scrollTop + chatContainer.clientHeight >= chatContainer.scrollHeight - 300; // 300px dari bagian bawah

			if (isAtBottom) {
				this.isNearBottom = true;
			} else {
				this.isNearBottom = false;
			}

			this.lastScrollTop = chatContainer.scrollTop;
			clearTimeout(this.scrollTimeout);
			this.scrollTimeout = setTimeout(() => {
				this.isScrollingUp = false;
			}, 1500);

			if (chatContainer.scrollTop < this.lastScrollTop) {
				this.isScrollingUp = true;
			} else {
				this.isScrollingUp = false;
			}
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
