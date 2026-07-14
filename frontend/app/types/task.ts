import type { PaginationMeta } from './api'

export type TaskStatus = 'pending' | 'in_progress' | 'completed'
export type TaskSort = 'due_date' | 'status' | 'title' | 'created_at' | 'updated_at'
export type SortDirection = 'asc' | 'desc'

export interface TaskPermissions {
  update: boolean
  delete: boolean
}

export interface Task {
  id: number
  user_id: number
  title: string
  description: string | null
  due_date: string | null
  status: TaskStatus
  can: TaskPermissions
  created_at: string
  updated_at: string
}

export interface TaskInput {
  title: string
  description: string | null
  due_date: string | null
  status: TaskStatus
}

export interface TaskListResponse {
  success: true
  data: Task[]
  links: { first: string | null, last: string | null, prev: string | null, next: string | null }
  meta: PaginationMeta
}

export interface TaskFilters {
  status: TaskStatus | ''
  search: string
  sort: TaskSort
  direction: SortDirection
  page: number
  per_page: number
}
