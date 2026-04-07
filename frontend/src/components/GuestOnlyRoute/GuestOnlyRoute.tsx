import { useEffect, useState, type ReactNode } from 'react'
import { Navigate } from 'react-router-dom'
import { getCurrentUser } from '@api/auth'

type GuestOnlyRouteProps = {
  children: ReactNode
}

export const GuestOnlyRoute = ({ children }: GuestOnlyRouteProps) => {
  const [status, setStatus] = useState<'checking' | 'guest' | 'authorized'>('checking')

  useEffect(() => {
    let isCancelled = false

    const token = localStorage.getItem('token')

    if (!token) {
      setStatus('guest')
      return
    }

    const checkCurrentUser = async () => {
      try {
        const user = await getCurrentUser(token)

        if (isCancelled) {
          return
        }

        if (user) {
          setStatus('authorized')
          return
        }

        localStorage.removeItem('token')
        setStatus('guest')
      } catch {
        localStorage.removeItem('token')

        if (!isCancelled) {
          setStatus('guest')
        }
      }
    }

    void checkCurrentUser()

    return () => {
      isCancelled = true
    }
  }, [])

  if (status === 'checking') {
    return null
  }

  if (status === 'authorized') {
    return <Navigate to="/library" replace />
  }

  return <>{children}</>
}
