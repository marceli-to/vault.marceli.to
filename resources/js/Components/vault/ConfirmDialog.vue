<script setup>
import { Button } from '@/Components/ui/button'

const props = defineProps({
	open: { type: Boolean, default: false },
	title: { type: String, default: 'Are you sure?' },
	description: { type: String, default: 'This action cannot be undone.' },
	confirmText: { type: String, default: 'Delete' },
	cancelText: { type: String, default: 'Cancel' },
	variant: { type: String, default: 'destructive' },
})

const emit = defineEmits(['confirm', 'cancel'])
</script>

<template>
	<Teleport to="body">
		<div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center">
			<!-- Overlay -->
			<div class="fixed inset-0 bg-black/80" @click="emit('cancel')" />

			<!-- Dialog -->
			<div class="relative z-50 w-full max-w-md rounded-lg border border-border bg-card p-6 shadow-lg animate-in fade-in zoom-in-95 duration-200">
				<h2 class="text-lg font-semibold text-foreground">{{ title }}</h2>
				<p class="mt-1 text-sm text-muted-foreground">{{ description }}</p>

				<div class="mt-4 flex justify-end gap-2">
					<Button
						variant="outline"
						class="border-border bg-transparent text-foreground hover:bg-secondary hover:text-foreground"
						@click="emit('cancel')"
					>
						{{ cancelText }}
					</Button>
					<Button
						:class="[
							'font-medium',
							variant === 'destructive'
								? 'bg-red-600 text-white hover:bg-red-700'
								: 'bg-violet-400 text-white hover:bg-violet-500'
						]"
						@click="emit('confirm')"
					>
						{{ confirmText }}
					</Button>
				</div>
			</div>
		</div>
	</Teleport>
</template>
