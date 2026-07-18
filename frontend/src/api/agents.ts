import { api } from './client'
import type { Agent, AgentResponse, AgentsResponse, Execution, ExecutionStatus, Paginated } from './types'

export async function fetchAgents(): Promise<AgentsResponse> {
  const { data } = await api.get<AgentsResponse>('/agents')

  return data
}

export async function fetchAgent(id: number): Promise<AgentResponse> {
  const { data } = await api.get<AgentResponse>(`/agents/${id}`)

  return data
}

export async function createAgent(payload: { name: string; description?: string }): Promise<Agent> {
  const { data } = await api.post<{ data: Agent }>('/agents', payload)

  return data.data
}

export async function fetchExecutions(
  agentId: number,
  page: number,
  status?: ExecutionStatus,
): Promise<Paginated<Execution>> {
  const { data } = await api.get<Paginated<Execution>>(`/agents/${agentId}/executions`, {
    params: { page, status },
  })

  return data
}

export async function registerExecution(agentId: number): Promise<Execution> {
  const { data } = await api.post<{ data: Execution }>(`/agents/${agentId}/executions`, {
    duration_ms: Math.floor(Math.random() * 4000) + 300,
    metadata: { channel: 'painel', simulated: true },
  })

  return data.data
}
