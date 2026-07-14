// @vitest-environment node
import { describe, expect, it } from 'vitest'
import { apiErrorBody, shouldRedirectToLogin } from '~/types/api'

describe('API error handling', () => {
  it('narrows Laravel validation errors', () => {
    const body = apiErrorBody({ data: { success: false, message: 'Invalid', code: 'VALIDATION_ERROR', errors: { title: ['Required'] } } })
    expect(body?.errors.title).toEqual(['Required'])
  })

  it('redirects 401 and 419 outside the login page', () => {
    expect(shouldRedirectToLogin(401, '/tasks')).toBe(true)
    expect(shouldRedirectToLogin(419, '/tasks')).toBe(true)
    expect(shouldRedirectToLogin(401, '/login')).toBe(false)
    expect(shouldRedirectToLogin(422, '/tasks')).toBe(false)
  })
})
