import type { ClientUsage } from '../api/types'
import { UsageBar } from './UsageBar'

export function UsageSummary({ usage }: { usage: ClientUsage }) {
  return (
    <div className="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
      <div className="flex flex-wrap items-baseline justify-between gap-2">
        <p className="text-sm text-gray-600">Consumo do mês</p>
        <p className="text-sm font-medium text-gray-900">
          {usage.used.toLocaleString('pt-BR')} / {usage.limit.toLocaleString('pt-BR')} execuções (
          {usage.usage_percent}%)
        </p>
      </div>
      <div className="mt-2">
        <UsageBar percent={usage.usage_percent} />
      </div>
      {usage.is_blocked ? (
        <p role="alert" className="mt-2 text-sm font-medium text-red-700">
          Limite do plano atingido — novas execuções estão bloqueadas.
        </p>
      ) : usage.usage_percent >= 80 ? (
        <p className="mt-2 text-sm font-medium text-amber-700">
          Atenção: consumo acima de 80% do limite do plano.
        </p>
      ) : null}
    </div>
  )
}
