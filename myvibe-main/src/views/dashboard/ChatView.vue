<template>
	<div class="container searchFriend">
		<h1 class="title ps-3 mt-4 mb-5"><span class="highlight">Message</span></h1>
		<div v-if="!isLoading" class="explore chat">
			<ul class="friend_list" style="padding-top: 4px">
				<li v-for="section in searchResult" :key="section.friend_id" class="mb-4" @click="goDM(section.friend.username)">
					<img :src="section.profile_picture" alt="" />
					<div class="text">
						<h2>{{ section.friend.name }}</h2>
						<p>
							<small class="text">{{ section.last_message }}</small> <span>{{ formatTimeAgo(section.last_message_at) }}</span>
						</p>
					</div>
				</li>
			</ul>
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
			searchResult: []
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
				}
				this.searchResult = response.data;
				this.isLoading = false;
			} catch (error) {
				console.error('Error fetching list:', error);
				this.isLoading = false;
			}
		},
		formatTimeAgo(dateString) {
			const parsedDate = parseISO(dateString);
			const distance = formatDistanceToNowStrict(parsedDate, { addSuffix: false });
			const [value, unit] = distance.split(' ');
			if (unit === 'second' || unit === 'seconds') {
				return `${value}s`;
			}
			if (unit === 'minute' || unit === 'minutes') {
				return `${value}m`;
			}
			if (unit === 'hour' || unit === 'hours') {
				return `${value}h`;
			}
			if (unit === 'day' || unit === 'days') {
				return `${value}d`;
			}
			if (unit === 'month' || unit === 'months') {
				return `${value}mo`;
			}
			if (unit === 'year' || unit === 'years') {
				return `${value}y`;
			}
			return distance;
		},
		goDM(username) {
			this.$router.push('/message/' + username);
		}
	}
};
</script>
