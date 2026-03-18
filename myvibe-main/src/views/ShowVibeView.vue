<template>
	<div class="showvibe-root">
		<!-- Morphing Blobs -->
		<div class="sv-blob sv-blob--1"></div>
		<div class="sv-blob sv-blob--2"></div>
		<div class="sv-blob sv-blob--3"></div>

		<div class="back-floating-glass" @click="goBack()">
			<fa icon="arrow-left-long" class="text-white" />
		</div>

		<section class="vibes vibes-canvas" ref="vibesCanvas" style="overflow: hidden">
			<div v-if="!isLoading" class="background" :style="{ backgroundImage: `url(${this.data.image_url})` }"></div>

			<div v-if="!isLoading" class="card-vibes anim-card-enter">
				<!-- Glass overlay -->
				<div class="card-vibes__glass-overlay"></div>

				<div class="poster" :style="{ backgroundImage: `url(${this.data.image_url})` }" @click="downloadStory" :class="[{ twothree: ['Film', 'Top', 'Reading'].some((word) => this.category.includes(word)) }]">
					<!-- Poster glass gradient -->
					<div class="poster__glass-gradient"></div>
					<div class="caption anim-caption">
						<h3>{{ this.data.desc }}</h3>
						<h1>{{ this.data.title }}</h1>
						<div class="stars">
							<fa v-for="star in Math.floor(rating)" :key="`star-${star}`" :icon="['fas', 'star']" />
							<fa v-if="rating % 1 >= 0.5" :key="'half-star'" :icon="['far', 'star-half-stroke']" />
							<fa v-for="emptyStar in 5 - Math.floor(rating) - (rating % 1 >= 0.5 ? 1 : 0)" :key="`empty-star-${emptyStar}`" :icon="['far', 'star']" />
							<div class="number glass-badge">{{ this.rating }}</div>
						</div>
					</div>
				</div>

				<div class="profile anim-profile" @click="goProfileUsername(this.username)">
					<img :src="this.profile_picture" alt="" crossorigin="anonymous" />
					<div class="badge glass-badge-name">{{ this.name }}</div>
				</div>

				<div class="comment glass-comment anim-comment">
					{{ this.data.comment }}
				</div>
			</div>

			<div v-else>
				<div class="loading">
					<fa icon="spinner" class="fa-spin-pulse" />
				</div>
			</div>
		</section>

		<!-- Modal -->
		<span data-bs-toggle="modal" data-bs-target="#notifModal" ref="notifModalBtn"></span>
		<div class="modal fade" id="notifModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="notifModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content glass-modal">
					<div class="modal-body notification">
						<div class="title">
							<h1>Network <span>Error</span></h1>
						</div>
						<p class="m-0 text-center">Please check your internet connection and try again.</p>
					</div>
					<div class="modal-button">
						<button type="button" class="btn-block purple glass-btn" data-bs-dismiss="modal" aria-label="Close" @click="refresh()">Refresh</button>
					</div>
					<div class="modal-button">
						<button type="button" class="btn-block glass-btn" data-bs-dismiss="modal" aria-label="Close" @click="goBack()">Close</button>
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

<style lang="scss" scoped>
@import '@/assets/scss/color.scss';

// =============================================
// SHOWVIBEVIEW — LIQUID GLASS REDESIGN
// =============================================

.showvibe-root {
	position: relative;
	min-height: 100vh;
	overflow: hidden;
}

// === MORPHING BLOBS ===
.sv-blob {
	position: fixed;
	filter: blur(55px);
	pointer-events: none;
	z-index: 0;
	border-radius: 50%;
}

.sv-blob--1 {
	top: -8%;
	right: -12%;
	width: min(240px, 55vw);
	height: min(240px, 55vw);
	background: radial-gradient(circle, rgba($purple, 0.25) 0%, rgba(#6c5ce7, 0.08) 40%, transparent 70%);
	animation: svBlobMorph1 8s ease-in-out infinite, svBlobFloat1 6s ease-in-out infinite;
}

.sv-blob--2 {
	bottom: 10%;
	left: -15%;
	width: min(200px, 50vw);
	height: min(200px, 50vw);
	background: radial-gradient(circle, rgba(#a29bfe, 0.18) 0%, rgba($purple, 0.06) 50%, transparent 70%);
	animation: svBlobMorph2 10s ease-in-out infinite, svBlobFloat2 7s ease-in-out infinite;
}

.sv-blob--3 {
	top: 40%;
	right: -10%;
	width: min(160px, 40vw);
	height: min(160px, 40vw);
	background: radial-gradient(circle, rgba(#74b9ff, 0.12) 0%, rgba($purple, 0.04) 50%, transparent 70%);
	animation: svBlobMorph1 12s ease-in-out infinite reverse, svBlobFloat1 9s ease-in-out infinite reverse;
}

@keyframes svBlobMorph1 {
	0%, 100% { border-radius: 42% 58% 70% 30% / 45% 45% 55% 55%; }
	25% { border-radius: 50% 50% 35% 65% / 60% 40% 60% 40%; }
	50% { border-radius: 35% 65% 50% 50% / 40% 55% 45% 60%; }
	75% { border-radius: 60% 40% 45% 55% / 50% 60% 40% 50%; }
}

@keyframes svBlobMorph2 {
	0%, 100% { border-radius: 55% 45% 40% 60% / 50% 35% 65% 50%; }
	33% { border-radius: 40% 60% 55% 45% / 65% 50% 50% 35%; }
	66% { border-radius: 50% 50% 60% 40% / 40% 60% 40% 60%; }
}

@keyframes svBlobFloat1 {
	0%, 100% { transform: translate(0, 0); }
	50% { transform: translate(-15px, 20px); }
}

@keyframes svBlobFloat2 {
	0%, 100% { transform: translate(0, 0); }
	50% { transform: translate(20px, -15px); }
}

// === GLASS BACK BUTTON ===
.back-floating-glass {
	position: fixed;
	top: 50px;
	left: 20px;
	z-index: 100;
	width: 40px;
	height: 40px;
	display: flex;
	align-items: center;
	justify-content: center;
	border-radius: 50%;
	background: rgba(255, 255, 255, 0.08);
	backdrop-filter: blur(12px);
	-webkit-backdrop-filter: blur(12px);
	border: 1px solid rgba(255, 255, 255, 0.12);
	box-shadow:
		0 4px 16px rgba(0, 0, 0, 0.2),
		inset 0 1px 0 rgba(255, 255, 255, 0.1);
	cursor: pointer;
	transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
	animation: svBackFadeIn 0.5s ease-out both;

	&:hover {
		background: rgba(255, 255, 255, 0.14);
		border-color: rgba($purple, 0.3);
		transform: scale(1.08);
		box-shadow:
			0 6px 20px rgba(0, 0, 0, 0.25),
			0 0 12px rgba($purple, 0.15),
			inset 0 1px 0 rgba(255, 255, 255, 0.15);
	}

	&:active {
		transform: scale(0.95);
	}
}

@keyframes svBackFadeIn {
	from { opacity: 0; transform: translateX(-20px); }
	to { opacity: 1; transform: translateX(0); }
}

// === CARD GLASS OVERLAY ===
.card-vibes {
	position: relative;
	overflow: hidden;

	&__glass-overlay {
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		pointer-events: none;
		z-index: 1;
		border-radius: inherit;
		background: linear-gradient(
			135deg,
			rgba(255, 255, 255, 0.04) 0%,
			transparent 40%,
			transparent 60%,
			rgba(255, 255, 255, 0.02) 100%
		);
	}
}

// === POSTER GLASS GRADIENT ===
.poster {
	position: relative;
	overflow: hidden;

	&__glass-gradient {
		position: absolute;
		bottom: 0;
		left: 0;
		right: 0;
		height: 60%;
		pointer-events: none;
		z-index: 1;
		background: linear-gradient(
			to top,
			rgba(0, 0, 0, 0.7) 0%,
			rgba(0, 0, 0, 0.3) 40%,
			transparent 100%
		);
	}
}

.caption {
	position: relative;
	z-index: 2;
}

// === GLASS BADGES ===
.glass-badge {
	background: rgba(255, 255, 255, 0.1) !important;
	backdrop-filter: blur(10px);
	-webkit-backdrop-filter: blur(10px);
	border: 1px solid rgba(255, 255, 255, 0.15);
	border-radius: 8px;
	padding: 2px 8px;
	text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
}

.glass-badge-name {
	background: rgba(255, 255, 255, 0.08) !important;
	backdrop-filter: blur(12px);
	-webkit-backdrop-filter: blur(12px);
	border: 1px solid rgba(255, 255, 255, 0.12) !important;
	box-shadow:
		0 2px 8px rgba(0, 0, 0, 0.15),
		inset 0 1px 0 rgba(255, 255, 255, 0.08);
	text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
}

// === GLASS COMMENT ===
.glass-comment {
	position: relative;
	background: rgba(255, 255, 255, 0.04) !important;
	backdrop-filter: blur(16px);
	-webkit-backdrop-filter: blur(16px);
	border: 1px solid rgba(255, 255, 255, 0.08) !important;
	border-radius: 16px !important;
	box-shadow:
		0 4px 16px rgba(0, 0, 0, 0.12),
		inset 0 1px 0 rgba(255, 255, 255, 0.06);
	overflow: hidden;

	&::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		height: 1px;
		background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.12), transparent);
		pointer-events: none;
	}
}

// === GLASS MODAL ===
.glass-modal {
	background: rgba(30, 30, 50, 0.85) !important;
	backdrop-filter: blur(20px);
	-webkit-backdrop-filter: blur(20px);
	border: 1px solid rgba(255, 255, 255, 0.1) !important;
	border-radius: 20px !important;
	box-shadow:
		0 16px 48px rgba(0, 0, 0, 0.4),
		inset 0 1px 0 rgba(255, 255, 255, 0.08);
}

.glass-btn {
	background: rgba(255, 255, 255, 0.06) !important;
	backdrop-filter: blur(10px);
	-webkit-backdrop-filter: blur(10px);
	border: 1px solid rgba(255, 255, 255, 0.1) !important;
	transition: all 0.3s ease;

	&:hover {
		background: rgba(255, 255, 255, 0.12) !important;
	}

	&.purple {
		background: rgba($purple, 0.3) !important;
		border-color: rgba($purple, 0.4) !important;
		box-shadow: 0 0 16px rgba($purple, 0.15);

		&:hover {
			background: rgba($purple, 0.45) !important;
			box-shadow: 0 0 24px rgba($purple, 0.25);
		}
	}
}

// === PROFILE GLASS ===
.profile {
	img {
		border: 2px solid rgba(255, 255, 255, 0.15) !important;
		box-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
		transition: all 0.3s ease;
	}

	&:hover img {
		border-color: rgba($purple, 0.4) !important;
		box-shadow: 0 4px 16px rgba($purple, 0.15);
	}
}

// === STARS GLOW ===
.stars {
	.fa-star, .fa-star-half-stroke {
		filter: drop-shadow(0 0 4px rgba($purple, 0.4));
	}
}

// === ENTRANCE ANIMATIONS ===
.anim-card-enter {
	animation: svCardSlideUp 0.7s cubic-bezier(0.23, 1, 0.32, 1) both;
}

.anim-caption {
	animation: svFadeSlideUp 0.6s cubic-bezier(0.23, 1, 0.32, 1) 0.3s both;
}

.anim-profile {
	animation: svFadeSlideUp 0.5s cubic-bezier(0.23, 1, 0.32, 1) 0.5s both;
}

.anim-comment {
	animation: svFadeSlideUp 0.5s cubic-bezier(0.23, 1, 0.32, 1) 0.65s both;
}

@keyframes svCardSlideUp {
	from {
		opacity: 0;
		transform: translateY(40px) scale(0.96);
	}
	to {
		opacity: 1;
		transform: translateY(0) scale(1);
	}
}

@keyframes svFadeSlideUp {
	from {
		opacity: 0;
		transform: translateY(20px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

// === LOADING ===
.loading {
	display: flex;
	justify-content: center;
	align-items: center;
	height: 80vh;
	color: $purple;
	font-size: 28px;
	filter: drop-shadow(0 0 12px rgba($purple, 0.4));
	animation: svPulseGlow 2s ease-in-out infinite;
}

@keyframes svPulseGlow {
	0%, 100% { filter: drop-shadow(0 0 12px rgba($purple, 0.4)); }
	50% { filter: drop-shadow(0 0 24px rgba($purple, 0.6)); }
}

// === HIDE OLD BACK BUTTON ===
.back-button-vibes {
	display: none !important;
}
</style>
