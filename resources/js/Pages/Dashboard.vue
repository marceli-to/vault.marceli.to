<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import NavSidebar from '@/Components/vault/NavSidebar.vue'
import EntryList from '@/Components/vault/EntryList.vue'
import EntryDetail from '@/Components/vault/EntryDetail.vue'
import EntryForm from '@/Components/vault/EntryForm.vue'
import SearchCommand from '@/Components/vault/SearchCommand.vue'
import ConfirmDialog from '@/Components/vault/ConfirmDialog.vue'
import {
  PhLightbulb,
  PhLink,
  PhList,
  PhMagnifyingGlass,
  PhNote,
  PhArrowLeft,
  PhCheckSquare,
  PhPlus,
  PhPushPin,
  PhSignOut,
  PhStack,
  PhX,
} from '@phosphor-icons/vue'
import { Button } from '@/Components/ui/button'

const props = defineProps({
  entries: Array,
  allEntries: Array,
  counts: Object,
  tags: Array,
  filters: Object,
})

const selectedEntry = ref(null)
const showForm = ref(false)
const editingEntry = ref(null)
const showSearch = ref(false)
const showConfirm = ref(false)
const pendingDelete = ref(null)
const showMobileMenu = ref(false)

function selectEntry(entry) {
  selectedEntry.value = entry
}

function openCreate() {
  editingEntry.value = null
  showForm.value = true
}

function openEdit(entry) {
  editingEntry.value = entry
  showForm.value = true
}

function closeForm() {
  showForm.value = false
  editingEntry.value = null
}

function deleteEntry(entry) {
  pendingDelete.value = entry
  showConfirm.value = true
}

function confirmDelete() {
  if (pendingDelete.value) {
	router.delete(route('entries.destroy', pendingDelete.value.id), {
	  preserveScroll: true,
	  onSuccess: () => { selectedEntry.value = null },
	})
  }
  showConfirm.value = false
  pendingDelete.value = null
}

function cancelDelete() {
  showConfirm.value = false
  pendingDelete.value = null
}

function togglePin(entry) {
  router.put(route('entries.update', entry.id), {
	...entry,
	is_pinned: !entry.is_pinned,
  }, { preserveScroll: true })
}

function closeMobileDetail() {
  selectedEntry.value = null
}

const navItems = computed(() => ([
  { label: 'All Entries', type: null, icon: PhStack, count: props.counts.all },
  { label: 'Ideas', type: 'idea', icon: PhLightbulb, count: props.counts.idea },
  { label: 'Links', type: 'link', icon: PhLink, count: props.counts.link },
  { label: 'Notes', type: 'note', icon: PhNote, count: props.counts.note },
  { label: 'Pinned', type: 'pinned', icon: PhPushPin, count: props.counts.pinned },
]))

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
  showMobileMenu.value = false
  router.get(route('dashboard'), params, { preserveState: true, preserveScroll: true })
}

function navigateTag(tag) {
  showMobileMenu.value = false
  router.get(route('dashboard'), { tag }, { preserveState: true, preserveScroll: true })
}

function clearTag() {
  showMobileMenu.value = false
  router.get(route('dashboard'), {}, { preserveState: true, preserveScroll: true })
}

const currentSelected = computed(() => {
  if (!selectedEntry.value) return null
  return props.entries.find(e => e.id === selectedEntry.value.id) || null
})
</script>

<template>
  <Head title="Dashboard" />

  <div class="h-screen bg-background md:hidden">
    <div class="flex h-full flex-col">
      <div class="flex h-[50px] items-center justify-between border-b border-border px-2">
      <Button v-if="currentSelected" variant="ghost" size="icon" class="h-8 w-8" @click="closeMobileDetail">
        <PhArrowLeft class="h-4 w-4 text-foreground" weight="thin" />
      </Button>
      <Button v-else variant="ghost" size="icon" class="h-8 w-8" @click="showMobileMenu = true">
        <PhList class="h-4 w-4 text-foreground" weight="thin" />
      </Button>
      <Button variant="ghost" size="icon" class="h-8 w-8" @click="showSearch = true">
        <PhMagnifyingGlass class="h-4 w-4 text-foreground" weight="thin" />
      </Button>
      <div class="flex items-center gap-1">
        <Button variant="ghost" size="icon" class="h-8 w-8" @click="openCreate">
        <PhPlus class="h-4 w-4 text-foreground" weight="thin" />
        </Button>
      </div>
      </div>

      <div class="flex-1 overflow-hidden">
      <EntryList
        v-if="!currentSelected"
        :entries="entries"
        :filters="filters"
        :selectedId="currentSelected?.id"
        @select="selectEntry"
      />

      <EntryDetail
        v-else
        :entry="currentSelected"
        @edit="openEdit"
        @delete="deleteEntry"
        @togglePin="togglePin"
      />
      </div>

      <div v-if="showMobileMenu" class="fixed inset-0 z-50 md:hidden">
      <div class="absolute inset-0 bg-black/70" @click="showMobileMenu = false" />
      <div class="absolute inset-y-0 left-0 w-[86vw] max-w-sm border-r border-border bg-background p-4 overflow-y-auto">
        <div class="-mx-4 -mt-4 mb-3 flex h-[50px] items-center border-b border-border px-2">
        <Button variant="ghost" size="icon" class="h-8 w-8" @click="showMobileMenu = false">
          <PhX class="h-4 w-4 text-foreground" weight="thin" />
        </Button>
        </div>

        <div class="space-y-5">
        <!-- Page switcher -->
        <div class="flex gap-2">
          <button
            class="flex flex-1 items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm bg-amber-500/20 text-amber-600 dark:bg-violet-500/15 dark:text-violet-400 transition-all"
          >
            <PhStack class="h-4 w-4" weight="thin" />
            Entries
          </button>
          <button
            @click="showMobileMenu = false; router.get(route('tasks.index'))"
            class="flex flex-1 items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm text-muted-foreground hover:bg-secondary hover:text-foreground transition-all"
          >
            <PhCheckSquare class="h-4 w-4" weight="thin" />
            Tasks
          </button>
        </div>

        <div class="space-y-2">
        			  <button
				v-for="item in navItems"
				:key="item.label"
				@click="navigate(item)"
				:class="[
				  'flex w-full items-center gap-2 rounded-md px-1.5 py-1.5 text-left text-sm transition-all',
				  activeType === item.type
					? 'text-amber-600 dark:text-violet-400'
					: 'text-muted-foreground hover:bg-secondary hover:text-foreground'
				]"
			  >
				<component :is="item.icon" :class="['h-3.5 w-3.5 shrink-0', activeType === item.type ? 'text-amber-600 dark:text-violet-400' : 'text-foreground']" weight="thin" />
				<span class="truncate">{{ item.label }}</span>
				<span class="ml-auto text-xs opacity-60">{{ item.count }}</span>
			  </button>
        </div>

        <div v-if="tags.length" class="space-y-2">
          <div class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Tags</div>
          <button
          v-if="filters?.tag"
          @click="clearTag"
          class="w-full rounded-md px-2 py-1 text-left text-xs text-muted-foreground hover:bg-secondary/50"
          >
          Clear current tag
          </button>
          <button
          v-for="tag in tags"
          :key="tag.name"
          @click="navigateTag(tag.name)"
          :class="[
            'flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-sm transition-all',
            filters?.tag === tag.name
            ? getTagColor(tag.name).activeBg + ' ' + getTagColor(tag.name).text
            : 'text-muted-foreground hover:bg-secondary/50 hover:text-foreground'
          ]"
          >
          <span :class="['h-2 w-2 rounded-full shrink-0', getTagColor(tag.name).dot]" />
          <span class="truncate flex-1 text-left">{{ tag.name }}</span>
          <span class="text-xs text-muted-foreground/60 shrink-0">{{ tag.count }}</span>
          </button>
        </div>

        <button
          type="button"
          class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs text-muted-foreground transition-colors hover:bg-secondary/50 hover:text-foreground"
          @click="router.post(route('logout'))"
        >
          <PhSignOut class="h-3.5 w-3.5" weight="thin" />
          <span>Log out</span>
        </button>
        </div>
      </div>
      </div>
    </div>
  </div>

  <div class="hidden h-screen bg-background md:flex">
	<!-- Column 1: Nav sidebar -->
	<NavSidebar
	  :counts="props.counts"
	  :filters="filters"
	  :tags="tags"
	  page="entries"
	  @create="openCreate"
	  @search="showSearch = true"
	/>

	<!-- Column 2: Entry list -->
	<EntryList
	  :entries="entries"
	  :filters="filters"
	  :selectedId="currentSelected?.id"
	  @select="selectEntry"
	/>

	<!-- Column 3: Detail panel -->
	<div class="flex flex-1 flex-col">
	  <!-- Top bar -->
	  <div class="flex items-center justify-between border-b border-border pl-6 pr-3 h-[50px]">
		<div class="text-xs text-muted-foreground font-mono">
		  {{ props.counts.all }} entries in vault
		</div>
		<Button variant="ghost" size="icon" class="h-8 w-8" @click="router.post(route('logout'))">
		  <PhSignOut class="h-4 w-4 text-foreground" weight="thin" />
		</Button>
	  </div>

	  <!-- Detail -->
	  <div class="flex-1 overflow-hidden">
		<EntryDetail
		  :entry="currentSelected"
		  @edit="openEdit"
		  @delete="deleteEntry"
		  @togglePin="togglePin"
		/>
	  </div>
	</div>
  </div>

  <!-- Dialogs -->
  <EntryForm :open="showForm" :entry="editingEntry" @close="closeForm" />
  <SearchCommand v-model:open="showSearch" :entries="allEntries" @select="selectEntry" />
  <ConfirmDialog
	:open="showConfirm"
	title="Delete entry"
	:description="'This will permanently delete &quot;' + (pendingDelete?.title || 'this entry') + '&quot;. This action cannot be undone.'"
	confirmText="Delete"
	variant="destructive"
	@confirm="confirmDelete"
	@cancel="cancelDelete"
  />
</template>
