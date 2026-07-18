import type { PaginationMeta } from '../api/types'

interface PaginationProps {
  meta: PaginationMeta
  onPageChange: (page: number) => void
}

export function Pagination({ meta, onPageChange }: PaginationProps) {
  if (meta.last_page <= 1) {
    return null
  }

  const buttonClass =
    'rounded border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-40'

  return (
    <nav aria-label="Paginação do histórico" className="flex flex-wrap items-center justify-between gap-2">
      <p className="text-sm text-gray-600">
        Página {meta.current_page} de {meta.last_page} · {meta.total} execuções
      </p>
      <div className="flex gap-2">
        <button
          disabled={meta.current_page <= 1}
          onClick={() => onPageChange(meta.current_page - 1)}
          className={buttonClass}
        >
          Anterior
        </button>
        <button
          disabled={meta.current_page >= meta.last_page}
          onClick={() => onPageChange(meta.current_page + 1)}
          className={buttonClass}
        >
          Próxima
        </button>
      </div>
    </nav>
  )
}
