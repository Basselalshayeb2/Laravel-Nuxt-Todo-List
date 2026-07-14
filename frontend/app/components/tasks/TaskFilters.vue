<script setup lang="ts">
import type { TaskFilters } from '~/types/task'

const props = defineProps<{ filters: TaskFilters }>()
const emit = defineEmits<{ change: [patch: Partial<TaskFilters>] }>()
const search = ref(props.filters.search)
let debounceTimer: ReturnType<typeof setTimeout> | undefined

watch(() => props.filters.search, value => { search.value = value })
watch(search, (value) => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => emit('change', { search: value.trim() }), 350)
})
onBeforeUnmount(() => clearTimeout(debounceTimer))

const statusItems = [
  { label: 'All statuses', value: 'all' },
  { label: 'Pending', value: 'pending' },
  { label: 'In progress', value: 'in_progress' },
  { label: 'Completed', value: 'completed' },
]
const sortItems = [
  { label: 'Recently created', value: 'created_at' },
  { label: 'Due date', value: 'due_date' },
  { label: 'Status', value: 'status' },
  { label: 'Title', value: 'title' },
  { label: 'Recently updated', value: 'updated_at' },
]
</script>

<template>
  <section class="task-filters" aria-label="Task filters">
    <UFormField label="Search tasks" class="task-filters__search">
      <UInput v-model="search" placeholder="Search title or description" aria-label="Search tasks" />
    </UFormField>
    <UFormField label="Status">
      <USelect
        :model-value="filters.status || 'all'"
        :items="statusItems"
        aria-label="Filter by status"
        @update:model-value="emit('change', { status: ($event === 'all' ? '' : String($event)) as TaskFilters['status'] })"
      />
    </UFormField>
    <UFormField label="Sort by">
      <USelect
        :model-value="filters.sort"
        :items="sortItems"
        aria-label="Sort tasks"
        @update:model-value="emit('change', { sort: String($event) as TaskFilters['sort'] })"
      />
    </UFormField>
    <UFormField label="Direction">
      <UButton
        color="neutral"
        variant="outline"
        block
        :label="filters.direction === 'asc' ? 'Ascending' : 'Descending'"
        @click="emit('change', { direction: filters.direction === 'asc' ? 'desc' : 'asc' })"
      />
    </UFormField>
  </section>
</template>
