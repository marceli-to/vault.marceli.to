<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { PhLightbulb, PhLink, PhNote, PhPushPin, PhPlus, PhMagnifyingGlass, PhStack } from '@phosphor-icons/vue'
import { ScrollArea } from '@/Components/ui/scroll-area'
import { Button } from '@/Components/ui/button'
import { Separator } from '@/Components/ui/separator'
import TypeIcon from './TypeIcon.vue'
import { groupEntriesByDate } from '@/lib/entryGrouping'

const props = defineProps({
  entries: Array,
  counts: Object,
  filters: Object,
  selectedId: [Number, null],
})

const emit = defineEmits(['select', 'create', 'search'])

const navItems = computed(() => [
  { label: 'All Entries', icon: PhStack, type: null, count: props.counts.all },
  { label: 'Ideas', icon: PhLightbulb, type: 'idea', count: props.counts.idea },
  { label: 'Links', icon: PhLink, type: 'link', count: props.counts.link },
  { label: 'Notes', icon: PhNote, type: 'note', count: props.counts.note },
  { label: 'Pinned', icon: PhPushPin, type: 'pinned', count: props.counts.pinned },
])

const activeType = computed(() => {
  if (props.filters?.pinned) return 'pinned'
  return props.filters?.type || null
})

function navigate(item) {
  const params = {}
  if (item.type === 'pinned') {
	params.pinned = 1
  } else if (item.type) {
	params.type = item.type
  }
  router.get(route('dashboard'), params, { preserveState: true, preserveScroll: true })
}

const groupedEntries = computed(() => groupEntriesByDate(props.entries))

function truncate(text, len = 60) {
  if (!text) return ''
  return text.length > len ? text.substring(0, len) + '…' : text
}

function timeAgo(date) {
  const now = new Date()
  const d = new Date(date)
  const diff = Math.floor((now - d) / 60000)
  if (diff < 1) return 'just now'
  if (diff < 60) return `${diff}m ago`
  if (diff < 1440) return `${Math.floor(diff / 60)}h ago`
  return `${Math.floor(diff / 1440)}d ago`
}
</script>

<template>
  <div class="flex h-full w-72 flex-col border-r border-border bg-card">
	<!-- Logo / Header -->
	<div class="flex items-center justify-between p-4 pb-2">
	  <div class="flex items-center gap-2">
		<svg width="24" height="18" viewBox="0 0 379 265" fill="none" xmlns="http://www.w3.org/2000/svg">
		  <path d="M179.138 265L24.2363 19.7487H0V0H201.618V19.7487H184.407C179.958 19.7487 176.211 21.5858 173.167 25.26C170.357 28.9341 168.952 33.9861 168.952 40.4159C168.952 45.3148 170.123 51.1323 172.464 57.8683C175.04 64.2981 179.138 71.1872 184.758 78.5355L239.553 151.101L278.191 97.3657C282.64 91.2421 285.801 85.2715 287.675 79.4541C289.782 73.3304 290.836 67.3599 290.836 61.5425C290.836 49.9076 286.504 40.1098 277.84 32.149C269.41 23.8821 257.467 19.7487 242.012 19.7487H219.181V0H379V19.7487H359.681L190.378 265H179.138ZM185.812 242.036L196.349 227.34L65.684 19.7487H45.6626L185.812 242.036Z" fill="currentColor"/>
		</svg>
	  </div>
	  <Button variant="ghost" size="icon" class="h-8 w-8" @click="emit('create')">
		<PhPlus class="h-4 w-4 text-foreground" weight="thin" />
	  </Button>
	</div>

	<!-- Search trigger -->
	<div class="px-3 pb-2">
	  <button
		@click="emit('search')"
		class="flex w-full items-center gap-2 rounded-lg border border-border bg-secondary/50 px-3 py-2 text-xs text-muted-foreground transition-colors hover:bg-secondary"
	  >
		<PhMagnifyingGlass class="h-3.5 w-3.5 text-foreground" weight="thin" />
		<span>Search…</span>
		<kbd class="ml-auto rounded border border-border bg-background px-1.5 py-0.5 text-[10px] font-mono">⌘K</kbd>
	  </button>
	</div>

	<!-- Navigation -->
	<nav class="px-2 py-1 space-y-2">
	  <button
		v-for="item in navItems"
		:key="item.label"
		@click="navigate(item)"
		:class="[
		  'flex w-full items-center gap-2.5 rounded-lg px-3 py-1.5 text-sm transition-all',
		  activeType === item.type
			? 'bg-amber-500/20 text-amber-600 dark:bg-violet-500/15 dark:text-violet-400'
			: 'text-muted-foreground hover:bg-secondary hover:text-foreground'
		]"
	  >
		<component :is="item.icon" :class="['h-4 w-4 shrink-0', activeType === item.type ? 'text-amber-600 dark:text-violet-400' : 'text-foreground']" weight="thin" />
		<span class="truncate">{{ item.label }}</span>
		<span class="ml-auto text-xs opacity-60">{{ item.count }}</span>
	  </button>
	</nav>

	<Separator class="my-2" />

	<!-- Entry list -->
	<ScrollArea class="flex-1">
	  <div class="px-2 pb-4">
		<template v-for="[group, items] in groupedEntries" :key="group">
		  <div class="mb-1 mt-3 px-3 text-[10px] font-semibold uppercase tracking-widest text-muted-foreground/60">
			{{ group }}
		  </div>
		  <div class="space-y-2">
		  <button
			v-for="entry in items"
			:key="entry.id"
			@click="emit('select', entry)"
			:class="[
			  'flex w-full flex-col gap-0.5 rounded-lg px-3 py-2 text-left transition-all',
			  selectedId === entry.id
				? 'bg-amber-500/20 ring-1 ring-amber-500/30 dark:bg-violet-500/15 dark:ring-violet-400/25'
				: 'hover:bg-secondary/50'
			]"
		  >
			<div class="flex items-center gap-2">
			  <TypeIcon :type="entry.type" class="h-3 w-3 shrink-0" />
			  <span class="truncate text-sm font-medium">{{ entry.title || truncate(entry.content, 40) }}</span>
			  <PhPushPin v-if="entry.is_pinned" class="ml-auto h-3 w-3 shrink-0 text-foreground" weight="thin" />
			</div>
			<div class="flex items-center gap-2 pl-5">
			  <span class="truncate text-xs text-muted-foreground">{{ truncate(entry.content, 50) }}</span>
			  <span class="ml-auto shrink-0 text-[10px] text-muted-foreground/50">{{ timeAgo(entry.created_at) }}</span>
			</div>
		  </button>
		  </div>
		</template>

		<div v-if="entries.length === 0" class="px-3 py-8 text-center text-sm text-muted-foreground">
		  No entries found
		</div>
	  </div>
	</ScrollArea>
  </div>
</template>
