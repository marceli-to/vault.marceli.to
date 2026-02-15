<script setup>
import { router } from '@inertiajs/vue3'
import { PhPencilSimple, PhTrash, PhCircle, PhCircleHalf, PhCheckCircle, PhClock, PhCalendar } from '@phosphor-icons/vue'
import { Button } from '@/Components/ui/button'
const props = defineProps({ task: Object })
const emit = defineEmits(['edit', 'delete', 'toggleStatus'])

const statusLabels = { open: 'Open', in_progress: 'In Progress', done: 'Done' }
const statusColors = {
  open: 'text-zinc-400 bg-zinc-400/10',
  in_progress: 'text-amber-400 bg-amber-400/10',
  done: 'text-emerald-400 bg-emerald-400/10',
}
const priorityLabels = { low: 'Low', normal: 'Normal', high: 'High' }
const priorityColors = {
  low: 'text-zinc-400 bg-zinc-400/10',
  normal: 'text-blue-400 bg-blue-400/10',
  high: 'text-red-400 bg-red-400/10',
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('en-US', {
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
    hour: '2-digit', minute: '2-digit',
  })
}

function formatDueDate(date) {
  if (!date) return null
  return new Date(date).toLocaleDateString('en-US', {
    weekday: 'short', month: 'long', day: 'numeric', year: 'numeric',
  })
}

function cycleStatus() {
  const order = ['open', 'in_progress', 'done']
  const idx = order.indexOf(props.task.status)
  const next = order[(idx + 1) % order.length]
  router.put(route('tasks.update', props.task.id), {
    ...props.task,
    status: next,
  }, { preserveScroll: true })
}

</script>

<template>
  <div v-if="task" class="flex h-full flex-col">
    <!-- Header -->
    <div class="flex items-start justify-between border-b border-border p-6 pb-4">
      <div class="flex-1 space-y-2">
        <div class="flex items-center gap-2">
          <button @click="cycleStatus" class="transition-transform hover:scale-110">
            <span :class="['rounded-full px-2 py-0.5 text-xs font-medium', statusColors[task.status]]">
              {{ statusLabels[task.status] }}
            </span>
          </button>
          <span :class="['rounded-full px-2 py-0.5 text-xs font-medium', priorityColors[task.priority]]">
            {{ priorityLabels[task.priority] }}
          </span>
        </div>
        <h1 :class="['text-xl font-semibold tracking-tight', task.status === 'done' ? 'line-through text-muted-foreground' : '']">
          {{ task.title }}
        </h1>
      </div>
      <div class="flex items-center gap-1">
        <Button variant="ghost" size="icon" class="h-8 w-8" @click="emit('edit', task)">
          <PhPencilSimple class="h-4 w-4 text-white" weight="thin" />
        </Button>
        <Button variant="ghost" size="icon" class="h-8 w-8 text-red-400 hover:text-red-300" @click="emit('delete', task)">
          <PhTrash class="h-4 w-4" weight="thin" />
        </Button>
      </div>
    </div>

    <!-- Content -->
    <div class="flex-1 overflow-auto p-6">
      <div v-if="task.description" class="prose prose-invert max-w-none whitespace-pre-wrap text-sm leading-relaxed text-foreground/90">
        {{ task.description }}
      </div>
      <div v-else class="text-sm text-muted-foreground/50 italic">
        No description
      </div>

      <div v-if="task.due_date" class="mt-4 inline-flex items-center gap-2 rounded-lg border border-border bg-secondary/30 px-3 py-2 text-sm text-muted-foreground">
        <PhCalendar class="h-4 w-4 text-white" weight="thin" />
        Due: {{ formatDueDate(task.due_date) }}
      </div>
    </div>

    <!-- Footer -->
    <div class="border-t border-border p-4">
      <div class="flex items-center justify-end">
        <div class="flex items-center gap-3 text-xs text-muted-foreground">
          <span class="flex items-center gap-1">
            <PhClock class="h-3 w-3 text-white" weight="thin" />
            {{ formatDate(task.created_at) }}
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Empty state -->
  <div v-else class="flex h-full flex-col items-center justify-center text-muted-foreground">
    <p class="text-sm">Select a task to view details</p>
  </div>
</template>
