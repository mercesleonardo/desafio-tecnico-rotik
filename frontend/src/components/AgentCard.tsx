import { memo } from 'react'
import { Link } from 'react-router-dom'
import type { Agent } from '../api/types'
import { Badge } from './Badge'

interface AgentCardProps {
  agent: Agent
  isBlocked: boolean
}

export const AgentCard = memo(function AgentCard({ agent, isBlocked }: AgentCardProps) {
  return (
    <Link
      to={`/agents/${agent.id}`}
      className="block h-full rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition hover:border-blue-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600"
    >
      <div className="flex items-start justify-between gap-2">
        <h3 className="font-medium text-gray-900">{agent.name}</h3>
        <div className="flex shrink-0 gap-1">
          {agent.status === 'inactive' && <Badge variant="neutral">Inativo</Badge>}
          {isBlocked && <Badge variant="danger">Bloqueado</Badge>}
        </div>
      </div>
      {agent.description && (
        <p className="mt-1 line-clamp-2 text-sm text-gray-600">{agent.description}</p>
      )}
      <p className="mt-3 text-sm text-gray-700">
        <span className="font-semibold">{agent.executions_this_month ?? 0}</span> execuções neste mês
      </p>
    </Link>
  )
})
