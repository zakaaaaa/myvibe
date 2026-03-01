<template>
	<div class="container">
		<div class="vibe-profile mt-4">
			<div class="profile-preview">
				<div class="image"><img :src="profile.profile_picture" alt="" /></div>
				<div class="info">
					<h1>{{ profile.name }}</h1>
					<div class="count">
						<div class="followers">
							<span class="fw-bold">{{ profile.follower }}</span> Followers
						</div>
						<div class="following">
							<span class="fw-bold">{{ profile.following }}</span> Following
						</div>
					</div>
					<ul class="summary">
						<li v-if="profile.mbti?.mbti_name">
							<span>{{ profile.mbti.mbti_name }}</span>
						</li>
						<li v-if="profile.zodiac?.zodiac_name">
							<span>{{ profile.zodiac.zodiac_name }}</span>
						</li>
						<li v-if="profile.enthusiast">
							<span>{{ profile.enthusiast }}</span>
						</li>
						<li v-if="profile.relationship?.relationship_name">
							<span>{{ profile.relationship.relationship_name }}</span>
						</li>
					</ul>
				</div>
				<div class="icon" @click="goSetting(profile.username)">
					<img src="@/assets/setting-btn.svg" alt="Logo" />
				</div>
			</div>
		</div>
		<div v-for="section in home" :key="section.id" class="vibe-section-preview mb-1">
			<div v-if="section.vibes.length > 0" class="content">
				<div class="head">
					<h1 class="title">
						{{ section.title[0] }} <span class="highlight">{{ section.title[1] }}</span>
					</h1>
					<RouterLink :to="'/more/' + section.id" class="more-btn"><fa :icon="['fas', 'angle-right']" /></RouterLink>
				</div>
				<ul :class="[{ 'justify-content-start': section.vibes.length < 3 }, { 'justify-content-space-between m-0': section.vibes.length >= 3 }, { twothree: ['Film', 'Top', 'Reading'].includes(section.title[0]) }]">
					<li v-for="vibe in section.vibes" :key="vibe.id" :style="{ backgroundImage: `url(${vibe.image_url})` }" @click="goDetail(profile.username + '/' + vibe.id)">
						<div class="caption">
							<h3>{{ vibe.desc }}</h3>
							<h1>{{ vibe.title.slice(0, 20) }}</h1>
							<div class="stars">
								<fa v-for="star in Math.floor(Number(vibe.rating))" :key="`star-${star}`" :icon="['fas', 'star']" />
								<fa v-if="Number(vibe.rating) % 1 >= 0.5" :key="`half-star-${vibe.id}`" :icon="['fas', 'star-half-stroke']" />
								<fa v-for="emptyStar in 5 - (Math.floor(Number(vibe.rating)) + (Number(vibe.rating) % 1 >= 0.5 ? 1 : 0))" :key="`empty-star-${emptyStar}`" :icon="['far', 'star']" />
							</div>
						</div>
					</li>
				</ul>
			</div>
		</div>
		<div v-for="section in others" :key="section.id" class="vibe-section-preview mb-1">
			<div v-if="section.vibes.length > 0" class="content">
				<div class="head">
					<h1 class="title">
						{{ section.category_title }}
					</h1>
					<RouterLink :to="'/more-other/' + section.category_id" class="more-btn"><fa :icon="['fas', 'angle-right']" /></RouterLink>
				</div>
				<ul :class="[{ 'justify-content-start': section.vibes.length < 3 }, { 'justify-content-space-between m-0': section.vibes.length >= 3 }]">
					<li v-for="vibe in section.vibes" :key="vibe.id" :style="{ backgroundImage: `url(${vibe.image_url})` }" @click="goDetail(profile.username + '/other/' + vibe.id)">
						<div class="caption">
							<h3>{{ vibe.desc }}</h3>
							<h1>{{ vibe.title.slice(0, 20) }}</h1>
							<div class="stars">
								<fa v-for="star in Math.floor(Number(vibe.rating))" :key="`star-${star}`" :icon="['fas', 'star']" />
								<fa v-if="Number(vibe.rating) % 1 >= 0.5" :key="`half-star-${vibe.id}`" :icon="['fas', 'star-half-stroke']" />
								<fa v-for="emptyStar in 5 - (Math.floor(Number(vibe.rating)) + (Number(vibe.rating) % 1 >= 0.5 ? 1 : 0))" :key="`empty-star-${emptyStar}`" :icon="['far', 'star']" />
							</div>
						</div>
					</li>
				</ul>
			</div>
		</div>
		<div class="pt-5"></div>
		<RouterLink to="/add" class="floating-add-btn">
			<svg xmlns="http://www.w3.org/2000/svg" width="50px" height="50px" viewBox="0 0 24 24">
				<title>add_circle_fill</title>
				<g id="add_circle_fill" fill="none">
					<path d="M24 0v24H0V0zM12.593 23.258l-.011.002-.071.035-.02.004-.014-.004-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01-.017.428.005.02.01.013.104.074.015.004.012-.004.104-.074.012-.016.004-.017-.017-.427c-.002-.01-.009-.017-.017-.018m.265-.113-.013.002-.185.093-.01.01-.003.011.018.43.005.012.008.007.201.093c.012.004.023 0 .029-.008l.004-.014-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014-.034.614c0 .012.007.02.017.024l.015-.002.201-.093.01-.008.004-.011.017-.43-.003-.012-.01-.01z" />
					<path fill="currentColor" d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2m0 5a1 1 0 0 0-.993.883L11 8v3H8a1 1 0 0 0-.117 1.993L8 13h3v3a1 1 0 0 0 1.993.117L13 16v-3h3a1 1 0 0 0 .117-1.993L16 11h-3V8a1 1 0 0 0-1-1" />
				</g>
			</svg>
		</RouterLink>
	</div>
</template>

<script>
export default {
	name: 'HomeView',
	props: ['profile', 'home', 'others'],
	data() {
		return {};
	},
	mounted() {},
	methods: {
		goDetail(path) {
			this.$router.push('/' + path);
		},
		goSetting(username) {
			this.$router.push('/setting/' + username);
		}
	}
};
</script>
