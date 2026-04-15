import { useEffect, useState, type ReactNode } from 'react'
import { Navigate } from 'react-router-dom'
import { getCurrentUser } from '@api/auth'

type AdminOnlyRouteProps = {
  children: ReactNode
}

type Status = 'checking' | 'guest' | 'forbidden' | 'admin'

export const AdminOnlyRoute = ({ children }: AdminOnlyRouteProps) => {
  const [status, setStatus] = useState<Status>('checking')

  useEffect(() => {
    let isCancelled = false

    const token = localStorage.getItem('token')

    if (!token) {
      setStatus('guest')
      return
    }

    const checkAccess = async () => {
      try {
        const user = await getCurrentUser(token)

        if (isCancelled) return

        if (!user) {
          localStorage.removeItem('token')
          setStatus('guest')
          return
        }

        if (user.roleId !== 1) {
          setStatus('forbidden')
          return
        }

        setStatus('admin')
      } catch {
        localStorage.removeItem('token')

        if (!isCancelled) {
          setStatus('guest')
        }
      }
    }

    void checkAccess()

    return () => {
      isCancelled = true
    }
  }, [])

  if (status === 'checking') {
    return null
  }

  if (status === 'guest' || status === 'forbidden') {
    return <Navigate to="/signin" replace />
  }

  return <>{children}</>
}
