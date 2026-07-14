// @vitest-environment node
import { describe, expect, it } from 'vitest'
import { parseTaskFilters, taskFiltersToQuery } from '~/utils/taskQuery'

describe('task URL query synchronization', () => {
  it('parses valid values and clamps pagination', () => {
    expect(parseTaskFilters({ status: 'completed', search: 'notes', page: '0', per_page: '999' })).toMatchObject({
      status: 'completed', search: 'notes', page: 1, per_page: 100,
    })
  })

  it('omits defaults and serializes active filters', () => {
    expect(taskFiltersToQuery({ status: 'pending', search: 'plan', sort: 'due_date', direction: 'asc', page: 2, per_page: 20 })).toEqual({
      status: 'pending', search: 'plan', sort: 'due_date', direction: 'asc', page: '2', per_page: '20',
    })
  })
})
