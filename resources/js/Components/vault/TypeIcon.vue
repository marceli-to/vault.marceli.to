<script setup>
import { PhLightbulb, PhLink, PhNote } from '@phosphor-icons/vue'

const props = defineProps({
  type: String,
  class: { type: String, default: 'w-4 h-4' },
})

const icons = {
  idea: PhLightbulb,
  link: PhLink,
  note: PhNote,
}
</script>

<template>
  <component :is="icons[type]" :class="[props.class, 'text-white']" weight="thin" />
</template>
