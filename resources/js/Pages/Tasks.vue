<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import NavSidebar from '@/Components/vault/NavSidebar.vue'
import TaskList from '@/Components/vault/TaskList.vue'
import TaskDetail from '@/Components/vault/TaskDetail.vue'
import TaskForm from '@/Components/vault/TaskForm.vue'
import ConfirmDialog from '@/Components/vault/ConfirmDialog.vue'
import { PhArrowLeft, PhCheckCircle, PhCircle, PhCircleHalf, PhList, PhMagnifyingGlass, PhPlus, PhSignOut, PhX } from '@phosphor-icons/vue'
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
const showMobileMenu = ref(false)

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

function closeMobileDetail() {
	selectedTask.value = null
}

const navItems = computed(() => ([
	{ label: 'All Tasks', type: null, icon: PhList, count: props.counts.all },
	{ label: 'Open', type: 'open', icon: PhCircle, count: props.counts.open },
	{ label: 'In Progress', type: 'in_progress', icon: PhCircleHalf, count: props.counts.in_progress },
	{ label: 'Done', type: 'done', icon: PhCheckCircle, count: props.counts.done },
]))

const activeStatus = computed(() => props.filters?.status || null)

function navigate(item) {
	const params = item.type ? { status: item.type } : {}
	showMobileMenu.value = false
	router.get(route('tasks.index'), params, { preserveState: true, preserveScroll: true })
}

const currentSelected = computed(() => {
	if (!selectedTask.value) return null
	return props.tasks.find(t => t.id === selectedTask.value.id) || null
})
</script>

<template>
	<Head title="Tasks" />

	<div class="h-screen bg-background md:hidden">
		<div class="flex h-full flex-col">
			<div class="flex h-[50px] items-center justify-between border-b border-border px-2">
				<Button v-if="currentSelected" variant="ghost" size="icon" class="h-8 w-8" @click="closeMobileDetail">
					<PhArrowLeft class="h-4 w-4 text-foreground" weight="thin" />
				</Button>
				<Button v-else variant="ghost" size="icon" class="h-8 w-8" @click="showMobileMenu = true">
					<PhList class="h-4 w-4 text-foreground" weight="thin" />
				</Button>
				<div class="text-xs font-mono text-muted-foreground">{{ props.counts.open }} open</div>
				<div class="flex items-center gap-1">
					<Button variant="ghost" size="icon" class="h-8 w-8" @click="openCreate">
						<PhPlus class="h-4 w-4 text-foreground" weight="thin" />
					</Button>
				</div>
			</div>

			<div class="flex-1 overflow-hidden">
				<TaskList
					v-if="!currentSelected"
					:tasks="tasks"
					:filters="filters"
					:selectedId="currentSelected?.id"
					@select="selectTask"
				/>

				<TaskDetail
					v-else
					:task="currentSelected"
					@edit="openEdit"
					@delete="deleteTask"
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
						<div class="space-y-2">
							<button
								v-for="item in navItems"
								:key="item.label"
								@click="navigate(item)"
								:class="[
									'flex w-full items-center gap-2.5 rounded-lg px-3 py-1.5 text-left text-sm transition-all',
									activeStatus === item.type
										? 'bg-amber-500/20 text-amber-600 dark:bg-violet-500/15 dark:text-violet-400'
										: 'text-muted-foreground hover:bg-secondary hover:text-foreground'
								]"
							>
								<component :is="item.icon" :class="['h-4 w-4 shrink-0', activeStatus === item.type ? 'text-amber-600 dark:text-violet-400' : 'text-foreground']" weight="thin" />
								<span class="truncate">{{ item.label }}</span>
								<span class="ml-auto text-xs opacity-60">{{ item.count }}</span>
							</button>
						</div>

						<div class="border-t border-border pt-4">
							<Button variant="ghost" class="w-full justify-start gap-2 text-muted-foreground" @click="router.post(route('logout'))">
								<PhSignOut class="h-4 w-4" weight="thin" />
								Log out
							</Button>
						</div>
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
					{{ props.counts.all }} tasks · {{ props.counts.open }} open
				</div>
				<Button variant="ghost" size="icon" class="h-8 w-8" @click="router.post(route('logout'))">
					<PhSignOut class="h-4 w-4 text-foreground" weight="thin" />
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
