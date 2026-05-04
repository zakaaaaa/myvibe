<template>
	<transition name="gif-picker">
		<div v-if="visible" class="gif-picker-overlay" @click.self="$emit('close')">
			<div class="gif-picker">
				<!-- Header: drag handle + search -->
				<div class="gif-picker__header">
					<div class="gif-picker__handle"></div>
					<div class="gif-picker__search">
						<fa icon="magnifying-glass" class="gif-picker__search-icon" />
						<input
							ref="searchInput"
							v-model="query"
							type="text"
							placeholder="Search GIFs..."
							class="gif-picker__input"
							@input="onSearchInput"
						/>
						<div v-if="query" class="gif-picker__clear" @click="clearSearch()">
							<fa icon="xmark" />
						</div>
					</div>
				</div>

				<!-- GIF Grid -->
				<div class="gif-picker__grid" ref="gridRef" @scroll="onScroll">
					<div
						v-for="gif in gifs"
						:key="gif.id"
						class="gif-picker__item"
						@click="selectGif(gif)"
					>
						<img
							:src="gif.preview"
							:alt="gif.title || 'GIF'"
							loading="lazy"
							:style="{ aspectRatio: `${gif.width} / ${gif.height}` }"
						/>
					</div>

					<div v-if="loading" class="gif-picker__loading">
						<fa icon="spinner" class="fa-spin-pulse" />
					</div>

					<div v-if="!loading && gifs.length === 0 && hasSearched" class="gif-picker__empty">
						<p>No GIFs found</p>
					</div>
				</div>

				<!-- GIPHY attribution (required by GIPHY ToS) -->
				<div class="gif-picker__attribution">
					<small>Powered by GIPHY</small>
				</div>
			</div>
		</div>
	</transition>
</template>

<script>
import dashboardService from '@/services/dashboardService';

export default {
	name: 'GifPicker',
	props: {
		visible: { type: Boolean, default: false }
	},
	emits: ['select', 'close'],
	data() {
		return {
			query: '',
			gifs: [],
			loading: false,
			offset: 0,
			hasMore: true,
			hasSearched: false,
			debounceTimer: null
		};
	},
	watch: {
		visible(val) {
			if (val) {
				this.$nextTick(() => {
					if (this.$refs.searchInput) this.$refs.searchInput.focus();
					if (this.gifs.length === 0) this.loadTrending();
				});
			}
		}
	},
	methods: {
		onSearchInput() {
			clearTimeout(this.debounceTimer);
			this.debounceTimer = setTimeout(() => {
				this.gifs = [];
				this.offset = 0;
				this.hasMore = true;
				if (this.query.trim()) {
					this.searchGifs();
				} else {
					this.loadTrending();
				}
			}, 350);
		},
		clearSearch() {
			this.query = '';
			this.gifs = [];
			this.offset = 0;
			this.hasMore = true;
			this.loadTrending();
		},
		async loadTrending() {
			if (this.loading || !this.hasMore) return;
			this.loading = true;
			try {
				const res = await dashboardService.trendingGiphy(24, this.offset);
				const items = res.data?.data || [];
				this.gifs.push(...items);
				this.offset += items.length;
				this.hasMore = items.length >= 24;
				this.hasSearched = true;
			} catch (err) {
				console.error('GIPHY trending failed:', err);
			} finally {
				this.loading = false;
			}
		},
		async searchGifs() {
			if (this.loading || !this.hasMore || !this.query.trim()) return;
			this.loading = true;
			try {
				const res = await dashboardService.searchGiphy(this.query.trim(), 24, this.offset);
				const items = res.data?.data || [];
				this.gifs.push(...items);
				this.offset += items.length;
				this.hasMore = items.length >= 24;
				this.hasSearched = true;
			} catch (err) {
				console.error('GIPHY search failed:', err);
			} finally {
				this.loading = false;
			}
		},
		onScroll(e) {
			const el = e.target;
			const nearBottom = el.scrollHeight - el.scrollTop - el.clientHeight < 200;
			if (nearBottom && !this.loading && this.hasMore) {
				if (this.query.trim()) this.searchGifs();
				else this.loadTrending();
			}
		},
		selectGif(gif) {
			this.$emit('select', {
				id: gif.id,
				url: gif.url,
				preview: gif.preview,
				width: gif.width,
				height: gif.height,
				title: gif.title || ''
			});
		}
	}
};
</script>

<style lang="scss" scoped>
@import '@/assets/scss/color.scss';

.gif-picker-overlay {
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	z-index: 998;
	background: rgba($black, 0.4);
	backdrop-filter: blur(6px);
	-webkit-backdrop-filter: blur(6px);
	display: flex;
	align-items: flex-end;
	justify-content: center;
}

.gif-picker {
	width: 100%;
	max-width: 460px;
	height: 65vh;
	max-height: 560px;
	background: rgba($background_second, 0.92);
	backdrop-filter: blur(24px);
	-webkit-backdrop-filter: blur(24px);
	border: 1px solid rgba($white, 0.1);
	border-bottom: none;
	border-radius: 20px 20px 0 0;
	display: flex;
	flex-direction: column;
	padding: 8px 12px;
	padding-bottom: env(safe-area-inset-bottom, 8px);
	box-shadow: 0 -8px 32px rgba($black, 0.4);

	&__handle {
		width: 36px;
		height: 4px;
		background: rgba($white, 0.2);
		border-radius: 2px;
		margin: 4px auto 10px;
		flex-shrink: 0;
	}

	&__header {
		flex-shrink: 0;
		margin-bottom: 10px;
	}

	&__search {
		position: relative;
		display: flex;
		align-items: center;
		background: rgba($white, 0.06);
		border: 1px solid rgba($white, 0.08);
		border-radius: 12px;
		padding: 0 12px;
		transition: all 0.2s ease;

		&:focus-within {
			border-color: rgba($purple, 0.4);
			background: rgba($white, 0.08);
		}
	}

	&__search-icon {
		color: rgba($white, 0.4);
		font-size: 12px;
		margin-right: 8px;
		flex-shrink: 0;
	}

	&__input {
		flex: 1;
		background: transparent;
		border: none;
		outline: none;
		padding: 10px 0;
		color: $white;
		font-size: 13px;

		&::placeholder { color: rgba($white, 0.35); }
	}

	&__clear {
		width: 22px;
		height: 22px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 50%;
		background: rgba($white, 0.08);
		color: rgba($white, 0.6);
		font-size: 10px;
		cursor: pointer;
		flex-shrink: 0;

		&:hover { background: rgba($white, 0.14); color: $white; }
	}

	&__grid {
		flex: 1;
		overflow-y: auto;
		display: grid;
		grid-template-columns: repeat(2, 1fr);
		gap: 6px;
		-webkit-overflow-scrolling: touch;
		padding: 2px;

		@media (min-width: 480px) {
			grid-template-columns: repeat(3, 1fr);
		}
	}

	&__item {
		cursor: pointer;
		border-radius: 10px;
		overflow: hidden;
		background: rgba($white, 0.04);
		border: 1px solid rgba($white, 0.06);
		transition: transform 0.15s ease;

		&:active { transform: scale(0.96); }
		&:hover { border-color: rgba($purple, 0.3); }

		img { width: 100%; height: auto; display: block; }
	}

	&__loading,
	&__empty {
		grid-column: 1 / -1;
		text-align: center;
		padding: 24px;
		color: rgba($white, 0.4);
		font-size: 18px;

		p { font-size: 13px; margin: 0; }
	}

	&__attribution {
		text-align: center;
		padding-top: 6px;
		flex-shrink: 0;

		small {
			font-size: 10px;
			color: rgba($white, 0.3);
			letter-spacing: 0.5px;
		}
	}
}

// Transitions
.gif-picker-enter-active { transition: all 0.3s cubic-bezier(0.23,1,0.32,1); }
.gif-picker-leave-active { transition: all 0.25s ease-in; }
.gif-picker-enter-from .gif-picker { transform: translateY(100%); }
.gif-picker-enter-from { opacity: 0; }
.gif-picker-leave-to .gif-picker { transform: translateY(100%); }
.gif-picker-leave-to { opacity: 0; }
</style>