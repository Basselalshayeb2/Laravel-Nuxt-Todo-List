export interface ApiSuccess<T> {
  success: true
  data: T
  message?: string
}

export interface ApiErrorBody {
  success: false
  message: string
  code: string
  errors: Record<string, string[]>
}

export interface PaginationLink {
  url: string | null
  label: string
  active: boolean
}

export interface PaginationMeta {
  current_page: number
  from: number | null
  last_page: number
  links: PaginationLink[]
  path: string
  per_page: number
  to: number | null
  total: number
}

export function apiErrorBody(error: unknown): ApiErrorBody | null {
  if (typeof error !== 'object' || error === null || !('data' in error)) return null
  const data = (error as { data?: unknown }).data
  if (typeof data !== 'object' || data === null || !('success' in data) || data.success !== false) return null
  return data as ApiErrorBody
}

export function isAuthFailure(status: number | undefined): boolean {
  return status === 401 || status === 419
}

export function shouldRedirectToLogin(status: number | undefined, path: string): boolean {
  return isAuthFailure(status) && path !== '/login'
}
