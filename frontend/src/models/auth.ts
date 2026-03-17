export type RegisterPayload = {
  login: string
  password: string
  password_confirmation: string
}

export type LoginPayload = {
  login: string
  password: string
}

export type UserDto = {
  user_id?: number
  login?: string
  role_id?: number
  created_at?: string | null
  updated_at?: string | null
}

export type AuthResponseDto = {
  access_token?: string
  token_type?: string
  user?: UserDto
}

export type User = {
  id: number
  login: string
  roleId: number | null
  createdAt: string | null
  updatedAt: string | null
}

export type AuthResult = {
  accessToken: string
  tokenType: string
  user: User | null
}

export type ApiErrorData = {
  message?: string
  errors?: Record<string, string[]>
}