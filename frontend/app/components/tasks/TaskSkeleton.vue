<template>
  <div class="task-skeleton" aria-label="Loading tasks" aria-busy="true">
    <USkeleton v-for="row in 5" :key="row" class="h-20 w-full rounded-2xl" />
  </div>
</template>
