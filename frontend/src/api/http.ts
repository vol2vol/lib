import { API_BASE_URL } from './api'
import type { ApiErrorData } from '@models/auth'

export class ApiError extends Error {
  status: number
  fieldErrors?: Record<string, string[]>

  constructor(message: string, status: number, fieldErrors?: Record<string, string[]>) {
    super(message)
    this.name = 'ApiError'
    this.status = status
    this.fieldErrors = fieldErrors
  }
}

const translateErrorMessage = (message: string) => {
  const messagesMap: Record<string, string> = {
    'The login has already been taken.': 'Этот логин уже занят.',
    'The password field must be at least 8 characters.': 'Пароль должен содержать минимум 8 символов.',
    'The password confirmation does not match.': 'Пароли не совпадают.',
    'The login field is required.': 'Введите логин.',
    'The password field is required.': 'Введите пароль.',
    'The password confirmation field is required.': 'Подтвердите пароль.',
    'The provided credentials are incorrect.': 'Неверный логин или пароль.',
    Unauthorized: 'Необходима авторизация.',
    Unauthenticated: 'Необходима авторизация.',
  }

  return messagesMap[message] || message
}

export const createHeaders = (token?: string, extra?: HeadersInit): HeadersInit => ({
  Accept: 'application/json',
  ...(token ? { Authorization: `Bearer ${token}` } : {}),
  ...extra,
})

export const buildUrl = (path: string) => `${API_BASE_URL}${path}`

export async function parseResponse<T>(response: Response): Promise<T> {
  const raw = await response.clone().text()

  let data: unknown = null

  try {
    data = raw ? JSON.parse(raw) : null
  } catch {
    if (!response.ok) {
      throw new ApiError('Некорректный ответ сервера', response.status)
    }

    return null as T
  }

  if (!response.ok) {
    const errorData = (data ?? {}) as ApiErrorData

    const fieldErrors = errorData.errors
      ? Object.fromEntries(
          Object.entries(errorData.errors).map(([key, messages]) => [
            key,
            messages.map(translateErrorMessage),
          ])
        )
      : undefined

    const fieldMessage = fieldErrors
      ? Object.values(fieldErrors).flat().join('\n')
      : ''

    const commonMessage = errorData.message
      ? translateErrorMessage(errorData.message)
      : ''

    const message = fieldMessage || commonMessage || `Ошибка запроса: ${response.status}`

    throw new ApiError(message, response.status, fieldErrors)
  }

  return data as T
}