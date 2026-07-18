export interface ClientUsage {
  used: number
  limit: number
  usage_percent: number
  is_blocked: boolean
}

export interface Agent {
  id: number
  name: string
  description: string | null
  status: 'active' | 'inactive'
  executions_this_month?: number
  created_at: string
}

export type ExecutionStatus = 'success' | 'failed' | 'blocked'

export interface Execution {
  id: number
  status: ExecutionStatus
  duration_ms: number | null
  metadata: Record<string, unknown> | null
  created_at: string
}

export interface User {
  id: number
  name: string
  email: string
  client: { id: number; name: string }
}

export interface LoginResponse {
  token: string
  user: User
}

export interface AgentsResponse {
  data: Agent[]
  meta: { usage: ClientUsage }
}

export interface AgentResponse {
  data: Agent
  meta: { usage: ClientUsage }
}

export interface PaginationMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface Paginated<T> {
  data: T[]
  meta: PaginationMeta
}
