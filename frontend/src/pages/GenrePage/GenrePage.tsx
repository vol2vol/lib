import { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { getBooks, getGenres } from '@api/library'
import { BookList } from '@components/BookList'
import { Header } from '@components/Header'
import { Pagination } from '@components/Pagination'
import type { Book, Genre } from '@models/library'
import styles from './GenrePage.module.css'

const DEFAULT_PER_PAGE = 15

export const GenrePage = () => {
  const navigate = useNavigate()
  const { genreId } = useParams<{ genreId: string }>()

  const [genre, setGenre] = useState<Genre | null>(null)
  const [search, setSearch] = useState('')
  const [appliedSearch, setAppliedSearch] = useState<string | undefined>(undefined)

  const [books, setBooks] = useState<Book[]>([])
  const [page, setPage] = useState(1)
  const [perPage, setPerPage] = useState(DEFAULT_PER_PAGE)
  const [total, setTotal] = useState(0)
  const [lastPage, setLastPage] = useState(1)

  const [isGenreLoading, setIsGenreLoading] = useState(true)
  const [isBooksLoading, setIsBooksLoading] = useState(true)

  const [genreError, setGenreError] = useState('')
  const [booksError, setBooksError] = useState('')
  const [validationError, setValidationError] = useState('')

  useEffect(() => {
    const parsedGenreId = Number(genreId)

    if (!genreId || Number.isNaN(parsedGenreId)) {
      navigate('/library', { replace: true })
      return
    }

    const loadGenre = async () => {
      try {
        setIsGenreLoading(true)
        setGenreError('')

        const genresData = await getGenres()
        const currentGenre = genresData.find((item) => item.id === parsedGenreId) ?? null

        setGenre(currentGenre)

        if (!currentGenre) {
          setGenreError('Жанр не найден')
        }
      } catch (err) {
        setGenreError(err instanceof Error ? err.message : 'Произошла ошибка при загрузке жанра')
      } finally {
        setIsGenreLoading(false)
      }
    }

    void loadGenre()
  }, [genreId, navigate])

  useEffect(() => {
    const parsedGenreId = Number(genreId)

    if (!genreId || Number.isNaN(parsedGenreId)) {
      return
    }

    const loadBooks = async () => {
      try {
        setIsBooksLoading(true)
        setBooksError('')

        const booksResponse = await getBooks({
          genre_id: parsedGenreId,
          page,
          per_page: perPage,
          search: appliedSearch,
        })

        setBooks(booksResponse.items)
        setTotal(booksResponse.total)
        setLastPage(booksResponse.lastPage)
      } catch (err) {
        setBooksError(err instanceof Error ? err.message : 'Произошла ошибка при загрузке книг')
      } finally {
        setIsBooksLoading(false)
      }
    }

    void loadBooks()
  }, [genreId, page, perPage, appliedSearch])

  const handleSearchSubmit = () => {
    const normalizedSearch = search.trim()

    if (normalizedSearch.length === 1) {
      setValidationError('Поле поиска должно содержать минимум 2 символа')
      return
    }

    setValidationError('')
    setPage(1)
    setAppliedSearch(normalizedSearch || undefined)
  }

  const handlePerPageChange = (value: number) => {
    setPerPage(value)
    setPage(1)
  }

  const isLoading = isGenreLoading || isBooksLoading
  const error = validationError || booksError || genreError

  return (
    <main className={styles.genrePage}>
      <Header
        leftVariant="logo"
        centerVariant="search"
        rightVariant="profile"
        searchValue={search}
        onSearchChange={(value) => {
          setSearch(value)
          setValidationError('')
        }}
        onSearchClick={handleSearchSubmit}
        onProfileClick={() => navigate('/profile')}
      />

      <section className={styles.container}>
        <section className={styles.section}>
          <div className={styles.sectionHeader}>
            <h1 className={styles.sectionTitle}>{genre?.name ?? 'Жанр'}</h1>

            <Pagination
              currentPage={page}
              lastPage={lastPage}
              perPage={perPage}
              total={total}
              onPageChange={setPage}
              onPerPageChange={handlePerPageChange}
            />
          </div>

          {isLoading ? <p className={styles.state}>Загрузка...</p> : null}
          {error ? <p className={styles.error}>{error}</p> : null}

          {!isLoading && !error ? (
            books.length > 0 ? (
              <BookList
                books={books}
                onBookClick={(book) => navigate(`/library/books/${book.id}`)}
              />
            ) : (
              <p className={styles.state}>В этом жанре книги не найдены</p>
            )
          ) : null}
        </section>
      </section>
    </main>
  )
}