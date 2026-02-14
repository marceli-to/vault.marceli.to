<script setup>
defineProps({ tag: String })
</script>

<template>
  <span class="inline-flex items-center rounded-full bg-emerald-400/10 px-2 py-0.5 text-xs font-medium text-emerald-300 ring-1 ring-inset ring-emerald-500/20 transition-colors hover:bg-emerald-500/20">
    {{ tag }}
  </span>
</template>
