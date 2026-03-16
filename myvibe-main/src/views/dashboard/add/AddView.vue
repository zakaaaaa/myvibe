<template>
	<section class="add pt-4" ref="sectionList" @scroll="handleScroll()">
		<!-- Blobs -->
		<div class="add-blob add-blob--1"></div>
		<div class="add-blob add-blob--2"></div>
		<div class="add-blob add-blob--3"></div>

		<div v-if="!isLoading">
			<!-- HOME: Category List -->
			<transition name="page-fade" mode="out-in">
				<div v-if="isHome" key="home" class="container page-enter-active">
					<RouterLink to="/dashboard" class="back-floating">
						<fa icon="arrow-left" />
					</RouterLink>
					<h1 class="title text-center mb-4 anim-title" style="padding-top: 40px">Add New <span class="highlight">Vibe</span></h1>
					<ul class="category_lists row">
						<li v-for="(section, index) in category" :key="section.id" class="col-6 mb-4" @click="clickAdd(section)" :style="{ animationDelay: (index * 0.08) + 's' }">
							<div class="content glass-card-hover" :style="{ backgroundImage: `url(${section.image})` }">
								<h1>{{ section.title }}</h1>
								<fa icon="angle-right" class="icon" />
							</div>
						</li>
					</ul>
				</div>
			</transition>

			<!-- SEARCH -->
			<transition name="page-fade" mode="out-in">
				<div v-if="isSearch" key="search" class="container page-enter-active">
					<div class="back-floating" @click="goBack()">
						<fa icon="arrow-left" />
					</div>
					<h1 class="title text-center mb-4 anim-title" style="padding-top: 40px">
						Add New <span class="highlight">{{ searchOption.title }}</span>
					</h1>
					<div class="search-box anim-search-box">
						<input type="text" v-model="searchQuery" placeholder="Search" @keyup="debounceSearch()" />
						<div class="icon" @click="searchVibe()">
							<fa icon="search" />
						</div>
					</div>
					<ul v-if="!isLoadingResult" class="result_list">
						<li v-for="(section, index) in searchResult" :key="index" class="mb-4" @click="selectVibe(section)" :style="{ animationDelay: (index * 0.06) + 's' }">
							<div class="content glass-card-hover" :style="{ backgroundImage: `url(${section.image_url})` }">
								<div class="text">
									<h3>{{ section.desc }}</h3>
									<h1>{{ section.title }}</h1>
								</div>
							</div>
						</li>
					</ul>
					<div v-else>
						<div class="loading">
							<fa icon="spinner" class="fa-spin-pulse" />
						</div>
					</div>
				</div>
			</transition>

			<!-- RATING -->
			<transition name="page-scale" mode="out-in">
				<div v-if="isRating" key="rating" class="container rating-page page-enter-active">
					<div class="back-floating back-floating--rating" @click="goBackFromRating()">
						<fa icon="arrow-left" />
					</div>
					<div class="background" :style="{ backgroundImage: `url(${selectedVibe.image_url})` }"></div>
					<div class="give-rating anim-rating">
						<input type="text" v-model="ratingInput" @input="validateRating" maxlength="3" />
						<small>Out of 5</small>
						<div class="btn btn-sm btn-post" @click="giveComment()">Post It!</div>
					</div>
				</div>
			</transition>

			<!-- COMMENT -->
			<transition name="page-slide-up" mode="out-in">
				<div v-if="isComment" key="comment" class="container comment-page page-enter-active">
					<div class="back-floating" @click="goBackFromLast()">
						<fa icon="arrow-left" />
					</div>
					<h1 class="title text-center mb-4 anim-title" style="padding-top: 40px">Tell the <span class="highlight">Vibes</span></h1>
					<div class="comment-content anim-comment-card" :style="{ backgroundImage: `url(${selectedVibe.image_url})` }">
						<!-- Glass overlay on card -->
						<div class="comment-content__glass-overlay"></div>
						<div class="rating">
							<h1>
								{{ selectedVibe.rating ? selectedVibe.rating.toFixed(1) : '0.0' }}
								<fa :icon="['fas', 'star']" />
							</h1>
						</div>
						<div class="text">
							<h3>{{ selectedVibe.desc }}</h3>
							<h1>{{ selectedVibe.title }}</h1>
						</div>
					</div>
					<div class="comment-box anim-comment-box">
						<textarea v-model="commentInput" placeholder="Write down your vibe here" maxlength="500" rows="4"></textarea>
						<div class="icon" @click="postVibe()">
							<fa :icon="['far', 'paper-plane']" />
						</div>
						<!-- Glass reflection on textarea -->
						<div class="comment-box__reflection"></div>
					</div>
				</div>
			</transition>
		</div>
		<div v-else>
			<div class="loading">
				<fa icon="spinner" class="fa-spin-pulse" />
			</div>
		</div>
	</section>
</template>

<script>
import axios from 'axios';
import dashboardService from '@/services/dashboardService';

export default {
	name: 'AddView',
	data() {
		return {
			isLoading: false,
			isLoadingResult: false,
			isHome: false,
			isSearch: false,
			isRating: false,
			isComment: false,
			category: [],
			searchOption: [],
			searchQuery: '',
			searchResult: [],
			debounceTimeout: null,
			nextPage: false,
			nextPageUrl: '',
			selectedVibe: {},
			ratingInput: '0.0',
			commentInput: '',
			corsLink: ''
		};
	},
	mounted() {
		this.getCors();
		this.categories();
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
		async categories() {
			this.isLoading = true;
			try {
				const response = await dashboardService.categories();
				this.category = [...response.data.data].sort((a, b) => a.id - b.id);
				this.isLoading = false;
			} catch (error) {
				console.error('Error fetching categories:', error);
			} finally {
				this.isHome = true;
			}
			this.isSearch = false;
			this.isRating = false;
			this.isComment = false;
			this.searchOption = [];
			this.searchResult = [];
			this.selectedVibe = {};
		},
		clickAdd(section) {
			this.isHome = false;
			this.isSearch = true;
			this.isRating = false;
			this.isComment = false;
			this.searchOption = section;
			if (section.title == 'Others') {
				this.$router.push('/add-others');
			}
		},
		debounceSearch() {
			if (this.debounceTimeout) clearTimeout(this.debounceTimeout);
			this.debounceTimeout = setTimeout(() => { this.searchVibe(); }, 1000);
		},
		async searchVibe() {
			if (!this.searchQuery.trim()) { this.searchResult = []; } else {
				this.isLoadingResult = true;
				if (this.searchQuery.length >= 2) {
					const apiUrl = this.searchOption.url + encodeURIComponent(this.searchQuery);
					const url = new URL(apiUrl);
					const authority = url.host;
					if (authority == 'api.deezer.com') {
						try {
							const response = await axios.get(this.corsLink + apiUrl);
							this.searchResult = [];
							for (let i = 0; i < response.data.data.length; i++) {
								if (response.data.data[i].type == 'artist') { response.data.data[i].type = 'Musician'; this.searchResult.push({ image_url: response.data.data[i].picture_xl, title: response.data.data[i].name, desc: response.data.data[i].type }); }
								else if (response.data.data[i].type == 'track') { this.searchResult.push({ image_url: response.data.data[i].album.cover_xl, title: response.data.data[i].title, desc: response.data.data[i].artist.name }); }
								else if (response.data.data[i].type == 'album') { this.searchResult.push({ image_url: response.data.data[i].cover_xl, title: response.data.data[i].title, desc: response.data.data[i].artist.name }); }
							}
							if (response.data.next) { this.nextPage = true; this.nextPageUrl = response.data.next; } else { this.nextPage = false; this.nextPageUrl = ''; }
							this.isLoadingResult = false;
						} catch (error) { console.error('Error fetching data:', error); this.isLoadingResult = false; }
					} else if (authority == 'api.themoviedb.org') {
						try {
							const response = await axios.get(apiUrl, { headers: { Authorization: `Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJjNmRkMGEyYzMzYWU5YzIwMjY5MzUzM2ExYjUxYzc4OCIsIm5iZiI6MTYyNzE0OTU3NS4wMDEsInN1YiI6IjYwZmM1NTA2MzEwMzI1MDAyMzczZDRlNCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.KkR9zGoqvA9P1U36rl37oSEyVS-M4rUtvpFpLCBl1bs` } });
							this.searchResult = [];
							if (this.searchOption.title == 'Top Shows') { for (let i = 0; i < response.data.results.length; i++) { this.searchResult.push({ image_url: 'https://image.tmdb.org/t/p/original' + response.data.results[i].poster_path, title: response.data.results[i].name, desc: response.data.results[i].first_air_date }); } }
							else { for (let i = 0; i < response.data.results.length; i++) { this.searchResult.push({ image_url: 'https://image.tmdb.org/t/p/original' + response.data.results[i].poster_path, title: response.data.results[i].title, desc: response.data.results[i].release_date }); } }
							if (response.data.total_pages > response.data.page) { this.nextPage = true; this.nextPageUrl = apiUrl.replace(/page=\d+/, 'page=' + (response.data.page + 1)); } else { this.nextPage = false; this.nextPageUrl = ''; }
							this.isLoadingResult = false;
						} catch (error) { console.error('Error fetching data:', error); this.isLoadingResult = false; }
					} else if (authority == 'www.googleapis.com') {
						try {
							const response = await axios.get(apiUrl);
							this.searchResult = [];
							for (let i = 0; i < response.data.items.length; i++) {
								let namesString = response.data.items[i].volumeInfo.authors ? response.data.items[i].volumeInfo.authors.join(', ') : response.data.items[i].volumeInfo.publisher;
								if (response.data.items[i].volumeInfo.imageLinks.thumbnail.startsWith('http://')) { response.data.items[i].volumeInfo.imageLinks.thumbnail = response.data.items[i].volumeInfo.imageLinks.thumbnail.replace('http://', 'https://'); }
								this.searchResult.push({ image_url: response.data.items[i].volumeInfo.imageLinks.thumbnail.replace(/zoom=\d+/, 'zoom=0'), title: response.data.items[i].volumeInfo.title, desc: namesString });
							}
							this.isLoadingResult = false;
						} catch (error) { console.error('Error fetching data:', error); this.isLoadingResult = false; }
					} else if (authority == 'itunes.apple.com') {
						try {
							const response = await axios.get(apiUrl + '&type=podcast');
							this.searchResult = [];
							for (let i = 0; i < response.data.results.length; i++) { this.searchResult.push({ image_url: response.data.results[i].artworkUrl600, title: response.data.results[i].trackName, desc: response.data.results[i].artistName }); }
							this.isLoadingResult = false;
						} catch (error) { console.error('Error fetching data:', error); this.isLoadingResult = false; }
					}
				}
			}
		},
		async loopVibe() {
			if (this.nextPage) {
				const params = this.nextPageUrl;
				const url = new URL(params);
				const authority = url.host;
				if (authority == 'api.deezer.com') {
					try {
						const response = await axios.get(this.corsLink + params);
						for (let i = 0; i < response.data.data.length; i++) {
							if (response.data.data[i].type == 'artist') { this.searchResult.push({ image_url: response.data.data[i].picture_big, title: response.data.data[i].name, desc: 'Musician' }); }
							else if (response.data.data[i].type == 'track') { this.searchResult.push({ image_url: response.data.data[i].artist.picture_big, title: response.data.data[i].title, desc: response.data.data[i].artist.name }); }
							else if (response.data.data[i].type == 'album') { this.searchResult.push({ image_url: response.data.data[i].cover_big, title: response.data.data[i].title, desc: response.data.data[i].artist.name }); }
						}
						if (response.data.next) { this.nextPage = true; this.nextPageUrl = response.data.next; } else { this.nextPage = false; this.nextPageUrl = ''; }
					} catch (error) { console.error('Error fetching data:', error); }
				} else if (authority == 'api.themoviedb.org') {
					try {
						const response = await axios.get(params, { headers: { Authorization: `Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJjNmRkMGEyYzMzYWU5YzIwMjY5MzUzM2ExYjUxYzc4OCIsIm5iZiI6MTYyNzE0OTU3NS4wMDEsInN1YiI6IjYwZmM1NTA2MzEwMzI1MDAyMzczZDRlNCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.KkR9zGoqvA9P1U36rl37oSEyVS-M4rUtvpFpLCBl1bs` } });
						if (this.searchOption.title == 'Top Shows') { for (let i = 0; i < response.data.results.length; i++) { this.searchResult.push({ image_url: 'https://image.tmdb.org/t/p/original' + response.data.results[i].poster_path, title: response.data.results[i].name, desc: response.data.results[i].first_air_date }); } }
						else { for (let i = 0; i < response.data.results.length; i++) { this.searchResult.push({ image_url: 'https://image.tmdb.org/t/p/original' + response.data.results[i].poster_path, title: response.data.results[i].title, desc: response.data.results[i].release_date }); } }
						if (response.data.total_pages > response.data.page) { this.nextPage = true; this.nextPageUrl = params.replace(/page=\d+/, 'page=' + (response.data.page + 1)); } else { this.nextPage = false; this.nextPageUrl = ''; }
					} catch (error) { console.error('Error fetching data:', error); }
				}
			}
		},
		handleScroll() {
			if (this.isSearch) {
				const container = this.$refs.sectionList;
				if (container.scrollTop + container.clientHeight >= container.scrollHeight - 10) { this.loopVibe(); }
			}
		},
		selectVibe(section) {
			this.selectedVibe = { ...section, rating: 0.0 };
			this.isHome = false; this.isSearch = false; this.isRating = true; this.isComment = false;
		},
		validateRating() {
			this.ratingInput = this.ratingInput.replace(',', '.');
			const parsed = parseFloat(this.ratingInput);
			if (this.ratingInput === '' || isNaN(parsed)) { this.selectedVibe.rating = 0.0; return; }
			if (parsed > 5.0) { this.ratingInput = '5.0'; this.selectedVibe.rating = 5.0; }
			else if (parsed < 0.0) { this.ratingInput = '0.0'; this.selectedVibe.rating = 0.0; }
			else { this.selectedVibe.rating = parsed; }
		},
		giveComment() {
			if (!this.ratingInput || isNaN(parseFloat(this.ratingInput))) { this.selectedVibe.rating = 0.0; } else { this.validateRating(); }
			this.isHome = false; this.isSearch = false; this.isRating = false; this.isComment = true;
		},
		async postVibe() {
			this.isLoading = true;
			this.selectedVibe.category_id = this.searchOption.id;
			this.selectedVibe.comment = this.commentInput;
			this.selectedVibe.rating = this.selectedVibe.rating.toFixed(1);
			try {
				const response = await dashboardService.postVibe(this.selectedVibe);
				this.$router.push('/' + response.data.data.path);
			} catch (error) { console.error('Error fetching data:', error); }
			finally { this.isLoading = false; this.isHome = true; this.isSearch = false; this.isRating = false; this.isComment = false; this.searchOption = []; this.searchResult = []; this.selectedVibe = {}; this.ratingInput = '0.0'; this.commentInput = ''; }
		},
		goBack() { this.isHome = true; this.isSearch = false; this.isRating = false; this.isComment = false; this.searchOption = []; this.searchResult = []; this.selectedVibe = {}; this.ratingInput = '0.0'; this.commentInput = ''; this.searchQuery = ''; },
		goBackFromRating() { this.isHome = false; this.isSearch = true; this.isRating = false; this.isComment = false; },
		goBackFromLast() { this.isHome = false; this.isSearch = false; this.isRating = true; this.isComment = false; }
	}
};
</script>

<style lang="scss" scoped>
@import '@/assets/scss/color.scss';

// =============================================
// MYVIBE ADDVIEW — LIQUID GLASS REDESIGN
// =============================================

// === PAGE TRANSITION ANIMATIONS ===
.page-fade-enter-active,
.page-fade-leave-active {
	transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
}
.page-fade-enter-from {
	opacity: 0;
	transform: translateX(30px);
}
.page-fade-leave-to {
	opacity: 0;
	transform: translateX(-20px);
}

.page-scale-enter-active,
.page-scale-leave-active {
	transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
}
.page-scale-enter-from {
	opacity: 0;
	transform: scale(0.92);
}
.page-scale-leave-to {
	opacity: 0;
	transform: scale(1.05);
}

.page-slide-up-enter-active,
.page-slide-up-leave-active {
	transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
}
.page-slide-up-enter-from {
	opacity: 0;
	transform: translateY(40px);
}
.page-slide-up-leave-to {
	opacity: 0;
	transform: translateY(-20px);
}

// === ENTRANCE ANIMATIONS FOR INNER ELEMENTS ===
.anim-title {
	animation: addEnterSlideDown 0.6s cubic-bezier(0.23, 1, 0.32, 1) both;
}

.anim-search-box {
	animation: addEnterSlideUp 0.5s cubic-bezier(0.23, 1, 0.32, 1) 0.15s both;
}

.anim-rating {
	animation: addEnterScale 0.6s cubic-bezier(0.23, 1, 0.32, 1) 0.1s both;
}

.anim-comment-card {
	animation: addEnterSlideUp 0.6s cubic-bezier(0.23, 1, 0.32, 1) 0.1s both;
}

.anim-comment-box {
	animation: addEnterSlideUp 0.5s cubic-bezier(0.23, 1, 0.32, 1) 0.25s both;
}

@keyframes addEnterSlideDown {
	from {
		opacity: 0;
		transform: translateY(-20px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

@keyframes addEnterSlideUp {
	from {
		opacity: 0;
		transform: translateY(24px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

@keyframes addEnterScale {
	from {
		opacity: 0;
		transform: scale(0.88);
	}
	to {
		opacity: 1;
		transform: scale(1);
	}
}

// === BLOBS ===
.add-blob {
	position: fixed;
	filter: blur(50px);
	pointer-events: none;
	z-index: 0;
}

.add-blob--1 {
	top: -8%;
	right: -12%;
	width: min(280px, 64vw);
	height: min(280px, 64vw);
	background: radial-gradient(circle, rgba($purple, 0.25) 0%, rgba(#6c5ce7, 0.08) 40%, transparent 70%);
	border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
	animation: addBlobMorph1 12s ease-in-out infinite;
}

.add-blob--2 {
	bottom: 5%;
	left: -10%;
	width: min(240px, 54vw);
	height: min(240px, 54vw);
	background: radial-gradient(circle, rgba(#4a3adf, 0.2) 0%, rgba($purple, 0.06) 40%, transparent 70%);
	border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
	animation: addBlobMorph2 15s ease-in-out infinite;
}

.add-blob--3 {
	top: 35%;
	left: 50%;
	transform: translateX(-50%);
	width: min(300px, 68vw);
	height: min(150px, 34vw);
	background: radial-gradient(ellipse, rgba($purple, 0.1) 0%, transparent 70%);
	border-radius: 50%;
	animation: addBlobFloat 9s ease-in-out infinite;
}

@keyframes addBlobMorph1 {
	0%, 100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(0, 0) rotate(0deg); }
	50% { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; transform: translate(-10px, 14px) rotate(35deg); }
}
@keyframes addBlobMorph2 {
	0%, 100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; transform: translate(0, 0) rotate(0deg); }
	50% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(12px, -10px) rotate(-35deg); }
}
@keyframes addBlobFloat {
	0%, 100% { transform: translateX(-50%) translateY(0) scale(1); opacity: 0.1; }
	50% { transform: translateX(-50%) translateY(-14px) scale(1.04); opacity: 0.16; }
}

// === GLASS BACK BUTTON ===
.back-floating {
	position: fixed;
	top: calc(env(safe-area-inset-top) + 16px);
	left: 16px;
	width: 44px;
	height: 44px;
	display: flex;
	align-items: center;
	justify-content: center;
	border-radius: 50%;
	color: $white;
	text-decoration: none;
	z-index: 100;
	font-size: 15px;
	cursor: pointer;
	background: rgba($white, 0.04);
	backdrop-filter: blur(20px);
	-webkit-backdrop-filter: blur(20px);
	border: 1px solid rgba($white, 0.08);
	box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba($white, 0.08);
	transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);

	&::before {
		content: '';
		position: absolute;
		top: 2px;
		left: 15%;
		right: 15%;
		height: 40%;
		background: linear-gradient(180deg, rgba($white, 0.1) 0%, transparent 100%);
		border-radius: 50%;
		pointer-events: none;
	}

	&:hover {
		transform: translateY(-2px) scale(1.08);
		background: rgba($purple, 0.15);
		border-color: rgba($purple, 0.25);
		color: $white;
		box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25), 0 0 16px rgba($purple, 0.1);
	}

	&:active { transform: scale(0.92); }

	&--rating {
		background: rgba($black, 0.3);
		border-color: rgba($white, 0.12);
		&:hover {
			background: rgba($purple, 0.3);
			border-color: rgba($purple, 0.4);
		}
	}
}

// === GLASS CARD HOVER ===
.glass-card-hover {
	transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1) !important;
	border: 1px solid rgba($white, 0.08) !important;
	box-shadow: 0 6px 24px rgba(0, 0, 0, 0.2) !important;
	position: relative;
	overflow: hidden;

	&::after {
		content: '';
		position: absolute;
		top: 0;
		left: -100%;
		width: 100%;
		height: 100%;
		background: linear-gradient(90deg, transparent, rgba($white, 0.08), transparent);
		transition: left 0.6s ease;
		z-index: 3;
		pointer-events: none;
	}

	&:hover {
		transform: translateY(-6px) scale(1.03) !important;
		border-color: rgba($purple, 0.25) !important;
		box-shadow:
			0 16px 40px rgba(0, 0, 0, 0.35),
			0 0 20px rgba($purple, 0.1),
			inset 0 1px 0 rgba($white, 0.08) !important;
		&::after { left: 100%; }
	}

	&:active {
		transform: translateY(-2px) scale(0.98) !important;
		box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3) !important;
	}
}

// === CATEGORY & RESULT ANIMATION ===
.category_lists li {
	animation: addCardSlideIn 0.5s ease-out both;
}

.result_list li {
	animation: addCardSlideIn 0.4s ease-out both;
}

@keyframes addCardSlideIn {
	from { opacity: 0; transform: translateY(20px); }
	to { opacity: 1; transform: translateY(0); }
}

// === SEARCH BOX — LIQUID GLASS ===
.search-box {
	position: relative;
	background: rgba($white, 0.03);
	backdrop-filter: blur(20px);
	-webkit-backdrop-filter: blur(20px);
	border: 1px solid rgba($white, 0.07);
	border-radius: 16px;
	box-shadow:
		0 4px 20px rgba(0, 0, 0, 0.15),
		inset 0 1px 0 rgba($white, 0.06);
	overflow: hidden;
	transition: all 0.3s ease;

	// Glass reflection
	&::before {
		content: '';
		position: absolute;
		top: 0;
		left: 10%;
		right: 10%;
		height: 1px;
		background: linear-gradient(90deg, transparent, rgba($white, 0.15), transparent);
		pointer-events: none;
		z-index: 2;
	}

	&:focus-within {
		border-color: rgba($purple, 0.3);
		box-shadow:
			0 0 0 3px rgba($purple, 0.06),
			0 8px 28px rgba(0, 0, 0, 0.2),
			inset 0 1px 0 rgba($white, 0.08);
	}

	input {
		background: transparent;
		border: none;
		color: $white;
		font-size: 15px;
		padding: 14px 50px 14px 18px;
		width: 100%;
		outline: none;

		&::placeholder {
			color: rgba($white, 0.3);
		}
	}

	.icon {
		position: absolute;
		right: 14px;
		top: 50%;
		transform: translateY(-50%);
		color: rgba($white, 0.4);
		cursor: pointer;
		transition: all 0.3s ease;
		z-index: 3;

		&:hover {
			color: $purple;
			filter: drop-shadow(0 0 6px rgba($purple, 0.4));
		}
	}
}

// === RATING PAGE ===
.rating-page {
	.give-rating {
		background: transparent !important;
		border: none !important;
		box-shadow: none !important;
		padding: 0 !important;

		input {
			text-shadow: 0 0 40px rgba($white, 0.4), 0 4px 20px rgba(0, 0, 0, 0.5);
		}

		small {
			opacity: 0.7;
			text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
		}

		.btn-post {
			background: linear-gradient(135deg, rgba($purple, 0.7), rgba($purple, 0.9)) !important;
			backdrop-filter: blur(16px);
			-webkit-backdrop-filter: blur(16px);
			border: 1px solid rgba($white, 0.15) !important;
			box-shadow: 0 6px 24px rgba($purple, 0.3), inset 0 1px 0 rgba($white, 0.15) !important;
			padding: 8px 36px !important;
			font-size: 16px !important;
			position: relative;
			overflow: hidden;
			transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);

			&::after {
				content: '';
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				height: 50%;
				background: linear-gradient(180deg, rgba($white, 0.12) 0%, transparent 100%);
				border-radius: 30px 30px 0 0;
				pointer-events: none;
			}

			&::before {
				content: '';
				position: absolute;
				top: 0;
				left: -100%;
				width: 100%;
				height: 100%;
				background: linear-gradient(90deg, transparent, rgba($white, 0.12), transparent);
				transition: left 0.5s ease;
				z-index: 1;
			}

			&:hover {
				transform: translateY(-3px);
				box-shadow: 0 12px 36px rgba($purple, 0.4), 0 0 20px rgba($purple, 0.15) !important;
				&::before { left: 100%; }
			}

			&:active {
				transform: translateY(-1px) scale(0.97);
			}
		}
	}
}

// =============================================
// COMMENT PAGE — FULL LIQUID GLASS
// =============================================
.comment-page {
	position: relative;
	z-index: 1;
}

.comment-content {
	position: relative;
	border-radius: 20px;
	overflow: hidden;
	background-position: center center;
	background-size: cover;
	background-repeat: no-repeat;
	aspect-ratio: 16 / 9;
	padding: 0 !important;
	border: 1px solid rgba($white, 0.1);
	box-shadow:
		0 12px 40px rgba(0, 0, 0, 0.35),
		0 0 0 1px rgba($white, 0.04),
		inset 0 1px 0 rgba($white, 0.08);

	// === Liquid glass overlay ===
	&__glass-overlay {
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: linear-gradient(
			180deg,
			rgba($background, 0.1) 0%,
			rgba($background, 0.25) 40%,
			rgba($background, 0.75) 100%
		);
		backdrop-filter: blur(1px);
		-webkit-backdrop-filter: blur(1px);
		z-index: 1;
		pointer-events: none;

		// Top edge light refraction
		&::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			height: 45%;
			background: linear-gradient(
				180deg,
				rgba($white, 0.08) 0%,
				rgba($white, 0.02) 40%,
				transparent 100%
			);
			pointer-events: none;
		}

		// Bottom purple ambient glow
		&::after {
			content: '';
			position: absolute;
			bottom: -10px;
			left: 15%;
			right: 15%;
			height: 30px;
			background: rgba($purple, 0.15);
			filter: blur(16px);
			border-radius: 50%;
			pointer-events: none;
		}
	}

	// Rating badge — glass pill
	.rating {
		position: absolute !important;
		top: 10px !important;
		left: 10px !important;
		right: auto !important;
		bottom: auto !important;
		z-index: 3;
		width: fit-content !important;
		max-width: fit-content !important;
		display: inline-flex !important;

		h1 {
			font-size: 16px !important;
			font-weight: 700 !important;
			color: $white !important;
			margin: 0 !important;
			display: inline-flex !important;
			align-items: center !important;
			gap: 4px !important;
			padding: 6px 12px !important;
			width: fit-content !important;
			max-width: fit-content !important;
			background: rgba($black, 0.35) !important;
			backdrop-filter: blur(12px) !important;
			-webkit-backdrop-filter: blur(12px) !important;
			border-radius: 30px !important;
			border: 1px solid rgba($white, 0.1) !important;
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;

			svg {
				color: $purple !important;
				font-size: 13px !important;
				filter: drop-shadow(0 0 4px rgba($purple, 0.5));
			}
		}
	}

	// Text overlay
	.text {
		position: absolute;
		bottom: 0;
		left: 0;
		right: 0;
		padding: 20px 16px;
		z-index: 3;

		h3 {
			font-size: 12px;
			font-weight: 500;
			color: rgba($white, 0.7);
			margin: 0 0 2px;
			text-shadow: 0 1px 4px rgba(0, 0, 0, 0.4);
		}

		h1 {
			font-size: 20px;
			font-weight: 700;
			color: $white;
			margin: 0;
			text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
			display: -webkit-box;
			-webkit-box-orient: vertical;
			-webkit-line-clamp: 2;
			overflow: hidden;
			text-overflow: ellipsis;
		}
	}
}

// === COMMENT BOX — LIQUID GLASS TEXTAREA ===
.comment-box {
	position: relative;
	margin-top: 16px;
	background: rgba($white, 0.035);
	backdrop-filter: blur(24px);
	-webkit-backdrop-filter: blur(24px);
	border: 1px solid rgba($white, 0.08);
	border-radius: 20px;
	padding: 16px;
	box-shadow:
		0 8px 32px rgba(0, 0, 0, 0.15),
		inset 0 1px 0 rgba($white, 0.06),
		0 0 0 1px rgba($white, 0.02);
	overflow: hidden;
	transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);

	// Glass top-edge reflection
	&__reflection {
		position: absolute;
		top: 0;
		left: 8%;
		right: 8%;
		height: 1px;
		background: linear-gradient(
			90deg,
			transparent,
			rgba($white, 0.18),
			transparent
		);
		pointer-events: none;
		z-index: 2;
	}

	&:focus-within {
		border-color: rgba($purple, 0.25);
		box-shadow:
			0 0 0 3px rgba($purple, 0.06),
			0 12px 36px rgba(0, 0, 0, 0.2),
			inset 0 1px 0 rgba($white, 0.08),
			0 0 20px rgba($purple, 0.05);
		background: rgba($white, 0.045);

		.comment-box__reflection {
			background: linear-gradient(
				90deg,
				transparent,
				rgba($purple, 0.3),
				transparent
			);
		}
	}

	textarea {
		width: 100%;
		background: transparent;
		border: none;
		color: $white;
		font-size: 14px;
		line-height: 1.6;
		resize: none;
		outline: none;
		padding: 0;
		padding-right: 40px;
		font-family: inherit;

		&::placeholder {
			color: rgba($white, 0.25);
			font-style: italic;
		}
	}

	.icon {
		position: absolute;
		right: 16px;
		bottom: 16px;
		color: $purple;
		font-size: 18px;
		cursor: pointer;
		transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
		z-index: 3;
		width: 38px;
		height: 38px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 50%;
		background: rgba($purple, 0.1);
		border: 1px solid rgba($purple, 0.15);

		&:hover {
			transform: scale(1.12) translateY(-2px);
			background: rgba($purple, 0.2);
			border-color: rgba($purple, 0.35);
			box-shadow: 0 4px 16px rgba($purple, 0.25), 0 0 12px rgba($purple, 0.15);
			filter: drop-shadow(0 0 6px rgba($purple, 0.4));
		}

		&:active {
			transform: scale(0.95);
		}
	}
}

// === TITLE HIGHLIGHT — SHIMMER ===
.title {
	color: $white1;
	font-weight: 600;
	position: relative;

	.highlight {
		color: $purple;
		position: relative;
		text-shadow: 0 0 20px rgba($purple, 0.3);

		&::after {
			content: attr(data-text);
			position: absolute;
			top: 0;
			left: 0;
			background: linear-gradient(
				90deg,
				transparent 0%,
				rgba($white, 0.25) 50%,
				transparent 100%
			);
			background-size: 200% 100%;
			-webkit-background-clip: text;
			background-clip: text;
			-webkit-text-fill-color: transparent;
			animation: addShimmer 3s ease-in-out infinite;
		}
	}
}

@keyframes addShimmer {
	0% { background-position: -200% center; }
	100% { background-position: 200% center; }
}

// === LOADING SPINNER ===
.loading {
	display: flex;
	justify-content: center;
	align-items: center;
	height: 50vh;
	color: $purple;
	font-size: 28px;
	filter: drop-shadow(0 0 12px rgba($purple, 0.4));
}
</style>