<script setup>
import { router } from '@inertiajs/vue3'
import { PhPencilSimple, PhTrash, PhCircle, PhCheckCircle, PhClock, PhCalendar, PhCurrencyDollarSimple } from '@phosphor-icons/vue'
import { Button } from '@/Components/ui/button'

const props = defineProps({ timeEntry: Object })
const emit = defineEmits(['edit', 'delete'])

const statusLabels = { open: 'Open', processed: 'Processed' }
const statusColors = {
	open: 'text-amber-400 bg-amber-400/10',
	processed: 'text-green-700 dark:text-green-500 bg-green-400/10',
}

function formatDate(date) {
	return new Date(date).toLocaleDateString('en-US', {
		weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
	})
}

function formatDateTime(date) {
	return new Date(date).toLocaleDateString('en-US', {
		weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
		hour: '2-digit', minute: '2-digit',
	})
}

function formatMinutes(minutes) {
	if (!minutes) return '—'
	const h = Math.floor(minutes / 60)
	const m = minutes % 60
	if (h === 0) return `${m} minutes`
	if (m === 0) return `${h} hour${h > 1 ? 's' : ''}`
	return `${h}h ${m}m`
}

function cycleStatus() {
	const next = props.timeEntry.status === 'open' ? 'processed' : 'open'
	router.put(route('time-entries.update', props.timeEntry.id), {
		...props.timeEntry,
		status: next,
	}, { preserveScroll: true })
}
</script>

<template>
	<div v-if="timeEntry" class="flex h-full flex-col">
		<!-- Header -->
		<div class="flex flex-col gap-3 border-b border-border p-4 md:flex-row md:items-start md:justify-between md:gap-4 md:p-6 md:pb-4">
			<div class="flex-1 space-y-2">
				<div class="flex flex-wrap items-center gap-2">
					<button @click="cycleStatus" class="transition-transform hover:scale-110">
						<span :class="['rounded-full px-2 py-0.5 text-xs font-medium', statusColors[timeEntry.status]]">
							{{ statusLabels[timeEntry.status] }}
						</span>
					</button>
					<span v-if="timeEntry.billable" class="flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium text-green-500 bg-green-400/10">
						<PhCurrencyDollarSimple class="h-3 w-3" weight="fill" />
						Billable
					</span>
					<span v-else class="rounded-full px-2 py-0.5 text-xs font-medium text-muted-foreground bg-secondary/50">
						Non-billable
					</span>
				</div>
				<h1 class="text-lg font-semibold tracking-tight md:text-xl">
					{{ timeEntry.project }}
				</h1>
				<div class="flex items-center gap-1 text-sm text-muted-foreground">
					<PhCalendar class="h-4 w-4" weight="thin" />
					{{ formatDate(timeEntry.date) }}
				</div>
			</div>
			<div class="flex items-center gap-1 self-end md:self-auto">
				<Button variant="ghost" size="icon" class="h-8 w-8" @click="emit('edit', timeEntry)">
					<PhPencilSimple class="h-4 w-4 text-foreground" weight="thin" />
				</Button>
				<Button variant="ghost" size="icon" class="h-8 w-8 text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300" @click="emit('delete', timeEntry)">
					<PhTrash class="h-4 w-4" weight="thin" />
				</Button>
			</div>
		</div>

		<!-- Content -->
		<div class="flex-1 overflow-auto p-4 md:p-6">
			<!-- Time summary -->
			<div class="grid grid-cols-2 gap-4 mb-6">
				<div class="rounded-lg border border-border p-4">
					<div class="text-xs text-muted-foreground mb-1">Actual Time</div>
					<div class="text-xl font-semibold text-amber-500">{{ formatMinutes(timeEntry.actual_minutes) }}</div>
				</div>
				<div class="rounded-lg border border-border p-4">
					<div class="text-xs text-muted-foreground mb-1">Estimated Time</div>
					<div class="text-xl font-semibold text-blue-400">{{ formatMinutes(timeEntry.estimated_minutes) }}</div>
				</div>
			</div>

			<!-- Description -->
			<div class="prose prose-invert max-w-none whitespace-pre-wrap text-sm leading-relaxed text-foreground/90">
				{{ timeEntry.description }}
			</div>

			<!-- Tags -->
			<div v-if="timeEntry.tags && timeEntry.tags.length" class="mt-4 flex flex-wrap gap-1.5">
				<span 
					v-for="tag in timeEntry.tags" 
					:key="tag"
					class="rounded-full bg-secondary/50 px-2 py-0.5 text-xs text-muted-foreground"
				>
					{{ tag }}
				</span>
			</div>
		</div>

		<!-- Footer -->
		<div class="border-t border-border p-4">
			<div class="flex items-center justify-start md:justify-end">
				<div class="flex items-center gap-3 text-xs text-muted-foreground">
					<span class="flex items-center gap-1">
						<PhClock class="h-3 w-3 text-foreground" weight="thin" />
						Created {{ formatDateTime(timeEntry.created_at) }}
					</span>
				</div>
			</div>
		</div>
	</div>

	<!-- Empty state -->
	<div v-else class="flex h-full flex-col items-center justify-center text-muted-foreground">
		<p class="text-sm">Select a time entry to view details</p>
	</div>
</template>
