import type { ApiSuccess } from '~/types/api'
import type { Task, TaskFilters, TaskInput, TaskListResponse } from '~/types/task'
import { parseTaskFilters, taskFiltersToQuery } from '~/utils/taskQuery'

export function useTasks() {
  const route = useRoute()
  const router = useRouter()

  const filters = computed<TaskFilters>(() => parseTaskFilters(route.query))

  const requestQuery = computed(() => ({
    ...(filters.value.status ? { status: filters.value.status } : {}),
    ...(filters.value.search ? { search: filters.value.search } : {}),
    sort: filters.value.sort,
    direction: filters.value.direction,
    page: filters.value.page,
    per_page: filters.value.per_page,
  }))

  const { data, status, error, refresh } = useAsyncData<TaskListResponse>(
    'tasks',
    () => useApi<TaskListResponse>('/api/tasks', { query: requestQuery.value }),
    { watch: [requestQuery] },
  )

  async function updateFilters(patch: Partial<TaskFilters>, resetPage = true): Promise<void> {
    const next = { ...filters.value, ...patch }
    if (resetPage && !('page' in patch)) next.page = 1

    await router.push({
      query: taskFiltersToQuery(next),
    })
  }

  async function createTask(input: TaskInput): Promise<Task> {
    const response = await useApi<ApiSuccess<Task>>('/api/tasks', { method: 'POST', body: input })
    await refresh()
    return response.data
  }

  async function updateTask(task: Task, input: Partial<TaskInput>): Promise<Task> {
    const response = await useApi<ApiSuccess<Task>>(`/api/tasks/${task.id}`, { method: 'PATCH', body: input })
    await refresh()
    return response.data
  }

  async function deleteTask(task: Task): Promise<void> {
    await useApi(`/api/tasks/${task.id}`, { method: 'DELETE' })
    if (data.value?.data.length === 1 && filters.value.page > 1) {
      await updateFilters({ page: filters.value.page - 1 }, false)
    } else {
      await refresh()
    }
  }

  return { data, status, error, filters, updateFilters, createTask, updateTask, deleteTask, refresh }
}
