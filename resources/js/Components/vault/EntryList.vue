<script setup>
import { computed } from 'vue'
import { ScrollArea } from '@/Components/ui/scroll-area'
import { PhPushPin } from '@phosphor-icons/vue'
import TypeIcon from './TypeIcon.vue'

const props = defineProps({
  entries: Array,
  selectedId: [Number, null],
  filters: Object,
})

const emit = defineEmits(['select'])

function groupEntries(entries) {
  const now = new Date()
  const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
  const yesterday = new Date(today); yesterday.setDate(yesterday.getDate() - 1)
  const weekAgo = new Date(today); weekAgo.setDate(weekAgo.getDate() - 7)
  const monthAgo = new Date(today); monthAgo.setMonth(monthAgo.getMonth() - 1)

  const groups = { 'Today': [], 'Yesterday': [], 'This Week': [], 'This Month': [], 'Older': [] }

  entries.forEach(entry => {
    const d = new Date(entry.created_at)
    if (d >= today) groups['Today'].push(entry)
    else if (d >= yesterday) groups['Yesterday'].push(entry)
    else if (d >= weekAgo) groups['This Week'].push(entry)
    else if (d >= monthAgo) groups['This Month'].push(entry)
    else groups['Older'].push(entry)
  })

  return Object.entries(groups).filter(([, items]) => items.length > 0)
}

const groupedEntries = computed(() => groupEntries(props.entries))

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
  <div class="flex h-full w-80 flex-col border-r border-border bg-background">
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
                  ? 'bg-emerald-400/10 ring-1 ring-emerald-500/20'
                  : 'hover:bg-secondary/50'
              ]"
            >
              <div class="flex items-center gap-2">
                <TypeIcon :type="entry.type" class="h-3 w-3 shrink-0" />
                <span class="truncate text-sm font-medium">{{ entry.title || truncate(entry.content, 40) }}</span>
                <PhPushPin v-if="entry.is_pinned" class="ml-auto h-3 w-3 shrink-0 text-white" weight="thin" />
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
