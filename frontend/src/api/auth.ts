import { buildUrl, createHeaders, parseResponse } from './http'
import type {
  AuthResponseDto,
  AuthResult,
  LoginPayload,
  RegisterPayload,
  User,
  UserDto,
} from '@models/auth'

const mapUser = (user?: UserDto | null): User | null => {
  if (!user) {
    return null
  }

  return {
    id: user.user_id ?? 0,
    login: user.login ?? '',
    roleId: user.role_id ?? null,
    createdAt: user.created_at ?? null,
    updatedAt: user.updated_at ?? null,
  }
}

const mapAuthResult = (data: AuthResponseDto): AuthResult => ({
  accessToken: data.access_token ?? '',
  tokenType: data.token_type ?? 'Bearer',
  user: mapUser(data.user),
})

export async function registerUser(payload: RegisterPayload): Promise<AuthResult> {
  const response = await fetch(buildUrl('/register'), {
    method: 'POST',
    headers: createHeaders(undefined, {
      'Content-Type': 'application/json',
    }),
    body: JSON.stringify(payload),
  })

  const data = await parseResponse<AuthResponseDto>(response)
  return mapAuthResult(data)
}

export async function loginUser(payload: LoginPayload): Promise<AuthResult> {
  const response = await fetch(buildUrl('/login'), {
    method: 'POST',
    headers: createHeaders(undefined, {
      'Content-Type': 'application/json',
    }),
    body: JSON.stringify(payload),
  })

  const data = await parseResponse<AuthResponseDto>(response)
  return mapAuthResult(data)
}

export async function logoutUser(token: string): Promise<void> {
  const response = await fetch(buildUrl('/logout'), {
    method: 'POST',
    headers: createHeaders(token),
  })

  await parseResponse<void>(response)
}

export async function getCurrentUser(token: string): Promise<User | null> {
  const response = await fetch(buildUrl('/user'), {
    method: 'GET',
    headers: createHeaders(token),
  })

  const data = await parseResponse<UserDto>(response)
  return mapUser(data)
}