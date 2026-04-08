// AdminPage.tsx
import { useEffect, useMemo, useState, useCallback } from 'react'
import { useNavigate } from 'react-router-dom'
import { getCurrentUser } from '@api/auth'
import {
  getAllBooks,
  getAdminGenres,
  getAdminAuthors,
  getAdminPublishers,
  createPublisher,
  updatePublisher,
  deletePublisher,
  createBook,
  updateBook,
  deleteBook,
  getBookById,
  createAuthor,
  updateAuthor,
  deleteAuthor,
  createGenre,
  updateGenre,
  deleteGenre,
  getBooks,
} from '@api/library'
import { Header } from '@components/Header'
import { FiltersPanel } from '@components/FiltersPanel'
import { Pagination } from '@components/Pagination'
import type { Book, Genre, Author, Publisher, PublisherFormPayload, AuthorFormPayload, GenreFormPayload, GetBooksParams } from '@models/library'
import type { BookFormPayload } from '@models/library'
import type { User } from '@models/auth'
import styles from './AdminPage.module.css'
import { Modal } from '@components/Modal/Modal'

type TabType = 'genres' | 'authors' | 'publishers' | 'books'

type GenreFormState = {
  name: string;
}

type AuthorFormState = {
  first_name: string;
  last_name: string;
  middle_name: string | null;
}

type PublisherFormState = {
  name: string;
}

type BookFormState = {
  title: string
  description: string
  authors: string[]
  genres: string[]
  publisher: string
  publishedYear: string
  coverFile: File | null
  files: File[]
}

type AppliedFilters = Pick<
  GetBooksParams,
  'search' | 'author_ids' | 'genre_ids' | 'publisher_id' | 'year_from' | 'year_to'
>

const DEFAULT_PER_PAGE = 15
const MAX_TEXT_LENGTH = 255
const MAX_SEARCH_LENGTH = 100
const MIN_BOOK_YEAR = 1800
const CURRENT_YEAR = new Date().getFullYear()
const MAX_COVER_BYTES = 5 * 1024 * 1024
const MAX_BOOK_FILE_BYTES = 50 * 1024 * 1024
const AUTHOR_NAME_ALLOWED_PATTERN = /[^A-Za-zА-Яа-яЁё\s'-]/g
const IMAGE_EXTENSIONS = new Set(['jpg', 'jpeg', 'png', 'gif'])
const BOOK_FILE_EXTENSIONS = new Set(['pdf', 'fb2', 'txt'])

const normalizeSingleLine = (value: string, maxLength = MAX_TEXT_LENGTH) =>
  value
    .replace(/[\r\n\t]+/g, ' ')
    .replace(/\s{2,}/g, ' ')
    .replace(/^\s+/, '')
    .slice(0, maxLength)

const normalizeAuthorInput = (value: string) =>
  normalizeSingleLine(value).replace(AUTHOR_NAME_ALLOWED_PATTERN, '')

const normalizeSearchInput = (value: string) =>
  value.replace(/[\r\n\t]+/g, ' ').slice(0, MAX_SEARCH_LENGTH)

const normalizeYearInput = (value: string) => value.replace(/\D+/g, '').slice(0, 4)

const getFileExtension = (fileName: string) => fileName.split('.').pop()?.toLowerCase() ?? ''

const validateCoverFile = (file: File) => {
  const extension = getFileExtension(file.name)

  if (!IMAGE_EXTENSIONS.has(extension)) {
    return 'Обложка должна быть в формате JPG, JPEG, PNG или GIF'
  }

  if (file.size > MAX_COVER_BYTES) {
    return 'Размер обложки не должен превышать 5 МБ'
  }

  return ''
}

const validateBookAttachment = (file: File) => {
  const extension = getFileExtension(file.name)

  if (!BOOK_FILE_EXTENSIONS.has(extension)) {
    return `Файл "${file.name}" должен быть в формате PDF, FB2 или TXT`
  }

  if (file.size > MAX_BOOK_FILE_BYTES) {
    return `Файл "${file.name}" превышает лимит 50 МБ`
  }

  return ''
}

const initialGenreFormState: GenreFormState = {
  name: '',
}

const initialAuthorFormState: AuthorFormState = {
  first_name: '',
  middle_name: '',
  last_name: '',
}

const initialPublisherFormState: PublisherFormState = {
  name: '',
}

const initialFormState: BookFormState = {
  title: '',
  description: '',
  authors: [],
  genres: [],
  publisher: '',
  publishedYear: '',
  coverFile: null,
  files: [],
}

export const AdminPage = () => {
  const navigate = useNavigate()
  const [activeTab, setActiveTab] = useState<TabType>('books')
  const [user, setUser] = useState<User | null>(null)
  const [authors, setAuthors] = useState<Author[]>([])
  const [genres, setGenres] = useState<Genre[]>([])
  const [publishers, setPublishers] = useState<Publisher[]>([])
  const [books, setBooks] = useState<Book[]>([])
  
  // Фильтры для книг
  const [search, setSearch] = useState('')
  const [draftAuthorIds, setDraftAuthorIds] = useState<number[]>([])
  const [draftGenreIds, setDraftGenreIds] = useState<number[]>([])
  const [draftPublisherId, setDraftPublisherId] = useState<number | null>(null)
  const [draftYearFrom, setDraftYearFrom] = useState('')
  const [draftYearTo, setDraftYearTo] = useState('')
  const [isFilterOpen, setIsFilterOpen] = useState(false)
  const [appliedFilters, setAppliedFilters] = useState<AppliedFilters>({})
  const [page, setPage] = useState(1)
  const [perPage, setPerPage] = useState(DEFAULT_PER_PAGE)
  const [total, setTotal] = useState(0)
  const [lastPage, setLastPage] = useState(1)
  const [isBooksLoading, setIsBooksLoading] = useState(true)
  const [booksError, setBooksError] = useState('')
  const [validationError, setValidationError] = useState('')
  
  const [selectedGenre, setSelectedGenre] = useState<Genre | null>(null)
  const [selectedAuthor, setSelectedAuthor] = useState<Author | null>(null)
  const [selectedPublisher, setSelectedPublisher] = useState<Publisher | null>(null)
  const [selectedBook, setSelectedBook] = useState<Book | null>(null)
  const [genreForm, setGenreForm] = useState<GenreFormState>(initialGenreFormState)
  const [authorForm, setAuthorForm] = useState<AuthorFormState>(initialAuthorFormState)
  const [publisherForm, setPublisherForm] = useState<PublisherFormState>(initialPublisherFormState)
  const [form, setForm] = useState<BookFormState>(initialFormState)
  const [error, setError] = useState('')
  const [successMessage, setSuccessMessage] = useState('')
  const [isLoading, setIsLoading] = useState(true)
  const [isGenreSaving, setIsGenreSaving] = useState(false)
  const [isAuthorSaving, setIsAuthorSaving] = useState(false)
  const [isPublisherSaving, setIsPublisherSaving] = useState(false)
  const [isSaving, setIsSaving] = useState(false)
  const [isLookupsLoading, setIsLookupsLoading] = useState(true)

  const [isGenreModalOpen, setIsGenreModalOpen] = useState(false);
  const [isAuthorModalOpen, setIsAuthorModalOpen] = useState(false);
  const [isPublisherModalOpen, setIsPublisherModalOpen] = useState(false);
  const [isBookModalOpen, setIsBookModalOpen] = useState(false);
  const [modalMode, setModalMode] = useState<'create' | 'edit'>('create');

  const token = localStorage.getItem('token')

  const loadLookups = useCallback(async () => {
    if (!token) return
    
    try {
      setIsLookupsLoading(true)
      const [genresData, authorsData, publishersData] = await Promise.all([
        getAdminGenres(token),
        getAdminAuthors(token),
        getAdminPublishers(token),
      ])
      setGenres(genresData)
      setAuthors(authorsData)
      setPublishers(publishersData)
    } catch (err) {
      console.error('Error loading lookups:', err)
    } finally {
      setIsLookupsLoading(false)
    }
  }, [token])

  const loadBooks = useCallback(async () => {
    if (!token) return
    
    try {
      setIsBooksLoading(true)
      setBooksError('')
      const booksResponse = await getBooks({
        page,
        per_page: perPage,
        search: appliedFilters.search,
        author_ids: appliedFilters.author_ids,
        genre_ids: appliedFilters.genre_ids,
        publisher_id: appliedFilters.publisher_id,
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
  }, [token, page, perPage, appliedFilters])

  const loadData = useCallback(async () => {
    if (!token) {
      navigate('/signin', { replace: true })
      return
    }

    try {
      setIsLoading(true)
      setError('')
      const currentUser = await getCurrentUser(token)
      if (!currentUser) {
        localStorage.removeItem('token')
        navigate('/signin', { replace: true })
        return
      }

      if (currentUser.roleId !== 1) {
        navigate('/profile', { replace: true })
        return
      }

      setUser(currentUser)
      await loadLookups()
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Произошла ошибка при загрузке данных'
      if (errorMessage.includes('401') || errorMessage.includes('Unauthorized') || errorMessage.includes('токен')) {
        localStorage.removeItem('token')
        navigate('/signin', { replace: true })
      } else {
        setError(errorMessage)
      }
    } finally {
      setIsLoading(false)
    }
  }, [navigate, token, loadLookups])

  useEffect(() => {
    void loadData()
  }, [loadData])

  useEffect(() => {
    if (activeTab === 'books') {
      void loadBooks()
    }
  }, [activeTab, loadBooks])

  const ensureAdminAccess = () => {
    if (!token) {
      navigate('/signin', { replace: true })
      return false
    }
    if (user?.roleId !== 1) {
      navigate('/profile', { replace: true })
      return false
    }
    return true
  }

  const handleCoverInputChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0] ?? null

    if (!file) {
      setForm((prev) => ({ ...prev, coverFile: null }))
      return
    }

    const validationMessage = validateCoverFile(file)

    if (validationMessage) {
      event.target.value = ''
      setForm((prev) => ({ ...prev, coverFile: null }))
      setError(validationMessage)
      return
    }

    setError('')
    setForm((prev) => ({ ...prev, coverFile: file }))
  }

  const handleFilesInputChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    const selectedFiles = event.target.files ? Array.from(event.target.files) : []

    if (selectedFiles.length === 0) {
      setForm((prev) => ({ ...prev, files: [] }))
      return
    }

    const validFiles: File[] = []
    let firstError = ''

    selectedFiles.forEach((file) => {
      const validationMessage = validateBookAttachment(file)

      if (validationMessage) {
        if (!firstError) {
          firstError = validationMessage
        }
        return
      }

      validFiles.push(file)
    })

    if (firstError) {
      setError(firstError)
    } else {
      setError('')
    }

    setForm((prev) => ({ ...prev, files: validFiles }))
  }

  // Функции для открытия модальных окон
  const openGenreModal = (genre?: Genre) => {
    if (genre) {
      setSelectedGenre(genre);
      setGenreForm({ name: genre.name });
      setModalMode('edit');
    } else {
      setSelectedGenre(null);
      setGenreForm(initialGenreFormState);
      setModalMode('create');
    }
    setIsGenreModalOpen(true);
  };

  const openAuthorModal = (author?: Author) => {
    if (author) {
      setSelectedAuthor(author);
      setAuthorForm({
        first_name: author.firstName,
        middle_name: author.middleName,
        last_name: author.lastName,
      });
      setModalMode('edit');
    } else {
      setSelectedAuthor(null);
      setAuthorForm(initialAuthorFormState);
      setModalMode('create');
    }
    setIsAuthorModalOpen(true);
  };

  const openPublisherModal = (publisher?: Publisher) => {
    if (publisher) {
      setSelectedPublisher(publisher);
      setPublisherForm({ name: publisher.name });
      setModalMode('edit');
    } else {
      setSelectedPublisher(null);
      setPublisherForm(initialPublisherFormState);
      setModalMode('create');
    }
    setIsPublisherModalOpen(true);
  };

  const openBookModal = (book?: Book) => {
    if (book) {
      setSelectedBook(book);
      // Загружаем полное описание книги
      if (token) {
        getBookById(book.id, token).then(fullBook => {
          setForm({
            title: fullBook.title,
            description: fullBook.description,
            authors: fullBook.authors.map((author) => author.id.toString()),
            genres: fullBook.genres.map((genre) => genre.id.toString()),
            publisher: fullBook.publisher.id.toString(),
            publishedYear: fullBook.publishedYear ? String(fullBook.publishedYear) : '',
            coverFile: null,
            files: [],
          });
        });
      }
      setModalMode('edit');
    } else {
      setSelectedBook(null);
      setForm(initialFormState);
      setModalMode('create');
    }
    setIsBookModalOpen(true);
  };

  const closeAllModals = () => {
    setIsGenreModalOpen(false);
    setIsAuthorModalOpen(false);
    setIsPublisherModalOpen(false);
    setIsBookModalOpen(false);
    setError('');
    setSuccessMessage('');
  };

  const handleTabChange = (tab: TabType) => {
    setActiveTab(tab)
    // Сбрасываем выделенные элементы при смене вкладки
    setSelectedGenre(null)
    setSelectedAuthor(null)
    setSelectedPublisher(null)
    setSelectedBook(null)
    setError('')
    setSuccessMessage('')
    setValidationError('')
    // Сбрасываем формы
    setGenreForm(initialGenreFormState)
    setAuthorForm(initialAuthorFormState)
    setPublisherForm(initialPublisherFormState)
    setForm(initialFormState)
    
    // Если переключаемся на вкладку книг, загружаем их
    if (tab === 'books') {
      setPage(1)
      setAppliedFilters({})
      setSearch('')
      setDraftAuthorIds([])
      setDraftGenreIds([])
      setDraftPublisherId(null)
      setDraftYearFrom('')
      setDraftYearTo('')
    }
  }

  const handleSearchSubmit = () => {
    if (activeTab !== 'books') {
      // Для других вкладок используем простой поиск по названию
      return
    }
    
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

    if (nextYearFrom !== undefined && nextYearTo !== undefined && nextYearFrom > nextYearTo) {
      setValidationError('Поле "Год от" не может быть больше поля "Год до"')
      return
    }

    setValidationError('')
    setPage(1)
    setAppliedFilters({
      search: normalizedSearch || undefined,
      author_ids: draftAuthorIds.length > 0 ? draftAuthorIds : undefined,
      genre_ids: draftGenreIds.length > 0 ? draftGenreIds : undefined,
      publisher_id: draftPublisherId ?? undefined,
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
    setDraftAuthorIds([])
    setDraftGenreIds([])
    setDraftPublisherId(null)
    setDraftYearFrom('')
    setDraftYearTo('')
    setValidationError('')
  }

  // Простой поиск для других вкладок (по названию)
  const filterBySearch = <T extends { name?: string; fullName?: string }>(
    items: T[],
    searchTerm: string
  ): T[] => {
    if (!searchTerm.trim()) return items
    const query = searchTerm.trim().toLowerCase()
    return items.filter(item => {
      const name = (item.name || item.fullName || '').toLowerCase()
      return name.includes(query)
    })
  }

  const filteredGenres = useMemo(() => {
    if (activeTab !== 'genres') return genres
    return filterBySearch(genres, search)
  }, [genres, search, activeTab])

  const filteredAuthors = useMemo(() => {
    if (activeTab !== 'authors') return authors
    return filterBySearch(authors, search)
  }, [authors, search, activeTab])

  const filteredPublishers = useMemo(() => {
    if (activeTab !== 'publishers') return publishers
    return filterBySearch(publishers, search)
  }, [publishers, search, activeTab])

  const handleSelectBook = async (book: Book) => {
    if (!ensureAdminAccess()) return
    if (!token) return
    setSelectedBook(book)
    setForm({
      title: book.title,
      description: (await getBookById(book.id, token)).description,
      authors: book.authors.map((author) => author.id.toString()),
      genres: book.genres.map((genre) => genre.id.toString()),
      publisher: book.publisher.id.toString(),
      publishedYear: book.publishedYear ? String(book.publishedYear) : '',
      coverFile: null,
      files: [],
    })
    setSuccessMessage('')
    setError('')
  }

  const handleCreateNew = () => {
    if (!ensureAdminAccess()) return
    setSelectedBook(null)
    setForm(initialFormState)
    setSuccessMessage('')
    setError('')
  }

  const handleDelete = async (bookId: number) => {
    if (!ensureAdminAccess()) return
    if (!token) return
    if (!window.confirm('Удалить книгу?')) return

    try {
      setIsLoading(true)
      setError('')
      setSuccessMessage('')
      await deleteBook(bookId, token)
      setSuccessMessage('Книга успешно удалена.')
      await loadBooks()
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Произошла ошибка при удалении')
    } finally {
      setIsLoading(false)
    }
  }

  const handleSelectAuthor = (author: Author) => {
    if (!ensureAdminAccess()) return
    if (!token) return

    setSelectedAuthor(author)
    setAuthorForm({
      first_name: author.firstName,
      middle_name: author.middleName,
      last_name: author.lastName,
    })
    setSuccessMessage('')
    setError('')
  }

  const handleDeleteAuthor = async (authorId: number) => {
    if (!ensureAdminAccess()) return
    if (!token) return
    if (!window.confirm('Удалить автора?')) return

    try {
      setIsLoading(true)
      setError('')
      setSuccessMessage('')
      await deleteAuthor(authorId, token)
      setSuccessMessage('Автор успешно удален.')
      await loadLookups()
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Произошла ошибка при удалении')
    } finally {
      setIsLoading(false)
    }
  }

  const handleSelectGenre = (genre: Genre) => {
    if (!ensureAdminAccess()) return
    if (!token) return

    setSelectedGenre(genre)
    setGenreForm({
      name: genre.name
    })
    setSuccessMessage('')
    setError('')
  }

  const handleDeleteGenre = async (genreId: number) => {
    if (!ensureAdminAccess()) return
    if (!token) return
    if (!window.confirm('Удалить жанр?')) return

    try {
      setIsLoading(true)
      setError('')
      setSuccessMessage('')
      await deleteGenre(genreId, token)
      setSuccessMessage('Жанр успешно удален.')
      await loadLookups()
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Произошла ошибка при удалении')
    } finally {
      setIsLoading(false)
    }
  }

  const handleSelectPublisher = (publisher: Publisher) => {
    if (!ensureAdminAccess()) return
    if (!token) return

    setSelectedPublisher(publisher)
    setPublisherForm({
      name: publisher.name
    })
    setSuccessMessage('')
    setError('')
  }

  const handleDeletePublisher = async (publisherId: number) => {
    if (!ensureAdminAccess()) return
    if (!token) return
    if (!window.confirm('Удалить издательство?')) return

    try {
      setIsLoading(true)
      setError('')
      setSuccessMessage('')
      await deletePublisher(publisherId, token)
      setSuccessMessage('Издательство успешно удалено.')
      await loadLookups()
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Произошла ошибка при удалении')
    } finally {
      setIsLoading(false)
    }
  }

  const handleSubmitGenre = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault()
    if (!ensureAdminAccess()) return
    if (!token) return

    const normalizedName = genreForm.name.trim()

    if (!normalizedName) {
      setError('Название жанра обязательно')
      return
    }

    const normalizedGenreKey = normalizedName.toLocaleLowerCase('ru-RU')
    const hasGenreDuplicate = genres.some((genre) => {
      if (selectedGenre && genre.id === selectedGenre.id) {
        return false
      }

      return genre.name.trim().toLocaleLowerCase('ru-RU') === normalizedGenreKey
    })

    if (hasGenreDuplicate) {
      setError('Жанр с таким названием уже существует')
      return
    }

    const payload: GenreFormPayload = {
      genre_name: normalizedName,
    }

    try {
      setIsGenreSaving(true)
      setError('')
      setSuccessMessage('')
      if (selectedGenre) {
        await updateGenre(selectedGenre.id, payload, token)
        setSuccessMessage('Жанр успешно обновлен.')
      } else {
        await createGenre(payload, token)
        setSuccessMessage('Жанр успешно добавлен.')
      }
      setGenreForm(initialGenreFormState)
      setSelectedGenre(null)
      await loadLookups()
      closeAllModals(); // Закрываем модалку после успешного сохранения
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Произошла ошибка при сохранении')
    } finally {
      setIsGenreSaving(false)
    }
  }

  const handleSubmitAuthor = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault()
    if (!ensureAdminAccess()) return
    if (!token) return

    const normalizedFirstName = authorForm.first_name.trim()
    const normalizedMiddleName = authorForm.middle_name ? authorForm.middle_name.trim() : ''
    const normalizedLastName = authorForm.last_name.trim()

    if (!normalizedFirstName) {
      setError('Имя обязательно')
      return
    }
    if (!normalizedLastName) {
      setError('Фамилия обязательна')
      return
    }

    const payload: AuthorFormPayload = {
      first_name: normalizedFirstName,
      middle_name: normalizedMiddleName || null,
      last_name: normalizedLastName,
    }

    try {
      setIsAuthorSaving(true)
      setError('')
      setSuccessMessage('')
      if (selectedAuthor) {
        await updateAuthor(selectedAuthor.id, payload, token)
        setSuccessMessage('Автор успешно обновлен.')
      } else {
        await createAuthor(payload, token)
        setSuccessMessage('Автор успешно добавлен.')
      }
      setAuthorForm(initialAuthorFormState)
      setSelectedAuthor(null)
      await loadLookups()
      closeAllModals()
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Произошла ошибка при сохранении')
    } finally {
      setIsAuthorSaving(false)
    }
  }

  const handleSubmitPublisher = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault()
    if (!ensureAdminAccess()) return
    if (!token) return

    const normalizedName = publisherForm.name.trim()

    if (!normalizedName) {
      setError('Название издательства обязательно')
      return
    }

    const normalizedPublisherKey = normalizedName.toLocaleLowerCase('ru-RU')
    const hasPublisherDuplicate = publishers.some((publisher) => {
      if (selectedPublisher && publisher.id === selectedPublisher.id) {
        return false
      }

      return publisher.name.trim().toLocaleLowerCase('ru-RU') === normalizedPublisherKey
    })

    if (hasPublisherDuplicate) {
      setError('Издательство с таким названием уже существует')
      return
    }

    const payload: PublisherFormPayload = {
      publisher_name: normalizedName,
    }

    try {
      setIsPublisherSaving(true)
      setError('')
      setSuccessMessage('')
      if (selectedPublisher) {
        await updatePublisher(selectedPublisher.id, payload, token)
        setSuccessMessage('Издательство успешно обновлено.')
      } else {
        await createPublisher(payload, token)
        setSuccessMessage('Издательство успешно добавлено.')
      }
      setPublisherForm(initialPublisherFormState)
      setSelectedPublisher(null)
      await loadLookups()
      closeAllModals();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Произошла ошибка при сохранении')
    } finally {
      setIsPublisherSaving(false)
    }
  }

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault()
    if (!ensureAdminAccess()) return
    if (!token) return

    const normalizedTitle = form.title.trim()
    const normalizedDescription = form.description.trim()
    const normalizedYear = normalizeYearInput(form.publishedYear)
    const numericYear = normalizedYear ? Number(normalizedYear) : NaN

    if (!normalizedTitle) {
      setError('Название книги обязательно')
      return
    }
    if (!normalizedDescription) {
      setError('Описание книги обязательно')
      return
    }
    if (form.authors.length === 0) {
      setError('Укажите хотя бы одного автора')
      return
    }
    if (!form.publisher) {
      setError('Выберите издательство')
      return
    }
    if (!normalizedYear || Number.isNaN(numericYear) || numericYear < MIN_BOOK_YEAR || numericYear > CURRENT_YEAR) {
      setError(`Год издания должен быть в диапазоне ${MIN_BOOK_YEAR}–${CURRENT_YEAR}`)
      return
    }
    if (form.genres.length === 0) {
      setError('Выберите хотя бы один жанр')
      return
    }

    const payload: BookFormPayload = {
      book_title: normalizedTitle,
      description: normalizedDescription,
      published_year: numericYear,
      authors: form.authors,
      genres: form.genres,
      publisher: form.publisher.trim(),
    }

    try {
      setIsSaving(true)
      setError('')
      setSuccessMessage('')
      if (selectedBook) {
        await updateBook(selectedBook.id, payload, token, form.coverFile ?? undefined, form.files)
        setSuccessMessage('Книга успешно обновлена.')
      } else {
        await createBook(payload, token, form.coverFile ?? undefined, form.files)
        setSuccessMessage('Книга успешно добавлена.')
      }
      setForm(initialFormState)
      setSelectedBook(null)
      await loadBooks()
      closeAllModals();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Произошла ошибка при сохранении')
    } finally {
      setIsSaving(false)
    }
  }

  const renderGenresTab = () => (
    <div className={styles.tabContent}>
      <div className={styles.listSection}>
        <div className={styles.sectionHeader}>
          <h2>Жанры ({filteredGenres.length})</h2>
          <button 
            className={styles.newButton}
            onClick={() => openGenreModal()}
          >
          Добавить
          </button>
        </div>
        <div className={styles.tableWrap}>
          <table className={styles.table}>
            <thead>
              <tr>
                <th>Название</th>
                <th>Действия</th>
              </tr>
            </thead>
            <tbody>
              {filteredGenres.map((genre) => (
                <tr key={genre.id}>
                  <td data-label="Название">{genre.name}</td>
                  <td data-label="Действия" className={styles.actionsCell}>
                    <button
                      className={styles.actionButton}
                      type="button"
                      onClick={() => openGenreModal(genre)}
                    >
                      Редактировать
                    </button>
                    <button
                      className={styles.actionButtonDanger}
                      type="button"
                      onClick={() => handleDeleteGenre(genre.id)}
                    >
                      Удалить
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      <Modal
        isOpen={isGenreModalOpen}
        onClose={closeAllModals}
        title={modalMode === 'create' ? 'Добавление жанра' : 'Редактирование жанра'}
      >
        <form className={styles.form} onSubmit={handleSubmitGenre}>
          <label className={styles.label}>
            <span className={styles.labelTitle}>Название</span>
            <span className={styles.fieldHint}>Обязательно · до 255 символов</span>
            <input
              className={styles.input}
              value={genreForm.name}
              maxLength={MAX_TEXT_LENGTH}
              onChange={(event) => setGenreForm((prev) => ({ ...prev, name: normalizeSingleLine(event.target.value) }))}
              autoFocus
            />
            <div className={styles.counterRow}>
              <span className={styles.counterText}>Символов: {genreForm.name.length} / {MAX_TEXT_LENGTH}</span>
            </div>
          </label>
          <button className={styles.saveButton} type="submit" disabled={isGenreSaving}>
            {isGenreSaving ? 'Сохранение...' : modalMode === 'create' ? 'Добавить' : 'Сохранить'}
          </button>
        </form>
      </Modal>
    </div>
  )

  const renderAuthorsTab = () => (
    <div className={styles.tabContent}>
      <div className={styles.listSection}>
        <div className={styles.sectionHeader}>
          <h2>Авторы ({filteredAuthors.length})</h2>
          <button 
            className={styles.newButton}
            onClick={() => openAuthorModal()}
          >
            Добавить
          </button>
        </div>
        <div className={styles.tableWrap}>
          <table className={styles.table}>
            <thead>
              <tr>
                <th>ФИО</th>
                <th>Действия</th>
              </tr>
            </thead>
            <tbody>
              {filteredAuthors.map((author) => (
                <tr key={author.id}>
                  <td data-label="ФИО">{author.fullName}</td>
                  <td data-label="Действия" className={styles.actionsCell}>
                    <button
                      className={styles.actionButton}
                      type="button"
                      onClick={() => openAuthorModal(author)}
                    >
                      Редактировать
                    </button>
                    <button
                      className={styles.actionButtonDanger}
                      type="button"
                      onClick={() => handleDeleteAuthor(author.id)}
                    >
                      Удалить
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      <Modal
        isOpen={isAuthorModalOpen}
        onClose={closeAllModals}
        title={modalMode === 'create' ? 'Добавление автора' : 'Редактирование автора'}
      >
        <form className={styles.form} onSubmit={handleSubmitAuthor}>
          <label className={styles.label}>
            <span className={styles.labelTitle}>Имя</span>
            <span className={styles.fieldHint}>Обязательно · буквы, пробел, дефис</span>
            <input
              className={styles.input}
              value={authorForm.first_name}
              maxLength={MAX_TEXT_LENGTH}
              onChange={(event) => setAuthorForm((prev) => ({ ...prev, first_name: normalizeAuthorInput(event.target.value) }))}
              autoFocus
            />
            <div className={styles.counterRow}>
              <span className={styles.counterText}>Символов: {authorForm.first_name.length} / {MAX_TEXT_LENGTH}</span>
            </div>
          </label>
          <label className={styles.label}>
            <span className={styles.labelTitle}>Отчество</span>
            <span className={styles.fieldHint}>Необязательно · буквы, пробел, дефис</span>
            <input
              className={styles.input}
              value={authorForm.middle_name || ''}
              maxLength={MAX_TEXT_LENGTH}
              onChange={(event) => setAuthorForm((prev) => ({ ...prev, middle_name: normalizeAuthorInput(event.target.value) }))}
            />
            <div className={styles.counterRow}>
              <span className={styles.counterText}>Символов: {(authorForm.middle_name || '').length} / {MAX_TEXT_LENGTH}</span>
            </div>
          </label>
          <label className={styles.label}>
            <span className={styles.labelTitle}>Фамилия</span>
            <span className={styles.fieldHint}>Обязательно · буквы, пробел, дефис</span>
            <input
              className={styles.input}
              value={authorForm.last_name}
              maxLength={MAX_TEXT_LENGTH}
              onChange={(event) => setAuthorForm((prev) => ({ ...prev, last_name: normalizeAuthorInput(event.target.value) }))}
            />
            <div className={styles.counterRow}>
              <span className={styles.counterText}>Символов: {authorForm.last_name.length} / {MAX_TEXT_LENGTH}</span>
            </div>
          </label>
          <button className={styles.saveButton} type="submit" disabled={isAuthorSaving}>
            {isAuthorSaving ? 'Сохранение...' : modalMode === 'create' ? 'Добавить' : 'Сохранить'}
          </button>
        </form>
      </Modal>
    </div>
  )

  const renderPublishersTab = () => (
    <div className={styles.tabContent}>
      <div className={styles.listSection}>
        <div className={styles.sectionHeader}>
          <h2>Издательства ({filteredPublishers.length})</h2>
          <button 
            className={styles.newButton}
            onClick={() => openPublisherModal()}
          >
          Добавить
          </button>
        </div>
        <div className={styles.tableWrap}>
          <table className={styles.table}>
            <thead>
              <tr>
                <th>Название</th>
                <th>Действия</th>
              </tr>
            </thead>
            <tbody>
              {filteredPublishers.map((publisher) => (
                <tr key={publisher.id}>
                  <td data-label="Название">{publisher.name}</td>
                  <td data-label="Действия" className={styles.actionsCell}>
                    <button
                      className={styles.actionButton}
                      type="button"
                      onClick={() => openPublisherModal(publisher)}
                    >
                      Редактировать
                    </button>
                    <button
                      className={styles.actionButtonDanger}
                      type="button"
                      onClick={() => handleDeletePublisher(publisher.id)}
                    >
                      Удалить
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      <Modal
        isOpen={isPublisherModalOpen}
        onClose={closeAllModals}
        title={modalMode === 'create' ? 'Добавление издательства' : 'Редактирование издательства'}
      >
        <form className={styles.form} onSubmit={handleSubmitPublisher}>
          <label className={styles.label}>
            <span className={styles.labelTitle}>Название</span>
            <span className={styles.fieldHint}>Обязательно · до 255 символов</span>
            <input
              className={styles.input}
              value={publisherForm.name}
              maxLength={MAX_TEXT_LENGTH}
              onChange={(event) => setPublisherForm((prev) => ({ ...prev, name: normalizeSingleLine(event.target.value) }))}
              autoFocus
            />
            <div className={styles.counterRow}>
              <span className={styles.counterText}>Символов: {publisherForm.name.length} / {MAX_TEXT_LENGTH}</span>
            </div>
          </label>
          <button className={styles.saveButton} type="submit" disabled={isPublisherSaving}>
            {isPublisherSaving ? 'Сохранение...' : modalMode === 'create' ? 'Добавить' : 'Сохранить'}
          </button>
        </form>
      </Modal>
    </div>
  )

  const renderBooksTab = () => {
    const hasAppliedFilters = Boolean(
      appliedFilters.search ||
      appliedFilters.author_ids?.length ||
      appliedFilters.genre_ids?.length ||
      appliedFilters.publisher_id ||
      appliedFilters.year_from ||
      appliedFilters.year_to
    )

    return (
      <div className={styles.tabContent}>
        {isFilterOpen && (
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
                setValidationError('')
              }}
              onGenreChange={(value) => {
                setDraftGenreIds(value)
                setValidationError('')
              }}
              onPublisherChange={(value) => {
                setDraftPublisherId(value)
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
              onApply={handleSearchSubmit}
            />
          </div>
        )}
        
        <div className={styles.listSection}>
          <div className={styles.sectionHeader}>
            <h2>Книги</h2>
            <button 
              className={styles.newButton}
              onClick={() => openBookModal()}
            >
              Добавить
            </button>            
          </div>
          <Pagination
            currentPage={page}
            lastPage={lastPage}
            perPage={perPage}
            total={total}
            onPageChange={setPage}
            onPerPageChange={handlePerPageChange}
          />
          
          {isBooksLoading ? (
            <p className={styles.state}>Загрузка книг...</p>
          ) : booksError ? (
            <p className={styles.error}>{booksError}</p>
          ) : books.length > 0 ? (
            <div className={styles.tableWrap}>
              <table className={styles.table}>
                <thead>
                  <tr>
                    <th>Название</th>
                    <th>Автор</th>
                    <th>Жанр</th>
                    <th>Издательство</th>
                    <th>Год</th>
                    <th>Файлы</th>
                    <th>Действия</th>
                  </tr>
                </thead>
                <tbody>
                  {books.map((book) => (
                    <tr key={book.id}>
                      <td data-label="Название">{book.title}</td>
                      <td data-label="Автор">{book.author}</td>
                      <td data-label="Жанр">{book.genre}</td>
                      <td data-label="Издательство">{book.publisher.name}</td>
                      <td data-label="Год">{book.publishedYear ?? ''}</td>
                      <td data-label="Файлы">{book.filesCount}</td>
                      <td data-label="Действия" className={styles.actionsCell}>
                        <button
                          className={styles.actionButton}
                          type="button"
                          onClick={() => openBookModal(book)}
                        >
                          Редактировать
                        </button>
                        <button
                          className={styles.actionButtonDanger}
                          type="button"
                          onClick={() => handleDelete(book.id)}
                        >
                          Удалить
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ) : (
            <p className={styles.state}>По выбранным параметрам книги не найдены</p>
          )}
        </div>
        <Modal
          isOpen={isBookModalOpen}
          onClose={closeAllModals}
          title={modalMode === 'create' ? 'Добавление книги' : 'Редактирование книги'}
        >
          <form className={styles.form} onSubmit={handleSubmit}>
            <label className={styles.label}>
              <span className={styles.labelTitle}>Название</span>
              <span className={styles.fieldHint}>Обязательно · до 255 символов</span>
              <input
                className={styles.input}
                value={form.title}
                maxLength={MAX_TEXT_LENGTH}
                onChange={(event) => setForm((prev) => ({ ...prev, title: normalizeSingleLine(event.target.value) }))}
              />
              <div className={styles.counterRow}>
                <span className={styles.counterText}>Символов: {form.title.length} / {MAX_TEXT_LENGTH}</span>
              </div>
            </label>
            <label className={styles.label}>
              <span className={styles.labelTitle}>Описание</span>
              <span className={styles.fieldHint}>Обязательно</span>
              <textarea
                className={styles.textarea}
                value={form.description}
                onChange={(event) =>
                  setForm((prev) => ({ ...prev, description: event.target.value }))
                }
              />
              <div className={styles.counterRow}>
                <span className={styles.counterText}>Символов: {form.description.length}</span>
              </div>
            </label>
            <label className={styles.label}>
              <span className={styles.labelTitle}>Авторы</span>
              <span className={styles.fieldHint}>Обязательно · выберите хотя бы одного автора</span>
              <div className={styles.genresContainer}>
                <select
                  className={styles.select}
                  multiple
                  value={form.authors}
                  onChange={(event) => {
                    const selected = Array.from(
                      event.target.selectedOptions,
                      (option) => option.value,
                    )
                    setForm((prev) => ({ ...prev, authors: selected }))
                  }}
                >
                  {authors.map((author) => (
                    <option key={author.id} value={author.id}>
                      {author.fullName}
                    </option>
                  ))}
                </select>
                <div className={styles.selectedGenres}>
                  {form.authors.map((authorId) => {
                    const author = authors.find(a => a.id.toString() === authorId)
                    return (
                      <div key={authorId} className={styles.genreTag}>
                        <span>{author?.fullName || authorId}</span>
                        <button
                          type="button"
                          className={styles.genreTagRemove}
                          onClick={() =>
                            setForm((prev) => ({
                              ...prev,
                              authors: prev.authors.filter((a) => a !== authorId),
                            }))
                          }
                        >
                          ×
                        </button>
                      </div>
                    )
                  })}
                </div>
              </div>
              <div className={styles.counterRow}>
                <span className={styles.counterText}>Выбрано авторов: {form.authors.length}</span>
              </div>
            </label>
            <label className={styles.label}>
              <span className={styles.labelTitle}>Издательство</span>
              <span className={styles.fieldHint}>Обязательно</span>
              <div className={styles.genresContainer}>
                <select
                  className={styles.select}
                  value={form.publisher}
                  onChange={(event) => {
                    setForm((prev) => ({ ...prev, publisher: event.target.value }))
                  }}
                >
                  <option value="">Выберите издательство</option>
                  {publishers.map((publisher) => (
                    <option key={publisher.id} value={publisher.id}>
                      {publisher.name}
                    </option>
                  ))}
                </select>
              </div>
              <div className={styles.counterRow}>
                <span className={styles.counterText}>{form.publisher ? 'Издательство выбрано' : 'Издательство не выбрано'}</span>
              </div>
            </label>
            <label className={styles.label}>
              <span className={styles.labelTitle}>Год издания</span>
              <span className={styles.fieldHint}>Обязательно · только 4 цифры · {MIN_BOOK_YEAR}–{CURRENT_YEAR}</span>
              <input
                className={styles.input}
                type="text"
                inputMode="numeric"
                maxLength={4}
                value={form.publishedYear}
                onChange={(event) =>
                  setForm((prev) => ({ ...prev, publishedYear: normalizeYearInput(event.target.value) }))
                }
              />
              <div className={styles.counterRow}>
                <span className={styles.counterText}>Символов: {form.publishedYear.length} / 4</span>
              </div>
            </label>
            <label className={styles.label}>
              <span className={styles.labelTitle}>Жанры</span>
              <span className={styles.fieldHint}>Обязательно · выберите хотя бы один жанр</span>
              <div className={styles.genresContainer}>
                <select
                  className={styles.select}
                  multiple
                  value={form.genres}
                  onChange={(event) => {
                    const selected = Array.from(
                      event.target.selectedOptions,
                      (option) => option.value,
                    )
                    setForm((prev) => ({ ...prev, genres: selected }))
                  }}
                >
                  {genres.map((genre) => (
                    <option key={genre.id} value={genre.id}>
                      {genre.name}
                    </option>
                  ))}
                </select>
                <div className={styles.selectedGenres}>
                  {form.genres.map((genreId) => {
                    const genre = genres.find(g => g.id.toString() === genreId)
                    return (
                      <div key={genreId} className={styles.genreTag}>
                        <span>{genre?.name || genreId}</span>
                        <button
                          type="button"
                          className={styles.genreTagRemove}
                          onClick={() =>
                            setForm((prev) => ({
                              ...prev,
                              genres: prev.genres.filter((g) => g !== genreId),
                            }))
                          }
                        >
                          ×
                        </button>
                      </div>
                    )
                  })}
                </div>
              </div>
              <div className={styles.counterRow}>
                <span className={styles.counterText}>Выбрано жанров: {form.genres.length}</span>
              </div>
            </label>
            <label className={styles.label}>
              <span className={styles.labelTitle}>Обложка</span>
              <span className={styles.fieldHint}>Необязательно · JPG, JPEG, PNG, GIF · до 5 МБ</span>
              <input
                type="file"
                accept=".jpg,.jpeg,.png,.gif,image/jpeg,image/png,image/gif"
                className={styles.inputFile}
                onChange={handleCoverInputChange}
              />
              <div className={styles.counterRow}>
                <span className={styles.counterText}>{form.coverFile ? `Выбран файл: ${form.coverFile.name}` : 'Файл не выбран'}</span>
              </div>
            </label>
            <label className={styles.label}>
              <span className={styles.labelTitle}>Файлы книги</span>
              <span className={styles.fieldHint}>Необязательно · PDF, FB2, TXT · до 50 МБ каждый</span>
              <input
                type="file"
                accept=".pdf,.fb2,.txt"
                className={styles.inputFile}
                multiple
                onChange={handleFilesInputChange}
              />
              <div className={styles.counterRow}>
                <span className={styles.counterText}>Выбрано файлов: {form.files.length}</span>
              </div>
            </label>
            <button className={styles.saveButton} type="submit" disabled={isSaving}>
              {isSaving ? 'Сохранение...' : selectedBook ? 'Сохранить' : 'Добавить'}
            </button>
          </form>
        </Modal>
      </div>
    )
  }

  const isLoadingPage = isLoading || (activeTab === 'books' && isBooksLoading) || (activeTab !== 'books' && isLookupsLoading)

  return (
    <main className={styles.adminPage}>
      <Header
        leftVariant="back"
        centerVariant="search"
        rightVariant="profile"
        searchValue={search}
        onSearchChange={(value) => {
          setSearch(normalizeSearchInput(value))
          setValidationError('')
        }}
        onSearchClick={activeTab === 'books' ? handleSearchSubmit : () => {
          // Для других вкладок просто применяем поиск
          setPage(1)
        }}
        onBackClick={() => navigate(-1)}
        onFilterClick={() => setIsFilterOpen((current) => !current)}
        onProfileClick={() => navigate('/profile')}
      />
      <section className={styles.container}>
        {isLoadingPage ? <p className={styles.status}>Загрузка...</p> : null}
        {error ? <p className={styles.error}>{error}</p> : null}
        {successMessage ? <p className={styles.success}>{successMessage}</p> : null}
        {validationError ? <p className={styles.error}>{validationError}</p> : null}
        
        <div className={styles.tabs}>
          <button
            className={`${styles.tab} ${activeTab === 'books' ? styles.activeTab : ''}`}
            onClick={() => handleTabChange('books')}
          >
            Книги
          </button>
          <button
            className={`${styles.tab} ${activeTab === 'authors' ? styles.activeTab : ''}`}
            onClick={() => handleTabChange('authors')}
          >
            Авторы
          </button>
          <button
            className={`${styles.tab} ${activeTab === 'genres' ? styles.activeTab : ''}`}
            onClick={() => handleTabChange('genres')}
          >
            Жанры
          </button>
          <button
            className={`${styles.tab} ${activeTab === 'publishers' ? styles.activeTab : ''}`}
            onClick={() => handleTabChange('publishers')}
          >
            Издательства
          </button>
        </div>

        {activeTab === 'books' && renderBooksTab()}
        {activeTab === 'authors' && renderAuthorsTab()}
        {activeTab === 'genres' && renderGenresTab()}
        {activeTab === 'publishers' && renderPublishersTab()}
      </section>
    </main>
  )
}