import { isAxiosError } from 'axios'

/**
 * Extrai os erros de validação (422) no formato do Laravel: { campo: [mensagens] }.
 */
export function getValidationErrors(error: unknown): Record<string, string[]> | null {
  if (isAxiosError(error) && error.response?.status === 422) {
    return (error.response.data as { errors?: Record<string, string[]> }).errors ?? null
  }

  return null
}
