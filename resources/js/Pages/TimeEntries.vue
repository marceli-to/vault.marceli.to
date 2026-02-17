<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import NavSidebar from '@/Components/vault/NavSidebar.vue'
import TimeEntryList from '@/Components/vault/TimeEntryList.vue'
import TimeEntryDetail from '@/Components/vault/TimeEntryDetail.vue'
import TimeEntryForm from '@/Components/vault/TimeEntryForm.vue'
import ConfirmDialog from '@/Components/vault/ConfirmDialog.vue'
import { ScrollArea } from '@/Components/ui/scroll-area'
import { 
	PhArrowLeft, 
	PhCheckCircle, 
	PhCircle, 
	PhCurrencyDollarSimple,
	PhList, 
	PhPlus, 
	PhSignOut, 
	PhTimer,
	PhX,
	PhFunnel,
	PhStack,
	PhCheckSquare,
} from '@phosphor-icons/vue'
import { Button } from '@/Components/ui/button'

const props = defineProps({
	timeEntries: Array,
	counts: Object,
	projects: Array,
	projectSummary: Array,
	filters: Object,
})

const selectedEntry = ref(null)
const showForm = ref(false)
const editingEntry = ref(null)
const showConfirm = ref(false)
const pendingDelete = ref(null)
const showMobileMenu = ref(false)
const showProjectPanel = ref(false)

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
		router.delete(route('time-entries.destroy', pendingDelete.value.id), {
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

function closeMobileDetail() {
	selectedEntry.value = null
}

const navItems = computed(() => ([
	{ label: 'All Entries', type: null, icon: PhList, count: props.counts.all },
	{ label: 'Open', type: 'open', icon: PhCircle, count: props.counts.open },
	{ label: 'Processed', type: 'processed', icon: PhCheckCircle, count: props.counts.processed },
	{ label: 'Billable', type: 'billable', icon: PhCurrencyDollarSimple, count: props.counts.billable },
]))

const activeStatus = computed(() => {
	if (props.filters?.billable === true || props.filters?.billable === '1') return 'billable'
	return props.filters?.status || null
})

function navigate(item) {
	let params = {}
	if (item.type === 'billable') {
		params.billable = 1
	} else if (item.type) {
		params.status = item.type
	}
	showMobileMenu.value = false
	router.get(route('time-entries.index'), params, { preserveState: true, preserveScroll: true })
}

function navigateProject(projectName) {
	showMobileMenu.value = false
	showProjectPanel.value = false
	if (props.filters?.project === projectName) {
		router.get(route('time-entries.index'), {}, { preserveState: true, preserveScroll: true })
	} else {
		router.get(route('time-entries.index'), { project: projectName }, { preserveState: true, preserveScroll: true })
	}
}

function clearFilters() {
	router.get(route('time-entries.index'), {}, { preserveState: true, preserveScroll: true })
}

const currentSelected = computed(() => {
	if (!selectedEntry.value) return null
	return props.timeEntries.find(e => e.id === selectedEntry.value.id) || null
})

function formatMinutes(minutes) {
	if (!minutes) return '0h'
	const h = Math.floor(minutes / 60)
	const m = minutes % 60
	if (h === 0) return `${m}m`
	if (m === 0) return `${h}h`
	return `${h}h ${m}m`
}

const totalActual = computed(() => {
	return props.timeEntries.reduce((sum, e) => sum + (e.actual_minutes || 0), 0)
})

const totalEstimated = computed(() => {
	return props.timeEntries.reduce((sum, e) => sum + (e.estimated_minutes || 0), 0)
})
</script>

<template>
	<Head title="Time Entries" />

	<!-- Mobile layout -->
	<div class="h-screen bg-background md:hidden">
		<div class="flex h-full flex-col">
			<div class="flex h-[50px] items-center justify-between border-b border-border px-2">
				<Button v-if="currentSelected" variant="ghost" size="icon" class="h-8 w-8" @click="closeMobileDetail">
					<PhArrowLeft class="h-4 w-4 text-foreground" weight="thin" />
				</Button>
				<Button v-else variant="ghost" size="icon" class="h-8 w-8" @click="showMobileMenu = true">
					<PhList class="h-4 w-4 text-foreground" weight="thin" />
				</Button>
				<div class="text-xs font-mono text-muted-foreground">{{ formatMinutes(totalActual) }} tracked</div>
				<div class="flex items-center gap-1">
					<Button variant="ghost" size="icon" class="h-8 w-8" @click="openCreate">
						<PhPlus class="h-4 w-4 text-foreground" weight="thin" />
					</Button>
				</div>
			</div>

			<div class="flex-1 overflow-hidden">
				<TimeEntryList
					v-if="!currentSelected"
					:timeEntries="timeEntries"
					:filters="filters"
					:selectedId="currentSelected?.id"
					@select="selectEntry"
				/>

				<TimeEntryDetail
					v-else
					:timeEntry="currentSelected"
					@edit="openEdit"
					@delete="deleteEntry"
				/>
			</div>

			<!-- Mobile menu -->
			<div v-if="showMobileMenu" class="fixed inset-0 z-50 md:hidden">
				<div class="absolute inset-0 bg-black/70" @click="showMobileMenu = false" />
				<div class="absolute inset-y-0 left-0 w-[86vw] max-w-sm border-r border-border bg-background p-4 overflow-y-auto">
					<div class="-mx-4 -mt-4 mb-3 flex h-[50px] items-center justify-between border-b border-border px-2">
						<Button variant="ghost" size="icon" class="h-8 w-8" @click="showMobileMenu = false">
							<PhX class="h-4 w-4 text-foreground" weight="thin" />
						</Button>
						<div class="flex items-center gap-2">
							<button
								@click="showMobileMenu = false; router.get(route('dashboard'))"
								class="flex items-center gap-1.5 px-2 py-1 text-sm text-muted-foreground hover:text-foreground transition-colors"
							>
								<PhStack class="h-4 w-4" weight="thin" />
								Entries
							</button>
							<button
								@click="showMobileMenu = false; router.get(route('tasks.index'))"
								class="flex items-center gap-1.5 px-2 py-1 text-sm text-muted-foreground hover:text-foreground transition-colors"
							>
								<PhCheckSquare class="h-4 w-4" weight="thin" />
								Tasks
							</button>
						</div>
					</div>

					<div class="space-y-5">
						<div class="space-y-2">
							<button
								v-for="item in navItems"
								:key="item.label"
								@click="navigate(item)"
								:class="[
									'flex w-full items-center gap-2 rounded-md px-1.5 py-1.5 text-left text-sm transition-all',
									activeStatus === item.type
										? 'text-amber-600 dark:text-violet-400'
										: 'text-muted-foreground hover:bg-secondary hover:text-foreground'
								]"
							>
								<component :is="item.icon" :class="['h-3.5 w-3.5 shrink-0', activeStatus === item.type ? 'text-amber-600 dark:text-violet-400' : 'text-foreground']" weight="thin" />
								<span class="truncate">{{ item.label }}</span>
								<span class="ml-auto text-xs opacity-60">{{ item.count }}</span>
							</button>
						</div>

						<!-- Projects -->
						<div v-if="projects.length" class="space-y-2">
							<div class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Projects</div>
							<button
								v-for="project in projects"
								:key="project.name"
								@click="navigateProject(project.name)"
								:class="[
									'flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-sm transition-all',
									filters?.project === project.name
										? 'bg-amber-400/10 text-amber-500'
										: 'text-muted-foreground hover:bg-secondary/50 hover:text-foreground'
								]"
							>
								<span class="truncate flex-1 text-left">{{ project.name }}</span>
								<span class="text-xs text-muted-foreground/60 shrink-0">{{ project.count }}</span>
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

	<!-- Desktop layout -->
	<div class="hidden h-screen bg-background md:flex">
		<!-- Column 1: Nav sidebar (icon-based) -->
		<div class="flex h-full w-16 flex-col items-center border-r border-border bg-card py-4">
			<!-- Logo -->
			<div class="mb-4">
				<svg width="20" height="15" viewBox="0 0 379 265" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M179.138 265L24.2363 19.7487H0V0H201.618V19.7487H184.407C179.958 19.7487 176.211 21.5858 173.167 25.26C170.357 28.9341 168.952 33.9861 168.952 40.4159C168.952 45.3148 170.123 51.1323 172.464 57.8683C175.04 64.2981 179.138 71.1872 184.758 78.5355L239.553 151.101L278.191 97.3657C282.64 91.2421 285.801 85.2715 287.675 79.4541C289.782 73.3304 290.836 67.3599 290.836 61.5425C290.836 49.9076 286.504 40.1098 277.84 32.149C269.41 23.8821 257.467 19.7487 242.012 19.7487H219.181V0H379V19.7487H359.681L190.378 265H179.138ZM185.812 242.036L196.349 227.34L65.684 19.7487H45.6626L185.812 242.036Z" fill="currentColor"/>
				</svg>
			</div>

			<!-- Page switcher -->
			<div class="flex flex-col items-center gap-1 mb-2">
				<button
					@click="router.get(route('dashboard'))"
					class="flex h-10 w-10 items-center justify-center rounded-lg text-muted-foreground hover:bg-secondary hover:text-foreground transition-all"
				>
					<PhStack class="h-5 w-5 text-foreground" weight="thin" />
				</button>
				<button
					@click="router.get(route('tasks.index'))"
					class="flex h-10 w-10 items-center justify-center rounded-lg text-muted-foreground hover:bg-secondary hover:text-foreground transition-all"
				>
					<PhCheckSquare class="h-5 w-5 text-foreground" weight="thin" />
				</button>
				<button
					class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-500/20 text-amber-600 dark:bg-violet-500/15 dark:text-violet-400 transition-all"
				>
					<PhTimer class="h-5 w-5 text-amber-600 dark:text-violet-400" weight="thin" />
				</button>
			</div>

			<!-- Navigation -->
			<nav class="flex flex-col items-center gap-1">
				<button
					v-for="item in navItems"
					:key="item.label"
					@click="navigate(item)"
					:class="[
						'flex h-10 w-10 items-center justify-center rounded-lg transition-all',
						activeStatus === item.type
							? 'bg-amber-500/20 text-amber-600 dark:bg-violet-500/15 dark:text-violet-400'
							: 'text-muted-foreground hover:bg-secondary hover:text-foreground'
					]"
					:title="item.label + ' (' + item.count + ')'"
				>
					<component :is="item.icon" :class="['h-5 w-5', activeStatus === item.type ? 'text-amber-600 dark:text-violet-400' : 'text-foreground']" weight="thin" />
				</button>
				<button
					@click="showProjectPanel = !showProjectPanel"
					:class="[
						'flex h-10 w-10 items-center justify-center rounded-lg transition-all',
						showProjectPanel || filters?.project
							? 'bg-amber-500/20 text-amber-600 dark:bg-violet-500/15 dark:text-violet-400'
							: 'text-muted-foreground hover:bg-secondary hover:text-foreground'
					]"
					title="Filter by project"
				>
					<PhFunnel :class="['h-5 w-5', showProjectPanel || filters?.project ? 'text-amber-600 dark:text-violet-400' : 'text-foreground']" weight="thin" />
				</button>
			</nav>

			<!-- Spacer -->
			<div class="flex-1" />

			<!-- Add button -->
			<Button variant="ghost" size="icon" class="h-10 w-10" @click="openCreate" title="New Time Entry">
				<PhPlus class="h-5 w-5 text-foreground" weight="thin" />
			</Button>
		</div>

		<!-- Project panel (collapsible) -->
		<div
			v-if="showProjectPanel && projects.length"
			class="h-full w-48 border-r border-border bg-card overflow-hidden animate-in fade-in slide-in-from-left-2 duration-150"
		>
			<div class="flex items-center gap-2 px-4 h-[50px] border-b border-border">
				<PhFunnel class="h-4 w-4 text-muted-foreground" weight="thin" />
				<span class="text-xs font-semibold tracking-tight text-muted-foreground uppercase">Projects</span>
			</div>
			<ScrollArea class="h-[calc(100%-50px)]">
				<div class="py-2 px-2">
					<button
						v-if="filters?.project"
						@click="clearFilters"
						class="flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-sm text-muted-foreground hover:bg-secondary/50 mb-1"
					>
						Clear filter
					</button>
					<button
						v-for="project in projects"
						:key="project.name"
						@click="navigateProject(project.name)"
						:class="[
							'flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-sm transition-all',
							filters?.project === project.name
								? 'bg-amber-400/10 text-amber-500'
								: 'text-muted-foreground hover:bg-secondary/50 hover:text-foreground'
						]"
					>
						<span class="truncate flex-1 text-left">{{ project.name }}</span>
						<span class="text-xs text-muted-foreground/60 shrink-0">{{ project.count }}</span>
					</button>
				</div>
			</ScrollArea>
		</div>

		<!-- Column 2: Time entry list -->
		<TimeEntryList
			:timeEntries="timeEntries"
			:filters="filters"
			:selectedId="currentSelected?.id"
			@select="selectEntry"
		/>

		<!-- Column 3: Detail panel -->
		<div class="flex flex-1 flex-col">
			<!-- Top bar with summary -->
			<div class="flex items-center justify-between border-b border-border pl-6 pr-3 h-[50px]">
				<div class="text-xs text-muted-foreground font-mono">
					{{ props.counts.all }} entries · {{ formatMinutes(totalActual) }} actual · {{ formatMinutes(totalEstimated) }} estimated
				</div>
				<Button variant="ghost" size="icon" class="h-8 w-8" @click="router.post(route('logout'))">
					<PhSignOut class="h-4 w-4 text-foreground" weight="thin" />
				</Button>
			</div>

			<!-- Project summary when no entry selected -->
			<div v-if="!currentSelected && projectSummary.length" class="border-b border-border p-4">
				<h3 class="text-xs font-semibold uppercase tracking-wide text-muted-foreground mb-3">Project Summary</h3>
				<div class="space-y-2">
					<div 
						v-for="p in projectSummary" 
						:key="p.project"
						class="flex items-center justify-between rounded-lg bg-secondary/30 px-3 py-2"
					>
						<div>
							<div class="text-sm font-medium">{{ p.project }}</div>
							<div class="text-xs text-muted-foreground">{{ p.entry_count }} entries</div>
						</div>
						<div class="text-right">
							<div class="text-sm font-medium text-amber-500">{{ formatMinutes(p.total_actual_minutes) }}</div>
							<div class="text-xs text-muted-foreground">{{ formatMinutes(p.total_estimated_minutes) }} est</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Detail -->
			<div class="flex-1 overflow-hidden">
				<TimeEntryDetail
					:timeEntry="currentSelected"
					@edit="openEdit"
					@delete="deleteEntry"
				/>
			</div>
		</div>
	</div>

	<!-- Dialogs -->
	<TimeEntryForm :open="showForm" :timeEntry="editingEntry" :projects="projects" @close="closeForm" />
	<ConfirmDialog
		:open="showConfirm"
		title="Delete time entry"
		:description="'This will permanently delete this time entry for &quot;' + (pendingDelete?.project || '') + '&quot;. This action cannot be undone.'"
		confirmText="Delete"
		variant="destructive"
		@confirm="confirmDelete"
		@cancel="cancelDelete"
	/>
</template>
