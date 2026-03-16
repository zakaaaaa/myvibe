<template>
	<Teleport to="body">
		<div class="onboarding-overlay" v-if="visible" @click.self="nextStep">
			<!-- Dark backdrop with spotlight cutout -->
			<div class="onboarding-backdrop"></div>

			<!-- Spotlight ring -->
			<div class="onboarding-spotlight" :style="spotlightStyle" :class="'step-' + currentStep">
				<div class="spotlight-ring"></div>
			</div>

			<!-- Glass tooltip -->
			<div class="onboarding-tooltip" :style="tooltipStyle" :class="'tooltip-step-' + currentStep">
				<div class="tooltip-glass">
					<!-- Step indicator -->
					<div class="tooltip-steps">
						<div v-for="n in totalSteps" :key="n" class="tooltip-dot" :class="{ active: n === currentStep, done: n < currentStep }"></div>
					</div>

					<!-- Icon -->
					<div class="tooltip-icon">
						<fa :icon="steps[currentStep - 1].icon" />
					</div>

					<!-- Content -->
					<h3 class="tooltip-title">{{ steps[currentStep - 1].title }}</h3>
					<p class="tooltip-desc">{{ steps[currentStep - 1].description }}</p>

					<!-- Actions -->
					<div class="tooltip-actions">
						<button v-if="currentStep > 1" class="tooltip-btn tooltip-btn--ghost" @click.stop="prevStep">Back</button>
						<button class="tooltip-btn tooltip-btn--primary" @click.stop="nextStep">
							{{ currentStep === totalSteps ? "Let's Go!" : 'Next' }}
							<fa :icon="currentStep === totalSteps ? 'rocket' : 'arrow-right'" class="ms-1" />
						</button>
					</div>

					<!-- Skip -->
					<button class="tooltip-skip" @click.stop="skip">Skip tour</button>
				</div>
			</div>
		</div>
	</Teleport>
</template>

<script>
export default {
	name: 'OnboardingTour',
	props: {
		show: { type: Boolean, default: false }
	},
	emits: ['complete'],
	data() {
		return {
			visible: false,
			currentStep: 1,
			steps: [
				{
					icon: 'user',
					title: 'Your Vibe Profile',
					description: 'This is your personal space! Your photo, followers, and personality tags live here.',
					target: '.vibe-profile',
					position: 'bottom'
				},
				{
					icon: 'gear',
					title: 'Customize Settings',
					description: 'Tap here to edit your profile, change your MBTI, zodiac, or manage your account.',
					target: '.vibe-profile .icon',
					position: 'bottom-left'
				},
				{
					icon: 'circle-plus',
					title: 'Add Your First Vibe',
					description: 'This is where the magic happens! Rate your favorite music, movies, books and more.',
					target: '.floating-add-btn',
					position: 'top'
				},
				{
					icon: 'compass',
					title: 'Navigate Your World',
					description: 'Home for your vibes, Search to discover others, and Chat to connect with friends.',
					target: '.floating_menu',
					position: 'top'
				}
			]
		};
	},
	computed: {
		totalSteps() {
			return this.steps.length;
		},
		spotlightStyle() {
			return this.getTargetRect();
		},
		tooltipStyle() {
			const step = this.steps[this.currentStep - 1];
			const rect = this.getTargetElement();
			if (!rect) return {};

			const pos = step.position;
			const style = {};

			if (pos === 'bottom' || pos === 'bottom-left') {
				style.top = (rect.bottom + 16) + 'px';
				style.left = '50%';
				style.transform = 'translateX(-50%)';
			} else if (pos === 'top') {
				style.bottom = (window.innerHeight - rect.top + 16) + 'px';
				style.left = '50%';
				style.transform = 'translateX(-50%)';
			}

			return style;
		}
	},
	watch: {
		show(val) {
			if (val) {
				this.currentStep = 1;
				this.visible = true;
				document.body.style.overflow = 'hidden';
			}
		}
	},
	methods: {
		getTargetElement() {
			const step = this.steps[this.currentStep - 1];
			const el = document.querySelector(step.target);
			if (!el) return null;
			return el.getBoundingClientRect();
		},
		getTargetRect() {
			const rect = this.getTargetElement();
			if (!rect) return {};

			const padding = 8;
			return {
				top: (rect.top - padding) + 'px',
				left: (rect.left - padding) + 'px',
				width: (rect.width + padding * 2) + 'px',
				height: (rect.height + padding * 2) + 'px'
			};
		},
		nextStep() {
			if (this.currentStep < this.totalSteps) {
				this.currentStep++;
			} else {
				this.complete();
			}
		},
		prevStep() {
			if (this.currentStep > 1) {
				this.currentStep--;
			}
		},
		skip() {
			this.complete();
		},
		complete() {
			this.visible = false;
			document.body.style.overflow = '';
			localStorage.setItem('onboarding_done', 'true');
			this.$emit('complete');
		}
	}
};
</script>

<style lang="scss" scoped>
@import '@/assets/scss/color.scss';

.onboarding-overlay {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	z-index: 9999;
}

.onboarding-backdrop {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.65);
	backdrop-filter: blur(2px);
	-webkit-backdrop-filter: blur(2px);
}

// === SPOTLIGHT ===
.onboarding-spotlight {
	position: absolute;
	z-index: 10000;
	transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
	pointer-events: none;

	.spotlight-ring {
		position: absolute;
		inset: -4px;
		border-radius: 20px;
		border: 2px solid rgba($purple, 0.5);
		box-shadow: 0 0 20px rgba($purple, 0.2), 0 0 40px rgba($purple, 0.1);
		animation: spotlightPulse 2s ease-in-out infinite;
	}

	&.step-1 .spotlight-ring { border-radius: 60px; }
	&.step-2 .spotlight-ring { border-radius: 50%; }
	&.step-3 .spotlight-ring { border-radius: 50%; }
	&.step-4 .spotlight-ring { border-radius: 30px 30px 0 0; }
}

@keyframes spotlightPulse {
	0%, 100% { opacity: 0.6; transform: scale(1); }
	50% { opacity: 1; transform: scale(1.02); }
}

// === TOOLTIP ===
.onboarding-tooltip {
	position: absolute;
	z-index: 10001;
	width: min(320px, 85vw);
	transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
	animation: tooltipAppear 0.4s ease-out both;
}

@keyframes tooltipAppear {
	from { opacity: 0; transform: translateX(-50%) translateY(10px); }
	to { opacity: 1; transform: translateX(-50%) translateY(0); }
}

.tooltip-glass {
	background: rgba($background, 0.8);
	backdrop-filter: blur(24px);
	-webkit-backdrop-filter: blur(24px);
	border: 1px solid rgba($white, 0.1);
	border-radius: 20px;
	padding: 20px;
	position: relative;
	box-shadow:
		0 12px 40px rgba(0, 0, 0, 0.4),
		inset 0 1px 0 rgba($white, 0.06);

	// Glass reflection
	&::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		height: 40%;
		background: linear-gradient(180deg, rgba($white, 0.05) 0%, transparent 100%);
		border-radius: 20px 20px 0 0;
		pointer-events: none;
	}
}

// === STEP DOTS ===
.tooltip-steps {
	display: flex;
	justify-content: center;
	gap: 6px;
	margin-bottom: 16px;
}

.tooltip-dot {
	width: 8px;
	height: 8px;
	border-radius: 50%;
	background: rgba($white, 0.15);
	transition: all 0.3s ease;

	&.active {
		background: $purple;
		width: 24px;
		border-radius: 4px;
		box-shadow: 0 0 8px rgba($purple, 0.4);
	}

	&.done {
		background: rgba($purple, 0.4);
	}
}

// === ICON ===
.tooltip-icon {
	width: 48px;
	height: 48px;
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	margin: 0 auto 12px;
	font-size: 20px;
	color: $purple;
	background: rgba($purple, 0.1);
	border: 1px solid rgba($purple, 0.2);
	box-shadow: 0 4px 16px rgba($purple, 0.1);
}

// === TEXT ===
.tooltip-title {
	color: $white1;
	font-size: 18px;
	font-weight: 700;
	text-align: center;
	margin-bottom: 6px;
}

.tooltip-desc {
	color: rgba($white1, 0.55);
	font-size: 13px;
	text-align: center;
	line-height: 1.5;
	margin-bottom: 18px;
}

// === BUTTONS ===
.tooltip-actions {
	display: flex;
	gap: 10px;
	justify-content: center;
}

.tooltip-btn {
	padding: 10px 24px;
	border-radius: 30px;
	font-size: 14px;
	font-weight: 600;
	border: none;
	cursor: pointer;
	transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
	font-family: inherit;

	&--primary {
		background: linear-gradient(135deg, rgba($purple, 0.7), rgba($purple, 0.9));
		color: $white;
		border: 1px solid rgba($white, 0.12);
		box-shadow: 0 4px 16px rgba($purple, 0.25), inset 0 1px 0 rgba($white, 0.15);
		position: relative;
		overflow: hidden;

		&::before {
			content: '';
			position: absolute;
			top: 0;
			left: -100%;
			width: 100%;
			height: 100%;
			background: linear-gradient(90deg, transparent, rgba($white, 0.12), transparent);
			transition: left 0.5s ease;
		}

		&:hover {
			transform: translateY(-2px);
			box-shadow: 0 8px 24px rgba($purple, 0.3);
			&::before { left: 100%; }
		}
	}

	&--ghost {
		background: rgba($white, 0.05);
		color: rgba($white1, 0.6);
		border: 1px solid rgba($white, 0.08);

		&:hover {
			background: rgba($white, 0.08);
			color: $white1;
		}
	}
}

.tooltip-skip {
	display: block;
	width: 100%;
	background: none;
	border: none;
	color: rgba($white1, 0.3);
	font-size: 11px;
	margin-top: 12px;
	cursor: pointer;
	font-family: inherit;
	transition: color 0.3s ease;

	&:hover {
		color: rgba($white1, 0.5);
	}
}
</style>