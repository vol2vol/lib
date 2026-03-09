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

  console.log('STATUS:', response.status)
  console.log('RAW RESPONSE:', raw)

  let data: unknown = null

  try {
    data = raw ? JSON.parse(raw) : null
  } catch {
    console.error('JSON PARSE ERROR:', raw)
    throw new Error('Некорректный ответ сервера')
  }

  if (!response.ok) {
    console.error('REQUEST FAILED:', data)

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

  console.log('PARSED DATA:', data)

  return data as T
}

export async function registerUser(payload: RegisterPayload): Promise<AuthResponse> {
  console.log('REGISTER PAYLOAD:', payload)
  console.log('REGISTER URL:', `${API_BASE_URL}/register`)

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
  console.log('LOGIN PAYLOAD:', payload)
  console.log('LOGIN URL:', `${API_BASE_URL}/login`)

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
