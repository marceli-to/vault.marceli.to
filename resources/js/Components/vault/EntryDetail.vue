<script setup>
import { router } from '@inertiajs/vue3'
import { PhPencilSimple, PhTrash, PhPushPin, PhPushPinSlash, PhArrowSquareOut, PhClock, PhTextT } from '@phosphor-icons/vue'
import { Button } from '@/Components/ui/button'
import { Separator } from '@/Components/ui/separator'
import TypeIcon from './TypeIcon.vue'
import TagBadge from './TagBadge.vue'

const props = defineProps({ entry: Object })

function filterByTag(tag) {
  router.get(route('dashboard'), { tag }, { preserveState: true, preserveScroll: true })
}
const emit = defineEmits(['edit', 'delete', 'togglePin'])

function formatDate(date) {
  return new Date(date).toLocaleDateString('en-US', {
	weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
	hour: '2-digit', minute: '2-digit',
  })
}

const typeLabels = { idea: 'Idea', link: 'Link', note: 'Note', reference: 'Reference' }
</script>

<template>
  <div v-if="entry" class="flex h-full flex-col">
	<!-- Header -->
	<div class="flex items-center gap-2 border-b border-border px-3 py-2 md:px-6 md:py-3 shrink-0">
	  <div class="flex items-center gap-1">
		<Button variant="ghost" size="icon" class="h-7 w-7 md:h-8 md:w-8" @click="emit('togglePin', entry)">
		  <PhPushPinSlash v-if="entry.is_pinned" class="h-3.5 w-3.5 md:h-4 md:w-4 text-foreground" weight="thin" />
		  <PhPushPin v-else class="h-3.5 w-3.5 md:h-4 md:w-4 text-foreground" weight="thin" />
		</Button>
		<Button variant="ghost" size="icon" class="h-7 w-7 md:h-8 md:w-8" @click="emit('edit', entry)">
		  <PhPencilSimple class="h-3.5 w-3.5 md:h-4 md:w-4 text-foreground" weight="thin" />
		</Button>
		<Button variant="ghost" size="icon" class="h-7 w-7 md:h-8 md:w-8 text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300" @click="emit('delete', entry)">
		  <PhTrash class="h-3.5 w-3.5 md:h-4 md:w-4" weight="thin" />
		</Button>
	  </div>
	  <div class="flex flex-wrap items-center gap-2 ml-auto">
		<TypeIcon :type="entry.type" class="h-3.5 w-3.5 md:h-4 md:w-4" />
		<span class="rounded-full bg-secondary px-2 py-0.5 text-xs font-medium text-muted-foreground capitalize">
		  {{ typeLabels[entry.type] }}
		</span>
		<span v-if="entry.is_pinned" class="rounded-full bg-yellow-500/10 px-2 py-0.5 text-xs font-medium text-yellow-600 dark:bg-violet-500/10 dark:text-violet-400">
		  Pinned
		</span>
	  </div>
	</div>

	<!-- Content -->
	<div class="flex-1 overflow-auto p-4 md:p-6">
	  <h1 v-if="entry.title" class="text-lg font-semibold tracking-tight md:text-xl mb-4">
		{{ entry.title }}
	  </h1>
	  <div class="prose prose-invert max-w-none whitespace-pre-wrap text-sm leading-relaxed text-foreground/90">
		{{ entry.content }}
	  </div>

	  <a
		v-if="entry.url"
		:href="entry.url"
		target="_blank"
		class="mt-4 flex items-center gap-2 rounded-lg bg-secondary/30 py-2 text-sm text-amber-500 dark:text-violet-400 transition-colors hover:bg-secondary max-w-full overflow-hidden"
	  >
		<PhArrowSquareOut class="h-4 w-4 shrink-0 text-foreground" weight="thin" />
		<span class="truncate">{{ entry.url }}</span>
	  </a>
	</div>

	<!-- Footer -->
	<div class="border-t border-border px-4 py-4 md:p-4 shrink-0">
	  <div class="flex flex-wrap gap-1.5">
		<TagBadge v-for="tag in entry.tags" :key="tag" :tag="tag" clickable @click="filterByTag(tag)" />
	  </div>
	</div>
  </div>

  <!-- Empty state -->
  <div v-else class="flex h-full flex-col items-center justify-center text-muted-foreground">
	<p class="text-sm">Select an entry to view details</p>
	<p class="text-xs text-muted-foreground/50">or press ⌘K to search</p>
  </div>
</template>
