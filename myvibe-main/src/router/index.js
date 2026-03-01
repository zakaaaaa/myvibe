import { createRouter, createWebHistory } from 'vue-router'
import StartedView from '@/views/StartedView.vue'
import LandingView from '@/views/LandingView.vue'
import RegisterView from '@/views/RegisterView.vue'
import LoginView from '@/views/LoginView.vue'
import ForgotPasswordView from '@/views/ForgotPasswordView.vue'
import ResendVerifyView from '@/views/ResendVerifyView.vue'
import FillProfileView from '@/views/FillProfileView.vue'
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
		component: LandingView
	},
	{
		path: '/register',
		name: 'register',
		component: RegisterView
	},
	{
		path: '/login',
		name: 'login',
		component: LoginView
	},
	{
		path: '/forgot-password',
		name: 'forgot-password',
		component: ForgotPasswordView
	},
	{
		path: '/resend-verify',
		name: 'resend-verify',
		component: ResendVerifyView
	},
	{
		path: '/fill-vibe',
		name: 'fill-vibe',
		component: FillProfileView
	},
	{
		path: '/dashboard',
		name: 'dashboard',
		component: DashboardView
	},
	{
		path: '/setting/:username',
		name: 'setting',
		component: SettingView
	},
	{
		path: '/more/:id',
		name: 'show-more',
		component: HomeMoreView
	},
	{
		path: '/more-other/:id',
		name: 'show-more-other',
		component: HomeMoreOtherView
	},
	{
		path: '/add',
		name: 'add',
		component: AddView
	},
	{
		path: '/add-others',
		name: 'add-others',
		component: OthersView
	},
	{
		path: '/:username/:slug',
		name: 'show-vibe',
		component: ShowVibeView
	},
	{
		path: '/:username/other/:slug',
		name: 'show-vibe-other',
		component: ShowVibeOtherView
	},
	{
		path: '/:username/more/:id',
		name: 'show-more-vibe',
		component: ShowMoreVibeView
	},
	{
		path: '/:username/more-other/:id',
		name: 'show-more-other-vibe',
		component: ShowMoreOtherVibeView
	},
	{
		path: '/:username',
		name: 'user-vibe',
		component: UserVibeView
	},
	{
		path: '/message/:username',
		name: 'chat-user-vibe',
		component: ChatUserVibeView
	},
	{
		path: '/about',
		name: 'about',
		// route level code-splitting
		// this generates a separate chunk (about.[hash].js) for this route
		// which is lazy-loaded when the route is visited.
		component: () => import(/* webpackChunkName: "about" */ '../views/AboutView.vue')
	}
]

const router = createRouter({
	history: createWebHistory(process.env.BASE_URL || '/'),
	routes
})

export default router
