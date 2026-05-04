import { createRouter, createWebHistory } from 'vue-router'
import StartedView from '@/views/StartedView.vue'
import LandingView from '@/views/LandingView.vue'
import RegisterView from '@/views/RegisterView.vue'
import LoginView from '@/views/LoginView.vue'
import ForgotPasswordView from '@/views/ForgotPasswordView.vue'
import ResendVerifyView from '@/views/ResendVerifyView.vue'
import FillProfileView from '@/views/FillProfileView.vue'
import WelcomeView from '@/views/WelcomeView.vue'
import DashboardView from '@/views/dashboard/DashboardView.vue'
import HomeMoreView from '@/views/dashboard/HomeMoreView.vue'
import AddView from '@/views/dashboard/add/AddView.vue'
import ShowVibeView from '@/views/ShowVibeView.vue'
import ShowMoreVibeView from '@/views/ShowMoreVibeView.vue'
import UserVibeView from '@/views/UserVibeView.vue'
import ChatUserVibeView from '@/views/ChatUserVibeView.vue'
import SettingView from '@/views/dashboard/SettingView.vue'
import OthersView from '@/views/dashboard/add/OthersView.vue'
import ShowVibeOtherView from '@/views/ShowVibeOtherView.vue'
import HomeMoreOtherView from '@/views/dashboard/HomeMoreOtherView.vue'
import ShowMoreOtherVibeView from '@/views/ShowMoreOtherVibeView.vue'

const routes = [
	{
		path: '/',
		name: 'splash',
		component: StartedView
	},
	{
		path: '/landing',
		name: 'landing',
		component: LandingView,
		meta: { guestOnly: true }
	},
	{
		path: '/register',
		name: 'register',
		component: RegisterView,
		meta: { guestOnly: true }
	},
	{
		path: '/login',
		name: 'login',
		component: LoginView,
		meta: { guestOnly: true }
	},
	{
		path: '/forgot-password',
		name: 'forgot-password',
		component: ForgotPasswordView,
		meta: { guestOnly: true }
	},
	{
		path: '/resend-verify',
		name: 'resend-verify',
		component: ResendVerifyView,
		meta: { guestOnly: true }
	},
	{
		path: '/fill-vibe',
		name: 'fill-vibe',
		component: FillProfileView,
		meta: { requiresAuth: true }
	},
	{
		path: '/welcome',
		name: 'welcome',
		component: WelcomeView,
		meta: { requiresAuth: true }
	},
	{
		path: '/dashboard',
		name: 'dashboard',
		component: DashboardView,
		meta: { requiresAuth: true }
	},
	{
		path: '/setting/:username',
		name: 'setting',
		component: SettingView,
		meta: { requiresAuth: true }
	},
	{
		path: '/more/:id',
		name: 'show-more',
		component: HomeMoreView,
		meta: { requiresAuth: true }
	},
	{
		path: '/more-other/:id',
		name: 'show-more-other',
		component: HomeMoreOtherView,
		meta: { requiresAuth: true }
	},
	{
		path: '/add',
		name: 'add',
		component: AddView,
		meta: { requiresAuth: true }
	},
	{
		path: '/add-others',
		name: 'add-others',
		component: OthersView,
		meta: { requiresAuth: true }
	},
	{
		path: '/message/:username',
		name: 'chat-user-vibe',
		component: ChatUserVibeView,
		meta: { requiresAuth: true }
	},
	{
		path: '/about',
		name: 'about',
		// route level code-splitting
		// this generates a separate chunk (about.[hash].js) for this route
		// which is lazy-loaded when the route is visited.
		component: () => import(/* webpackChunkName: "about" */ '../views/AboutView.vue')
	},

	/*
	|----------------------------------------------------------------------
	| PUBLIC PROFILE & VIBE ROUTES (boleh diakses guest)
	|----------------------------------------------------------------------
	| PENTING: Letakkan SETELAH semua route specific (/dashboard, /add, dll)
	| supaya pattern /:username tidak match path specific.
	| Vue Router match dari atas ke bawah — route specific menang kalau di atas.
	*/
	{
		path: '/:username/other/:slug',
		name: 'show-vibe-other',
		component: ShowVibeOtherView,
		meta: { public: true }
	},
	{
		path: '/:username/more/:id',
		name: 'show-more-vibe',
		component: ShowMoreVibeView,
		meta: { public: true }
	},
	{
		path: '/:username/more-other/:id',
		name: 'show-more-other-vibe',
		component: ShowMoreOtherVibeView,
		meta: { public: true }
	},
	{
		path: '/:username/:slug',
		name: 'show-vibe',
		component: ShowVibeView,
		meta: { public: true }
	},
	{
		path: '/:username',
		name: 'user-vibe',
		component: UserVibeView,
		meta: { public: true }
	}
]

const router = createRouter({
	history: createWebHistory(process.env.BASE_URL || '/'),
	routes
})

/*
|--------------------------------------------------------------------------
| Navigation Guard
|--------------------------------------------------------------------------
| - public route   : siapa pun boleh masuk (guest atau logged-in)
| - guestOnly      : hanya guest, kalau udah login → redirect ke /dashboard
| - requiresAuth   : harus login, kalau guest → redirect ke /landing
| - tanpa meta     : behavior default (boleh diakses)
*/
router.beforeEach((to, from, next) => {
	const token = localStorage.getItem('token')
	const isLoggedIn = !!token

	// Public routes — siapa pun boleh akses
	if (to.meta.public) {
		return next()
	}

	// Guest-only routes (login, register, dll) — kalau udah login redirect ke dashboard
	if (to.meta.guestOnly && isLoggedIn) {
		return next('/dashboard')
	}

	// Authenticated routes — kalau guest redirect ke landing
	if (to.meta.requiresAuth && !isLoggedIn) {
		return next('/landing')
	}

	next()
})

export default router