<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { PhLightbulb, PhLink, PhNote, PhPushPin, PhPlus, PhMagnifyingGlass, PhStack } from '@phosphor-icons/vue'
import { Button } from '@/Components/ui/button'
import { Separator } from '@/Components/ui/separator'
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from '@/Components/ui/tooltip'

const props = defineProps({
  counts: Object,
  filters: Object,
})

const emit = defineEmits(['create', 'search'])

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
</script>

<template>
  <div class="flex h-full w-16 flex-col items-center border-r border-border bg-card py-4">
    <!-- Logo -->
    <div class="mb-4">
      <svg width="20" height="15" viewBox="0 0 379 265" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M179.138 265L24.2363 19.7487H0V0H201.618V19.7487H184.407C179.958 19.7487 176.211 21.5858 173.167 25.26C170.357 28.9341 168.952 33.9861 168.952 40.4159C168.952 45.3148 170.123 51.1323 172.464 57.8683C175.04 64.2981 179.138 71.1872 184.758 78.5355L239.553 151.101L278.191 97.3657C282.64 91.2421 285.801 85.2715 287.675 79.4541C289.782 73.3304 290.836 67.3599 290.836 61.5425C290.836 49.9076 286.504 40.1098 277.84 32.149C269.41 23.8821 257.467 19.7487 242.012 19.7487H219.181V0H379V19.7487H359.681L190.378 265H179.138ZM185.812 242.036L196.349 227.34L65.684 19.7487H45.6626L185.812 242.036Z" fill="white"/>
      </svg>
    </div>

    <!-- Search -->
    <TooltipProvider :delay-duration="0">
      <Tooltip>
        <TooltipTrigger asChild>
          <Button variant="ghost" size="icon" class="mb-1 h-10 w-10" @click="emit('search')">
            <PhMagnifyingGlass class="h-5 w-5 text-white" weight="thin" />
          </Button>
        </TooltipTrigger>
        <TooltipContent side="right"><span>Search <kbd class="ml-1 text-[10px] opacity-60">⌘K</kbd></span></TooltipContent>
      </Tooltip>
    </TooltipProvider>

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
                  ? 'bg-emerald-300/10 text-emerald-300'
                  : 'text-zinc-400 hover:bg-secondary hover:text-foreground'
              ]"
            >
              <component :is="item.icon" :class="['h-5 w-5', activeType === item.type ? 'text-emerald-300' : 'text-white']" weight="thin" />
            </button>
          </TooltipTrigger>
          <TooltipContent side="right">{{ item.label }} ({{ item.count }})</TooltipContent>
        </Tooltip>
      </TooltipProvider>
    </nav>

    <!-- Spacer -->
    <div class="flex-1" />

    <!-- Add button -->
    <TooltipProvider :delay-duration="0">
      <Tooltip>
        <TooltipTrigger asChild>
          <Button variant="ghost" size="icon" class="h-10 w-10" @click="emit('create')">
            <PhPlus class="h-5 w-5 text-white" weight="thin" />
          </Button>
        </TooltipTrigger>
        <TooltipContent side="right">New Entry</TooltipContent>
      </Tooltip>
    </TooltipProvider>
  </div>
</template>
