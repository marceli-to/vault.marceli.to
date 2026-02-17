<script setup>
import { computed } from 'vue'
import { ScrollArea } from '@/Components/ui/scroll-area'
import { PhCircle, PhCheckCircle, PhCurrencyDollar, PhCurrencyDollarSimple } from '@phosphor-icons/vue'

const props = defineProps({
	timeEntries: Array,
	selectedId: [Number, null],
	filters: Object,
})

const emit = defineEmits(['select'])

const statusIcons = {
	open: PhCircle,
	processed: PhCheckCircle,
}

const statusColors = {
	open: 'text-amber-400',
	processed: 'text-green-700 dark:text-green-500',
}

const title = computed(() => {
	if (props.filters?.project) return props.filters.project
	if (props.filters?.status === 'open') return 'Open'
	if (props.filters?.status === 'processed') return 'Processed'
	if (props.filters?.billable === true || props.filters?.billable === '1') return 'Billable'
	return 'All Time Entries'
})

function formatDate(date) {
	return new Date(date).toLocaleDateString('en-US', {
		weekday: 'short',
		month: 'short',
		day: 'numeric',
	})
}

function formatMinutes(minutes) {
	if (!minutes) return '—'
	const h = Math.floor(minutes / 60)
	const m = minutes % 60
	if (h === 0) return `${m}m`
	if (m === 0) return `${h}h`
	return `${h}h ${m}m`
}
</script>

<template>
	<div class="flex h-full w-full flex-col border-b border-border bg-background md:w-[25rem] md:border-b-0 md:border-r">
		<!-- Header -->
		<div class="flex items-center justify-between px-4 border-b border-border h-[50px]">
			<h2 class="text-sm font-semibold tracking-tight">{{ title }}</h2>
			<span class="text-xs text-muted-foreground">{{ timeEntries.length }}</span>
		</div>

		<!-- Time entry list -->
		<ScrollArea class="flex-1">
			<div class="px-3 py-2">
				<div class="space-y-1">
					<button
						v-for="entry in timeEntries"
						:key="entry.id"
						@click="emit('select', entry)"
						:class="[
							'flex w-full items-start gap-3 rounded-lg px-3 py-2.5 text-left transition-all',
							selectedId === entry.id
								? 'bg-amber-500/20 ring-1 ring-amber-500/30 dark:bg-violet-500/15 dark:ring-violet-400/25'
								: 'hover:bg-secondary/50'
						]"
					>
						<!-- Status icon -->
						<component
							:is="statusIcons[entry.status]"
							:class="['h-4 w-4 shrink-0 mt-0.5', statusColors[entry.status]]"
							weight="thin"
						/>

						<div class="flex-1 min-w-0">
							<div class="flex items-center gap-2">
								<span class="truncate text-sm font-medium">
									{{ entry.project }}
								</span>
								<PhCurrencyDollarSimple 
									v-if="entry.billable" 
									class="h-3 w-3 text-green-500 shrink-0" 
									weight="fill" 
								/>
							</div>
							<div class="flex items-center gap-2 mt-0.5">
								<span class="truncate text-xs text-muted-foreground flex-1">
									{{ entry.description.substring(0, 60) }}{{ entry.description.length > 60 ? '…' : '' }}
								</span>
							</div>
							<div class="flex items-center gap-3 mt-1 text-[10px] text-muted-foreground/60">
								<span>{{ formatDate(entry.date) }}</span>
								<span v-if="entry.actual_minutes" class="text-amber-500">{{ formatMinutes(entry.actual_minutes) }} actual</span>
								<span v-if="entry.estimated_minutes" class="text-blue-400">{{ formatMinutes(entry.estimated_minutes) }} est</span>
							</div>
						</div>
					</button>
				</div>

				<div v-if="timeEntries.length === 0" class="px-3 py-8 text-center text-sm text-muted-foreground">
					No time entries found
				</div>
			</div>
		</ScrollArea>
	</div>
</template>
