<script setup>
const props = defineProps({
  tag: String,
  clickable: { type: Boolean, default: false },
})

const emit = defineEmits(['click'])

const colors = [
  { bg: 'bg-emerald-400/10', text: 'text-emerald-300', ring: 'ring-emerald-500/20', dot: 'bg-emerald-400' },
  { bg: 'bg-cyan-400/10', text: 'text-cyan-300', ring: 'ring-cyan-500/20', dot: 'bg-cyan-400' },
  { bg: 'bg-violet-400/10', text: 'text-violet-300', ring: 'ring-violet-500/20', dot: 'bg-violet-400' },
  { bg: 'bg-amber-400/10', text: 'text-amber-300', ring: 'ring-amber-500/20', dot: 'bg-amber-400' },
  { bg: 'bg-rose-400/10', text: 'text-rose-300', ring: 'ring-rose-500/20', dot: 'bg-rose-400' },
  { bg: 'bg-sky-400/10', text: 'text-sky-300', ring: 'ring-sky-500/20', dot: 'bg-sky-400' },
  { bg: 'bg-orange-400/10', text: 'text-orange-300', ring: 'ring-orange-500/20', dot: 'bg-orange-400' },
  { bg: 'bg-fuchsia-400/10', text: 'text-fuchsia-300', ring: 'ring-fuchsia-500/20', dot: 'bg-fuchsia-400' },
  { bg: 'bg-lime-400/10', text: 'text-lime-300', ring: 'ring-lime-500/20', dot: 'bg-lime-400' },
  { bg: 'bg-indigo-400/10', text: 'text-indigo-300', ring: 'ring-indigo-500/20', dot: 'bg-indigo-400' },
]

function hashTag(tag) {
  let hash = 0
  for (let i = 0; i < tag.length; i++) {
    hash = ((hash << 5) - hash) + tag.charCodeAt(i)
    hash |= 0
  }
  return Math.abs(hash) % colors.length
}

function getColor(tag) {
  return colors[hashTag(tag)]
}
</script>

<template>
  <span
    :class="[
      'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset transition-colors',
      getColor(tag).bg,
      getColor(tag).text,
      getColor(tag).ring,
      clickable ? 'cursor-pointer hover:brightness-125' : '',
    ]"
    @click="clickable && emit('click', tag)"
  >
    {{ tag }}
  </span>
</template>
