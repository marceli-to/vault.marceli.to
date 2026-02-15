<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
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

watch(search, (val) => {
  if (!val) {
	filtered.value = props.entries.slice(0, 10)
	return
  }
  const q = val.toLowerCase()
  filtered.value = props.entries.filter(e =>
	(e.title && e.title.toLowerCase().includes(q)) ||
	e.content.toLowerCase().includes(q) ||
	e.tags.some(t => t.toLowerCase().includes(q))
  ).slice(0, 10)
})

watch(() => props.open, (val) => {
  if (val) {
	search.value = ''
	filtered.value = props.entries.slice(0, 10)
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
onUnmounted(() => window.removeEventListener('keydown', handleKeydown))
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
