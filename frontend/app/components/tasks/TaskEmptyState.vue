<script setup lang="ts">
defineProps<{ filtered: boolean }>()
defineEmits<{ create: [] }>()
</script>

<template>
  <section class="empty-state" aria-labelledby="empty-title">
    <div class="empty-state__mark" aria-hidden="true">✓</div>
    <h2 id="empty-title">{{ filtered ? 'No tasks match these filters' : 'Your list is clear' }}</h2>
    <p>{{ filtered ? 'Adjust the search or status to see more work.' : 'Create a task to give the day a clear next step.' }}</p>
    <UButton v-if="!filtered" label="Create first task" size="lg" @click="$emit('create')" />
  </section>
</template>
