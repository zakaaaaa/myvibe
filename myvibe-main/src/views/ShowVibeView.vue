<template>
	<div>
		<div class="back-button-vibes" @click="goBack()"><fa icon="arrow-left-long" class="text-white" /></div>
		<section class="vibes vibes-canvas" ref="vibesCanvas" style="overflow: hidden">
			<div v-if="!isLoading" class="background" :style="{ backgroundImage: `url(${this.data.image_url})` }"></div>
			<div v-if="!isLoading" class="card-vibes">
				<div class="poster" :style="{ backgroundImage: `url(${this.data.image_url})` }" @click="downloadStory" :class="[{ twothree: ['Film', 'Top', 'Reading'].some((word) => this.category.includes(word)) }]">
					<div class="caption">
						<h3>{{ this.data.desc }}</h3>
						<h1>{{ this.data.title }}</h1>
						<div class="stars">
							<fa v-for="star in Math.floor(rating)" :key="`star-${star}`" :icon="['fas', 'star']" />
							<fa v-if="rating % 1 >= 0.5" :key="'half-star'" :icon="['far', 'star-half-stroke']" />
							<fa v-for="emptyStar in 5 - Math.floor(rating) - (rating % 1 >= 0.5 ? 1 : 0)" :key="`empty-star-${emptyStar}`" :icon="['far', 'star']" />
							<div class="number">{{ this.rating }}</div>
						</div>
					</div>
				</div>
				<div class="profile" @click="goProfileUsername(this.username)">
					<img :src="this.profile_picture" alt="" crossorigin="anonymous" />
					<div class="badge">{{ this.name }}</div>
				</div>
				<div class="comment">
					{{ this.data.comment }}
				</div>
			</div>
			<div v-else>
				<div class="loading">
					<fa icon="spinner" class="fa-spin-pulse" />
				</div>
			</div>
		</section>
		<span data-bs-toggle="modal" data-bs-target="#notifModal" ref="notifModalBtn"></span>
		<div class="modal fade" id="notifModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="notifModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body notification">
						<div class="title">
							<h1>Network <span>Error</span></h1>
						</div>
						<p class="m-0 text-center">Please check your internet connection and try again.</p>
					</div>
					<div class="modal-button">
						<button type="button" class="btn-block purple" data-bs-dismiss="modal" aria-label="Close" @click="refresh()">Refresh</button>
					</div>
					<div class="modal-button">
						<button type="button" class="btn-block" data-bs-dismiss="modal" aria-label="Close" @click="goBack()">Close</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { Share } from '@capacitor/share';
import { Filesystem, Directory } from '@capacitor/filesystem';
import domtoimage from 'dom-to-image';
import dashboardService from '@/services/dashboardService';
import { Capacitor } from '@capacitor/core';

import avatar from '@/assets/avatar.png';
import axios from 'axios';

export default {
	name: 'ShowVibeView',
	data() {
		return {
			isLoading: false,
			data: {},
			rating: '0',
			profile_picture: avatar,
			name: '',
			username: '',
			category: '',
			corsLink: '',
			authority: ''
		};
	},
	mounted() {
		this.getCors();
		this.getParams();
	},
	methods: {
		async getCors() {
			try {
				const response = await dashboardService.getCors();
				this.corsLink = response.data.url;
			} catch (error) {
				console.error('Error fetching data:', error);
			}
		},
		updateMetaTags() {
			const description = this.data.title + ' - ' + this.data.desc;
			const thumbnail = this.data.image_url;

			let metaDescription = document.querySelector('meta[name="description"]');
			if (!metaDescription) {
				metaDescription = document.createElement('meta');
				metaDescription.name = 'description';
				document.head.appendChild(metaDescription);
			}
			metaDescription.content = description;

			let ogImage = document.querySelector('meta[property="og:image"]');
			if (!ogImage) {
				ogImage = document.createElement('meta');
				ogImage.setAttribute('property', 'og:image');
				document.head.appendChild(ogImage);
			}
			ogImage.content = thumbnail;

			let ogDescription = document.querySelector('meta[property="og:description"]');
			if (!ogDescription) {
				ogDescription = document.createElement('meta');
				ogDescription.setAttribute('property', 'og:description');
				document.head.appendChild(ogDescription);
			}
			ogDescription.content = description;

			let twitterImage = document.querySelector('meta[name="twitter:image"]');
			if (!twitterImage) {
				twitterImage = document.createElement('meta');
				twitterImage.name = 'twitter:image';
				document.head.appendChild(twitterImage);
			}
			twitterImage.content = thumbnail;
		},
		async urlToBase64(blob) {
			return new Promise((resolve, reject) => {
				const reader = new FileReader();
				reader.readAsDataURL(blob);
				reader.onloadend = () => resolve(reader.result);
				reader.onerror = reject;
			});
		},
		async fetchAndConvertToBase64(url) {
			try {
				const response = await axios.get(url, {
					responseType: 'blob',
					validateStatus: function (status) {
						return status >= 200 && status < 300;
					}
				});
				return await this.urlToBase64(response.data);
			} catch (error) {
				const corsUrl = this.corsLink + url;
				try {
					const response = await axios.get(corsUrl, {
						responseType: 'blob',
						validateStatus: function (status) {
							return status >= 200 && status < 300;
						}
					});
					return await this.urlToBase64(response.data);
				} catch (corsError) {
					console.error('Error fetching data:', corsError);
					this.showNotifModal();
					return '';
				}
			}
		},
		async getParams() {
			this.isLoading = true;
			const username = this.$route.params.username;
			const slug = this.$route.params.slug;
			const url = `${username}/${slug}`;
			try {
				const response = await dashboardService.getVibe(url);
				response.data.path = url;
				this.data = response.data;
				const urlcheck = new URL(this.data.image_url);
				this.authority = urlcheck.host;
				if (this.authority != 'image.tmdb.org') {
					if (this.data.image_url.startsWith('http://')) {
						this.data.image_url = this.data.image_url.replace('http://', 'https://');
					}
					this.data.image_url = await this.fetchAndConvertToBase64(this.data.image_url);
				}
				if (this.data.user.profile_picture) {
					const profilePictureUrl = process.env.VUE_APP_API_URL + '/' + this.data.user.profile_picture;
					this.profile_picture = await this.fetchAndConvertToBase64(profilePictureUrl);
				} else {
					this.profile_picture = avatar;
				}
				this.name = this.data.user.name;
				this.username = this.data.user.username;
				this.rating = this.data.rating;
				this.category = this.data.category.title;
				this.isLoading = false;
				this.updateMetaTags();
			} catch (error) {
				console.error('Error fetching data:', error);
				this.showNotifModal();
				this.$router.go(-1);
			}
		},
		async downloadStory() {
			if (this.authority != 'image.tmdb.org') {
				const element = this.$refs.vibesCanvas;
				const isMobile = Capacitor.isNativePlatform();
				await this.waitForElementLoad(element);
				if (isMobile) {
					const scale = 3;
					const style = {
						transform: 'scale(' + scale + ')',
						transformOrigin: 'top left',
						width: element.offsetWidth + 'px',
						height: element.offsetHeight + 'px'
					};
					const param = {
						height: element.offsetHeight * scale,
						width: element.offsetWidth * scale,
						quality: 1,
						style
					};
					const dataUrl = await domtoimage.toPng(element, param);
					const base64Data = dataUrl.split(',')[1];
					const fileName = `${this.data.id}.png`;
					await Filesystem.writeFile({
						path: fileName,
						data: base64Data,
						directory: Directory.Documents
					});
					await Share.share({
						title: 'My Vibe',
						text: 'Check out this vibe!',
						url: 'https://myvibeapp.site/' + this.data.path,
						dialogTitle: 'Share via'
					});
				} else {
					const { toPng } = await import('html-to-image');
					const dataUrl = await toPng(element);
					const link = document.createElement('a');
					link.href = dataUrl;
					link.download = this.data.id + '.png';
					link.click();
					await Share.share({
						title: 'My Vibe',
						text: 'Check out this my vibe!',
						url: 'https://myvibeapp.site/' + this.data.path,
						dialogTitle: 'Share via'
					});
				}
			}
		},
		goProfileUsername(username) {
			this.$router.push('/' + username);
		},
		waitForElementLoad(element) {
			return new Promise((resolve, reject) => {
				const checkLoaded = () => {
					if (element && element.offsetHeight && element.offsetWidth) {
						resolve();
					} else {
						setTimeout(checkLoaded, 100);
					}
				};
				checkLoaded();
			});
		},
		goBack() {
			var backdrop = document.querySelector('.modal-backdrop');
			if (backdrop) {
				backdrop.remove();
			}
			this.$router.go(-1);
		},
		refresh() {
			var backdrop = document.querySelector('.modal-backdrop');
			if (backdrop) {
				backdrop.remove();
			}
			window.location.reload();
		},
		showNotifModal() {
			this.$refs.notifModalBtn.click();
		}
	}
};
</script>
