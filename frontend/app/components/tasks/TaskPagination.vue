<script setup lang="ts">
import type { PaginationMeta } from '~/types/api'

defineProps<{ meta: PaginationMeta }>()
defineEmits<{ page: [page: number] }>()
</script>

<template>
  <nav v-if="meta.last_page > 1" class="task-pagination" aria-label="Task pages">
    <p>Showing {{ meta.from }}–{{ meta.to }} of {{ meta.total }}</p>
    <UPagination
      :page="meta.current_page"
      :total="meta.total"
      :items-per-page="meta.per_page"
      @update:page="$emit('page', $event)"
    />
  </nav>
</template>
