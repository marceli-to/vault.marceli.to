<script setup>
import { PhPencilSimple, PhTrash, PhPushPin, PhPushPinSlash, PhArrowSquareOut, PhClock, PhTextT } from '@phosphor-icons/vue'
import { Button } from '@/Components/ui/button'
import { Separator } from '@/Components/ui/separator'
import TypeIcon from './TypeIcon.vue'
import TagBadge from './TagBadge.vue'

const props = defineProps({ entry: Object })
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
    <div class="flex items-start justify-between border-b border-border p-6 pb-4">
      <div class="flex-1 space-y-2">
        <div class="flex items-center gap-2">
          <TypeIcon :type="entry.type" class="h-4 w-4" />
          <span class="rounded-full bg-secondary px-2 py-0.5 text-xs font-medium text-muted-foreground capitalize">
            {{ typeLabels[entry.type] }}
          </span>
          <span v-if="entry.is_pinned" class="rounded-full bg-yellow-500/10 px-2 py-0.5 text-xs font-medium text-yellow-400">
            Pinned
          </span>
        </div>
        <h1 v-if="entry.title" class="text-xl font-semibold tracking-tight">
          {{ entry.title }}
        </h1>
      </div>
      <div class="flex items-center gap-1">
        <Button variant="ghost" size="icon" class="h-8 w-8" @click="emit('togglePin', entry)">
          <PhPushPinSlash v-if="entry.is_pinned" class="h-4 w-4 text-white" weight="thin" />
          <PhPushPin v-else class="h-4 w-4 text-white" weight="thin" />
        </Button>
        <Button variant="ghost" size="icon" class="h-8 w-8" @click="emit('edit', entry)">
          <PhPencilSimple class="h-4 w-4 text-white" weight="thin" />
        </Button>
        <Button variant="ghost" size="icon" class="h-8 w-8 text-red-400 hover:text-red-300" @click="emit('delete', entry)">
          <PhTrash class="h-4 w-4" weight="thin" />
        </Button>
      </div>
    </div>

    <!-- Content -->
    <div class="flex-1 overflow-auto p-6">
      <div class="prose prose-invert max-w-none whitespace-pre-wrap text-sm leading-relaxed text-foreground/90">
        {{ entry.content }}
      </div>

      <a
        v-if="entry.url"
        :href="entry.url"
        target="_blank"
        class="mt-4 inline-flex items-center gap-2 rounded-lg border border-border bg-secondary/30 px-3 py-2 text-sm text-emerald-300 transition-colors hover:bg-secondary"
      >
        <PhArrowSquareOut class="h-4 w-4 text-white" weight="thin" />
        {{ entry.url }}
      </a>
    </div>

    <!-- Footer -->
    <div class="border-t border-border p-4">
      <div class="flex items-center justify-between">
        <div class="flex flex-wrap gap-1.5">
          <TagBadge v-for="tag in entry.tags" :key="tag" :tag="tag" />
        </div>
        <div class="flex items-center gap-3 text-xs text-muted-foreground">
          <span class="flex items-center gap-1">
            <PhTextT class="h-3 w-3 text-white" weight="thin" />
            {{ entry.word_count }} words
          </span>
          <span class="flex items-center gap-1">
            <PhClock class="h-3 w-3 text-white" weight="thin" />
            {{ formatDate(entry.created_at) }}
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Empty state -->
  <div v-else class="flex h-full flex-col items-center justify-center text-muted-foreground">
    <p class="text-sm">Select an entry to view details</p>
    <p class="text-xs text-muted-foreground/50">or press ⌘K to search</p>
  </div>
</template>
