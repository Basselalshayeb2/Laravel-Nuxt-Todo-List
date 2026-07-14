<script setup lang="ts">
import type { Task, TaskInput, TaskStatus } from '~/types/task'
import { apiErrorBody } from '~/types/api'
import { taskSchema } from '~/utils/validation'

const props = defineProps<{ open: boolean, task: Task | null, busy: boolean }>()
const emit = defineEmits<{ close: [], save: [input: TaskInput] }>()

interface TaskFormState {
  title: string
  description: string
  due_date: string
  status: TaskStatus
}

const state = reactive<TaskFormState>({ title: '', description: '', due_date: '', status: 'pending' })
const serverErrors = ref<Record<string, string[]>>({})

watch(() => [props.open, props.task] as const, () => {
  if (!props.open) return
  state.title = props.task?.title ?? ''
  state.description = props.task?.description ?? ''
  state.due_date = props.task?.due_date ?? ''
  state.status = props.task?.status ?? 'pending'
  serverErrors.value = {}
}, { immediate: true })

function submit(): void {
  serverErrors.value = {}
  emit('save', {
    title: state.title.trim(),
    description: state.description?.trim() || null,
    due_date: state.due_date || null,
    status: state.status,
  })
}

defineExpose({
  setApiError(error: unknown): void {
    serverErrors.value = apiErrorBody(error)?.errors ?? {}
  },
})
</script>

<template>
  <UModal :open="open" :title="task ? 'Edit task' : 'Create task'" description="Keep the next step specific and easy to scan." @update:open="!$event && emit('close')">
    <template #body>
      <UForm :schema="taskSchema" :state="state" class="task-form" @submit="submit">
        <UFormField label="Title" name="title" required :error="serverErrors.title?.[0]">
          <UInput v-model="state.title" autofocus placeholder="What needs to happen?" />
        </UFormField>
        <UFormField label="Description" name="description" :error="serverErrors.description?.[0]">
          <UTextarea v-model="state.description" :rows="4" placeholder="Add useful context (optional)" />
        </UFormField>
        <div class="task-form__row">
          <UFormField label="Due date" name="due_date" :error="serverErrors.due_date?.[0]">
            <UInput v-model="state.due_date" type="date" />
          </UFormField>
          <UFormField label="Status" name="status" :error="serverErrors.status?.[0]">
            <USelect
              v-model="state.status"
              :items="[
                { label: 'Pending', value: 'pending' },
                { label: 'In progress', value: 'in_progress' },
                { label: 'Completed', value: 'completed' },
              ]"
            />
          </UFormField>
        </div>
        <div class="modal-actions">
          <UButton label="Cancel" color="neutral" variant="ghost" type="button" @click="emit('close')" />
          <UButton :label="task ? 'Save changes' : 'Create task'" type="submit" :loading="busy" />
        </div>
      </UForm>
    </template>
  </UModal>
</template>
