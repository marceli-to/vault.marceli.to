<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import NavSidebar from '@/Components/vault/NavSidebar.vue'
import EntryList from '@/Components/vault/EntryList.vue'
import EntryDetail from '@/Components/vault/EntryDetail.vue'
import EntryForm from '@/Components/vault/EntryForm.vue'
import SearchCommand from '@/Components/vault/SearchCommand.vue'
import ConfirmDialog from '@/Components/vault/ConfirmDialog.vue'
import { PhSignOut } from '@phosphor-icons/vue'
import { Button } from '@/Components/ui/button'

const props = defineProps({
  entries: Array,
  allEntries: Array,
  counts: Object,
  filters: Object,
})

const selectedEntry = ref(null)
const showForm = ref(false)
const editingEntry = ref(null)
const showSearch = ref(false)
const showConfirm = ref(false)
const pendingDelete = ref(null)

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

const currentSelected = computed(() => {
  if (!selectedEntry.value) return null
  return props.entries.find(e => e.id === selectedEntry.value.id) || null
})
</script>

<template>
  <Head title="Dashboard" />

  <div class="flex h-screen bg-background">
    <!-- Column 1: Nav sidebar -->
    <NavSidebar
      :counts="counts"
      :filters="filters"
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
          {{ counts.all }} entries in vault
        </div>
        <Button variant="ghost" size="icon" class="h-8 w-8" @click="router.post(route('logout'))">
          <PhSignOut class="h-4 w-4 text-white" weight="thin" />
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
