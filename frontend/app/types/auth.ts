export type UserRole = 'user' | 'admin'

export interface User {
  id: number
  name: string
  email: string
  role: UserRole
}

export interface LoginInput {
  email: string
  password: string
}
