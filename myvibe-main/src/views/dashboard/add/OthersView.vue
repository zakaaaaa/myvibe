<template>
	<section class="add">
		<div v-if="!isLoading">
			<div v-if="isHome" class="container">
				<div class="back">
					<RouterLink to="/add"> <fa icon="arrow-left-long" class="text-white" /></RouterLink>
				</div>
				<h1 class="title text-center mb-5">Add New <span class="highlight">List</span></h1>
				<div class="choose-box" @click="openList()">
					<div class="icon">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
							<g id="album_2_line" fill="none" fill-rule="evenodd">
								<path d="M24 0v24H0V0zM12.593 23.258l-.011.002-.071.035-.02.004-.014-.004-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01-.017.428.005.02.01.013.104.074.015.004.012-.004.104-.074.012-.016.004-.017-.017-.427c-.002-.01-.009-.017-.017-.018m.265-.113-.013.002-.185.093-.01.01-.003.011.018.43.005.012.008.007.201.093c.012.004.023 0 .029-.008l.004-.014-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014-.034.614c0 .012.007.02.017.024l.015-.002.201-.093.01-.008.004-.011.017-.43-.003-.012-.01-.01z" />
								<path fill="currentColor" d="M6 3a1 1 0 1 0 0 2h12a1 1 0 1 0 0-2zM4.18 6a2 2 0 0 0-1.972 2.329l1.666 10A2 2 0 0 0 5.847 20h12.306a2 2 0 0 0 1.973-1.671l1.666-10A2 2 0 0 0 19.82 6zm0 2h15.64l-1.667 10H5.847z" />
							</g>
						</svg>
					</div>
					<div class="text">{{ this.boxText }}</div>
					<div class="icon">
						<fa icon="chevron-down" />
					</div>
				</div>
				<ul v-if="isList" class="add-list">
					<li class="add">
						<input type="text" v-model="addCategoryQuery" placeholder="Add Playlist" />
						<div class="btn-add" @click="addPlaylist()"><fa icon="plus" /></div>
					</li>
					<li v-for="section in listCategory" :key="section.id">
						<span @click="selectVibe(section)">{{ section.title }}</span>
						<div class="btn-rm" @click="removePlaylist(section.id)"><fa icon="minus" /></div>
					</li>
				</ul>
				<div class="search-box" v-if="isFillImage" style="margin-top: 25px">
					<input type="text" v-model="image_url" placeholder="Copy image link from google" />
					<div class="icon text-white" @click="postImage()">
						<fa :icon="['far', 'paper-plane']" />
					</div>
				</div>
			</div>
			<div v-if="isRating" class="container rating-page">
				<div class="background" :style="{ backgroundImage: `url(${selectedPlaylist.image_url})` }"></div>
				<div class="give-rating">
					<input type="text" v-model="ratingInput" @input="validateRating" maxlength="3" />
					<small>Out of 5</small>
					<div class="btn btn-sm btn-post" @click="giveComment()">Post It!</div>
				</div>
			</div>
			<div v-if="isComment" class="container">
				<h1 class="title text-center mb-5">Tell the <span class="highlight">Vibes</span></h1>
				<div class="comment-content" :style="{ backgroundImage: `url(${selectedPlaylist.image_url})` }">
					<div class="rating">
						<h1>
							{{ selectedPlaylist.rating ? selectedPlaylist.rating.toFixed(1) : '0.0' }}
							<fa :icon="['fas', 'star']" />
						</h1>
					</div>
					<div class="text">
						<h1>{{ this.boxText }}</h1>
					</div>
				</div>
				<div class="comment-box">
					<input type="text" v-model="selectedPlaylist.title" placeholder="Write title here" />
				</div>
				<div class="comment-box">
					<input type="text" v-model="selectedPlaylist.desc" placeholder="Write sub title here" />
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
		<span data-bs-toggle="modal" data-bs-target="#notifModal" ref="notifModalBtn"></span>
		<div class="modal fade" id="notifModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="notifModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body notification">
						<div class="logo">
							<img src="@/assets/logo.png" alt="" />
							<h1>My <span>Vibe</span></h1>
						</div>
						<h2>{{ message }}</h2>
						<div class="btn-group-action">
							<button type="button" class="btn btn-sm btn-dismiss" data-bs-dismiss="modal" aria-label="Close" v-if="showDismiss" ref="dismissNotifModal"><fa icon="circle-xmark" /> Close</button>
							<RouterLink :to="linkTo" class="btn btn-sm btn-link" v-if="showLink" @click="confirmLink"> <fa icon="circle-check" /> {{ linkText }} </RouterLink>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</template>

<script>
import axios from 'axios';
import dashboardService from '@/services/dashboardService';

export default {
	name: 'OthersView',
	data() {
		return {
			isLoading: false,
			isHome: true,
			isList: false,
			isFillImage: false,
			isRating: false,
			isComment: false,
			boxText: 'Choose Playlist',
			addCategoryQuery: '',
			image_url: '',
			listCategory: [],
			selectedPlaylist: [],
			message: '',
			showLink: false,
			linkTo: '/',
			linkText: 'Okay',
			showDismiss: false,
			deletedId: '',
			ratingInput: '0.0',
			commentInput: ''
		};
	},
	mounted() {
		this.getOthers();
	},
	methods: {
		openList() {
			this.isList = true;
			this.isFillImage = false;
		},
		async getOthers() {
			this.deletedId = '';
			this.addCategoryQuery = '';
			this.boxText = 'Choose Playlist';
			this.isLoading = true;
			try {
				const response = await dashboardService.getCategoryOther();
				this.listCategory = response.data.data;
				this.isLoading = false;
			} catch (error) {
				console.error('Error fetching categories:', error);
				this.isLoading = false;
			}
		},
		async addPlaylist() {
			this.deletedId = '';
			if (this.addCategoryQuery) {
				this.isLoading = true;
				try {
					const params = {
						title: this.addCategoryQuery
					};
					const response = await dashboardService.postCategoryOther(params);
					this.getOthers();
					this.isLoading = false;
				} catch (error) {
					console.error('Error add playlist:', error);
					this.isLoading = false;
				}
			}
		},
		selectVibe(section) {
			this.selectedPlaylist = section;
			this.boxText = section.title;
			this.selectedPlaylist.title = '';
			this.isList = false;
			this.isFillImage = true;
		},
		async removePlaylist(id) {
			this.message = 'Are you sure to remove this playlist?';
			this.showLink = true;
			this.showDismiss = true;
			this.linkText = 'Yes';
			this.linkTo = '';
			this.deletedId = id;
			this.showNotifModal();
		},
		async confirmLink() {
			try {
				await dashboardService.deleteCategoryOther(this.deletedId);
				this.getOthers();
				this.dismissNotifModal();
			} catch (error) {
				this.message = 'Failed to remove';
				this.showLink = false;
				this.showDismiss = true;
				this.linkTo = '';
				this.showNotifModal();
			} finally {
				var backdrop = document.querySelector('.modal-backdrop');
				if (backdrop) {
					backdrop.remove();
				}
			}
		},
		showNotifModal() {
			this.$refs.notifModalBtn.click();
		},
		dismissNotifModal() {
			this.$refs.dismissNotifModal.click();
		},
		async postImage() {
			if (this.image_url) {
				try {
					const response = await axios.get(this.image_url, { method: 'HEAD', mode: 'cors' });
					const contentType = response.headers.get('Content-Type');
					if (contentType && contentType.startsWith('image/')) {
						this.selectedPlaylist.image_url = this.image_url;
						this.isHome = false;
						this.isList = false;
						this.isFillImage = false;
						this.isRating = true;
						this.isComment = false;
					} else {
						this.message = 'The URL is not a valid image.';
						this.showLink = false;
						this.showDismiss = true;
						this.linkTo = '';
						this.showNotifModal();
					}
				} catch (error) {
					this.message = 'The URL is not accessible.';
					this.showLink = false;
					this.showDismiss = true;
					this.linkTo = '';
					this.showNotifModal();
				}
			}
		},
		validateRating() {
			this.ratingInput = this.ratingInput.replace(',', '.');
			const parsed = parseFloat(this.ratingInput);
			if (this.ratingInput === '' || isNaN(parsed)) {
				this.selectedPlaylist.rating = 0.0;
				return;
			}
			if (parsed > 5.0) {
				this.ratingInput = '5.0';
				this.selectedPlaylist.rating = 5.0;
			} else if (parsed < 0.0) {
				this.ratingInput = '0.0';
				this.selectedPlaylist.rating = 0.0;
			} else {
				this.selectedPlaylist.rating = parsed;
			}
		},
		giveComment() {
			this.ratingInput = this.ratingInput.replace(',', '.');
			const parsed = parseFloat(this.ratingInput);
			if (this.ratingInput === '' || isNaN(parsed)) {
				this.selectedPlaylist.rating = 0.0;
			}
			this.isHome = false;
			this.isList = false;
			this.isFillImage = false;
			this.isRating = false;
			this.isComment = true;
		},
		async postVibe() {
			this.isLoading = true;
			const postData = {
				category_other_id: this.selectedPlaylist.id,
				image_url: this.selectedPlaylist.image_url,
				rating: this.selectedPlaylist.rating.toFixed(1),
				title: this.selectedPlaylist.title,
				desc: this.selectedPlaylist.desc,
				comment: this.commentInput
			};
			try {
				const response = await dashboardService.postOtherVibe(postData);
				const getPath = response.data.data.path.split('/').filter(Boolean);
				this.$router.push('/' + getPath[0] + '/other/' + getPath[1]);
			} catch (error) {
				console.error('Error fetching data:', error);
			}
		}
	}
};
</script>
