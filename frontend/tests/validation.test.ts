// @vitest-environment node
import { describe, expect, it } from 'vitest'
import { loginSchema, taskSchema } from '~/utils/validation'

describe('form validation', () => {
  it('rejects an invalid login form', () => {
    const result = loginSchema.safeParse({ email: 'not-an-email', password: '' })
    expect(result.success).toBe(false)
    if (!result.success) expect(result.error.flatten().fieldErrors).toMatchObject({ email: expect.any(Array), password: expect.any(Array) })
  })

  it('accepts a valid task and rejects invalid title, date, and status', () => {
    expect(taskSchema.safeParse({ title: 'Plan week', description: '', due_date: '2026-07-20', status: 'pending' }).success).toBe(true)
    expect(taskSchema.safeParse({ title: 'x', description: '', due_date: 'tomorrow', status: 'blocked' }).success).toBe(false)
  })
})
