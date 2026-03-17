import { useEffect, useMemo, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { getBooks, getGenres } from '@api/library'
import { BookList } from '@components/BookList'
import { GenreList } from '@components/GenreList'
import { Header } from '@components/Header'
import type { Book, Genre } from 'models/library'
import styles from './HomePage.module.css'

export const HomePage = () => {
  const navigate = useNavigate()

  const [genres, setGenres] = useState<Genre[]>([])
  const [books, setBooks] = useState<Book[]>([])
  const [search, setSearch] = useState('')
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState('')

  useEffect(() => {
    const loadData = async () => {
      try {
        setIsLoading(true)
        setError('')

        const [genresData, booksResponse] = await Promise.all([getGenres(), getBooks()])

        setGenres(genresData)
        setBooks(booksResponse.items)
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Произошла ошибка при загрузке данных')
      } finally {
        setIsLoading(false)
      }
    }

    void loadData()
  }, [])

  const filteredBooks = useMemo(() => {
    const query = search.trim().toLowerCase()

    if (!query) {
      return books
    }

    return books.filter((book) =>
      [book.title, book.author, book.genre, book.publisher]
        .join(' ')
        .toLowerCase()
        .includes(query)
    )
  }, [books, search])

  return (
    <main className={styles.homePage}>
      <Header
        searchValue={search}
        onSearchChange={setSearch}
        onProfileClick={() => navigate('/profile')}
      />

      <section className={styles.container}>
        {isLoading ? <p className={styles.state}>Загрузка...</p> : null}
        {error ? <p className={styles.error}>{error}</p> : null}

        {!isLoading && !error ? (
          <>
            <section className={styles.section}>
              <h2 className={styles.sectionTitle}>Жанры</h2>
              <GenreList genres={genres} />
            </section>

            <section className={styles.section}>
              <h2 className={styles.sectionTitle}>Популярное</h2>
              <BookList books={filteredBooks} />
            </section>
          </>
        ) : null}
      </section>
    </main>
  )
}