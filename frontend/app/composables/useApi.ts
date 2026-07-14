import { $fetch as ofetch, type FetchOptions } from 'ofetch'
import { apiErrorBody, shouldRedirectToLogin } from '~/types/api'

export async function useApi<T>(path: string, options: FetchOptions<'json'> = {}): Promise<T> {
  const config = useRuntimeConfig()
  const route = useNuxtApp().$router.currentRoute.value
  const headers = new Headers(options.headers as HeadersInit | undefined)

  headers.set('Accept', 'application/json')

  if (import.meta.server) {
    const requestHeaders = useRequestHeaders(['cookie', 'origin', 'referer'])
    for (const [name, value] of Object.entries(requestHeaders)) {
      if (value) headers.set(name, value)
    }
    if (!headers.has('Origin')) headers.set('Origin', config.public.siteUrl)
  } else {
    const token = useCookie('XSRF-TOKEN').value
    if (token && path !== '/sanctum/csrf-cookie') {
      headers.set('X-XSRF-TOKEN', decodeURIComponent(token))
    }
  }

  const baseURL = import.meta.server ? config.apiInternalBaseUrl : ''

  try {
    return await ofetch<T>(path, {
      ...options,
      baseURL,
      credentials: 'include',
      headers,
    })
  } catch (error: unknown) {
    const body = apiErrorBody(error)
    const status = typeof error === 'object' && error !== null && 'status' in error
      ? Number((error as { status?: unknown }).status)
      : undefined

    if (shouldRedirectToLogin(status, route.path)) {
      useState('auth.user', () => null).value = null
      await navigateTo({ path: '/login', query: { redirect: route.fullPath } })
    }

    if (body) throw Object.assign(new Error(body.message), { data: body, status })
    throw error
  }
}
