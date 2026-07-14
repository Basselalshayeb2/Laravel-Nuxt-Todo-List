import { mountSuspended } from '@nuxt/test-utils/runtime'
import { describe, expect, it, vi } from 'vitest'
import TaskEmptyState from '~/components/tasks/TaskEmptyState.vue'
import TaskFilters from '~/components/tasks/TaskFilters.vue'
import TaskTable from '~/components/tasks/TaskTable.vue'
import type { Task } from '~/types/task'

const task: Task = {
  id: 1, user_id: 1, title: 'Write tests', description: null, due_date: null, status: 'pending',
  can: { update: false, delete: false }, created_at: '2026-07-14T10:00:00Z', updated_at: '2026-07-14T10:00:00Z',
}

describe('task states and permissions', () => {
  it('shows directional empty-state copy', async () => {
    const wrapper = await mountSuspended(TaskEmptyState, { props: { filtered: true } })
    expect(wrapper.text()).toContain('No tasks match these filters')
  })

  it('hides actions without API permissions and shows them when allowed', async () => {
    const wrapper = await mountSuspended(TaskTable, { props: { tasks: [task] } })
    expect(wrapper.text()).not.toContain('Edit')
    expect(wrapper.text()).not.toContain('Delete')

    await wrapper.setProps({ tasks: [{ ...task, can: { update: true, delete: true } }] })
    expect(wrapper.text()).toContain('Edit')
    expect(wrapper.text()).toContain('Delete')
  })

  it('debounces search changes', async () => {
    vi.useFakeTimers()
    const wrapper = await mountSuspended(TaskFilters, {
      props: { filters: { status: '', search: '', sort: 'created_at', direction: 'desc', page: 1, per_page: 10 } },
    })
    await wrapper.find('input[aria-label="Search tasks"]').setValue('notes')
    await vi.advanceTimersByTimeAsync(350)
    expect(wrapper.emitted('change')?.[0]).toEqual([{ search: 'notes' }])
    vi.useRealTimers()
  })
})
