<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue'
import {
  CommandDialog,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '@/Components/ui/command'
import TypeIcon from './TypeIcon.vue'

const props = defineProps({
  open: Boolean,
  entries: { type: Array, default: () => [] },
})

const emit = defineEmits(['update:open', 'select'])

const search = ref('')
const filtered = ref([])
const indexedEntries = ref([])

const MAX_RESULTS = 10
const SEARCH_DEBOUNCE_MS = 120
let searchDebounceId = null

function buildSearchText(entry) {
  const title = (entry.title || '').toLowerCase()
  const content = (entry.content || '').toLowerCase()
  const tags = Array.isArray(entry.tags)
    ? entry.tags.map(tag => String(tag).toLowerCase()).join(' ')
    : ''

  return `${title} ${content} ${tags}`
}

function runSearch(query) {
  if (!query) {
    filtered.value = props.entries.slice(0, MAX_RESULTS)
    return
  }

  const q = query.toLowerCase()
  const matches = []

  for (const item of indexedEntries.value) {
    if (item.searchText.includes(q)) {
      matches.push(item.entry)
      if (matches.length >= MAX_RESULTS) break
    }
  }

  filtered.value = matches
}

watch(() => props.entries, (entries) => {
  indexedEntries.value = entries.map(entry => ({
    entry,
    searchText: buildSearchText(entry),
  }))

  runSearch(search.value)
}, { immediate: true })

watch(search, (val) => {
  if (searchDebounceId) clearTimeout(searchDebounceId)
  searchDebounceId = setTimeout(() => runSearch(val), SEARCH_DEBOUNCE_MS)
})

watch(() => props.open, (val) => {
  if (val) {
    search.value = ''
    runSearch('')
  }
})

function selectEntry(entry) {
  emit('select', entry)
  emit('update:open', false)
}

// ⌘K shortcut
function handleKeydown(e) {
  if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
	e.preventDefault()
	emit('update:open', !props.open)
  }
}

onMounted(() => window.addEventListener('keydown', handleKeydown))
onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown)
  if (searchDebounceId) clearTimeout(searchDebounceId)
})
</script>

<template>
  <CommandDialog :open="open" @update:open="$emit('update:open', $event)">
	<CommandInput v-model="search" placeholder="Search entries..." />
	<CommandList>
	  <CommandEmpty>No entries found.</CommandEmpty>
	  <CommandGroup heading="Entries">
		<CommandItem
		  v-for="entry in filtered"
		  :key="entry.id"
		  :value="entry.title || entry.content.substring(0, 50)"
		  @select="selectEntry(entry)"
		  class="flex items-center gap-3 cursor-pointer"
		>
		  <TypeIcon :type="entry.type" class="h-4 w-4 shrink-0" />
		  <div class="flex flex-col gap-0.5 overflow-hidden">
			<span class="truncate text-sm">{{ entry.title || entry.content.substring(0, 60) }}</span>
			<span v-if="entry.title" class="truncate text-xs opacity-60">{{ entry.content.substring(0, 80) }}</span>
		  </div>
		</CommandItem>
	  </CommandGroup>
	</CommandList>
  </CommandDialog>
</template>
