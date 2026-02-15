<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import NavSidebar from '@/Components/vault/NavSidebar.vue'
import TaskList from '@/Components/vault/TaskList.vue'
import TaskDetail from '@/Components/vault/TaskDetail.vue'
import TaskForm from '@/Components/vault/TaskForm.vue'
import ConfirmDialog from '@/Components/vault/ConfirmDialog.vue'
import { PhSignOut } from '@phosphor-icons/vue'
import { Button } from '@/Components/ui/button'

const props = defineProps({
	tasks: Array,
	counts: Object,
	filters: Object,
})

const selectedTask = ref(null)
const showForm = ref(false)
const editingTask = ref(null)
const showConfirm = ref(false)
const pendingDelete = ref(null)

function selectTask(task) {
	selectedTask.value = task
}

function openCreate() {
	editingTask.value = null
	showForm.value = true
}

function openEdit(task) {
	editingTask.value = task
	showForm.value = true
}

function closeForm() {
	showForm.value = false
	editingTask.value = null
}

function deleteTask(task) {
	pendingDelete.value = task
	showConfirm.value = true
}

function confirmDelete() {
	if (pendingDelete.value) {
		router.delete(route('tasks.destroy', pendingDelete.value.id), {
			preserveScroll: true,
			onSuccess: () => { selectedTask.value = null },
		})
	}
	showConfirm.value = false
	pendingDelete.value = null
}

function cancelDelete() {
	showConfirm.value = false
	pendingDelete.value = null
}

const currentSelected = computed(() => {
	if (!selectedTask.value) return null
	return props.tasks.find(t => t.id === selectedTask.value.id) || null
})
</script>

<template>
	<Head title="Tasks" />

	<div class="flex h-screen bg-background">
		<!-- Column 1: Nav sidebar -->
		<NavSidebar
			:counts="counts"
			:filters="filters"
			page="tasks"
			@create="openCreate"
		/>

		<!-- Column 2: Task list -->
		<TaskList
			:tasks="tasks"
			:filters="filters"
			:selectedId="currentSelected?.id"
			@select="selectTask"
		/>

		<!-- Column 3: Detail panel -->
		<div class="flex flex-1 flex-col">
			<!-- Top bar -->
			<div class="flex items-center justify-between border-b border-border pl-6 pr-3 h-[50px]">
				<div class="text-xs text-muted-foreground font-mono">
					{{ counts.all }} tasks · {{ counts.open }} open
				</div>
				<Button variant="ghost" size="icon" class="h-8 w-8" @click="router.post(route('logout'))">
					<PhSignOut class="h-4 w-4 text-white" weight="thin" />
				</Button>
			</div>

			<!-- Detail -->
			<div class="flex-1 overflow-hidden">
				<TaskDetail
					:task="currentSelected"
					@edit="openEdit"
					@delete="deleteTask"
				/>
			</div>
		</div>
	</div>

	<!-- Dialogs -->
	<TaskForm :open="showForm" :task="editingTask" @close="closeForm" />
	<ConfirmDialog
		:open="showConfirm"
		title="Delete task"
		:description="'This will permanently delete &quot;' + (pendingDelete?.title || 'this task') + '&quot;. This action cannot be undone.'"
		confirmText="Delete"
		variant="destructive"
		@confirm="confirmDelete"
		@cancel="cancelDelete"
	/>
</template>
