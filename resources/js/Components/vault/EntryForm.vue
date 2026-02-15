<script setup>
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/Components/ui/dialog'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Textarea } from '@/Components/ui/textarea'
import { PhLightbulb, PhLink, PhNote } from '@phosphor-icons/vue'
import { Checkbox } from '@/Components/ui/checkbox'

const props = defineProps({
  open: Boolean,
  entry: { type: Object, default: null },
})

const emit = defineEmits(['close'])

const types = [
  { value: 'idea', label: 'Idea', icon: PhLightbulb, color: 'text-violet-500 bg-violet-400/10 border-violet-500/20' },
  { value: 'link', label: 'Link', icon: PhLink, color: 'text-violet-500 bg-violet-400/10 border-violet-500/20' },
  { value: 'note', label: 'Note', icon: PhNote, color: 'text-violet-500 bg-violet-400/10 border-violet-500/20' },
]

const form = useForm({
  title: '',
  content: '',
  url: '',
  type: 'note',
  tags: [],
  is_pinned: false,
})

const tagInput = ref('')

watch(() => props.entry, (entry) => {
  if (entry) {
	form.title = entry.title || ''
	form.content = entry.content || ''
	form.url = entry.url || ''
	form.type = entry.type || 'note'
	form.tags = entry.tags ? [...entry.tags] : []
	form.is_pinned = entry.is_pinned || false
  } else {
	form.reset()
  }
}, { immediate: true })

watch(() => props.open, (val) => {
  if (!val) return
  if (!props.entry) form.reset()
})

function addTag() {
  const tag = tagInput.value.trim().toLowerCase()
  if (tag && !form.tags.includes(tag)) {
	form.tags.push(tag)
  }
  tagInput.value = ''
}

function removeTag(tag) {
  form.tags = form.tags.filter(t => t !== tag)
}

function submit() {
  if (props.entry) {
	form.put(route('entries.update', props.entry.id), {
	  preserveScroll: true,
	  onSuccess: () => emit('close'),
	})
  } else {
	form.post(route('entries.store'), {
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
		<DialogTitle>{{ entry ? 'Edit Entry' : 'New Entry' }}</DialogTitle>
		<DialogDescription class="text-sm text-muted-foreground">{{ entry ? 'Update the details of this entry.' : 'Add a new entry to your vault.' }}</DialogDescription>
	  </DialogHeader>

	  <form @submit.prevent="submit" class="space-y-4">
		<!-- Type selector -->
		<div class="flex gap-2">
		  <button
			v-for="t in types"
			:key="t.value"
			type="button"
			@click="form.type = t.value"
			:class="[
			  'flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-medium transition-all',
			  form.type === t.value ? t.color : 'border-border text-muted-foreground hover:bg-secondary'
			]"
		  >
			<component :is="t.icon" class="h-3.5 w-3.5" weight="thin" />
			{{ t.label }}
		  </button>
		</div>

		<Input v-model="form.title" placeholder="Title (optional)" />

		<Textarea
		  v-model="form.content"
		  placeholder="What's on your mind?"
		  class="min-h-[120px] resize-none"
		/>

		<Input v-model="form.url" placeholder="URL (optional)" />

		<!-- Tags -->
		<div>
		  <div class="flex flex-wrap gap-1.5 mb-2" v-if="form.tags.length">
			<span
			  v-for="tag in form.tags"
			  :key="tag"
			  class="inline-flex items-center gap-1 rounded-full bg-violet-400/10 px-2 py-0.5 text-xs text-violet-500"
			>
			  {{ tag }}
			  <button type="button" @click="removeTag(tag)" class="hover:text-violet-200">&times;</button>
			</span>
		  </div>
		  <Input
			v-model="tagInput"
			placeholder="Add tag and press Enter"
			@keydown.enter.prevent="addTag"
		  />
		</div>

		<label class="flex items-center gap-2 text-sm cursor-pointer">
		  <Checkbox :checked="form.is_pinned" @update:checked="form.is_pinned = $event" class="border-zinc-600 data-[state=checked]:bg-violet-400 data-[state=checked]:border-violet-600" />
		  Pin this entry
		</label>

		<DialogFooter>
		  <Button type="button" variant="outline" @click="$emit('close')">Cancel</Button>
		  <Button type="submit" :disabled="form.processing || !form.content" class="bg-violet-400 hover:bg-violet-500">
			{{ entry ? 'Update' : 'Create' }}
		  </Button>
		</DialogFooter>
	  </form>
	</DialogContent>
  </Dialog>
</template>
