import { API_BASE_URL } from '@api/api'
import type {
  ApiErrorResponse,
  LoginPayload,
  LoginResponse,
  RegisterPayload,
  RegisterResponse,
} from '@models/auth'

const getErrorMessage = (data: ApiErrorResponse) => {
  const validationMessage = data.errors
    ? Object.values(data.errors).flat().join('\n')
    : null

  return validationMessage || data.message || 'Произошла ошибка'
}

export const registerUser = async (
  payload: RegisterPayload
): Promise<RegisterResponse> => {
  const response = await fetch(`${API_BASE_URL}/register`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(payload),
  })

  const data: RegisterResponse & ApiErrorResponse = await response.json()

  if (!response.ok) {
    throw new Error(getErrorMessage(data))
  }

  return data
}

export const loginUser = async (
  payload: LoginPayload
): Promise<LoginResponse> => {
  const response = await fetch(`${API_BASE_URL}/login`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(payload),
  })

  const data: LoginResponse & ApiErrorResponse = await response.json()

  if (!response.ok) {
    throw new Error(getErrorMessage(data))
  }

  return data
}