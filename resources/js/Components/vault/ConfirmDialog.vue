<script setup>
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/Components/ui/alert-dialog'
import { PhWarning } from '@phosphor-icons/vue'

const props = defineProps({
  open: { type: Boolean, default: false },
  title: { type: String, default: 'Are you sure?' },
  description: { type: String, default: 'This action cannot be undone.' },
  confirmText: { type: String, default: 'Delete' },
  cancelText: { type: String, default: 'Cancel' },
  variant: { type: String, default: 'destructive' }, // destructive | default
})

const emit = defineEmits(['confirm', 'cancel'])
</script>

<template>
  <AlertDialog :open="open" @update:open="val => !val && emit('cancel')">
    <AlertDialogContent class="border-zinc-800 bg-zinc-900 sm:max-w-md">
      <AlertDialogHeader>
        <AlertDialogTitle class="text-zinc-100">{{ title }}</AlertDialogTitle>
        <AlertDialogDescription class="mt-1 text-zinc-400">
          {{ description }}
        </AlertDialogDescription>
      </AlertDialogHeader>
      <AlertDialogFooter class="mt-4 gap-2 sm:gap-2">
        <AlertDialogCancel
          class="border-zinc-700 bg-transparent text-zinc-300 hover:bg-zinc-800 hover:text-zinc-100"
          @click="emit('cancel')"
        >
          {{ cancelText }}
        </AlertDialogCancel>
        <AlertDialogAction
          :class="[
            'font-medium',
            variant === 'destructive'
              ? 'bg-red-600 text-white hover:bg-red-700'
              : 'bg-emerald-300 text-white hover:bg-emerald-400'
          ]"
          @click="emit('confirm')"
        >
          {{ confirmText }}
        </AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>
