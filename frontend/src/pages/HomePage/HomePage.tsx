import { useEffect, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { getAuthors, getBooks, getGenres } from '@api/library'
import { BookList } from '@components/BookList'
import { FiltersPanel } from '@components/FiltersPanel'
import { GenreList } from '@components/GenreList'
import { Header } from '@components/Header'
import { Pagination } from '@components/Pagination'
import type { Author, Book, Genre, GetBooksParams } from '@models/library'
import styles from './HomePage.module.css'

const DEFAULT_PER_PAGE = 15

type AppliedFilters = Pick<
  GetBooksParams,
  'search' | 'author_id' | 'genre_id' | 'year_from' | 'year_to'
>

export const HomePage = () => {
  const navigate = useNavigate()

  const [genres, setGenres] = useState<Genre[]>([])
  const [authors, setAuthors] = useState<Author[]>([])
  const [books, setBooks] = useState<Book[]>([])

  const [search, setSearch] = useState('')
  const [draftAuthorId, setDraftAuthorId] = useState('')
  const [draftGenreId, setDraftGenreId] = useState('')
  const [draftYearFrom, setDraftYearFrom] = useState('')
  const [draftYearTo, setDraftYearTo] = useState('')
  const [isFilterOpen, setIsFilterOpen] = useState(false)

  const [appliedFilters, setAppliedFilters] = useState<AppliedFilters>({})
  const [page, setPage] = useState(1)
  const [perPage, setPerPage] = useState(DEFAULT_PER_PAGE)
  const [total, setTotal] = useState(0)
  const [lastPage, setLastPage] = useState(1)

  const [isLookupsLoading, setIsLookupsLoading] = useState(true)
  const [isBooksLoading, setIsBooksLoading] = useState(true)

  const [lookupError, setLookupError] = useState('')
  const [booksError, setBooksError] = useState('')
  const [validationError, setValidationError] = useState('')

  useEffect(() => {
    const loadLookups = async () => {
      try {
        setIsLookupsLoading(true)
        setLookupError('')

        const [genresData, authorsData] = await Promise.all([getGenres(), getAuthors()])

        setGenres(genresData)
        setAuthors(authorsData)
      } catch (err) {
        setLookupError(err instanceof Error ? err.message : 'Произошла ошибка при загрузке фильтров')
      } finally {
        setIsLookupsLoading(false)
      }
    }

    void loadLookups()
  }, [])

  useEffect(() => {
    const loadBooks = async () => {
      try {
        setIsBooksLoading(true)
        setBooksError('')

        const booksResponse = await getBooks({
          page,
          per_page: perPage,
          search: appliedFilters.search,
          author_id: appliedFilters.author_id,
          genre_id: appliedFilters.genre_id,
          year_from: appliedFilters.year_from,
          year_to: appliedFilters.year_to,
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
  }, [page, perPage, appliedFilters])

  const handleSearchSubmit = () => {
    const normalizedSearch = search.trim()

    if (normalizedSearch.length === 1) {
      setValidationError('Поле поиска должно содержать минимум 2 символа')
      return
    }

    if (draftYearFrom && draftYearFrom.length < 4) {
      setValidationError('Поле "Год от" должно содержать 4 цифры')
      return
    }

    if (draftYearTo && draftYearTo.length < 4) {
      setValidationError('Поле "Год до" должно содержать 4 цифры')
      return
    }

    const nextYearFrom = draftYearFrom ? Number(draftYearFrom) : undefined
    const nextYearTo = draftYearTo ? Number(draftYearTo) : undefined

    if (
      nextYearFrom !== undefined &&
      nextYearTo !== undefined &&
      nextYearFrom > nextYearTo
    ) {
      setValidationError('Поле "Год от" не может быть больше поля "Год до"')
      return
    }

    setValidationError('')
    setPage(1)
    setAppliedFilters({
      search: normalizedSearch || undefined,
      author_id: draftAuthorId ? Number(draftAuthorId) : undefined,
      genre_id: draftGenreId ? Number(draftGenreId) : undefined,
      year_from: nextYearFrom,
      year_to: nextYearTo,
    })
  }

  const handlePerPageChange = (value: number) => {
    setPerPage(value)
    setPage(1)
  }

  const handleClearDraftFilters = () => {
    setSearch('')
    setDraftAuthorId('')
    setDraftGenreId('')
    setDraftYearFrom('')
    setDraftYearTo('')
    setValidationError('')
  }

  const isLoading = isLookupsLoading || isBooksLoading
  const error = validationError || booksError || lookupError
  const hasAppliedFilters =
    Boolean(appliedFilters.search) ||
    Boolean(appliedFilters.author_id) ||
    Boolean(appliedFilters.genre_id) ||
    Boolean(appliedFilters.year_from) ||
    Boolean(appliedFilters.year_to)

  return (
    <main className={styles.homePage}>
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
        onFilterClick={() => setIsFilterOpen((current) => !current)}
        onProfileClick={() => navigate('/profile')}
      />

      <section className={styles.container}>
        {isFilterOpen ? (
          <div className={styles.filtersBlock}>
            <FiltersPanel
              authors={authors}
              genres={genres}
              authorId={draftAuthorId}
              genreId={draftGenreId}
              yearFrom={draftYearFrom}
              yearTo={draftYearTo}
              onAuthorChange={(value) => {
                setDraftAuthorId(value)
                setValidationError('')
              }}
              onGenreChange={(value) => {
                setDraftGenreId(value)
                setValidationError('')
              }}
              onYearFromChange={(value) => {
                setDraftYearFrom(value)
                setValidationError('')
              }}
              onYearToChange={(value) => {
                setDraftYearTo(value)
                setValidationError('')
              }}
              onClear={handleClearDraftFilters}
            />
          </div>
        ) : null}

        {isLoading ? <p className={styles.state}>Загрузка...</p> : null}
        {error ? <p className={styles.error}>{error}</p> : null}

        {!isLoading && !error ? (
          <>
            <section className={styles.section}>
              <h2 className={styles.sectionTitle}>Жанры</h2>
              <GenreList
                genres={genres}
                onGenreClick={(genre) => navigate(`/library/genres/${genre.id}`)}
              />
            </section>

            <section className={styles.section}>
              <div className={styles.sectionHeader}>
                <h2 className={styles.sectionTitle}>
                  {hasAppliedFilters ? 'Результаты поиска' : 'Каталог'}
                </h2>

                <Pagination
                  currentPage={page}
                  lastPage={lastPage}
                  perPage={perPage}
                  total={total}
                  onPageChange={setPage}
                  onPerPageChange={handlePerPageChange}
                />
              </div>

              {books.length > 0 ? (
                <BookList
                  books={books}
                  onBookClick={(book) => navigate(`/library/books/${book.id}`)}
                />
              ) : (
                <p className={styles.state}>По выбранным параметрам книги не найдены</p>
              )}
            </section>
          </>
        ) : null}
      </section>
    </main>
  )
}