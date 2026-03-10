import { useEffect, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { getBooks } from '@api/library'
import { ApiError, getCurrentUser, logoutUser } from '@api/auth'
import { BookList } from '@components/BookList'
import { Header } from '@components/Header'
import type { Book } from 'models/library'
import styles from './ProfilePage.module.css'

type User = {
  user_id: number
  login: string
  role_id: number
  created_at: string
  updated_at: string
}

export const ProfilePage = () => {
  const navigate = useNavigate()

  const [books, setBooks] = useState<Book[]>([])
  const [user, setUser] = useState<User | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const [isLogoutLoading, setIsLogoutLoading] = useState(false)
  const [error, setError] = useState('')

  useEffect(() => {
    const token = localStorage.getItem('token')

    if (!token) {
      navigate('/signin', { replace: true })
      return
    }

    const loadData = async () => {
      try {
        setIsLoading(true)
        setError('')

        const [userData, booksData] = await Promise.all([
          getCurrentUser(token),
          getBooks(),
        ])

        setUser(userData)
        setBooks(booksData)
      } catch (err) {
        localStorage.removeItem('token')

        if (err instanceof ApiError) {
          setError(err.message)
        } else {
          setError(err instanceof Error ? err.message : 'Произошла ошибка')
        }

        navigate('/signin', { replace: true })
      } finally {
        setIsLoading(false)
      }
    }

    void loadData()
  }, [navigate])

  const handleLogout = async () => {
    const token = localStorage.getItem('token')

    if (!token) {
      navigate('/signin', { replace: true })
      return
    }

    try {
      setIsLogoutLoading(true)
      await logoutUser(token)
    } catch {
      localStorage.removeItem('token')
      navigate('/signin', { replace: true })
      return
    }

    localStorage.removeItem('token')
    navigate('/signin', { replace: true })
  }

  return (
    <main className={styles.profilePage}>
      <Header
        showBackButton
        showSearch={false}
        showExit
        onBackClick={() => navigate(-1)}
        onExitClick={handleLogout}
      />

      <section className={styles.container}>
        {user ? <h2 className={styles.userLogin}>{user.login}</h2> : null}

        <section className={styles.section}>
          <h2 className={styles.sectionTitle}>Избранное</h2>

          {isLoading ? <p className={styles.state}>Загрузка...</p> : null}
          {isLogoutLoading ? <p className={styles.state}>Выход...</p> : null}
          {error ? <p className={styles.error}>{error}</p> : null}

          {!isLoading && !error ? <BookList books={books} /> : null}
        </section>
      </section>
    </main>
  )
}