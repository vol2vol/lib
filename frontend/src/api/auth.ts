import { API_BASE_URL } from './api'

type RegisterPayload = {
  login: string
  password: string
  password_confirmation: string
}

type LoginPayload = {
  login: string
  password: string
}

type AuthResponse = {
  access_token: string
  token_type: string
  user: {
    user_id: number
    login: string
    role_id: number
    created_at: string
    updated_at: string
  }
}

type UserResponse = {
  user_id: number
  login: string
  role_id: number
  created_at: string
  updated_at: string
}

type ApiErrorData = {
  message?: string
  errors?: Record<string, string[]>
}

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
    'The password field must be at least 8 characters.':
      'Пароль должен содержать минимум 8 символов.',
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

async function parseResponse<T>(response: Response): Promise<T> {
  const raw = await response.clone().text()

  let data: unknown = null

  try {
    data = raw ? JSON.parse(raw) : null
  } catch {
    if (!response.ok) {
      throw new Error('Некорректный ответ сервера')
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

    const message =
      fieldMessage ||
      commonMessage ||
      `Ошибка запроса: ${response.status}`

    throw new ApiError(message, response.status, fieldErrors)
  }

  return data as T
}

export async function registerUser(payload: RegisterPayload): Promise<AuthResponse> {
  const response = await fetch(`${API_BASE_URL}/register`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
    },
    body: JSON.stringify(payload),
  })

  return parseResponse<AuthResponse>(response)
}

export async function loginUser(payload: LoginPayload): Promise<AuthResponse> {
  const response = await fetch(`${API_BASE_URL}/login`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
    },
    body: JSON.stringify(payload),
  })

  return parseResponse<AuthResponse>(response)
}

export async function logoutUser(token: string): Promise<void> {
  const response = await fetch(`${API_BASE_URL}/logout`, {
    method: 'POST',
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${token}`,
    },
  })

  await parseResponse<void>(response)
}

export async function getCurrentUser(token: string): Promise<UserResponse> {
  const response = await fetch(`${API_BASE_URL}/user`, {
    method: 'GET',
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${token}`,
    },
  })

  return parseResponse<UserResponse>(response)
}