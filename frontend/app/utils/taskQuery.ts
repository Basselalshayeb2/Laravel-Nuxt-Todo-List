import type { TaskFilters, TaskStatus } from '~/types/task'

function queryValue(value: unknown): string {
  if (Array.isArray(value)) return typeof value[0] === 'string' ? value[0] : ''
  return typeof value === 'string' ? value : ''
}

export function parseTaskFilters(query: Record<string, unknown>): TaskFilters {
  const status = queryValue(query.status)
  const sort = queryValue(query.sort)

  return {
    status: ['pending', 'in_progress', 'completed'].includes(status) ? status as TaskStatus : '',
    search: queryValue(query.search),
    sort: ['due_date', 'status', 'title', 'created_at', 'updated_at'].includes(sort)
      ? sort as TaskFilters['sort']
      : 'created_at',
    direction: queryValue(query.direction) === 'asc' ? 'asc' : 'desc',
    page: Math.max(1, Number(queryValue(query.page)) || 1),
    per_page: Math.min(100, Math.max(1, Number(queryValue(query.per_page)) || 10)),
  }
}

export function taskFiltersToQuery(filters: TaskFilters): Record<string, string> {
  return {
    ...(filters.status ? { status: filters.status } : {}),
    ...(filters.search ? { search: filters.search } : {}),
    ...(filters.sort !== 'created_at' ? { sort: filters.sort } : {}),
    ...(filters.direction !== 'desc' ? { direction: filters.direction } : {}),
    ...(filters.page > 1 ? { page: String(filters.page) } : {}),
    ...(filters.per_page !== 10 ? { per_page: String(filters.per_page) } : {}),
  }
}
