<template>
	<section class="add pt-4" ref="sectionList" @scroll="handleScroll()">
		<div v-if="!isLoading">
			<div v-if="isHome" class="container">
				<div class="back-top">
					<RouterLink to="/dashboard"> <fa icon="arrow-left-long" class="text-white" /></RouterLink>
				</div>
				<h1 class="title text-center mb-5">Add New <span class="highlight">Vibe</span></h1>
				<ul class="category_lists row">
					<li v-for="section in category" :key="section.id" class="col-6 mb-4" @click="clickAdd(section)">
						<div class="content" :style="{ backgroundImage: `url(${section.image})` }">
							<h1>{{ section.title }}</h1>
							<fa icon="angle-right" class="icon" />
						</div>
					</li>
				</ul>
			</div>
			<div v-if="isSearch" class="container">
				<div class="back-top" @click="goBack()">
					<div><fa icon="arrow-left-long" class="text-white" /></div>
				</div>
				<h1 class="title text-center mb-5">
					Add New <span class="highlight">{{ searchOption.title }}</span>
				</h1>
				<div class="search-box">
					<input type="text" v-model="searchQuery" placeholder="Search" @keyup="debounceSearch()" />
					<div class="icon" @click="searchVibe()">
						<fa icon="search" />
					</div>
				</div>
				<ul v-if="!isLoadingResult" class="result_list">
					<li v-for="section in searchResult" :key="section.id" class="mb-4" @click="selectVibe(section)">
						<div class="content" :style="{ backgroundImage: `url(${section.image_url})` }">
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
			<div v-if="isRating" class="container rating-page">
				<div class="back" @click="goBackFromRating()">
					<div><fa icon="arrow-left-long" class="text-white" /></div>
				</div>
				<div class="background" :style="{ backgroundImage: `url(${selectedVibe.image_url})` }"></div>
				<div class="give-rating">
					<input type="text" v-model="ratingInput" @input="validateRating" maxlength="3" />
					<small>Out of 5</small>
					<div class="btn btn-sm btn-post" @click="giveComment()">Post It!</div>
				</div>
			</div>
			<div v-if="isComment" class="container">
				<div class="back-top" @click="goBackFromLast()">
					<div><fa icon="arrow-left-long" class="text-white" /></div>
				</div>
				<h1 class="title text-center mb-5">Tell the <span class="highlight">Vibes</span></h1>
				<div class="comment-content" :style="{ backgroundImage: `url(${selectedVibe.image_url})` }">
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
				<div class="comment-box">
					<textarea type="text" v-model="commentInput" placeholder="Write down your vibe here" maxlength="500" rows="4"> </textarea>
					<div class="icon" @click="postVibe()">
						<fa :icon="['far', 'paper-plane']" />
					</div>
				</div>
			</div>
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
			if (this.debounceTimeout) {
				clearTimeout(this.debounceTimeout);
			}
			this.debounceTimeout = setTimeout(() => {
				this.searchVibe();
			}, 1000);
		},
		async searchVibe() {
			if (!this.searchQuery.trim()) {
				this.searchResult = [];
			} else {
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
								if (response.data.data[i].type == 'artist') {
									response.data.data[i].type = 'Musician';
									this.searchResult.push({
										image_url: response.data.data[i].picture_xl,
										title: response.data.data[i].name,
										desc: response.data.data[i].type
									});
								} else if (response.data.data[i].type == 'track') {
									this.searchResult.push({
										image_url: response.data.data[i].album.cover_xl,
										title: response.data.data[i].title,
										desc: response.data.data[i].artist.name
									});
								} else if (response.data.data[i].type == 'album') {
									this.searchResult.push({
										image_url: response.data.data[i].cover_xl,
										title: response.data.data[i].title,
										desc: response.data.data[i].artist.name
									});
								}
							}
							if (response.data.next) {
								this.nextPage = true;
								this.nextPageUrl = response.data.next;
							} else {
								this.nextPage = false;
								this.nextPageUrl = '';
							}
							this.isLoadingResult = false;
						} catch (error) {
							console.error('Error fetching data:', error);
							this.isLoadingResult = false;
						}
					} else if (authority == 'api.themoviedb.org') {
						try {
							const response = await axios.get(apiUrl, {
								headers: {
									Authorization: `Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJjNmRkMGEyYzMzYWU5YzIwMjY5MzUzM2ExYjUxYzc4OCIsIm5iZiI6MTYyNzE0OTU3NS4wMDEsInN1YiI6IjYwZmM1NTA2MzEwMzI1MDAyMzczZDRlNCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.KkR9zGoqvA9P1U36rl37oSEyVS-M4rUtvpFpLCBl1bs`
								}
							});
							this.searchResult = [];
							if (this.searchOption.title == 'Top Shows') {
								for (let i = 0; i < response.data.results.length; i++) {
									this.searchResult.push({
										image_url: 'https://image.tmdb.org/t/p/original' + response.data.results[i].poster_path,
										title: response.data.results[i].name,
										desc: response.data.results[i].first_air_date
									});
								}
							} else {
								for (let i = 0; i < response.data.results.length; i++) {
									this.searchResult.push({
										image_url: 'https://image.tmdb.org/t/p/original' + response.data.results[i].poster_path,
										title: response.data.results[i].title,
										desc: response.data.results[i].release_date
									});
								}
							}
							if (response.data.total_pages > response.data.page) {
								this.nextPage = true;
								this.nextPageUrl = apiUrl.replace(/page=\d+/, 'page=' + (response.data.page + 1));
							} else {
								this.nextPage = false;
								this.nextPageUrl = '';
							}
							this.isLoadingResult = false;
						} catch (error) {
							console.error('Error fetching data:', error);
							this.isLoadingResult = false;
						}
					} else if (authority == 'www.googleapis.com') {
						try {
							const response = await axios.get(apiUrl);
							this.searchResult = [];
							for (let i = 0; i < response.data.items.length; i++) {
								let namesString = '';
								if (response.data.items[i].volumeInfo.authors) {
									namesString = response.data.items[i].volumeInfo.authors.join(', ');
								} else {
									namesString = response.data.items[i].volumeInfo.publisher;
								}
								if (response.data.items[i].volumeInfo.imageLinks.thumbnail.startsWith('http://')) {
									response.data.items[i].volumeInfo.imageLinks.thumbnail = response.data.items[i].volumeInfo.imageLinks.thumbnail.replace('http://', 'https://');
								}
								this.searchResult.push({
									image_url: response.data.items[i].volumeInfo.imageLinks.thumbnail.replace(/zoom=\d+/, 'zoom=0'),
									title: response.data.items[i].volumeInfo.title,
									desc: namesString
								});
								console.log(response.data.items[i].volumeInfo.title);
							}
							this.isLoadingResult = false;
						} catch (error) {
							console.error('Error fetching data:', error);
							this.isLoadingResult = false;
						}
					} else if (authority == 'itunes.apple.com') {
						const urlNew = apiUrl + '&type=podcast';
						try {
							const response = await axios.get(urlNew);
							this.searchResult = [];
							for (let i = 0; i < response.data.results.length; i++) {
								this.searchResult.push({
									image_url: response.data.results[i].artworkUrl600,
									title: response.data.results[i].trackName,
									desc: response.data.results[i].artistName
								});
							}
							this.isLoadingResult = false;
						} catch (error) {
							console.error('Error fetching data:', error);
							this.isLoadingResult = false;
						}
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
							if (response.data.data[i].type == 'artist') {
								response.data.data[i].type = 'Musician';
								this.searchResult.push({
									image_url: response.data.data[i].picture_big,
									title: response.data.data[i].name,
									desc: response.data.data[i].type
								});
							} else if (response.data.data[i].type == 'track') {
								response.data.data[i].type = response.data.data[i].artist.name;
								this.searchResult.push({
									image_url: response.data.data[i].artist.picture_big,
									title: response.data.data[i].title,
									desc: response.data.data[i].artist.name
								});
							} else if (response.data.data[i].type == 'album') {
								this.searchResult.push({
									image_url: response.data.data[i].cover_big,
									title: response.data.data[i].title,
									desc: response.data.data[i].artist.name
								});
							}
						}
						if (response.data.next) {
							this.nextPage = true;
							this.nextPageUrl = response.data.next;
						} else {
							this.nextPage = false;
							this.nextPageUrl = '';
						}
						this.isLoadingResult = false;
					} catch (error) {
						console.error('Error fetching data:', error);
					}
				} else if (authority == 'api.themoviedb.org') {
					try {
						const response = await axios.get(params, {
							headers: {
								Authorization: `Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJjNmRkMGEyYzMzYWU5YzIwMjY5MzUzM2ExYjUxYzc4OCIsIm5iZiI6MTYyNzE0OTU3NS4wMDEsInN1YiI6IjYwZmM1NTA2MzEwMzI1MDAyMzczZDRlNCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.KkR9zGoqvA9P1U36rl37oSEyVS-M4rUtvpFpLCBl1bs`
							}
						});
						if (this.searchOption.title == 'Top Shows') {
							for (let i = 0; i < response.data.results.length; i++) {
								this.searchResult.push({
									image_url: 'https://image.tmdb.org/t/p/original' + response.data.results[i].poster_path,
									title: response.data.results[i].name,
									desc: response.data.results[i].first_air_date
								});
							}
						} else {
							for (let i = 0; i < response.data.results.length; i++) {
								this.searchResult.push({
									image_url: 'https://image.tmdb.org/t/p/original' + response.data.results[i].poster_path,
									title: response.data.results[i].title,
									desc: response.data.results[i].release_date
								});
							}
						}
						if (response.data.total_pages > response.data.page) {
							this.nextPage = true;
							this.nextPageUrl = params.replace(/page=\d+/, 'page=' + (response.data.page + 1));
						} else {
							this.nextPage = false;
							this.nextPageUrl = '';
						}
						console.log(this.nextPageUrl);
						this.isLoadingResult = false;
					} catch (error) {
						console.error('Error fetching data:', error);
						this.isLoadingResult = false;
					}
				}
			}
		},
		handleScroll() {
			if (this.isSearch) {
				const container = this.$refs.sectionList;
				const scrollTop = container.scrollTop;
				const scrollHeight = container.scrollHeight;
				const clientHeight = container.clientHeight;
				if (scrollTop + clientHeight >= scrollHeight - 10) {
					this.loopVibe();
				}
			}
		},
		selectVibe(section) {
			this.selectedVibe = {
				...section,
				rating: 0.0
			};
			this.isHome = false;
			this.isSearch = false;
			this.isRating = true;
			this.isComment = false;
		},
		validateRating() {
			this.ratingInput = this.ratingInput.replace(',', '.');
			const parsed = parseFloat(this.ratingInput);
			if (this.ratingInput === '' || isNaN(parsed)) {
				this.selectedVibe.rating = 0.0;
				return;
			}
			if (parsed > 5.0) {
				this.ratingInput = '5.0';
				this.selectedVibe.rating = 5.0;
			} else if (parsed < 0.0) {
				this.ratingInput = '0.0';
				this.selectedVibe.rating = 0.0;
			} else {
				this.selectedVibe.rating = parsed;
			}
		},
		giveComment() {
			if (!this.ratingInput || isNaN(parseFloat(this.ratingInput))) {
				this.selectedVibe.rating = 0.0;
			} else {
				this.validateRating();
			}
			this.isHome = false;
			this.isSearch = false;
			this.isRating = false;
			this.isComment = true;
		},
		async postVibe() {
			this.isLoading = true;
			this.selectedVibe.category_id = this.searchOption.id;
			this.selectedVibe.comment = this.commentInput;
			this.selectedVibe.rating = this.selectedVibe.rating.toFixed(1);
			try {
				const response = await dashboardService.postVibe(this.selectedVibe);
				this.$router.push('/' + response.data.data.path);
				this.isLoading = false;
			} catch (error) {
				console.error('Error fetching data:', error);
			} finally {
				this.isHome = true;
				this.isSearch = false;
				this.isRating = false;
				this.isComment = false;
				this.searchOption = [];
				this.searchResult = [];
				this.selectedVibe = {};
				this.ratingInput = '0.0';
				this.commentInput = '';
			}
		},
		goBack() {
			this.isHome = true;
			this.isSearch = false;
			this.isRating = false;
			this.isComment = false;
			this.searchOption = [];
			this.searchResult = [];
			this.selectedVibe = {};
			this.ratingInput = '0.0';
			this.commentInput = '';
			this.searchQuery = '';
		},
		goBackFromRating() {
			this.isHome = false;
			this.isSearch = true;
			this.isRating = false;
			this.isComment = false;
		},
		goBackFromLast() {
			this.isHome = false;
			this.isSearch = false;
			this.isRating = true;
			this.isComment = false;
		}
	}
};
</script>
