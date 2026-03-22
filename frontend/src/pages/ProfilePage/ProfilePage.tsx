import { useEffect, useMemo, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { getCurrentUser, logoutUser } from '@api/auth'
import { ApiError } from '@api/http'
import { getAuthors, getFavorites, getGenres, getPublishers } from '@api/library'
import { BookList } from '@components/BookList'
import { FiltersPanel } from '@components/FiltersPanel'
import { Header } from '@components/Header'
import { Pagination } from '@components/Pagination'
import type { User } from '@models/auth'
import type { Author, Book, Genre, Publisher } from '@models/library'
import styles from './ProfilePage.module.css'

const DEFAULT_PER_PAGE = 15

const normalizeQuery = (value: string) => value.trim().toLowerCase()

const getRoleLabel = (roleId?: number | null) => {
  if (roleId === 1) {
    return 'Администратор'
  }

  return 'Читатель'
}

export const ProfilePage = () => {
  const navigate = useNavigate()

  const [favorites, setFavorites] = useState<Book[]>([])
  const [user, setUser] = useState<User | null>(null)
  const [authors, setAuthors] = useState<Author[]>([])
  const [genres, setGenres] = useState<Genre[]>([])
  const [publishers, setPublishers] = useState<Publisher[]>([])

  const [search, setSearch] = useState('')
  const [draftAuthorIds, setDraftAuthorIds] = useState<number[]>([])
  const [draftGenreIds, setDraftGenreIds] = useState<number[]>([])
  const [draftPublisherId, setDraftPublisherId] = useState<number | null>(null)
  const [draftYearFrom, setDraftYearFrom] = useState('')
  const [draftYearTo, setDraftYearTo] = useState('')
  const [isFilterOpen, setIsFilterOpen] = useState(false)

  const [appliedSearch, setAppliedSearch] = useState('')
  const [appliedAuthorIds, setAppliedAuthorIds] = useState<number[]>([])
  const [appliedGenreIds, setAppliedGenreIds] = useState<number[]>([])
  const [appliedPublisherId, setAppliedPublisherId] = useState<number | null>(null)
  const [appliedYearFrom, setAppliedYearFrom] = useState<number | undefined>(undefined)
  const [appliedYearTo, setAppliedYearTo] = useState<number | undefined>(undefined)

  const [page, setPage] = useState(1)
  const [perPage, setPerPage] = useState(DEFAULT_PER_PAGE)

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

        const [userData, favoritesData, authorsData, genresData, publishersData] = await Promise.all([
          getCurrentUser(token),
          getFavorites(token, { all: true }),
          getAuthors(),
          getGenres(),
          getPublishers(),
        ])

        setUser(userData)
        setFavorites(favoritesData.items)
        setAuthors(authorsData)
        setGenres(genresData)
        setPublishers(publishersData)
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

  const filteredFavorites = useMemo(() => {
    const query = normalizeQuery(appliedSearch)
    const selectedPublisherName =
      appliedPublisherId === null
        ? ''
        : normalizeQuery(publishers.find((publisher) => publisher.id === appliedPublisherId)?.name ?? '')

    return favorites.filter((book) => {
      const matchesSearch =
        query.length === 0 ||
        [book.title, book.author, book.genre, book.publisher, book.description]
          .join(' ')
          .toLowerCase()
          .includes(query)

      const matchesAuthor =
        appliedAuthorIds.length === 0 ||
        book.authors.some((author) => appliedAuthorIds.includes(author.id))

      const matchesGenre =
        appliedGenreIds.length === 0 ||
        book.genres.some((genre) => appliedGenreIds.includes(genre.id))

      const matchesPublisher =
        selectedPublisherName.length === 0 || normalizeQuery(book.publisher) === selectedPublisherName

      const matchesYearFrom =
        appliedYearFrom === undefined ||
        (book.publishedYear !== null && book.publishedYear >= appliedYearFrom)

      const matchesYearTo =
        appliedYearTo === undefined ||
        (book.publishedYear !== null && book.publishedYear <= appliedYearTo)

      return (
        matchesSearch &&
        matchesAuthor &&
        matchesGenre &&
        matchesPublisher &&
        matchesYearFrom &&
        matchesYearTo
      )
    })
  }, [
    favorites,
    publishers,
    appliedSearch,
    appliedAuthorIds,
    appliedGenreIds,
    appliedPublisherId,
    appliedYearFrom,
    appliedYearTo,
  ])

  const lastPage = Math.max(1, Math.ceil(filteredFavorites.length / perPage))

  useEffect(() => {
    if (page > lastPage) {
      setPage(lastPage)
    }
  }, [page, lastPage])

  const visibleBooks = useMemo(() => {
    const start = (page - 1) * perPage
    const end = start + perPage
    return filteredFavorites.slice(start, end)
  }, [filteredFavorites, page, perPage])

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

  const handleSearchSubmit = () => {
    const normalizedSearch = search.trim()

    if (normalizedSearch.length === 1) {
      setError('Поле поиска должно содержать минимум 2 символа')
      return
    }

    if (draftYearFrom && draftYearFrom.length < 4) {
      setError('Поле "Год от" должно содержать 4 цифры')
      return
    }

    if (draftYearTo && draftYearTo.length < 4) {
      setError('Поле "Год до" должно содержать 4 цифры')
      return
    }

    const nextYearFrom = draftYearFrom ? Number(draftYearFrom) : undefined
    const nextYearTo = draftYearTo ? Number(draftYearTo) : undefined

    if (nextYearFrom !== undefined && nextYearTo !== undefined && nextYearFrom > nextYearTo) {
      setError('Поле "Год от" не может быть больше поля "Год до"')
      return
    }

    setError('')
    setPage(1)
    setAppliedSearch(normalizedSearch)
    setAppliedAuthorIds(draftAuthorIds)
    setAppliedGenreIds(draftGenreIds)
    setAppliedPublisherId(draftPublisherId)
    setAppliedYearFrom(nextYearFrom)
    setAppliedYearTo(nextYearTo)
  }

  const handleClearDraftFilters = () => {
    setSearch('')
    setDraftAuthorIds([])
    setDraftGenreIds([])
    setDraftPublisherId(null)
    setDraftYearFrom('')
    setDraftYearTo('')
    setError('')
  }

  const handlePerPageChange = (value: number) => {
    setPerPage(value)
    setPage(1)
  }

  const hasAppliedFilters =
    Boolean(appliedSearch) ||
    appliedAuthorIds.length > 0 ||
    appliedGenreIds.length > 0 ||
    appliedPublisherId !== null ||
    appliedYearFrom !== undefined ||
    appliedYearTo !== undefined

  return (
    <main className={styles.profilePage}>
      <Header
        leftVariant="back"
        centerVariant="search"
        rightVariant="exit"
        searchValue={search}
        onSearchChange={(value) => {
          setSearch(value)
          setError('')
        }}
        onSearchClick={handleSearchSubmit}
        onFilterClick={() => setIsFilterOpen((current) => !current)}
        onBackClick={() => navigate(-1)}
        onExitClick={handleLogout}
      />

      <section className={styles.container}>
        {isFilterOpen ? (
          <div className={styles.filtersBlock}>
            <FiltersPanel
              authors={authors}
              genres={genres}
              publishers={publishers}
              authorIds={draftAuthorIds}
              genreIds={draftGenreIds}
              publisherId={draftPublisherId}
              yearFrom={draftYearFrom}
              yearTo={draftYearTo}
              onAuthorChange={(value) => {
                setDraftAuthorIds(value)
                setError('')
              }}
              onGenreChange={(value) => {
                setDraftGenreIds(value)
                setError('')
              }}
              onPublisherChange={(value) => {
                setDraftPublisherId(value)
                setError('')
              }}
              onYearFromChange={(value) => {
                setDraftYearFrom(value)
                setError('')
              }}
              onYearToChange={(value) => {
                setDraftYearTo(value)
                setError('')
              }}
              onClear={handleClearDraftFilters}
            />
          </div>
        ) : null}

        <section className={styles.hero}>
          <div className={styles.summaryCard}>
            <div className={styles.summaryMain}>
              <p className={styles.userCaption}>Профиль</p>
              <h1 className={styles.userLogin}>{user?.login ?? 'Пользователь'}</h1>
              <p className={styles.userRole}>{getRoleLabel(user?.roleId)}</p>

              {user?.roleId === 1 ? (
                <button
                  className={styles.adminButton}
                  type="button"
                  onClick={() => navigate('/admin')}
                >
                  Открыть админ-панель
                </button>
              ) : null}
            </div>

            <div className={styles.summaryDivider} />

            <div className={styles.summaryStats}>
              <span className={styles.statLabel}>Всего в избранном</span>
              <strong className={styles.statValue}>{favorites.length}</strong>
            </div>
          </div>
        </section>

        <section className={styles.section}>
          <div className={styles.sectionHeader}>
            <h2 className={styles.sectionTitle}>
              {hasAppliedFilters ? 'Результаты по избранному' : 'Избранное'}
            </h2>

            <Pagination
              currentPage={page}
              lastPage={lastPage}
              perPage={perPage}
              total={filteredFavorites.length}
              onPageChange={setPage}
              onPerPageChange={handlePerPageChange}
            />
          </div>

          {isLoading ? <p className={styles.state}>Загрузка...</p> : null}
          {isLogoutLoading ? <p className={styles.state}>Выход...</p> : null}
          {!isLoading && error ? <p className={styles.error}>{error}</p> : null}

          {!isLoading && !error && filteredFavorites.length === 0 ? (
            <p className={styles.state}>В избранном пока нет книг по выбранным параметрам</p>
          ) : null}

          {!isLoading && !error && visibleBooks.length > 0 ? (
            <BookList
              books={visibleBooks}
              onBookClick={(book) => navigate(`/library/books/${book.id}`)}
            />
          ) : null}
        </section>
      </section>
    </main>
  )
}