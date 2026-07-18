import type { Execution, ExecutionStatus } from '../api/types'
import { Badge } from './Badge'

const statusConfig: Record<ExecutionStatus, { label: string; variant: 'success' | 'warning' | 'danger' }> = {
  success: { label: 'Sucesso', variant: 'success' },
  failed: { label: 'Falha', variant: 'warning' },
  blocked: { label: 'Bloqueada', variant: 'danger' },
}

export function ExecutionsTable({ executions }: { executions: Execution[] }) {
  return (
    <div className="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
      <table className="w-full text-left text-sm">
        <caption className="sr-only">Histórico de execuções do agente</caption>
        <thead>
          <tr className="border-b border-gray-200 bg-gray-50 text-gray-600">
            <th scope="col" className="px-4 py-2 font-medium">Status</th>
            <th scope="col" className="px-4 py-2 font-medium">Duração</th>
            <th scope="col" className="px-4 py-2 font-medium">Canal</th>
            <th scope="col" className="px-4 py-2 font-medium">Data</th>
          </tr>
        </thead>
        <tbody>
          {executions.map((execution) => {
            const status = statusConfig[execution.status]

            return (
              <tr key={execution.id} className="border-b border-gray-100 last:border-0">
                <td className="px-4 py-2">
                  <Badge variant={status.variant}>{status.label}</Badge>
                </td>
                <td className="px-4 py-2 text-gray-700">
                  {execution.duration_ms != null ? `${execution.duration_ms} ms` : '—'}
                </td>
                <td className="px-4 py-2 text-gray-700">{String(execution.metadata?.channel ?? '—')}</td>
                <td className="px-4 py-2 text-gray-700">
                  {new Date(execution.created_at).toLocaleString('pt-BR')}
                </td>
              </tr>
            )
          })}
        </tbody>
      </table>
    </div>
  )
}
