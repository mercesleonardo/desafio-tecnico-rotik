import { useQuery } from '@tanstack/react-query'
import { Link } from 'react-router-dom'
import { fetchAgents } from '../api/agents'
import { AgentCard } from '../components/AgentCard'
import { EmptyState } from '../components/EmptyState'
import { ErrorState } from '../components/ErrorState'
import { Skeleton } from '../components/Skeleton'
import { UsageSummary } from '../components/UsageSummary'

function AgentsPageSkeleton() {
  return (
    <div className="space-y-6" aria-busy="true" aria-label="Carregando agentes">
      <Skeleton className="h-9 w-40" />
      <Skeleton className="h-24 w-full" />
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {Array.from({ length: 6 }, (_, index) => (
          <Skeleton key={index} className="h-32" />
        ))}
      </div>
    </div>
  )
}

export function AgentsPage() {
  const { data, isPending, isError, refetch } = useQuery({
    queryKey: ['agents'],
    queryFn: fetchAgents,
  })

  if (isPending) {
    return <AgentsPageSkeleton />
  }

  if (isError) {
    return (
      <ErrorState
        message="Não foi possível carregar os agentes."
        onRetry={() => void refetch()}
      />
    )
  }

  const { data: agents, meta } = data

  return (
    <section aria-labelledby="agents-heading" className="space-y-6">
      <div className="flex flex-wrap items-center justify-between gap-3">
        <h2 id="agents-heading" className="text-lg font-semibold text-gray-900">
          Agentes
        </h2>
        <Link
          to="/agents/new"
          className="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
        >
          Novo agente
        </Link>
      </div>

      <UsageSummary usage={meta.usage} />

      {agents.length === 0 ? (
        <EmptyState
          title="Nenhum agente cadastrado ainda"
          description="Cadastre o primeiro agente de IA deste cliente para começar o monitoramento."
          action={
            <Link
              to="/agents/new"
              className="inline-block rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
            >
              Cadastrar agente
            </Link>
          }
        />
      ) : (
        <ul className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {agents.map((agent) => (
            <li key={agent.id}>
              <AgentCard agent={agent} isBlocked={meta.usage.is_blocked} />
            </li>
          ))}
        </ul>
      )}
    </section>
  )
}
