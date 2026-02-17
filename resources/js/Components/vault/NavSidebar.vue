<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { PhLightbulb, PhLink, PhNote, PhPushPin, PhPlus, PhMagnifyingGlass, PhStack, PhTag, PhCheckSquare, PhCircle, PhCircleHalf, PhCheckCircle, PhSun, PhMoon, PhTimer } from '@phosphor-icons/vue'
import { Button } from '@/Components/ui/button'
import { Separator } from '@/Components/ui/separator'
import { ScrollArea } from '@/Components/ui/scroll-area'
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from '@/Components/ui/tooltip'

const props = defineProps({
  counts: Object,
  filters: Object,
  tags: { type: Array, default: () => [] },
  page: { type: String, default: 'entries' },
})

const showTags = ref(false)
const showAllTags = ref(false)
const tagSearch = ref('')
const isDark = ref(document.documentElement.classList.contains('dark'))

const TAG_LIMIT = 10

const filteredTags = computed(() => {
	if (!props.tags.length) return []
	const q = tagSearch.value.trim().toLowerCase()
	if (q) return props.tags.filter(t => t.name.toLowerCase().includes(q))
	if (showAllTags.value) return props.tags
	return props.tags.slice(0, TAG_LIMIT)
})

const hasMoreTags = computed(() => !tagSearch.value && props.tags.length > TAG_LIMIT)

function toggleTheme() {
	isDark.value = !isDark.value
	document.documentElement.classList.toggle('dark', isDark.value)
	localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
}

const tagColors = [
  { dot: 'bg-amber-400', text: 'text-amber-500', activeBg: 'bg-amber-400/10' },
  { dot: 'bg-cyan-400', text: 'text-cyan-300', activeBg: 'bg-cyan-400/10' },
  { dot: 'bg-amber-400', text: 'text-amber-500', activeBg: 'bg-amber-400/10' },
  { dot: 'bg-amber-400', text: 'text-amber-300', activeBg: 'bg-amber-400/10' },
  { dot: 'bg-rose-400', text: 'text-rose-300', activeBg: 'bg-rose-400/10' },
  { dot: 'bg-sky-400', text: 'text-sky-300', activeBg: 'bg-sky-400/10' },
  { dot: 'bg-orange-400', text: 'text-orange-300', activeBg: 'bg-orange-400/10' },
  { dot: 'bg-fuchsia-400', text: 'text-fuchsia-300', activeBg: 'bg-fuchsia-400/10' },
  { dot: 'bg-lime-400', text: 'text-lime-300', activeBg: 'bg-lime-400/10' },
  { dot: 'bg-indigo-400', text: 'text-indigo-300', activeBg: 'bg-indigo-400/10' },
]

function hashTag(tag) {
  let hash = 0
  for (let i = 0; i < tag.length; i++) {
	hash = ((hash << 5) - hash) + tag.charCodeAt(i)
	hash |= 0
  }
  return Math.abs(hash) % tagColors.length
}

function getTagColor(tag) {
  return tagColors[hashTag(tag)]
}

function navigateTag(tagName) {
  const routeName = props.page === 'tasks' ? 'tasks.index' : 'dashboard'
  if (props.filters?.tag === tagName) {
	router.get(route(routeName), {}, { preserveState: true, preserveScroll: true })
  } else {
	router.get(route(routeName), { tag: tagName }, { preserveState: true, preserveScroll: true })
  }
}

const emit = defineEmits(['create', 'search'])

const entryNavItems = computed(() => [
  { label: 'Ideas', icon: PhLightbulb, type: 'idea', count: props.counts.idea },
  { label: 'Links', icon: PhLink, type: 'link', count: props.counts.link },
  { label: 'Notes', icon: PhNote, type: 'note', count: props.counts.note },
  { label: 'Pinned', icon: PhPushPin, type: 'pinned', count: props.counts.pinned },
])

const taskNavItems = computed(() => [
  { label: 'Open', icon: PhCircle, type: 'open', count: props.counts.open },
  { label: 'In Progress', icon: PhCircleHalf, type: 'in_progress', count: props.counts.in_progress },
  { label: 'Done', icon: PhCheckCircle, type: 'done', count: props.counts.done },
])

const navItems = computed(() => props.page === 'tasks' ? taskNavItems.value : entryNavItems.value)

const activeType = computed(() => {
  if (props.page === 'tasks') {
	return props.filters?.status || null
  }
  if (props.filters?.pinned) return 'pinned'
  return props.filters?.type || null
})

function navigate(item) {
  if (props.page === 'tasks') {
	const params = item.type ? { status: item.type } : {}
	router.get(route('tasks.index'), params, { preserveState: true, preserveScroll: true })
  } else {
	const params = {}
	if (item.type === 'pinned') {
	  params.pinned = 1
	} else if (item.type) {
	  params.type = item.type
	}
	router.get(route('dashboard'), params, { preserveState: true, preserveScroll: true })
  }
}

function switchPage(target) {
  if (target === 'tasks') {
	router.get(route('tasks.index'))
  } else if (target === 'time-entries') {
	router.get(route('time-entries.index'))
  } else {
	router.get(route('dashboard'))
  }
}
</script>

<template>
  <div class="flex h-full">
  <div class="flex h-full w-16 flex-col items-center border-r border-border bg-card py-4">
	<!-- Logo -->
	<div class="mb-4">
	  <svg width="20" height="15" viewBox="0 0 379 265" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M179.138 265L24.2363 19.7487H0V0H201.618V19.7487H184.407C179.958 19.7487 176.211 21.5858 173.167 25.26C170.357 28.9341 168.952 33.9861 168.952 40.4159C168.952 45.3148 170.123 51.1323 172.464 57.8683C175.04 64.2981 179.138 71.1872 184.758 78.5355L239.553 151.101L278.191 97.3657C282.64 91.2421 285.801 85.2715 287.675 79.4541C289.782 73.3304 290.836 67.3599 290.836 61.5425C290.836 49.9076 286.504 40.1098 277.84 32.149C269.41 23.8821 257.467 19.7487 242.012 19.7487H219.181V0H379V19.7487H359.681L190.378 265H179.138ZM185.812 242.036L196.349 227.34L65.684 19.7487H45.6626L185.812 242.036Z" fill="currentColor"/>
	  </svg>
	</div>

	<!-- Page switcher -->
	<div class="flex flex-col items-center gap-1 mb-2">
	  <TooltipProvider :delay-duration="0">
		<Tooltip>
		  <TooltipTrigger asChild>
			<button
			  @click="switchPage('entries')"
			  :class="[
				'flex h-10 w-10 items-center justify-center rounded-lg transition-all',
				page === 'entries'
				  ? 'bg-amber-500/20 text-amber-600 dark:bg-violet-500/15 dark:text-violet-400'
				  : 'text-muted-foreground hover:bg-secondary hover:text-foreground'
			  ]"
			>
			  <PhStack :class="['h-5 w-5', page === 'entries' ? 'text-amber-600 dark:text-violet-400' : 'text-foreground']" weight="thin" />
			</button>
		  </TooltipTrigger>
		  <TooltipContent side="right">Entries</TooltipContent>
		</Tooltip>
	  </TooltipProvider>
	  <TooltipProvider :delay-duration="0">
		<Tooltip>
		  <TooltipTrigger asChild>
			<button
			  @click="switchPage('tasks')"
			  :class="[
				'flex h-10 w-10 items-center justify-center rounded-lg transition-all',
				page === 'tasks'
				  ? 'bg-amber-500/20 text-amber-600 dark:bg-violet-500/15 dark:text-violet-400'
				  : 'text-muted-foreground hover:bg-secondary hover:text-foreground'
			  ]"
			>
			  <PhCheckSquare :class="['h-5 w-5', page === 'tasks' ? 'text-amber-600 dark:text-violet-400' : 'text-foreground']" weight="thin" />
			</button>
		  </TooltipTrigger>
		  <TooltipContent side="right">Tasks</TooltipContent>
		</Tooltip>
	  </TooltipProvider>
	  <TooltipProvider :delay-duration="0">
		<Tooltip>
		  <TooltipTrigger asChild>
			<button
			  @click="switchPage('time-entries')"
			  :class="[
				'flex h-10 w-10 items-center justify-center rounded-lg transition-all',
				page === 'time-entries'
				  ? 'bg-amber-500/20 text-amber-600 dark:bg-violet-500/15 dark:text-violet-400'
				  : 'text-muted-foreground hover:bg-secondary hover:text-foreground'
			  ]"
			>
			  <PhTimer :class="['h-5 w-5', page === 'time-entries' ? 'text-amber-600 dark:text-violet-400' : 'text-foreground']" weight="thin" />
			</button>
		  </TooltipTrigger>
		  <TooltipContent side="right">Time Entries</TooltipContent>
		</Tooltip>
	  </TooltipProvider>
	</div>

	<!-- Search -->
	<Button v-if="page === 'entries'" variant="ghost" size="icon" class="mb-1 h-10 w-10" @click="emit('search')">
	  <PhMagnifyingGlass class="h-5 w-5 text-foreground" weight="thin" />
	</Button>

	<!-- Navigation -->
	<nav class="flex flex-col items-center gap-1">
	  <TooltipProvider :delay-duration="0" v-for="item in navItems" :key="item.label">
		<Tooltip>
		  <TooltipTrigger asChild>
			<button
			  @click="navigate(item)"
			  :class="[
				'flex h-10 w-10 items-center justify-center rounded-lg transition-all',
				activeType === item.type
				  ? 'bg-amber-500/20 text-amber-600 dark:bg-violet-500/15 dark:text-violet-400'
				  : 'text-muted-foreground hover:bg-secondary hover:text-foreground'
			  ]"
			>
			  <component :is="item.icon" :class="['h-5 w-5', activeType === item.type ? 'text-amber-600 dark:text-violet-400' : 'text-foreground']" weight="thin" />
			</button>
		  </TooltipTrigger>
		  <TooltipContent side="right">{{ item.label }} ({{ item.count }})</TooltipContent>
		</Tooltip>
	  </TooltipProvider>
	  <TooltipProvider v-if="page === 'entries'" :delay-duration="0">
		<Tooltip>
		  <TooltipTrigger asChild>
			<button
			  @click="showTags = !showTags"
			  :class="[
				'flex h-10 w-10 items-center justify-center rounded-lg transition-all',
				showTags || filters?.tag
				  ? 'bg-amber-500/20 text-amber-600 dark:bg-violet-500/15 dark:text-violet-400'
				  : 'text-muted-foreground hover:bg-secondary hover:text-foreground'
			  ]"
			>
			  <PhTag :class="['h-5 w-5', showTags || filters?.tag ? 'text-amber-600 dark:text-violet-400' : 'text-foreground']" weight="thin" />
			</button>
		  </TooltipTrigger>
		  <TooltipContent side="right">Tags</TooltipContent>
		</Tooltip>
	  </TooltipProvider>
	</nav>

	<!-- Spacer -->
	<div class="flex-1" />

	<!-- Theme toggle -->
	<TooltipProvider :delay-duration="0">
		<Tooltip>
			<TooltipTrigger asChild>
				<button
					@click="toggleTheme"
					class="flex h-10 w-10 items-center justify-center rounded-lg text-muted-foreground hover:bg-secondary hover:text-foreground transition-all"
				>
					<PhMoon v-if="isDark" class="h-5 w-5 text-foreground" weight="thin" />
					<PhSun v-else class="h-5 w-5 text-foreground" weight="thin" />
				</button>
			</TooltipTrigger>
			<TooltipContent side="right">{{ isDark ? 'Light mode' : 'Dark mode' }}</TooltipContent>
		</Tooltip>
	</TooltipProvider>

	<!-- Add button -->
	<TooltipProvider :delay-duration="0">
	  <Tooltip>
		<TooltipTrigger asChild>
		  <Button variant="ghost" size="icon" class="h-10 w-10" @click="emit('create')">
			<PhPlus class="h-5 w-5 text-foreground" weight="thin" />
		  </Button>
		</TooltipTrigger>
		<TooltipContent side="right">New Entry</TooltipContent>
	  </Tooltip>
	</TooltipProvider>
  </div>

  <!-- Tags panel -->
  <div
	v-if="showTags && tags.length"
	class="h-full w-48 border-r border-border bg-card overflow-hidden animate-in fade-in slide-in-from-left-2 duration-150"
  >
	  <div class="flex items-center gap-2 px-4 h-[50px] border-b border-border">
		<PhTag class="h-4 w-4 text-muted-foreground" weight="thin" />
		<span class="text-xs font-semibold tracking-tight text-muted-foreground uppercase">Tags</span>
	  </div>
	  <div class="px-2 pt-2">
		<input
		  v-model="tagSearch"
		  type="text"
		  placeholder="Filter…"
		  class="w-full rounded-md border border-border bg-secondary/30 px-2 py-1 text-xs text-foreground placeholder:text-muted-foreground/50 focus:outline-none focus:ring-1 focus:ring-ring"
		/>
	  </div>
	  <ScrollArea class="h-[calc(100%-90px)]">
		<div class="py-2 px-2">
		  <button
			v-for="tag in filteredTags"
			:key="tag.name"
			@click="navigateTag(tag.name)"
			:class="[
			  'flex w-full items-center gap-2 rounded-md px-2 py-1 text-sm transition-all',
			  filters?.tag === tag.name
				? getTagColor(tag.name).activeBg + ' ' + getTagColor(tag.name).text
				: 'text-muted-foreground hover:bg-secondary/50 hover:text-foreground'
			]"
		  >
			<span :class="['h-2 w-2 rounded-full shrink-0', getTagColor(tag.name).dot]" />
			<span class="truncate flex-1 text-left">{{ tag.name }}</span>
			<span class="text-xs text-muted-foreground/60 shrink-0">{{ tag.count }}</span>
		  </button>
		  <button
			v-if="hasMoreTags"
			@click="showAllTags = !showAllTags"
			class="flex w-full items-center justify-center rounded-md px-2 py-1 text-xs text-muted-foreground/60 hover:text-muted-foreground transition-all"
		  >
			{{ showAllTags ? 'Show less' : `Show all (${tags.length})` }}
		  </button>
		</div>
	  </ScrollArea>
	</div>
  </div>
</template>
