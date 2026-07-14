<script setup lang="ts">
import type { Task, TaskStatus } from '~/types/task'

defineProps<{ tasks: Task[] }>()
defineEmits<{ edit: [task: Task], delete: [task: Task], status: [task: Task, status: TaskStatus] }>()

const statusLabels: Record<TaskStatus, string> = {
  pending: 'Pending',
  in_progress: 'In progress',
  completed: 'Completed',
}

const statusItems = Object.entries(statusLabels).map(([value, label]) => ({ value, label }))

function formatDate(value: string | null): string {
  if (!value) return 'No deadline'
  return new Intl.DateTimeFormat('en', { month: 'short', day: 'numeric', year: 'numeric', timeZone: 'UTC' })
    .format(new Date(`${value}T00:00:00Z`))
}
</script>

<template>
  <div class="task-table-wrap">
    <table class="task-table">
      <thead>
        <tr>
          <th scope="col">Task</th>
          <th scope="col">Due</th>
          <th scope="col">Status</th>
          <th scope="col"><span class="sr-only">Actions</span></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="task in tasks" :key="task.id" :data-testid="`task-${task.id}`">
          <td data-label="Task">
            <strong>{{ task.title }}</strong>
            <p v-if="task.description">{{ task.description }}</p>
          </td>
          <td data-label="Due"><span class="task-date">{{ formatDate(task.due_date) }}</span></td>
          <td data-label="Status">
            <USelect
              v-if="task.can.update"
              :model-value="task.status"
              :items="statusItems"
              :aria-label="`Change status for ${task.title}`"
              @update:model-value="$emit('status', task, String($event) as TaskStatus)"
            />
            <UBadge v-else :class="`status-badge status-badge--${task.status}`" variant="subtle">
              {{ statusLabels[task.status] }}
            </UBadge>
          </td>
          <td data-label="Actions">
            <div class="task-actions">
              <UButton v-if="task.can.update" label="Edit" color="neutral" variant="ghost" size="sm" @click="$emit('edit', task)" />
              <UButton v-if="task.can.delete" label="Delete" color="error" variant="ghost" size="sm" @click="$emit('delete', task)" />
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
