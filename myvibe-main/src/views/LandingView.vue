<template>
	<section class="started">
		<div class="swiper-section">
			<swiper
				:effect="'coverflow'"
				:grabCursor="true"
				:loop="true"
				:centeredSlides="true"
				:slidesPerView="'auto'"
				:autoplay="{
					delay: 2500,
					disableOnInteraction: false
				}"
				:coverflowEffect="{
					rotate: 50,
					stretch: 0,
					depth: 100,
					modifier: 1,
					slideShadows: true
				}"
				:pagination="false"
				:modules="modules"
				class="myVibeSwipe"
			>
				<swiper-slide style="background-image: url('https://cdn-images.dzcdn.net/images/artist/ed04cfc9f8c7c016277edf011439d0ac/1000x1000-000000-80-0-0.jpg')"></swiper-slide>
				<swiper-slide style="background-image: url('https://cdn-images.dzcdn.net/images/artist/c651ef540834eaf05d385f5638dbe720/1000x1000-000000-80-0-0.jpg')"></swiper-slide>
				<swiper-slide style="background-image: url('https://cdn-images.dzcdn.net/images/artist/858326f41aab9458e91392c08e29d37b/1000x1000-000000-80-0-0.jpg')"></swiper-slide>
				<swiper-slide style="background-image: url('https://cdn-images.dzcdn.net/images/artist/8eab1a9a644889aabaca1e193e05f984/1000x1000-000000-80-0-0.jpg')"></swiper-slide>
				<swiper-slide style="background-image: url('https://cdn-images.dzcdn.net/images/artist/6d6503360e6cc202c9a1ed8617f3296b/1000x1000-000000-80-0-0.jpg')"></swiper-slide>
				<swiper-slide style="background-image: url('https://cdn-images.dzcdn.net/images/artist/b468c00b0d6ba7af3d95be530e1a2a08/1000x1000-000000-80-0-0.jpg')"></swiper-slide>
				<swiper-slide style="background-image: url('https://cdn-images.dzcdn.net/images/artist/ab78ffb62d9fd01181885316e55c868a/1000x1000-000000-80-0-0.jpg')"></swiper-slide>
				<swiper-slide style="background-image: url('https://cdn-images.dzcdn.net/images/artist/22dd86b628a03d8dad3c7dfb33320a91/1000x1000-000000-80-0-0.jpg')"></swiper-slide>
				<swiper-slide style="background-image: url('https://cdn-images.dzcdn.net/images/artist/245d3346a03388562d56ab5a63f6549d/1000x1000-000000-80-0-0.jpg')"></swiper-slide>
				<swiper-slide style="background-image: url('https://cdn-images.dzcdn.net/images/artist/3087954bca22f306324912e5ac8375c3/1000x1000-000000-80-0-0.jpg')"></swiper-slide>
			</swiper>
			<h1 class="title">Show Your <span class="highlight">Vibe</span> <span class="d-block">to The World!</span></h1>
		</div>
		<div class="bottom-float-action">
			<RouterLink to="/register" class="btn-action">Register Here!</RouterLink>
			<span class="or"> Or continue with</span>
			<div class="d-flex">
				<div class="login-socmed mb-4">
					<button class="btn-login-socmed" @click="signInGoogle">
						<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" style="margin-right: 5px" preserveAspectRatio="xMidYMid" viewBox="0 0 256 262" id="google">
							<path fill="#4285F4" d="M255.878 133.451c0-10.734-.871-18.567-2.756-26.69H130.55v48.448h71.947c-1.45 12.04-9.283 30.172-26.69 42.356l-.244 1.622 38.755 30.023 2.685.268c24.659-22.774 38.875-56.282 38.875-96.027"></path>
							<path fill="#34A853" d="M130.55 261.1c35.248 0 64.839-11.605 86.453-31.622l-41.196-31.913c-11.024 7.688-25.82 13.055-45.257 13.055-34.523 0-63.824-22.773-74.269-54.25l-1.531.13-40.298 31.187-.527 1.465C35.393 231.798 79.49 261.1 130.55 261.1"></path>
							<path fill="#FBBC05" d="M56.281 156.37c-2.756-8.123-4.351-16.827-4.351-25.82 0-8.994 1.595-17.697 4.206-25.82l-.073-1.73L15.26 71.312l-1.335.635C5.077 89.644 0 109.517 0 130.55s5.077 40.905 13.925 58.602l42.356-32.782"></path>
							<path fill="#EB4335" d="M130.55 50.479c24.514 0 41.05 10.589 50.479 19.438l36.844-35.974C195.245 12.91 165.798 0 130.55 0 79.49 0 35.393 29.301 13.925 71.947l42.211 32.783c10.59-31.477 39.891-54.251 74.414-54.251"></path>
						</svg>
						<span v-if="!loadingGoogle">Google</span>
						<span v-else> <fa icon="spinner" class="fa-spin" /></span>
					</button>
				</div>
				<div class="login-socmed mb-4">
					<button class="btn-login-socmed" @click="signInApple">
						<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" style="margin-right: 5px" viewBox="0 0 16 16" id="apple">
							<g fill-rule="evenodd">
								<path d="M11.6218,4.7571c-0.0193291,-2.02332 1.65317,-2.99846 1.72865,-3.04467c-0.939167,-1.37684 -2.40442,-1.56532 -2.92624,-1.58735c-1.24564,-0.125075 -2.431,0.734352 -3.06342,0.734352c-0.630702,0 -1.60726,-0.7153 -2.63953,-0.694961c-1.35921,0.0188997 -2.61125,0.789063 -3.31105,2.00483c-1.41025,2.44865 -0.360481,6.07511 1.01479,8.0616c0.671226,0.970026 1.47351,2.06429 2.52553,2.02476c1.0148,-0.0397892 1.39689,-0.654453 2.62078,-0.654453c1.22488,0 1.56888,0.654453 2.64224,0.634268c1.08898,-0.0196037 1.78139,-0.990762 2.44848,-1.96475c0.771157,-1.12822 1.0887,-2.22035 1.10719,-2.27707c-0.0234604,-0.0085233 -2.12539,-0.815491 -2.14743,-3.23655v0Z" transform="translate(.745 3.743)"></path>
								<path d="M2.49696,2.55553c0.559066,-0.677623 0.934193,-1.61793 0.831414,-2.55553c-0.804695,0.031969 -1.78039,0.537152 -2.3568,1.21266c-0.51813,0.599437 -0.97158,1.55665 -0.848624,2.47462c0.897671,0.0697992 1.81466,-0.455431 2.37401,-1.13175v0Z" transform="translate(7.857)"></path>
							</g>
						</svg>
						<span v-if="!loadingApple">Apple</span>
						<span v-else> <fa icon="spinner" class="fa-spin" /></span>
					</button>
				</div>
			</div>
			<p>Already a member? <RouterLink to="/login">Sign in Here!</RouterLink></p>
		</div>
		<span data-bs-toggle="modal" data-bs-target="#notifModal" ref="notifModalBtn"></span>
		<div class="modal fade" id="notifModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="notifModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body notification">
						<div class="title">
							<h1>
								{{ titleNotif }} <span>{{ titleNotifSecond }}</span>
							</h1>
						</div>
						<p class="m-0 text-center">{{ message }}</p>
					</div>
					<div class="modal-button" v-if="showLink">
						<RouterLink :to="linkTo" class="btn-block purple" @click="clickLink"> {{ linkText }} </RouterLink>
					</div>
					<div class="modal-button" v-if="showDismiss">
						<button type="button" class="btn-block" data-bs-dismiss="modal" aria-label="Close">Close</button>
					</div>
				</div>
			</div>
		</div>
		<!-- <span data-bs-toggle="modal" data-bs-target="#notifModalGoogle" ref="notifModalGoogleBtn"></span>
		<div class="modal fade" id="notifModalGoogle" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="notifModalGoogleLabel" aria-hidden="true">
			<div class="modal-dialog modal-sm modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body notification">
						<div class="logo">
							<img src="@/assets/logo.png" alt="" />
							<h1>Hello <span>There</span></h1>
						</div>
						<p class="m-0">"We value your privacy. By signing in with Google, we will only collect your name and email address. This information is used solely for authentication purposes, and we do not store or use your data for any other reason. By continuing, you agree to provide access to your name and email address for login purposes only."</p>
					</div>
					<div class="modal-button">
						<button class="btn-block purple" @click="signInGoogle">Sure, Let’s Go!</button>
					</div>
					<div class="modal-button">
						<button type="button" class="btn-block" data-bs-dismiss="modal" aria-label="Close">Close</button>
					</div>
				</div>
			</div>
		</div> -->
	</section>
</template>

<script>
import { Swiper, SwiperSlide } from 'swiper/vue';

import 'swiper/css';

import 'swiper/css/effect-coverflow';

import { Autoplay, EffectCoverflow } from 'swiper/modules';

import authService from '@/services/authService';
import axios from 'axios';
import { googleTokenLogin } from 'vue3-google-login';
import { requestPermission } from '@/services/firebaseMessaging';
import { Capacitor } from '@capacitor/core';
import { GoogleAuth, User } from '@codetrix-studio/capacitor-google-auth';
import { SignInWithApple } from '@capacitor-community/apple-sign-in';

export default {
	name: 'LandingView',
	components: {
		Swiper,
		SwiperSlide
	},
	data() {
		return {
			loading: false,
			loadingGoogle: false,
			loadingApple: false,
			titleNotif: '',
			titleNotifSecond: '',
			message: '',
			showLink: false,
			linkTo: '/',
			linkText: '',
			showDismiss: false
		};
	},
	setup() {
		return {
			modules: [Autoplay, EffectCoverflow]
		};
	},
	mounted() {
		const isMobile = Capacitor.isNativePlatform();
		if (isMobile) {
			GoogleAuth.initialize({
				clientId: '46760281130-718s2v8sbmtiu3h1ak5c2shchucfak8k.apps.googleusercontent.com',
				scopes: ['profile', 'email'],
				grantOfflineAccess: true
			});
		}
	},
	methods: {
		// signInGoogleCheck() {
		// 	this.$refs.notifModalGoogleBtn.click();
		// },

		signInGoogle() {
			this.loadingGoogle = true;
			var backdrop = document.querySelector('.modal-backdrop');
			if (backdrop) {
				backdrop.remove();
			}
			const isMobile = Capacitor.isNativePlatform();
			if (isMobile) {
				GoogleAuth.signIn()
					.then((response) => {
						const responseGoogle = response;
						this.completeLogin(responseGoogle);
					})
					.catch((error) => {
						this.loadingGoogle = false;
						this.titleNotif = 'Login';
						this.titleNotifSecond = 'Failed';
						this.message = 'Looks like something went wrong when you tried to log in';
						this.showLink = false;
						this.showDismiss = true;
						this.showNotifModal();
					});
			} else {
				googleTokenLogin()
					.then((response) => {
						this.completeLoginWeb(response.access_token);
					})
					.catch((error) => {
						this.loadingGoogle = false;
						this.titleNotif = 'Login';
						this.titleNotifSecond = 'Failed';
						this.message = 'Looks like something went wrong when you tried to log in';
						this.showLink = false;
						this.showDismiss = true;
						this.showNotifModal();
					});
			}
		},

		async signInApple() {
			this.loadingApple = true;
			try {
				const response = await SignInWithApple.authorize({
					clientId: 'co.myvibe.app',
					redirectURI: 'https://myvibeapp.co/dashboard',
					scopes: 'email name'
				});
				const responseNew = {
					name: `${response.response.givenName} ${response.response.familyName}`,
					email: response.response.email,
					user: response.response.user
				};
				this.completeLoginApple(responseNew);
			} catch (error) {
				this.loadingApple = false;
				this.titleNotif = 'Login';
				this.titleNotifSecond = 'Failed';
				this.message = 'Looks like something went wrong when you tried to log in';
				this.showLink = false;
				this.showDismiss = true;
				this.showNotifModal();
			}
		},

		async completeLoginApple(data) {
			try {
				let responseApi = '';
				if (!data.email) {
					responseApi = await authService.loginGoogle({
						user_apple_id: data.user
					});
				} else {
					responseApi = await authService.loginGoogle({
						name: data.name,
						email: data.email,
						user_apple_id: data.user
					});
				}
				if (responseApi.data && responseApi.data.token) {
					const token = responseApi.data.token;
					localStorage.setItem('token', token);
					this.getProfile();
				} else {
					this.loadingApple = false;
					this.titleNotif = 'Login';
					this.titleNotifSecond = 'Failed';
					this.message = 'Looks like something went wrong when you tried to log in';
					this.showLink = false;
					this.showDismiss = true;
					this.showNotifModal();
				}
			} catch (error) {
				this.loadingApple = false;
				this.titleNotif = 'Login';
				this.titleNotifSecond = 'Failed';
				this.message = 'Error fetching Google profile data';
				this.showLink = false;
				this.showDismiss = true;
				this.showNotifModal();
				throw error;
			}
		},

		async completeLogin(responseGoogle) {
			try {
				const responseApi = await authService.loginGoogle({
					name: responseGoogle.name,
					email: responseGoogle.email
				});
				if (responseApi.data && responseApi.data.token) {
					const token = responseApi.data.token;
					localStorage.setItem('token', token);
					this.getProfile();
				} else {
					this.loadingGoogle = false;
					this.titleNotif = 'Login';
					this.titleNotifSecond = 'Failed';
					this.message = 'Looks like something went wrong when you tried to log in';
					this.showLink = false;
					this.showDismiss = true;
					this.showNotifModal();
				}
			} catch (error) {
				this.loadingGoogle = false;
				this.titleNotif = 'Login';
				this.titleNotifSecond = 'Failed';
				this.message = 'Error fetching Google profile data';
				this.showLink = false;
				this.showDismiss = true;
				this.showNotifModal();
				throw error;
			}
		},

		async completeLoginWeb(token) {
			const url = 'https://www.googleapis.com/oauth2/v3/userinfo';
			try {
				const response = await axios.get(url, {
					headers: {
						Authorization: `Bearer ${token}`
					}
				});
				const responseApi = await authService.loginGoogle({
					name: response.data.name,
					email: response.data.email
				});
				if (responseApi.data && responseApi.data.token) {
					const token = responseApi.data.token;
					localStorage.setItem('token', token);
					this.getProfile();
				} else {
					this.loadingGoogle = false;
					this.titleNotif = 'Login';
					this.titleNotifSecond = 'Failed';
					this.message = 'Looks like something went wrong when you tried to log in';
					this.showLink = false;
					this.showDismiss = true;
					this.showNotifModal();
				}
			} catch (error) {
				this.loadingGoogle = false;
				this.titleNotif = 'Login';
				this.titleNotifSecond = 'Failed';
				this.message = 'Error fetching Google profile data';
				this.showLink = false;
				this.showDismiss = true;
				this.showNotifModal();
				throw error;
			}
		},

		async getProfile() {
			try {
				const responseApiProfile = await authService.profile();
				this.requestPushPermission();
				if (!responseApiProfile.data.username) {
					this.$router.push('/fill-vibe');
				} else {
					this.$router.push('/dashboard');
				}
				this.loginGoogle = false;
			} catch (error) {
				this.loadingGoogle = false;
				this.titleNotif = 'Login';
				this.titleNotifSecond = 'Failed';
				this.message = 'Error fetching data';
				this.showLink = false;
				this.showDismiss = true;
				this.showNotifModal();
				throw error;
			}
		},

		async login() {
			this.loading = true;
			try {
				const response = await authService.login({
					email: this.email,
					password: this.password
				});
				if (response.data && response.data.token) {
					const token = response.data.token;
					localStorage.setItem('token', token);
					this.requestPushPermission();
					if (!response.data.user.mbti_id) {
						this.$router.push('/fill-vibe');
					} else {
						this.$router.push('/dashboard');
					}
					this.loginGoogle = false;
				} else {
					this.loading = false;
					this.titleNotif = 'Login';
					this.titleNotifSecond = 'Failed';
					this.message = 'Looks like something went wrong when you tried to log in';
					this.showLink = false;
					this.showDismiss = true;
					this.showNotifModal();
				}
			} catch (error) {
				console.log(error);
				this.titleNotif = 'Login';
				this.titleNotifSecond = 'Failed';
				this.message = error.response.data.message;
				this.showLink = false;
				this.showDismiss = true;
				this.linkTo = '';
				this.showNotifModal();
			} finally {
				this.loading = false;
			}
		},

		showNotifModal() {
			this.$refs.notifModalBtn.click();
		},

		async requestPushPermission() {
			const token = await requestPermission();
			if (token) {
				const formData = new FormData();
				formData.append('_method', 'PUT');
				formData.append('fcm_token', token);
				const response = await authService.profile_put(formData);
			} else {
				console.error('Notification Permission Denied.');
			}
		}
	}
};
</script>
