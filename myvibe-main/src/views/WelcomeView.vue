<template>
	<section class="welcome-screen">
		<!-- Blobs -->
		<div class="wl-blob wl-blob--1"></div>
		<div class="wl-blob wl-blob--2"></div>
		<div class="wl-blob wl-blob--3"></div>
		<div class="wl-blob wl-blob--4"></div>

		<!-- Particles -->
		<div class="wl-particles">
			<div class="wl-particle" v-for="n in 12" :key="n" :style="particleStyle(n)"></div>
		</div>

		<!-- Content -->
		<div class="wl-content">
			<!-- Animated checkmark -->
			<div class="wl-check-ring">
				<div class="wl-check-circle">
					<svg class="wl-checkmark" viewBox="0 0 52 52">
						<circle class="wl-checkmark__circle" cx="26" cy="26" r="25" fill="none" />
						<path class="wl-checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
					</svg>
				</div>
			</div>

			<!-- Text -->
			<div class="wl-text">
				<h1 class="wl-title">You're All Set!</h1>
				<p class="wl-subtitle">Your vibe is ready to shine</p>
			</div>

			<!-- Tagline cards -->
			<div class="wl-cards">
				<div class="wl-card wl-card--1">
					<fa icon="music" class="wl-card__icon" />
					<span>Share your music taste</span>
				</div>
				<div class="wl-card wl-card--2">
					<fa icon="film" class="wl-card__icon" />
					<span>Rate your favorite films</span>
				</div>
				<div class="wl-card wl-card--3">
					<fa icon="users" class="wl-card__icon" />
					<span>Connect with vibers</span>
				</div>
			</div>

			<!-- Progress bar -->
			<div class="wl-progress">
				<div class="wl-progress__bar"></div>
			</div>
			<p class="wl-redirect-text">Taking you to your dashboard...</p>
		</div>
	</section>
</template>

<script>
export default {
	name: 'WelcomeView',
	mounted() {
		// Auto redirect to dashboard after 4 seconds
		this.redirectTimer = setTimeout(() => {
			this.$router.push('/dashboard?from=welcome');
		}, 4000);
	},
	beforeUnmount() {
		if (this.redirectTimer) {
			clearTimeout(this.redirectTimer);
		}
	},
	methods: {
		particleStyle(n) {
			const angle = (n / 12) * 360;
			const delay = (n * 0.15) + 0.5;
			const size = 4 + Math.random() * 6;
			const distance = 80 + Math.random() * 60;
			return {
				'--angle': angle + 'deg',
				'--delay': delay + 's',
				'--size': size + 'px',
				'--distance': distance + 'px'
			};
		}
	}
};
</script>

<style lang="scss" scoped>
@import '@/assets/scss/color.scss';

.welcome-screen {
	position: relative;
	width: 100%;
	height: 100%;
	display: flex;
	justify-content: center;
	align-items: center;
	overflow: hidden;
	background: $background;
}

// === BLOBS ===
.wl-blob {
	position: absolute;
	filter: blur(55px);
	pointer-events: none;
	z-index: 0;
}

.wl-blob--1 {
	top: -10%;
	left: -15%;
	width: min(320px, 72vw);
	height: min(320px, 72vw);
	background: radial-gradient(circle, rgba($purple, 0.35) 0%, rgba(#6c5ce7, 0.12) 40%, transparent 70%);
	border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
	animation: wlBlobMorph1 10s ease-in-out infinite;
}

.wl-blob--2 {
	bottom: -5%;
	right: -12%;
	width: min(280px, 64vw);
	height: min(280px, 64vw);
	background: radial-gradient(circle, rgba(#4a3adf, 0.3) 0%, rgba($purple, 0.1) 40%, transparent 70%);
	border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
	animation: wlBlobMorph2 12s ease-in-out infinite;
}

.wl-blob--3 {
	top: 30%;
	right: -8%;
	width: min(200px, 46vw);
	height: min(200px, 46vw);
	background: radial-gradient(circle, rgba(#9b8fff, 0.2) 0%, transparent 70%);
	border-radius: 50% 30% 40% 60% / 40% 60% 50% 50%;
	animation: wlBlobMorph3 14s ease-in-out infinite;
}

.wl-blob--4 {
	bottom: 20%;
	left: -10%;
	width: min(180px, 40vw);
	height: min(180px, 40vw);
	background: radial-gradient(circle, rgba($purple, 0.18) 0%, transparent 70%);
	border-radius: 60% 40% 60% 40% / 50% 50% 50% 50%;
	animation: wlBlobMorph1 16s ease-in-out infinite reverse;
}

@keyframes wlBlobMorph1 {
	0%, 100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(0, 0) rotate(0deg); }
	50% { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; transform: translate(15px, 20px) rotate(45deg); }
}
@keyframes wlBlobMorph2 {
	0%, 100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; transform: translate(0, 0) rotate(0deg); }
	50% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translate(-18px, -14px) rotate(-45deg); }
}
@keyframes wlBlobMorph3 {
	0%, 100% { border-radius: 50% 30% 40% 60% / 40% 60% 50% 50%; transform: translate(0, 0) rotate(0deg); }
	50% { border-radius: 40% 60% 50% 40% / 60% 40% 60% 50%; transform: translate(-10px, 12px) rotate(30deg); }
}

// === PARTICLES ===
.wl-particles {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	width: 0;
	height: 0;
	z-index: 1;
}

.wl-particle {
	position: absolute;
	width: var(--size);
	height: var(--size);
	border-radius: 50%;
	background: $purple;
	opacity: 0;
	animation: wlParticleBurst 1.5s ease-out var(--delay) forwards;
	box-shadow: 0 0 6px rgba($purple, 0.5);
}

@keyframes wlParticleBurst {
	0% {
		opacity: 0.8;
		transform: rotate(var(--angle)) translateY(0);
	}
	60% {
		opacity: 0.6;
	}
	100% {
		opacity: 0;
		transform: rotate(var(--angle)) translateY(calc(var(--distance) * -1));
	}
}

// === CONTENT ===
.wl-content {
	position: relative;
	z-index: 2;
	display: flex;
	flex-direction: column;
	align-items: center;
	padding: 0 24px;
}

// === CHECKMARK ===
.wl-check-ring {
	width: 100px;
	height: 100px;
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	position: relative;
	margin-bottom: 24px;
	animation: wlRingAppear 0.6s ease-out 0.3s both;

	background: rgba($white, 0.04);
	backdrop-filter: blur(16px);
	-webkit-backdrop-filter: blur(16px);
	border: 1px solid rgba($purple, 0.2);
	box-shadow: 0 8px 32px rgba($purple, 0.15), inset 0 1px 0 rgba($white, 0.06);

	&::before {
		content: '';
		position: absolute;
		inset: -6px;
		border-radius: 50%;
		border: 1px solid rgba($purple, 0.1);
		animation: wlOuterRing 2s ease-in-out 1s infinite;
	}

	&::after {
		content: '';
		position: absolute;
		top: 3px;
		left: 18%;
		right: 18%;
		height: 30%;
		background: linear-gradient(180deg, rgba($white, 0.1) 0%, transparent 100%);
		border-radius: 50%;
		pointer-events: none;
	}
}

.wl-check-circle {
	width: 60px;
	height: 60px;
}

.wl-checkmark {
	width: 60px;
	height: 60px;
	border-radius: 50%;
	stroke-width: 2;
	stroke: $purple;
	stroke-miterlimit: 10;
}

.wl-checkmark__circle {
	stroke-dasharray: 166;
	stroke-dashoffset: 166;
	stroke: rgba($purple, 0.3);
	animation: wlStroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) 0.5s forwards;
}

.wl-checkmark__check {
	stroke-dasharray: 48;
	stroke-dashoffset: 48;
	stroke: $purple;
	stroke-width: 3;
	stroke-linecap: round;
	animation: wlStroke 0.4s cubic-bezier(0.65, 0, 0.45, 1) 0.9s forwards;
}

@keyframes wlStroke {
	100% { stroke-dashoffset: 0; }
}

@keyframes wlRingAppear {
	from { opacity: 0; transform: scale(0.5); }
	to { opacity: 1; transform: scale(1); }
}

@keyframes wlOuterRing {
	0%, 100% { opacity: 0.3; transform: scale(1); }
	50% { opacity: 0.7; transform: scale(1.06); }
}

// === TEXT ===
.wl-text {
	text-align: center;
	margin-bottom: 28px;
}

.wl-title {
	color: $white1;
	font-size: 28px;
	font-weight: 700;
	margin-bottom: 6px;
	animation: wlTextReveal 0.7s ease-out 1.1s both;

	text-shadow: 0 0 30px rgba($purple, 0.2);
}

.wl-subtitle {
	color: rgba($white1, 0.45);
	font-size: 14px;
	animation: wlTextReveal 0.7s ease-out 1.3s both;
}

@keyframes wlTextReveal {
	from { opacity: 0; transform: translateY(16px); filter: blur(6px); }
	to { opacity: 1; transform: translateY(0); filter: blur(0); }
}

// === FEATURE CARDS ===
.wl-cards {
	display: flex;
	flex-direction: column;
	gap: 10px;
	width: 100%;
	max-width: 300px;
	margin-bottom: 32px;
}

.wl-card {
	display: flex;
	align-items: center;
	gap: 12px;
	padding: 14px 18px;
	border-radius: 16px;
	background: rgba($white, 0.03);
	backdrop-filter: blur(16px);
	-webkit-backdrop-filter: blur(16px);
	border: 1px solid rgba($white, 0.06);
	box-shadow: inset 0 1px 0 rgba($white, 0.04), 0 2px 10px rgba(0, 0, 0, 0.1);
	color: rgba($white1, 0.7);
	font-size: 13px;
	font-weight: 400;
	position: relative;

	&::before {
		content: '';
		position: absolute;
		top: 0; left: 0; right: 0;
		height: 45%;
		background: linear-gradient(180deg, rgba($white, 0.025) 0%, transparent 100%);
		border-radius: 16px 16px 0 0;
		pointer-events: none;
	}

	&__icon {
		font-size: 16px;
		color: $purple;
		width: 20px;
		text-align: center;
		flex-shrink: 0;
	}

	&--1 { animation: wlCardSlide 0.6s ease-out 1.5s both; }
	&--2 { animation: wlCardSlide 0.6s ease-out 1.7s both; }
	&--3 { animation: wlCardSlide 0.6s ease-out 1.9s both; }
}

@keyframes wlCardSlide {
	from { opacity: 0; transform: translateX(-20px); }
	to { opacity: 1; transform: translateX(0); }
}

// === PROGRESS BAR ===
.wl-progress {
	width: 200px;
	height: 3px;
	border-radius: 3px;
	background: rgba($white, 0.06);
	overflow: hidden;
	margin-bottom: 10px;
	animation: wlFadeIn 0.5s ease-out 2.2s both;
}

.wl-progress__bar {
	height: 100%;
	border-radius: 3px;
	background: linear-gradient(90deg, $purple, #9b8fff);
	width: 0;
	animation: wlProgressFill 3.5s ease-in-out 0.5s forwards;
	box-shadow: 0 0 8px rgba($purple, 0.4);
}

@keyframes wlProgressFill {
	from { width: 0; }
	to { width: 100%; }
}

.wl-redirect-text {
	color: rgba($white1, 0.3);
	font-size: 11px;
	letter-spacing: 0.3px;
	animation: wlFadeIn 0.5s ease-out 2.4s both;
}

@keyframes wlFadeIn {
	from { opacity: 0; }
	to { opacity: 1; }
}
</style>