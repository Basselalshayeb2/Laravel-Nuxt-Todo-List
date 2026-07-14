<template>
  <div class="auth-shell">
    <NuxtLink to="/" class="brand" aria-label="TaskFlow home">
      <span class="brand__mark" aria-hidden="true"><i /><i /><i /></span>
      <span>TaskFlow</span>
    </NuxtLink>
    <main class="auth-shell__main"><slot /></main>
  </div>
</template>
