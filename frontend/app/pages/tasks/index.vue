<script setup lang="ts">
import type { Task, TaskInput, TaskStatus } from '~/types/task'

definePageMeta({ middleware: 'auth' })
useSeoMeta({ title: 'My tasks — TaskFlow', robots: 'noindex, nofollow' })

const { user } = useAuth()
const { data, status, error, filters, updateFilters, createTask, updateTask, deleteTask } = useTasks()
const formOpen = ref(false)
const editingTask = ref<Task | null>(null)
const deletingTask = ref<Task | null>(null)
const formBusy = ref(false)
const deleteBusy = ref(false)
const formRef = ref<{ setApiError: (error: unknown) => void } | null>(null)
const toast = useToast()

function openCreate(): void {
  editingTask.value = null
  formOpen.value = true
}

function openEdit(task: Task): void {
  editingTask.value = task
  formOpen.value = true
}

async function saveTask(input: TaskInput): Promise<void> {
  formBusy.value = true
  try {
    if (editingTask.value) await updateTask(editingTask.value, input)
    else await createTask(input)
    toast.add({ title: editingTask.value ? 'Changes saved' : 'Task created', color: 'success' })
    formOpen.value = false
  } catch (error: unknown) {
    formRef.value?.setApiError(error)
  } finally {
    formBusy.value = false
  }
}

async function changeStatus(task: Task, taskStatus: TaskStatus): Promise<void> {
  try {
    await updateTask(task, { status: taskStatus })
    toast.add({ title: 'Status updated', color: 'success' })
  } catch {
    toast.add({ title: 'Status could not be updated', color: 'error' })
  }
}

async function confirmDelete(): Promise<void> {
  if (!deletingTask.value) return
  deleteBusy.value = true
  try {
    await deleteTask(deletingTask.value)
    toast.add({ title: 'Task deleted', color: 'success' })
    deletingTask.value = null
  } finally {
    deleteBusy.value = false
  }
}
</script>

<template>
  <div class="tasks-page">
    <header class="tasks-heading">
      <div>
        <p class="eyebrow">{{ user?.role === 'admin' ? 'All team tasks' : 'Your working list' }}</p>
        <h1>{{ user?.role === 'admin' ? 'Keep the whole board moving.' : 'What moves today?' }}</h1>
        <p>{{ data?.meta.total ?? 0 }} {{ data?.meta.total === 1 ? 'task' : 'tasks' }} in this view</p>
      </div>
      <UButton label="Create task" size="lg" @click="openCreate" />
    </header>

    <TaskFilters :filters="filters" @change="updateFilters($event)" />

    <UAlert v-if="error" color="error" variant="subtle" title="Tasks could not be loaded" description="Refresh the page or try again in a moment." />
    <TaskSkeleton v-else-if="status === 'pending'" />
    <TaskEmptyState
      v-else-if="!data?.data.length"
      :filtered="Boolean(filters.status || filters.search)"
      @create="openCreate"
    />
    <template v-else>
      <TaskTable :tasks="data.data" @edit="openEdit" @delete="deletingTask = $event" @status="changeStatus" />
      <TaskPagination :meta="data.meta" @page="updateFilters({ page: $event }, false)" />
    </template>

    <TaskForm ref="formRef" :open="formOpen" :task="editingTask" :busy="formBusy" @close="formOpen = false" @save="saveTask" />
    <TaskDeleteModal :task="deletingTask" :busy="deleteBusy" @close="deletingTask = null" @confirm="confirmDelete" />
  </div>
</template>
