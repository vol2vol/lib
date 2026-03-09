export type RegisterPayload = {
  login: string
  password: string
  password_confirmation: string
}

export type RegisterResponse = {
  token?: string
  access_token?: string
  message?: string
}

export type LoginPayload = {
  login: string
  password: string
}

export type LoginResponse = {
  token?: string
  access_token?: string
  message?: string
}

export type ApiErrorResponse = {
  message?: string
  errors?: Record<string, string[]>
}