<script setup>
import { computed } from 'vue'
import { ScrollArea } from '@/Components/ui/scroll-area'
import { PhCircle, PhCircleHalf, PhCheckCircle, PhWarningCircle } from '@phosphor-icons/vue'

const props = defineProps({
	tasks: Array,
	selectedId: [Number, null],
	filters: Object,
})

const emit = defineEmits(['select'])

const statusIcons = {
	open: PhCircle,
	in_progress: PhCircleHalf,
	done: PhCheckCircle,
}

const statusColors = {
	open: 'text-zinc-400',
	in_progress: 'text-amber-400',
	done: 'text-amber-500',
}

const priorityIndicator = {
	high: 'bg-red-400',
	normal: 'bg-zinc-500',
	low: 'bg-zinc-700',
}

const title = computed(() => {
	if (props.filters?.tag) return `Tagged: ${props.filters.tag}`
	if (props.filters?.status === 'open') return 'Open'
	if (props.filters?.status === 'in_progress') return 'In Progress'
	if (props.filters?.status === 'done') return 'Done'
	if (props.filters?.priority) return `${props.filters.priority.charAt(0).toUpperCase() + props.filters.priority.slice(1)} Priority`
	return 'All Tasks'
})

const tasksWithDue = computed(() => {
	return props.tasks.map(task => ({
		task,
		due: formatDue(task.due_date),
	}))
})

function timeAgo(date) {
	const now = new Date()
	const d = new Date(date)
	const diff = Math.floor((now - d) / 60000)
	if (diff < 1) return 'just now'
	if (diff < 60) return `${diff}m ago`
	if (diff < 1440) return `${Math.floor(diff / 60)}h ago`
	return `${Math.floor(diff / 1440)}d ago`
}

function formatDue(date) {
	if (!date) return null
	const d = new Date(date)
	const now = new Date()
	const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
	const due = new Date(d.getFullYear(), d.getMonth(), d.getDate())
	const diff = Math.floor((due - today) / 86400000)
	if (diff < 0) return { text: `${Math.abs(diff)}d overdue`, class: 'text-red-400' }
	if (diff === 0) return { text: 'Today', class: 'text-amber-400' }
	if (diff === 1) return { text: 'Tomorrow', class: 'text-amber-300' }
	if (diff <= 7) return { text: `${diff}d`, class: 'text-zinc-400' }
	return { text: d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }), class: 'text-zinc-500' }
}
</script>

<template>
	<div class="flex h-full w-[25rem] flex-col border-r border-border bg-background">
		<!-- Header -->
		<div class="flex items-center justify-between px-4 border-b border-border h-[50px]">
			<h2 class="text-sm font-semibold tracking-tight">{{ title }}</h2>
			<span class="text-xs text-muted-foreground">{{ tasks.length }}</span>
		</div>

		<!-- Task list -->
		<ScrollArea class="flex-1">
			<div class="px-3 py-2">
				<div class="space-y-1">
					<button
						v-for="item in tasksWithDue"
						:key="item.task.id"
						@click="emit('select', item.task)"
						:class="[
							'flex w-full items-start gap-3 rounded-lg px-3 py-2.5 text-left transition-all',
							selectedId === item.task.id
								? 'bg-amber-500/20 ring-1 ring-amber-500/30 dark:bg-violet-500/15 dark:ring-violet-400/25'
								: 'hover:bg-secondary/50'
						]"
					>
						<!-- Status icon -->
						<component
							:is="statusIcons[item.task.status]"
							:class="['h-4 w-4 shrink-0 mt-0.5', statusColors[item.task.status]]"
							weight="thin"
						/>

						<div class="flex-1 min-w-0">
							<div class="flex items-center gap-2">
								<span :class="['h-1.5 w-1.5 rounded-full shrink-0', priorityIndicator[item.task.priority]]" />
								<span :class="['truncate text-sm font-medium', item.task.status === 'done' ? 'line-through text-muted-foreground' : '']">
									{{ item.task.title }}
								</span>
							</div>
							<div class="flex items-center gap-2 mt-0.5 pl-3.5">
								<span v-if="item.task.description" class="truncate text-xs text-muted-foreground">
									{{ item.task.description.substring(0, 50) }}{{ item.task.description.length > 50 ? '…' : '' }}
								</span>
								<span class="ml-auto shrink-0 text-[10px] text-muted-foreground/50" v-if="!item.due">
									{{ timeAgo(item.task.created_at) }}
								</span>
								<span v-if="item.due" :class="['ml-auto shrink-0 text-[10px]', item.due.class]">
									{{ item.due.text }}
								</span>
							</div>
						</div>
					</button>
				</div>

				<div v-if="tasks.length === 0" class="px-3 py-8 text-center text-sm text-muted-foreground">
					No tasks found
				</div>
			</div>
		</ScrollArea>
	</div>
</template>
