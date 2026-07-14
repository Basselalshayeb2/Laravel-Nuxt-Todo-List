<script setup lang="ts">
const { user, logout } = useAuth()
const loggingOut = ref(false)

async function handleLogout(): Promise<void> {
  loggingOut.value = true
  try {
    await logout()
  } finally {
    loggingOut.value = false
  }
}
</script>

<template>
  <div class="site-shell">
    <header class="site-header">
      <NuxtLink to="/" class="brand" aria-label="TaskFlow home">
        <span class="brand__mark" aria-hidden="true"><i /><i /><i /></span>
        <span>TaskFlow</span>
      </NuxtLink>
      <nav class="site-nav" aria-label="Primary navigation">
        <template v-if="user">
          <span class="site-nav__user">{{ user.name }} <small>{{ user.role }}</small></span>
          <UButton to="/tasks" label="Tasks" color="neutral" variant="ghost" class="site-nav__tasks" />
          <UButton label="Log out" color="neutral" variant="outline" :loading="loggingOut" @click="handleLogout" />
        </template>
        <UButton v-else to="/login" label="Log in" />
      </nav>
    </header>
    <main>
      <slot />
    </main>
  </div>
</template>
