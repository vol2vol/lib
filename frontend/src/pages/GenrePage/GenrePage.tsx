import { useEffect, useMemo, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { getBooks, getGenres } from '@api/library'
import { BookCard } from '@components/BookCard'
import { Header } from '@components/Header'
import type { Book, Genre } from '@models/library'
import styles from './GenrePage.module.css'

export const GenrePage = () => {
  const navigate = useNavigate()
  const { genreId } = useParams<{ genreId: string }>()

  const [genre, setGenre] = useState<Genre | null>(null)
  const [books, setBooks] = useState<Book[]>([])
  const [search, setSearch] = useState('')
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState('')

  useEffect(() => {
    const parsedGenreId = Number(genreId)

    if (!genreId || Number.isNaN(parsedGenreId)) {
      navigate('/library', { replace: true })
      return
    }

    const loadData = async () => {
      try {
        setIsLoading(true)
        setError('')

        const [genresData, booksData] = await Promise.all([
          getGenres(),
          getBooks({ genre_id: parsedGenreId }),
        ])

        const currentGenre = genresData.find((item) => item.id === parsedGenreId) ?? null

        setGenre(currentGenre)
        setBooks(booksData.items)

        if (!currentGenre) {
          setError('Жанр не найден')
        }
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Произошла ошибка при загрузке данных')
      } finally {
        setIsLoading(false)
      }
    }

    void loadData()
  }, [genreId, navigate])

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
    <main className={styles.genrePage}>
      <Header
        leftVariant="logo"
        centerVariant="search"
        rightVariant="profile"
        searchValue={search}
        onSearchChange={setSearch}
        onProfileClick={() => navigate('/profile')}
      />

      <section className={styles.container}>
        <h1 className={styles.title}>{genre?.name ?? 'Жанр'}</h1>

        {isLoading ? <p className={styles.state}>Загрузка...</p> : null}
        {error ? <p className={styles.error}>{error}</p> : null}

        {!isLoading && !error ? (
          filteredBooks.length > 0 ? (
            <div className={styles.grid}>
              {filteredBooks.map((book) => (
                <BookCard
                  key={book.id}
                  book={book}
                  onClick={() => navigate(`/library/books/${book.id}`)}
                />
              ))}
            </div>
          ) : (
            <p className={styles.state}>В этом жанре пока нет книг</p>
          )
        ) : null}
      </section>
    </main>
  )
}