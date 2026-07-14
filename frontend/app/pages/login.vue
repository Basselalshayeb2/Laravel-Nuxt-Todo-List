<script setup lang="ts">
import { apiErrorBody } from '~/types/api'
import type { LoginInput } from '~/types/auth'
import { loginSchema } from '~/utils/validation'

definePageMeta({ layout: 'auth', middleware: 'guest' })
useSeoMeta({ title: 'Log in — TaskFlow', robots: 'noindex, nofollow' })

const route = useRoute()
const { login } = useAuth()
const state = reactive<LoginInput>({ email: '', password: '' })
const fieldErrors = ref<Record<string, string[]>>({})
const message = ref('')
const busy = ref(false)

async function submit(): Promise<void> {
  fieldErrors.value = {}
  message.value = ''
  busy.value = true
  try {
    await login(state)
    const redirect = typeof route.query.redirect === 'string' && route.query.redirect.startsWith('/')
      ? route.query.redirect
      : '/tasks'
    await navigateTo(redirect)
  } catch (error: unknown) {
    const body = apiErrorBody(error)
    fieldErrors.value = body?.errors ?? {}
    message.value = body?.message ?? 'Login could not be completed. Try again.'
  } finally {
    busy.value = false
  }
}
</script>

<template>
  <section class="login-card" aria-labelledby="login-title">
    <p class="eyebrow">Welcome back</p>
    <h1 id="login-title">Pick up where you left off.</h1>
    <p class="login-card__intro">Use one of the demo accounts or your assigned credentials.</p>
    <UAlert v-if="message" color="error" variant="subtle" :description="message" />
    <UForm :schema="loginSchema" :state="state" class="login-form" @submit="submit">
      <UFormField label="Email" name="email" required :error="fieldErrors.email?.[0]">
        <UInput v-model="state.email" type="email" autocomplete="email" placeholder="you@example.com" autofocus />
      </UFormField>
      <UFormField label="Password" name="password" required :error="fieldErrors.password?.[0]">
        <UInput v-model="state.password" type="password" autocomplete="current-password" />
      </UFormField>
      <UButton type="submit" label="Log in" block size="lg" :loading="busy" />
    </UForm>
    <div class="demo-note">
      <strong>Demo user</strong>
      <code>user@example.com</code><code>Password123!</code>
    </div>
  </section>
</template>
