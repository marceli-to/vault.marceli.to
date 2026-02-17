<script setup>
import { ref, watch, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/Components/ui/dialog'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Textarea } from '@/Components/ui/textarea'
import { Checkbox } from '@/Components/ui/checkbox'
import { PhCircle, PhCheckCircle } from '@phosphor-icons/vue'

const props = defineProps({
	open: Boolean,
	timeEntry: { type: Object, default: null },
	projects: { type: Array, default: () => [] },
})

const emit = defineEmits(['close'])

const statuses = [
	{ value: 'open', label: 'Open', icon: PhCircle, color: 'text-amber-300 bg-amber-400/10 border-amber-500/20' },
	{ value: 'processed', label: 'Processed', icon: PhCheckCircle, color: 'text-green-500 bg-green-400/10 border-green-500/20' },
]

const form = useForm({
	project: '',
	description: '',
	date: '',
	actual_minutes: '',
	estimated_minutes: '',
	billable: true,
	status: 'open',
	tags: [],
})

const uniqueProjects = computed(() => {
	return props.projects.map(p => p.name)
})

watch(() => props.timeEntry, (entry) => {
	if (entry) {
		form.project = entry.project || ''
		form.description = entry.description || ''
		form.date = entry.date ? entry.date.substring(0, 10) : ''
		form.actual_minutes = entry.actual_minutes ?? ''
		form.estimated_minutes = entry.estimated_minutes ?? ''
		form.billable = entry.billable ?? true
		form.status = entry.status || 'open'
		form.tags = entry.tags || []
	} else {
		form.reset()
		form.billable = true
		form.status = 'open'
		form.date = new Date().toISOString().substring(0, 10)
	}
}, { immediate: true })

watch(() => props.open, (val) => {
	if (!val) return
	if (!props.timeEntry) {
		form.reset()
		form.billable = true
		form.status = 'open'
		form.date = new Date().toISOString().substring(0, 10)
	}
})

function submit() {
	const data = {
		...form.data(),
		actual_minutes: form.actual_minutes ? parseInt(form.actual_minutes) : null,
		estimated_minutes: form.estimated_minutes ? parseInt(form.estimated_minutes) : null,
	}

	if (props.timeEntry) {
		form.put(route('time-entries.update', props.timeEntry.id), {
			preserveScroll: true,
			onSuccess: () => emit('close'),
		})
	} else {
		form.post(route('time-entries.store'), {
			preserveScroll: true,
			onSuccess: () => { form.reset(); form.billable = true; form.status = 'open'; emit('close') },
		})
	}
}
</script>

<template>
	<Dialog :open="open" @update:open="$emit('close')">
		<DialogContent class="w-[calc(100vw-1.5rem)] sm:w-full sm:max-w-lg">
			<DialogHeader>
				<DialogTitle>{{ timeEntry ? 'Edit Time Entry' : 'New Time Entry' }}</DialogTitle>
				<DialogDescription class="text-sm text-muted-foreground">{{ timeEntry ? 'Update this time entry.' : 'Log your time.' }}</DialogDescription>
			</DialogHeader>

			<form @submit.prevent="submit" class="space-y-4">
				<!-- Project with datalist for autocomplete -->
				<div>
					<label class="text-xs font-medium text-muted-foreground mb-1.5 block">Project</label>
					<Input 
						v-model="form.project" 
						placeholder="e.g. bambole.ch" 
						list="project-list"
					/>
					<datalist id="project-list">
						<option v-for="project in uniqueProjects" :key="project" :value="project" />
					</datalist>
				</div>

				<div>
					<label class="text-xs font-medium text-muted-foreground mb-1.5 block">Description</label>
					<Textarea
						v-model="form.description"
						placeholder="What did you work on?"
						class="min-h-[80px] resize-none"
					/>
				</div>

				<!-- Date -->
				<div>
					<label class="text-xs font-medium text-muted-foreground mb-1.5 block">Date</label>
					<Input v-model="form.date" type="date" />
				</div>

				<!-- Time inputs -->
				<div class="grid grid-cols-2 gap-4">
					<div>
						<label class="text-xs font-medium text-muted-foreground mb-1.5 block">Actual Minutes</label>
						<Input 
							v-model="form.actual_minutes" 
							type="number" 
							min="0" 
							placeholder="e.g. 30"
						/>
					</div>
					<div>
						<label class="text-xs font-medium text-muted-foreground mb-1.5 block">Estimated Minutes</label>
						<Input 
							v-model="form.estimated_minutes" 
							type="number" 
							min="0"
							placeholder="e.g. 120"
						/>
					</div>
				</div>

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

				<!-- Billable checkbox -->
				<div class="flex items-center gap-2">
					<Checkbox 
						id="billable" 
						:checked="form.billable"
						@update:checked="form.billable = $event"
					/>
					<label for="billable" class="text-sm text-muted-foreground cursor-pointer">
						Billable
					</label>
				</div>

				<DialogFooter class="pt-1 gap-3 sm:gap-3">
					<Button type="button" variant="outline" @click="$emit('close')">Cancel</Button>
					<Button type="submit" :disabled="form.processing || !form.project || !form.description || !form.date" class="bg-amber-600 hover:bg-amber-700">
						{{ timeEntry ? 'Update' : 'Create' }}
					</Button>
				</DialogFooter>
			</form>
		</DialogContent>
	</Dialog>
</template>
