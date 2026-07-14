<script setup lang="ts">
import type { Task } from '~/types/task'

defineProps<{ task: Task | null, busy: boolean }>()
defineEmits<{ close: [], confirm: [] }>()
</script>

<template>
  <UModal :open="Boolean(task)" title="Delete task?" description="This action cannot be undone." @update:open="!$event && $emit('close')">
    <template #body>
      <p>“{{ task?.title }}” will be permanently removed.</p>
      <div class="modal-actions">
        <UButton label="Keep task" color="neutral" variant="ghost" @click="$emit('close')" />
        <UButton label="Delete task" color="error" :loading="busy" @click="$emit('confirm')" />
      </div>
    </template>
  </UModal>
</template>
