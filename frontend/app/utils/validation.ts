import { z } from 'zod'

export const loginSchema = z.object({
  email: z.email('Enter a valid email address.'),
  password: z.string().min(1, 'Enter your password.'),
})

export const taskSchema = z.object({
  title: z.string().trim().min(3, 'Use at least 3 characters.').max(255, 'Use no more than 255 characters.'),
  description: z.string().nullable(),
  due_date: z.union([z.literal(''), z.iso.date('Enter a valid date.')]).nullable(),
  status: z.enum(['pending', 'in_progress', 'completed']),
})
