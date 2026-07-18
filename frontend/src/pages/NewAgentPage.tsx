import { useMutation, useQueryClient } from '@tanstack/react-query'
import { useState, type FormEvent } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { createAgent } from '../api/agents'
import { getValidationErrors } from '../api/errors'

export function NewAgentPage() {
  const navigate = useNavigate()
  const queryClient = useQueryClient()
  const [name, setName] = useState('')
  const [description, setDescription] = useState('')

  const { mutate, isPending, error } = useMutation({
    mutationFn: createAgent,
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['agents'] })
      navigate('/', { replace: true })
    },
  })

  const validationErrors = getValidationErrors(error)
  const genericError =
    error && !validationErrors ? 'Não foi possível cadastrar o agente. Tente novamente.' : null

  function handleSubmit(event: FormEvent) {
    event.preventDefault()
    mutate({ name, description: description.trim() || undefined })
  }

  return (
    <section aria-labelledby="new-agent-heading" className="mx-auto max-w-lg">
      <h2 id="new-agent-heading" className="text-lg font-semibold text-gray-900">
        Novo agente
      </h2>

      <form onSubmit={handleSubmit} className="mt-4 space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        {genericError && (
          <p role="alert" className="rounded bg-red-50 p-3 text-sm text-red-700">
            {genericError}
          </p>
        )}

        <div>
          <label htmlFor="name" className="block text-sm font-medium text-gray-700">
            Nome <span aria-hidden="true" className="text-red-600">*</span>
          </label>
          <input
            id="name"
            required
            maxLength={255}
            value={name}
            onChange={(event) => setName(event.target.value)}
            aria-invalid={Boolean(validationErrors?.name)}
            aria-describedby={validationErrors?.name ? 'name-error' : undefined}
            className="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-blue-600 focus:outline-none focus:ring-1 focus:ring-blue-600 aria-[invalid=true]:border-red-500"
          />
          {validationErrors?.name && (
            <p id="name-error" role="alert" className="mt-1 text-sm text-red-700">
              {validationErrors.name[0]}
            </p>
          )}
        </div>

        <div>
          <label htmlFor="description" className="block text-sm font-medium text-gray-700">
            Descrição
          </label>
          <textarea
            id="description"
            rows={3}
            maxLength={1000}
            value={description}
            onChange={(event) => setDescription(event.target.value)}
            aria-invalid={Boolean(validationErrors?.description)}
            aria-describedby={validationErrors?.description ? 'description-error' : undefined}
            className="mt-1 w-full rounded border border-gray-300 px-3 py-2 focus:border-blue-600 focus:outline-none focus:ring-1 focus:ring-blue-600"
          />
          {validationErrors?.description && (
            <p id="description-error" role="alert" className="mt-1 text-sm text-red-700">
              {validationErrors.description[0]}
            </p>
          )}
        </div>

        <div className="flex items-center justify-end gap-3">
          <Link to="/" className="rounded border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
            Cancelar
          </Link>
          <button
            type="submit"
            disabled={isPending}
            className="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
          >
            {isPending ? 'Salvando…' : 'Cadastrar'}
          </button>
        </div>
      </form>
    </section>
  )
}
