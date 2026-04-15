// AdminPage.tsx
import { useEffect, useMemo, useState, useCallback } from 'react'
import { useNavigate } from 'react-router-dom'
import { getCurrentUser } from '@api/auth'
import {
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
  getAdminUsers,
  updateUser,
  createUser,
  deleteUser,
} from '@api/library'
import { Header } from '@components/Header'
import { FiltersPanel } from '@components/FiltersPanel'
import { Pagination } from '@components/Pagination'
import type { Book, Genre, Author, Publisher, PublisherFormPayload, AuthorFormPayload, GenreFormPayload, GetBooksParams, UserFormPayload } from '@models/library'
import type { BookFormPayload } from '@models/library'
import type { User } from '@models/auth'
import styles from './AdminPage.module.css'
import { Modal } from '@components/Modal/Modal'

type TabType = 'genres' | 'authors' | 'publishers' | 'books' | 'users'

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

type UserFormState = {
  login: string;
  role_id: number;
  password: string;
  password_confirmation: string;
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

const initialUserFormState: UserFormState = {
  login: '',
  role_id: 2, // По умолчанию обычный пользователь
  password: '',
  password_confirmation: '',
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
  const [users, setUsers] = useState<User[]>([])
  
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
  const [modalFormError, setModalFormError] = useState('')
  const [modalFieldErrors, setModalFieldErrors] = useState<Record<string, string>>({})
  
  const [selectedGenre, setSelectedGenre] = useState<Genre | null>(null)
  const [selectedAuthor, setSelectedAuthor] = useState<Author | null>(null)
  const [selectedPublisher, setSelectedPublisher] = useState<Publisher | null>(null)
  const [selectedBook, setSelectedBook] = useState<Book | null>(null)
  const [selectedUser, setSelectedUser] = useState<User | null>(null)
  const [genreForm, setGenreForm] = useState<GenreFormState>(initialGenreFormState)
  const [authorForm, setAuthorForm] = useState<AuthorFormState>(initialAuthorFormState)
  const [publisherForm, setPublisherForm] = useState<PublisherFormState>(initialPublisherFormState)
  const [userForm, setUserForm] = useState<UserFormState>(initialUserFormState)
  const [form, setForm] = useState<BookFormState>(initialFormState)
  const [error, setError] = useState('')
  const [successMessage, setSuccessMessage] = useState('')
  const [isLoading, setIsLoading] = useState(true)
  const [isGenreSaving, setIsGenreSaving] = useState(false)
  const [isAuthorSaving, setIsAuthorSaving] = useState(false)
  const [isPublisherSaving, setIsPublisherSaving] = useState(false)
  const [isUserSaving, setIsUserSaving] = useState(false)
  const [isSaving, setIsSaving] = useState(false)
  const [isLookupsLoading, setIsLookupsLoading] = useState(true)

  const [isGenreModalOpen, setIsGenreModalOpen] = useState(false);
  const [isAuthorModalOpen, setIsAuthorModalOpen] = useState(false);
  const [isPublisherModalOpen, setIsPublisherModalOpen] = useState(false);
  const [isBookModalOpen, setIsBookModalOpen] = useState(false);
  const [isUserModalOpen, setIsUserModalOpen] = useState(false)
  const [modalMode, setModalMode] = useState<'create' | 'edit'>('create');

  const token = localStorage.getItem('token')

  const resetModalValidation = useCallback(() => {
    setModalFormError('')
    setModalFieldErrors({})
  }, [])

  const clearModalFieldError = useCallback((field: string) => {
    setModalFieldErrors((prev) => {
      if (!(field in prev)) {
        return prev
      }

      const next = { ...prev }
      delete next[field]
      return next
    })
  }, [])

  const getModalFieldError = (field: string) => modalFieldErrors[field]

  const loadLookups = useCallback(async () => {
    if (!token) return
    
    try {
      setIsLookupsLoading(true)
      const [genresData, authorsData, publishersData, usersData] = await Promise.all([
        getAdminGenres(token),
        getAdminAuthors(token),
        getAdminPublishers(token),
        getAdminUsers(token)
      ])
      setGenres(genresData)
      setAuthors(authorsData)
      setPublishers(publishersData)
      setUsers(usersData)
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
      clearModalFieldError('bookCoverFile')
      setForm((prev) => ({ ...prev, coverFile: null }))
      return
    }

    const validationMessage = validateCoverFile(file)

    if (validationMessage) {
      event.target.value = ''
      setForm((prev) => ({ ...prev, coverFile: null }))
      setModalFormError('')
      setModalFieldErrors((prev) => ({ ...prev, bookCoverFile: validationMessage }))
      return
    }

    clearModalFieldError('bookCoverFile')
    setForm((prev) => ({ ...prev, coverFile: file }))
  }

  const handleFilesInputChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    const selectedFiles = event.target.files ? Array.from(event.target.files) : []

    if (selectedFiles.length === 0) {
      clearModalFieldError('bookFiles')
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
      setModalFormError('')
      setModalFieldErrors((prev) => ({ ...prev, bookFiles: firstError }))
    } else {
      clearModalFieldError('bookFiles')
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
    resetModalValidation();
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
    resetModalValidation();
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
    resetModalValidation();
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
    resetModalValidation();
    setIsBookModalOpen(true);
  };

  const openUserModal = (user?: User) => {
    if (user) {
      setSelectedUser(user)
      setUserForm({
        login: user.login,
        role_id: user.roleId,
        password: '',
        password_confirmation: ''
      })
      setModalMode('edit')
    } else {
      setSelectedUser(null)
      setUserForm(initialUserFormState)
      setModalMode('create')
    }
    resetModalValidation()
    setIsUserModalOpen(true)
  }
  
  const closeAllModals = () => {
    setIsGenreModalOpen(false);
    setIsAuthorModalOpen(false);
    setIsPublisherModalOpen(false);
    setIsBookModalOpen(false);
    setIsUserModalOpen(false)
    resetModalValidation()
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
    setSelectedUser(null)
    setError('')
    setSuccessMessage('')
    setValidationError('')
    resetModalValidation()
    // Сбрасываем формы
    setGenreForm(initialGenreFormState)
    setAuthorForm(initialAuthorFormState)
    setPublisherForm(initialPublisherFormState)
    setUserForm(initialUserFormState)
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
  const filterBySearch = <T extends { login?: string, name?: string; fullName?: string }>(
    items: T[],
    searchTerm: string
  ): T[] => {
    if (!searchTerm.trim()) return items
    const query = searchTerm.trim().toLowerCase()
    return items.filter(item => {
      const name = (item.login || item.name || item.fullName || '').toLowerCase()
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

  const filteredUsers = useMemo(() => {
    if (activeTab !== 'users') return users
    return filterBySearch(users, search)
  }, [users, search, activeTab])

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

  const handleDeleteUser = async (userId: number) => {
    if (!ensureAdminAccess()) return
    if (!token) return
    
    const userToDelete = users.find(u => u.id === userId)
    if (!window.confirm(`Удалить пользователя "${userToDelete?.login}"?`)) return

    try {
      setIsLoading(true)
      setError('')
      setSuccessMessage('')
      await deleteUser(userId, token)
      setSuccessMessage('Пользователь успешно удалён.')
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
    const nextErrors: Record<string, string> = {}

    if (!normalizedName) {
      nextErrors.genreName = 'Название жанра обязательно'
    }

    const normalizedGenreKey = normalizedName.toLocaleLowerCase('ru-RU')
    const hasGenreDuplicate = normalizedName && genres.some((genre) => {
      if (selectedGenre && genre.id === selectedGenre.id) {
        return false
      }

      return genre.name.trim().toLocaleLowerCase('ru-RU') === normalizedGenreKey
    })

    if (hasGenreDuplicate) {
      nextErrors.genreName = 'Жанр с таким названием уже существует'
    }

    if (Object.keys(nextErrors).length > 0) {
      setModalFormError('')
      setModalFieldErrors(nextErrors)
      return
    }

    const payload: GenreFormPayload = {
      genre_name: normalizedName,
    }

    try {
      setIsGenreSaving(true)
      resetModalValidation()
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
      setModalFormError(err instanceof Error ? err.message : 'Произошла ошибка при сохранении')
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
    const nextErrors: Record<string, string> = {}

    if (!normalizedFirstName) {
      nextErrors.authorFirstName = 'Имя обязательно'
    }
    if (!normalizedLastName) {
      nextErrors.authorLastName = 'Фамилия обязательна'
    }

    if (Object.keys(nextErrors).length > 0) {
      setModalFormError('')
      setModalFieldErrors(nextErrors)
      return
    }

    const payload: AuthorFormPayload = {
      first_name: normalizedFirstName,
      middle_name: normalizedMiddleName || null,
      last_name: normalizedLastName,
    }

    try {
      setIsAuthorSaving(true)
      resetModalValidation()
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
      setModalFormError(err instanceof Error ? err.message : 'Произошла ошибка при сохранении')
    } finally {
      setIsAuthorSaving(false)
    }
  }

  const handleSubmitPublisher = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault()
    if (!ensureAdminAccess()) return
    if (!token) return

    const normalizedName = publisherForm.name.trim()
    const nextErrors: Record<string, string> = {}

    if (!normalizedName) {
      nextErrors.publisherName = 'Название издательства обязательно'
    }

    const normalizedPublisherKey = normalizedName.toLocaleLowerCase('ru-RU')
    const hasPublisherDuplicate = normalizedName && publishers.some((publisher) => {
      if (selectedPublisher && publisher.id === selectedPublisher.id) {
        return false
      }

      return publisher.name.trim().toLocaleLowerCase('ru-RU') === normalizedPublisherKey
    })

    if (hasPublisherDuplicate) {
      nextErrors.publisherName = 'Издательство с таким названием уже существует'
    }

    if (Object.keys(nextErrors).length > 0) {
      setModalFormError('')
      setModalFieldErrors(nextErrors)
      return
    }

    const payload: PublisherFormPayload = {
      publisher_name: normalizedName,
    }

    try {
      setIsPublisherSaving(true)
      resetModalValidation()
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
      setModalFormError(err instanceof Error ? err.message : 'Произошла ошибка при сохранении')
    } finally {
      setIsPublisherSaving(false)
    }
  }

  const handleSubmitUser = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault()
    if (!ensureAdminAccess()) return
    if (!token) return

    const normalizedLogin = userForm.login.trim()
    const nextErrors: Record<string, string> = {}

    if (!normalizedLogin) {
      nextErrors.userLogin = 'Логин обязателен'
    }

    if (normalizedLogin.length < 1) {
      nextErrors.userLogin = 'Логин должен содержать минимум 1 символ'
    }

    if (modalMode === 'create' && !userForm.password) {
      nextErrors.userPassword = 'Пароль обязателен при создании пользователя'
    }

    if (userForm.password && userForm.password.length < 6) {
      nextErrors.userPassword = 'Пароль должен содержать минимум 6 символов'
    }

    if ((modalMode === 'create' || userForm.password || userForm.password_confirmation) && userForm.password !== userForm.password_confirmation) {
      nextErrors.userPasswordConfirmation = 'Пароли должны совпадать'
    }

    // Проверка на дубликат логина
    const hasLoginDuplicate = normalizedLogin && users.some((u) => {
      if (selectedUser && u.id === selectedUser.id) return false
      return u.login.toLowerCase() === normalizedLogin.toLowerCase()
    })

    if (hasLoginDuplicate) {
      nextErrors.userLogin = 'Пользователь с таким логином уже существует'
    }

    if (Object.keys(nextErrors).length > 0) {
      setModalFormError('')
      setModalFieldErrors(nextErrors)
      return
    }

    const payload: UserFormPayload = {
      login: normalizedLogin,
      role_id: userForm.role_id,
      password: userForm.password,
      password_confirmation: userForm.password_confirmation,
    }

    try {
      setIsUserSaving(true)
      resetModalValidation()
      setSuccessMessage('')
      
      if (selectedUser) {
        await updateUser(selectedUser.id, payload, token)
        setSuccessMessage('Пользователь успешно обновлён.')
      } else {
        await createUser(payload, token)
        setSuccessMessage('Пользователь успешно создан.')
      }
      
      setUserForm(initialUserFormState)
      setSelectedUser(null)
      await loadLookups() // Перезагружаем список пользователей
      closeAllModals()
    } catch (err) {
      setModalFormError(err instanceof Error ? err.message : 'Произошла ошибка при сохранении')
    } finally {
      setIsUserSaving(false)
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
    const nextErrors: Record<string, string> = {}

    if (!normalizedTitle) {
      nextErrors.bookTitle = 'Название книги обязательно'
    }
    if (!normalizedDescription) {
      nextErrors.bookDescription = 'Описание книги обязательно'
    }
    if (form.authors.length === 0) {
      nextErrors.bookAuthors = 'Укажите хотя бы одного автора'
    }
    if (!form.publisher) {
      nextErrors.bookPublisher = 'Выберите издательство'
    }
    if (!normalizedYear || Number.isNaN(numericYear) || numericYear < MIN_BOOK_YEAR || numericYear > CURRENT_YEAR) {
      nextErrors.bookPublishedYear = `Год издания должен быть в диапазоне ${MIN_BOOK_YEAR}–${CURRENT_YEAR}`
    }
    if (form.genres.length === 0) {
      nextErrors.bookGenres = 'Выберите хотя бы один жанр'
    }

    if (Object.keys(nextErrors).length > 0) {
      setModalFormError('')
      setModalFieldErrors((prev) => ({ ...prev, ...nextErrors }))
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
      resetModalValidation()
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
      setModalFormError(err instanceof Error ? err.message : 'Произошла ошибка при сохранении')
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
          {modalFormError ? <p className={styles.formError}>{modalFormError}</p> : null}
          <label className={styles.label}>
            <span className={styles.labelTitle}>Название</span>
            <span className={styles.fieldHint}>Обязательно · до 255 символов</span>
            <input
              className={`${styles.input} ${getModalFieldError('genreName') ? styles.inputError : ''}`}
              value={genreForm.name}
              maxLength={MAX_TEXT_LENGTH}
              onChange={(event) => {
                clearModalFieldError('genreName')
                setGenreForm((prev) => ({ ...prev, name: normalizeSingleLine(event.target.value) }))
              }}
              autoFocus
            />
            <div className={styles.counterRow}>
              {getModalFieldError('genreName') ? <span className={styles.fieldError}>{getModalFieldError('genreName')}</span> : null}
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
          {modalFormError ? <p className={styles.formError}>{modalFormError}</p> : null}
          <label className={styles.label}>
            <span className={styles.labelTitle}>Имя</span>
            <span className={styles.fieldHint}>Обязательно · буквы, пробел, дефис</span>
            <input
              className={`${styles.input} ${getModalFieldError('authorFirstName') ? styles.inputError : ''}`}
              value={authorForm.first_name}
              maxLength={MAX_TEXT_LENGTH}
              onChange={(event) => {
                clearModalFieldError('authorFirstName')
                setAuthorForm((prev) => ({ ...prev, first_name: normalizeAuthorInput(event.target.value) }))
              }}
              autoFocus
            />
            <div className={styles.counterRow}>
              {getModalFieldError('authorFirstName') ? <span className={styles.fieldError}>{getModalFieldError('authorFirstName')}</span> : null}
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
              {getModalFieldError('authorLastName') ? <span className={styles.fieldError}>{getModalFieldError('authorLastName')}</span> : null}
              <span className={styles.counterText}>Символов: {(authorForm.middle_name || '').length} / {MAX_TEXT_LENGTH}</span>
            </div>
          </label>
          <label className={styles.label}>
            <span className={styles.labelTitle}>Фамилия</span>
            <span className={styles.fieldHint}>Обязательно · буквы, пробел, дефис</span>
            <input
              className={`${styles.input} ${getModalFieldError('authorLastName') ? styles.inputError : ''}`}
              value={authorForm.last_name}
              maxLength={MAX_TEXT_LENGTH}
              onChange={(event) => {
                clearModalFieldError('authorLastName')
                setAuthorForm((prev) => ({ ...prev, last_name: normalizeAuthorInput(event.target.value) }))
              }}
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
          {modalFormError ? <p className={styles.formError}>{modalFormError}</p> : null}
          <label className={styles.label}>
            <span className={styles.labelTitle}>Название</span>
            <span className={styles.fieldHint}>Обязательно · до 255 символов</span>
            <input
              className={`${styles.input} ${getModalFieldError('publisherName') ? styles.inputError : ''}`}
              value={publisherForm.name}
              maxLength={MAX_TEXT_LENGTH}
              onChange={(event) => {
                clearModalFieldError('publisherName')
                setPublisherForm((prev) => ({ ...prev, name: normalizeSingleLine(event.target.value) }))
              }}
              autoFocus
            />
            <div className={styles.counterRow}>
              {getModalFieldError('publisherName') ? <span className={styles.fieldError}>{getModalFieldError('publisherName')}</span> : null}
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
            {modalFormError ? <p className={styles.formError}>{modalFormError}</p> : null}
            <label className={styles.label}>
              <span className={styles.labelTitle}>Название</span>
              <span className={styles.fieldHint}>Обязательно · до 255 символов</span>
              <input
                className={`${styles.input} ${getModalFieldError('bookTitle') ? styles.inputError : ''}`}
                value={form.title}
                maxLength={MAX_TEXT_LENGTH}
                onChange={(event) => {
                  clearModalFieldError('bookTitle')
                  setForm((prev) => ({ ...prev, title: normalizeSingleLine(event.target.value) }))
                }}
              />
              <div className={styles.counterRow}>
                {getModalFieldError('bookTitle') ? <span className={styles.fieldError}>{getModalFieldError('bookTitle')}</span> : null}
                <span className={styles.counterText}>Символов: {form.title.length} / {MAX_TEXT_LENGTH}</span>
              </div>
            </label>
            <label className={styles.label}>
              <span className={styles.labelTitle}>Описание</span>
              <span className={styles.fieldHint}>Обязательно</span>
              <textarea
                className={`${styles.textarea} ${getModalFieldError('bookDescription') ? styles.inputError : ''}`}
                value={form.description}
                onChange={(event) => {
                  clearModalFieldError('bookDescription')
                  setForm((prev) => ({ ...prev, description: event.target.value }))
                }}
              />
              <div className={styles.counterRow}>
                {getModalFieldError('bookDescription') ? <span className={styles.fieldError}>{getModalFieldError('bookDescription')}</span> : null}
                <span className={styles.counterText}>Символов: {form.description.length}</span>
              </div>
            </label>
            <label className={styles.label}>
              <span className={styles.labelTitle}>Авторы</span>
              <span className={styles.fieldHint}>Обязательно · выберите хотя бы одного автора</span>
              <div className={styles.genresContainer}>
                <select
                  className={`${styles.select} ${getModalFieldError('bookAuthors') ? styles.inputError : ''}`}
                  multiple
                  value={form.authors}
                  onChange={(event) => {
                    const selected = Array.from(
                      event.target.selectedOptions,
                      (option) => option.value,
                    )
                    clearModalFieldError('bookAuthors')
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
                {getModalFieldError('bookAuthors') ? <span className={styles.fieldError}>{getModalFieldError('bookAuthors')}</span> : null}
                <span className={styles.counterText}>Выбрано авторов: {form.authors.length}</span>
              </div>
            </label>
            <label className={styles.label}>
              <span className={styles.labelTitle}>Издательство</span>
              <span className={styles.fieldHint}>Обязательно</span>
              <div className={styles.genresContainer}>
                <select
                  className={`${styles.select} ${getModalFieldError('bookPublisher') ? styles.inputError : ''}`}
                  value={form.publisher}
                  onChange={(event) => {
                    clearModalFieldError('bookPublisher')
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
                {getModalFieldError('bookPublisher') ? <span className={styles.fieldError}>{getModalFieldError('bookPublisher')}</span> : null}
                <span className={styles.counterText}>{form.publisher ? 'Издательство выбрано' : 'Издательство не выбрано'}</span>
              </div>
            </label>
            <label className={styles.label}>
              <span className={styles.labelTitle}>Год издания</span>
              <span className={styles.fieldHint}>Обязательно · только 4 цифры · {MIN_BOOK_YEAR}–{CURRENT_YEAR}</span>
              <input
                className={`${styles.input} ${getModalFieldError('bookPublishedYear') ? styles.inputError : ''}`}
                type="text"
                inputMode="numeric"
                maxLength={4}
                value={form.publishedYear}
                onChange={(event) => {
                  clearModalFieldError('bookPublishedYear')
                  setForm((prev) => ({ ...prev, publishedYear: normalizeYearInput(event.target.value) }))
                }}
              />
              <div className={styles.counterRow}>
                {getModalFieldError('bookPublishedYear') ? <span className={styles.fieldError}>{getModalFieldError('bookPublishedYear')}</span> : null}
                <span className={styles.counterText}>Символов: {form.publishedYear.length} / 4</span>
              </div>
            </label>
            <label className={styles.label}>
              <span className={styles.labelTitle}>Жанры</span>
              <span className={styles.fieldHint}>Обязательно · выберите хотя бы один жанр</span>
              <div className={styles.genresContainer}>
                <select
                  className={`${styles.select} ${getModalFieldError('bookGenres') ? styles.inputError : ''}`}
                  multiple
                  value={form.genres}
                  onChange={(event) => {
                    const selected = Array.from(
                      event.target.selectedOptions,
                      (option) => option.value,
                    )
                    clearModalFieldError('bookGenres')
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
                {getModalFieldError('bookGenres') ? <span className={styles.fieldError}>{getModalFieldError('bookGenres')}</span> : null}
                <span className={styles.counterText}>Выбрано жанров: {form.genres.length}</span>
              </div>
            </label>
            <label className={styles.label}>
              <span className={styles.labelTitle}>Обложка</span>
              <span className={styles.fieldHint}>Необязательно · JPG, JPEG, PNG, GIF · до 5 МБ</span>
              <input
                type="file"
                accept=".jpg,.jpeg,.png,.gif,image/jpeg,image/png,image/gif"
                className={`${styles.inputFile} ${getModalFieldError('bookCoverFile') ? styles.inputError : ''}`}
                onChange={handleCoverInputChange}
              />
              <div className={styles.counterRow}>
                {getModalFieldError('bookCoverFile') ? <span className={styles.fieldError}>{getModalFieldError('bookCoverFile')}</span> : null}
                <span className={styles.counterText}>{form.coverFile ? `Выбран файл: ${form.coverFile.name}` : 'Файл не выбран'}</span>
              </div>
            </label>
            <label className={styles.label}>
              <span className={styles.labelTitle}>Файлы книги</span>
              <span className={styles.fieldHint}>Необязательно · PDF, FB2, TXT · до 50 МБ каждый</span>
              <input
                type="file"
                accept=".pdf,.fb2,.txt"
                className={`${styles.inputFile} ${getModalFieldError('bookFiles') ? styles.inputError : ''}`}
                multiple
                onChange={handleFilesInputChange}
              />
              <div className={styles.counterRow}>
                {getModalFieldError('bookFiles') ? <span className={styles.fieldError}>{getModalFieldError('bookFiles')}</span> : null}
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

  const renderUsersTab = () => (
    <div className={styles.tabContent}>
      <div className={styles.listSection}>
        <div className={styles.sectionHeader}>
          <h2>Пользователи ({filteredUsers.length})</h2>
        </div>
        <div className={styles.tableWrap}>
          <table className={styles.table}>
            <thead>
              <tr>
                <th>Логин</th>
                <th>Роль</th>
                <th>Действия</th>
              </tr>
            </thead>
            <tbody>
              {filteredUsers.map((u) => (
                <tr key={u.id}>
                  <td data-label="Логин">{u.login}</td>
                  <td data-label="Роль">
                    {u.roleId === 1 ? 'Администратор' : 'Пользователь'}
                  </td>
                  <td data-label="Действия" className={styles.actionsCell}>
                    <button
                      className={styles.actionButton}
                      type="button"
                      onClick={() => openUserModal(u)}
                    >
                      Редактировать
                    </button>
                    <button
                      className={styles.actionButtonDanger}
                      type="button"
                      onClick={() => handleDeleteUser(u.id)}
                      disabled={u.id === user?.id} // Защита от удаления себя
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
        isOpen={isUserModalOpen}
        onClose={closeAllModals}
        title={modalMode === 'create' ? 'Добавление пользователя' : 'Редактирование пользователя'}
      >
        <form className={styles.form} onSubmit={handleSubmitUser}>
          {modalFormError ? <p className={styles.formError}>{modalFormError}</p> : null}
          <label className={styles.label}>
            <span className={styles.labelTitle}>Логин</span>
            <span className={styles.fieldHint}>Обязательно · минимум 1 символ</span>
            <input
              className={`${styles.input} ${getModalFieldError('userLogin') ? styles.inputError : ''}`}
              value={userForm.login}
              maxLength={255}
              onChange={(event) => {
                const value = event.target.value.replace(/[^\w.@+-]/g, '')
                clearModalFieldError('userLogin')
                setUserForm((prev) => ({ ...prev, login: value }))
              }}
              autoFocus
              placeholder="Введите логин"
            />
            <div className={styles.counterRow}>
              {getModalFieldError('userLogin') ? <span className={styles.fieldError}>{getModalFieldError('userLogin')}</span> : null}
              <span className={styles.counterText}>Символов: {userForm.login.length} / 255</span>
            </div>
          </label>

          <label className={styles.label}>
            <span className={styles.labelTitle}>Роль</span>
            <span className={styles.fieldHint}>Выберите роль пользователя</span>
            <select
              className={styles.select}
              value={userForm.role_id}
              onChange={(event) => setUserForm((prev) => ({ 
                ...prev, 
                role_id: Number(event.target.value) 
              }))}
            >
              <option value={2}>Пользователь</option>
              <option value={1}>Администратор</option>
            </select>
          </label>

          <label className={styles.label}>
            <span className={styles.labelTitle}>
              {modalMode === 'create' ? 'Пароль' : 'Новый пароль (необязательно)'}
            </span>
            <span className={styles.fieldHint}>
              {modalMode === 'create' 
                ? 'Обязательно · минимум 8 символов' 
                : 'Оставьте пустым, чтобы не менять · минимум 8 символов'}
            </span>
            <input
              className={`${styles.input} ${getModalFieldError('userPassword') ? styles.inputError : ''}`}
              type="password"
              value={userForm.password}
              maxLength={255}
              onChange={(event) => {
                clearModalFieldError('userPassword')
                clearModalFieldError('userPasswordConfirmation')
                setUserForm((prev) => ({ ...prev, password: event.target.value }))
              }}
              placeholder={modalMode === 'create' ? 'Введите пароль' : 'Оставьте пустым для сохранения текущего'}
            />
            <div className={styles.counterRow}>
              {getModalFieldError('userPassword') ? <span className={styles.fieldError}>{getModalFieldError('userPassword')}</span> : null}
              <span className={styles.counterText}>Символов: {userForm.password.length} / 255</span>
            </div>
          </label>

          <label className={styles.label}>
            <span className={styles.labelTitle}>
              {modalMode === 'create' ? 'Подтверждение пароля' : 'Потверждение нового пароля (необязательно)'}
            </span>
            <span className={styles.fieldHint}>
              {modalMode === 'create' 
                ? 'Обязательно · минимум 8 символов' 
                : 'Оставьте пустым, чтобы не менять · минимум 8 символов'}
            </span>
            <input
              className={`${styles.input} ${getModalFieldError('userPasswordConfirmation') ? styles.inputError : ''}`}
              type="password"
              value={userForm.password_confirmation}
              maxLength={255}
              onChange={(event) => {
                clearModalFieldError('userPasswordConfirmation')
                setUserForm((prev) => ({ ...prev, password_confirmation: event.target.value }))
              }}
              placeholder={modalMode === 'create' ? 'Введите подтверждение пароля' : 'Оставьте пустым для сохранения текущего'}
            />
            <div className={styles.counterRow}>
              {getModalFieldError('userPasswordConfirmation') ? <span className={styles.fieldError}>{getModalFieldError('userPasswordConfirmation')}</span> : null}
              <span className={styles.counterText}>Символов: {userForm.password_confirmation.length} / 255</span>
            </div>
          </label>

          <button className={styles.saveButton} type="submit" disabled={isUserSaving}>
            {isUserSaving ? 'Сохранение...' : modalMode === 'create' ? 'Создать' : 'Сохранить'}
          </button>
        </form>
      </Modal>
    </div>
  )

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
          <button
            className={`${styles.tab} ${activeTab === 'users' ? styles.activeTab : ''}`}
            onClick={() => handleTabChange('users')}
          >
            Пользователи
          </button>
        </div>

        {activeTab === 'books' && renderBooksTab()}
        {activeTab === 'authors' && renderAuthorsTab()}
        {activeTab === 'genres' && renderGenresTab()}
        {activeTab === 'publishers' && renderPublishersTab()}
        {activeTab === 'users' && renderUsersTab()}
      </section>
    </main>
  )
}