<script setup>
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/Components/ui/dialog'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Textarea } from '@/Components/ui/textarea'
import { PhCircle, PhCircleHalf, PhCheckCircle, PhArrowDown, PhEquals, PhArrowUp } from '@phosphor-icons/vue'

const props = defineProps({
  open: Boolean,
  task: { type: Object, default: null },
})

const emit = defineEmits(['close'])

const statuses = [
  { value: 'open', label: 'Open', icon: PhCircle, color: 'text-zinc-300 bg-zinc-400/10 border-zinc-500/20' },
  { value: 'in_progress', label: 'In Progress', icon: PhCircleHalf, color: 'text-amber-300 bg-amber-400/10 border-amber-500/20' },
  { value: 'done', label: 'Done', icon: PhCheckCircle, color: 'text-emerald-300 bg-emerald-400/10 border-emerald-500/20' },
]

const priorities = [
  { value: 'low', label: 'Low', icon: PhArrowDown, color: 'text-zinc-300 bg-zinc-400/10 border-zinc-500/20' },
  { value: 'normal', label: 'Normal', icon: PhEquals, color: 'text-blue-300 bg-blue-400/10 border-blue-500/20' },
  { value: 'high', label: 'High', icon: PhArrowUp, color: 'text-red-300 bg-red-400/10 border-red-500/20' },
]

const form = useForm({
  title: '',
  description: '',
  status: 'open',
  priority: 'normal',
  due_date: '',
})


watch(() => props.task, (task) => {
  if (task) {
    form.title = task.title || ''
    form.description = task.description || ''
    form.status = task.status || 'open'
    form.priority = task.priority || 'normal'
    form.due_date = task.due_date ? task.due_date.substring(0, 10) : ''
  } else {
    form.reset()
  }
}, { immediate: true })

watch(() => props.open, (val) => {
  if (!val) return
  if (!props.task) form.reset()
})

function submit() {
  const data = {
    ...form.data(),
    due_date: form.due_date || null,
  }

  if (props.task) {
    form.put(route('tasks.update', props.task.id), {
      preserveScroll: true,
      onSuccess: () => emit('close'),
    })
  } else {
    form.post(route('tasks.store'), {
      preserveScroll: true,
      onSuccess: () => { form.reset(); emit('close') },
    })
  }
}
</script>

<template>
  <Dialog :open="open" @update:open="$emit('close')">
    <DialogContent class="sm:max-w-lg">
      <DialogHeader>
        <DialogTitle>{{ task ? 'Edit Task' : 'New Task' }}</DialogTitle>
        <DialogDescription class="text-sm text-muted-foreground">{{ task ? 'Update this task.' : 'Create a new task.' }}</DialogDescription>
      </DialogHeader>

      <form @submit.prevent="submit" class="space-y-4">
        <Input v-model="form.title" placeholder="Task title" />

        <Textarea
          v-model="form.description"
          placeholder="Description (optional)"
          class="min-h-[80px] resize-none"
        />

        <!-- Status selector -->
        <div>
          <label class="text-xs font-medium text-muted-foreground mb-1.5 block">Status</label>
          <div class="flex gap-2">
            <button
              v-for="s in statuses"
              :key="s.value"
              type="button"
              @click="form.status = s.value"
              :class="[
                'flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-medium transition-all',
                form.status === s.value ? s.color : 'border-border text-muted-foreground hover:bg-secondary'
              ]"
            >
              <component :is="s.icon" class="h-3.5 w-3.5" weight="thin" />
              {{ s.label }}
            </button>
          </div>
        </div>

        <!-- Priority selector -->
        <div>
          <label class="text-xs font-medium text-muted-foreground mb-1.5 block">Priority</label>
          <div class="flex gap-2">
            <button
              v-for="p in priorities"
              :key="p.value"
              type="button"
              @click="form.priority = p.value"
              :class="[
                'flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-medium transition-all',
                form.priority === p.value ? p.color : 'border-border text-muted-foreground hover:bg-secondary'
              ]"
            >
              <component :is="p.icon" class="h-3.5 w-3.5" weight="thin" />
              {{ p.label }}
            </button>
          </div>
        </div>

        <!-- Due date -->
        <div>
          <label class="text-xs font-medium text-muted-foreground mb-1.5 block">Due date</label>
          <Input v-model="form.due_date" type="date" />
        </div>

        <DialogFooter>
          <Button type="button" variant="outline" @click="$emit('close')">Cancel</Button>
          <Button type="submit" :disabled="form.processing || !form.title" class="bg-emerald-400 hover:bg-emerald-500">
            {{ task ? 'Update' : 'Create' }}
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
