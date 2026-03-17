import { useEffect, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { getCurrentUser, logoutUser } from '@api/auth'
import { ApiError } from '@api/http'
import { getBooks } from '@api/library'
import { BookList } from '@components/BookList'
import { Header } from '@components/Header'
import type { User } from '@models/auth'
import type { Book } from '@models/library'
import styles from './ProfilePage.module.css'

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
        setBooks(booksData.items)
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
    } finally {
      setIsLogoutLoading(false)
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

          {!isLoading && !error ? (
            <BookList
              books={books}
              onBookClick={(book) => navigate(`/library/books/${book.id}`)}
            />
          ) : null}
        </section>
      </section>
    </main>
  )
}