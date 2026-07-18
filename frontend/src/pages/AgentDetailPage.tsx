import { keepPreviousData, useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { isAxiosError } from 'axios'
import { useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import { fetchAgent, fetchExecutions, registerExecution } from '../api/agents'
import { getValidationErrors } from '../api/errors'
import type { ExecutionStatus } from '../api/types'
import { Badge } from '../components/Badge'
import { EmptyState } from '../components/EmptyState'
import { ErrorState } from '../components/ErrorState'
import { ExecutionsTable } from '../components/ExecutionsTable'
import { Pagination } from '../components/Pagination'
import { Skeleton } from '../components/Skeleton'
import { UsageSummary } from '../components/UsageSummary'

type Feedback = { type: 'success' | 'error'; message: string }

function getSimulationErrorMessage(error: unknown): string {
  if (isAxiosError(error) && error.response?.status === 429) {
    const detail = (error.response.data as { errors?: { limit?: string[] } }).errors?.limit?.[0]

    return `Execução bloqueada: limite mensal do plano atingido. ${detail ?? ''}`.trim()
  }

  const validationErrors = getValidationErrors(error)

  if (validationErrors?.agent) {
    return validationErrors.agent[0]
  }

  return 'Não foi possível registrar a execução. Tente novamente.'
}

export function AgentDetailPage() {
  const { id } = useParams()
  const agentId = Number(id)
  const queryClient = useQueryClient()
  const [page, setPage] = useState(1)
  const [statusFilter, setStatusFilter] = useState<ExecutionStatus | ''>('')
  const [feedback, setFeedback] = useState<Feedback | null>(null)

  const agentQuery = useQuery({
    queryKey: ['agents', agentId],
    queryFn: () => fetchAgent(agentId),
  })

  const executionsQuery = useQuery({
    queryKey: ['agents', agentId, 'executions', { page, status: statusFilter }],
    queryFn: () => fetchExecutions(agentId, page, statusFilter || undefined),
    placeholderData: keepPreviousData,
  })

  const simulation = useMutation({
    mutationFn: () => registerExecution(agentId),
    onSettled: () => queryClient.invalidateQueries({ queryKey: ['agents'] }),
    onSuccess: () => setFeedback({ type: 'success', message: 'Execução registrada com sucesso.' }),
    onError: (error) => setFeedback({ type: 'error', message: getSimulationErrorMessage(error) }),
  })

  if (agentQuery.isPending) {
    return (
      <div className="space-y-4" aria-busy="true" aria-label="Carregando agente">
        <Skeleton className="h-8 w-64" />
        <Skeleton className="h-24 w-full" />
        <Skeleton className="h-64 w-full" />
      </div>
    )
  }

  if (agentQuery.isError) {
    const isForbidden = isAxiosError(agentQuery.error) && agentQuery.error.response?.status === 403

    return (
      <ErrorState
        message={isForbidden ? 'Você não tem acesso a este agente.' : 'Não foi possível carregar o agente.'}
        onRetry={isForbidden ? undefined : () => void agentQuery.refetch()}
      />
    )
  }

  const { data: agent, meta } = agentQuery.data

  return (
    <section aria-labelledby="agent-heading" className="space-y-6">
      <Link to="/" className="text-sm text-blue-700 hover:underline">
        ← Voltar para agentes
      </Link>

      <div className="flex flex-wrap items-center justify-between gap-3">
        <div>
          <h2 id="agent-heading" className="flex items-center gap-2 text-lg font-semibold text-gray-900">
            {agent.name}
            {agent.status === 'inactive' && <Badge variant="neutral">Inativo</Badge>}
            {meta.usage.is_blocked && <Badge variant="danger">Bloqueado</Badge>}
          </h2>
          {agent.description && <p className="mt-1 text-sm text-gray-600">{agent.description}</p>}
        </div>
        <button
          onClick={() => simulation.mutate()}
          disabled={simulation.isPending}
          className="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
        >
          {simulation.isPending ? 'Registrando…' : 'Simular execução'}
        </button>
      </div>

      {feedback && (
        <p
          role="alert"
          className={`rounded p-3 text-sm ${
            feedback.type === 'success' ? 'bg-emerald-50 text-emerald-800' : 'bg-red-50 text-red-700'
          }`}
        >
          {feedback.message}
        </p>
      )}

      <UsageSummary usage={meta.usage} />

      <div className="flex flex-wrap items-center justify-between gap-3">
        <h3 className="font-medium text-gray-900">Histórico de execuções</h3>
        <div className="flex items-center gap-2">
          <label htmlFor="status-filter" className="text-sm text-gray-600">
            Status
          </label>
          <select
            id="status-filter"
            value={statusFilter}
            onChange={(event) => {
              setStatusFilter(event.target.value as ExecutionStatus | '')
              setPage(1)
            }}
            className="rounded border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-600 focus:outline-none"
          >
            <option value="">Todos</option>
            <option value="success">Sucesso</option>
            <option value="failed">Falha</option>
            <option value="blocked">Bloqueada</option>
          </select>
        </div>
      </div>

      {executionsQuery.isPending ? (
        <Skeleton className="h-64 w-full" />
      ) : executionsQuery.isError ? (
        <ErrorState
          message="Não foi possível carregar o histórico."
          onRetry={() => void executionsQuery.refetch()}
        />
      ) : executionsQuery.data.data.length === 0 ? (
        <EmptyState
          title="Nenhuma execução encontrada"
          description={statusFilter ? 'Tente remover o filtro de status.' : 'Este agente ainda não executou nada.'}
        />
      ) : (
        <div className={`space-y-3 ${executionsQuery.isPlaceholderData ? 'opacity-60' : ''}`}>
          <ExecutionsTable executions={executionsQuery.data.data} />
          <Pagination meta={executionsQuery.data.meta} onPageChange={setPage} />
        </div>
      )}
    </section>
  )
}
