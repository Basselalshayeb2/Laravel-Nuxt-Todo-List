import type { ApiSuccess } from '~/types/api'
import type { LoginInput, User } from '~/types/auth'

export function useAuth() {
  const user = useState<User | null | undefined>('auth.user', () => undefined)
  const initializing = useState<boolean>('auth.initializing', () => false)

  async function initialize(): Promise<User | null> {
    if (user.value !== undefined) return user.value
    if (initializing.value) return null

    initializing.value = true
    try {
      const response = await useApi<ApiSuccess<User>>('/api/user')
      user.value = response.data
    } catch {
      user.value = null
    } finally {
      initializing.value = false
    }

    return user.value
  }

  async function login(input: LoginInput): Promise<User> {
    await useApi<unknown>('/sanctum/csrf-cookie')
    const response = await useApi<ApiSuccess<User>>('/api/auth/login', { method: 'POST', body: input })
    user.value = response.data
    return response.data
  }

  async function logout(): Promise<void> {
    await useApi('/api/auth/logout', { method: 'POST' })
    user.value = null
    await navigateTo('/login')
  }

  return { user, initializing, initialize, login, logout }
}
