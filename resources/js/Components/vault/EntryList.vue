<script setup>
import { computed } from 'vue'
import { ScrollArea } from '@/Components/ui/scroll-area'
import { PhPushPin } from '@phosphor-icons/vue'
import TypeIcon from './TypeIcon.vue'
import { groupEntriesByDate } from '@/lib/entryGrouping'

const props = defineProps({
  entries: Array,
  selectedId: [Number, null],
  filters: Object,
})

const emit = defineEmits(['select'])

const groupedEntries = computed(() => groupEntriesByDate(props.entries))

const title = computed(() => {
  if (props.filters?.tag) return `Tagged: ${props.filters.tag}`
  if (props.filters?.pinned) return 'Pinned'
  if (props.filters?.type === 'idea') return 'Ideas'
  if (props.filters?.type === 'link') return 'Links'
  if (props.filters?.type === 'note') return 'Notes'
  return 'All Entries'
})

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
  <div class="flex h-full w-[25rem] flex-col border-r border-border bg-background">
	<!-- Header -->
	<div class="flex items-center justify-between px-4 border-b border-border h-[50px]">
	  <h2 class="text-sm font-semibold tracking-tight">{{ title }}</h2>
	  <span class="text-xs text-muted-foreground">{{ entries.length }}</span>
	</div>

	<!-- Entry list -->
	<ScrollArea class="flex-1">
	  <div class="px-3 py-2">
		<template v-for="[group, items] in groupedEntries" :key="group">
		  <div class="mb-1 mt-3 first:mt-1 px-3 text-[10px] font-semibold uppercase tracking-widest text-muted-foreground/60">
			{{ group }}
		  </div>
		  <div class="space-y-3">
			<button
			  v-for="entry in items"
			  :key="entry.id"
			  @click="emit('select', entry)"
			  :class="[
				'flex w-full flex-col gap-0.5 rounded-lg px-3 py-2.5 text-left transition-all',
				selectedId === entry.id
				  ? 'bg-amber-500/20 ring-1 ring-amber-500/30 dark:bg-violet-500/15 dark:ring-violet-400/25'
				  : 'hover:bg-secondary/50'
			  ]"
			>
			  <div class="flex items-center gap-2">
				<TypeIcon :type="entry.type" class="h-3 w-3 shrink-0" />
				<span :class="['truncate text-sm font-medium', entry.is_pinned ? 'text-yellow-600 dark:text-yellow-400' : '']">{{ entry.title || truncate(entry.content, 40) }}</span>
				<PhPushPin v-if="entry.is_pinned" class="ml-auto h-3 w-3 shrink-0 text-yellow-600 dark:text-yellow-400" weight="thin" />
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
