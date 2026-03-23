import { buildUrl, createHeaders, parseResponse, ApiError } from './http'
import type {
  Book,
  BookDetailsResponseDto,
  BookDto,
  BookFile,
  BookFileDto,
  BookListResponseDto,
  BooksListResult,
  Author,
  AuthorDto,
  Genre,
  GenreDto,
  Publisher,
  PublisherDto,
  BookFormPayload,
  GetBooksParams,
  GetFavoritesParams,
  FavoritesResponseDto,
  PublisherFormPayload,
  AuthorFormPayload,
  GenreFormPayload,
} from '@models/library'

const createQueryString = (params?: GetBooksParams | GetFavoritesParams) => {
  if (!params) {
    return ''
  }

  const searchParams = new URLSearchParams()

  Object.entries(params).forEach(([key, value]) => {
    if (value === undefined || value === null) {
      return
    }

    if (typeof value === 'string') {
      const normalized = value.trim()

      if (!normalized) {
        return
      }

      searchParams.set(key, normalized)
      return
    }

    if (Array.isArray(value)) {
      value.forEach((item) => {
        if (item === undefined || item === null) {
          return
        }

        searchParams.append(`${key}[]`, String(item))
      })
      return
    }

    searchParams.set(key, String(value))
  })

  const query = searchParams.toString()
  return query ? `?${query}` : ''
}

const normalizeUrl = (url?: string | null) => {
  if (!url) {
    return null
  }

  if (url.startsWith('http://') || url.startsWith('https://')) {
    return url
  }

  if (url.startsWith('/api/')) {
    return url
  }

  return url.startsWith('/') ? url : `/${url}`
}

const mapAuthor = (author: AuthorDto, index: number): Author => ({
  id: author.author_id ?? index,
  lastName: author.last_name ?? '',
  firstName: author.first_name ?? '',
  middleName: author.middle_name ?? null,
  fullName:
    [author.last_name, author.first_name, author.middle_name].filter(Boolean).join(' ') ||
    `Автор ${index + 1}`,
})

const mapGenre = (genre: GenreDto, index: number): Genre => ({
  id: genre.genre_id ?? index,
  name: genre.genre_name ?? `Жанр ${index + 1}`,
})

const mapPublisher = (publisher: PublisherDto, index: number): Publisher => ({
  id: publisher.publisher_id ?? index,
  name: publisher.publisher_name ?? `Издательство ${index + 1}`,
})

const getGenreName = (genres?: BookDto['genres']) => {
  if (!Array.isArray(genres) || genres.length === 0) {
    return 'Без жанра'
  }

  return genres.map((item) => item.genre_name).filter(Boolean).join(', ') || 'Без жанра'
}

const getAuthorName = (authors?: BookDto['authors']) => {
  if (!Array.isArray(authors) || authors.length === 0) {
    return 'Автор неизвестен'
  }

  return (
    authors
      .map((author) =>
        [author.last_name, author.first_name, author.middle_name].filter(Boolean).join(' ')
      )
      .filter(Boolean)
      .join(', ') || 'Автор неизвестен'
  )
}

const mapBookFile = (file: BookFileDto, index: number): BookFile => ({
  id: file.file_id ?? index,
  formatId: file.format_id ?? null,
  formatName: file.format_name ?? 'Неизвестный формат',
  sizeBytes: file.file_size_bytes ?? 0,
  sizeMb: file.file_size_mb ?? 0,
  readUrl: normalizeUrl(file.read_url),
  downloadUrl: normalizeUrl(file.download_url),
})

const mapBook = (book: BookDto, index: number): Book => ({
  id: book.book_id ?? index,
  title: book.book_title ?? `Книга ${index + 1}`,
  description: book.description ?? '',
  genre: getGenreName(book.genres),
  author: getAuthorName(book.authors),
  genres: Array.isArray(book.genres) ? book.genres.map(mapGenre) : [],
  authors: Array.isArray(book.authors) ? book.authors.map(mapAuthor) : [],
  publisher: [book.publisher].map(mapPublisher)[0],
  publishedYear: book.published_year ?? null,
  coverUrl: normalizeUrl(book.cover_url),
  isFavorited: Boolean(book.is_favorited),
  filesCount: book.files_count ?? book.files?.length ?? 0,
  files: Array.isArray(book.files) ? book.files.map(mapBookFile) : [],
})

const extractFileName = (response: Response, fallbackFileName: string) => {
  const contentDisposition = response.headers.get('content-disposition') ?? ''

  const utf8Match = contentDisposition.match(/filename\*=UTF-8''([^;]+)/i)
  if (utf8Match?.[1]) {
    return decodeURIComponent(utf8Match[1])
  }

  const plainMatch = contentDisposition.match(/filename="?([^"]+)"?/i)
  if (plainMatch?.[1]) {
    return plainMatch[1]
  }

  return fallbackFileName
}

const getProtectedFileBlob = async (
  path: string,
  token: string,
  fallbackFileName: string
): Promise<{ blob: Blob; fileName: string; contentType: string }> => {
  const response = await fetch(buildUrl(path), {
    method: 'GET',
    headers: createHeaders(token),
  })

  if (!response.ok) {
    const raw = await response.clone().text()
    let message = `Ошибка запроса: ${response.status}`

    if (raw) {
      try {
        const data = JSON.parse(raw)
        if (data?.message) {
          message = data.message
        }
      } catch {
        message = 'Не удалось получить файл'
      }
    }

    throw new ApiError(message, response.status)
  }

  const blob = await response.blob()
  const fileName = extractFileName(response, fallbackFileName)
  const contentType = response.headers.get('content-type') ?? blob.type ?? ''

  return { blob, fileName, contentType }
}

export const getGenres = async (): Promise<Genre[]> => {
  const response = await fetch(buildUrl('/genres'), {
    headers: createHeaders(),
  })

  const data = await parseResponse<GenreDto[]>(response)
  return data.map(mapGenre)
}

export const getAuthors = async (): Promise<Author[]> => {
  const response = await fetch(buildUrl('/authors'), {
    headers: createHeaders(),
  })

  const data = await parseResponse<AuthorDto[]>(response)
  return data.map(mapAuthor)
}

export const getPublishers = async (): Promise<Publisher[]> => {
  const response = await fetch(buildUrl('/publishers'), {
    headers: createHeaders(),
  })

  const data = await parseResponse<PublisherDto[]>(response)
  return data.map(mapPublisher)
}

export const getAdminGenres = async (token: string): Promise<Genre[]> => {
  const response = await fetch(buildUrl('/genres'), {
    headers: createHeaders(token),
  })

  const data = await parseResponse<{ data: GenreDto[] } | GenreDto[]>(response)
  const genres = Array.isArray(data) ? data : Array.isArray(data.data) ? data.data : []
  return genres.map(mapGenre)
}

export const getAdminAuthors = async (token: string): Promise<Author[]> => {
  const response = await fetch(buildUrl('/authors'), {
    headers: createHeaders(token),
  })

  const data = await parseResponse<{ data: AuthorDto[] } | AuthorDto[]>(response)
  const authors = Array.isArray(data) ? data : Array.isArray(data.data) ? data.data : []
  return authors.map(mapAuthor)
}

export const getAdminPublishers = async (token: string): Promise<Publisher[]> => {
  const response = await fetch(buildUrl('/publishers'), {
    headers: createHeaders(token),
  })

  const data = await parseResponse<{ data: PublisherDto[] } | PublisherDto[]>(response)
  const publishers = Array.isArray(data) ? data : Array.isArray(data.data) ? data.data : []
  return publishers.map(mapPublisher)
}

export const getBooks = async (params?: GetBooksParams): Promise<BooksListResult> => {
  const response = await fetch(buildUrl(`/books${createQueryString(params)}`), {
    headers: createHeaders(),
  })

  const data = await parseResponse<BookListResponseDto>(response)
  const items = Array.isArray(data.data) ? data.data.map(mapBook) : []

  return {
    items,
    currentPage: data.current_page ?? 1,
    lastPage: data.last_page ?? 1,
    perPage: data.per_page ?? items.length,
    total: data.total ?? items.length,
    nextPageUrl: data.next_page_url ?? null,
    prevPageUrl: data.prev_page_url ?? null,
    message: data.message ?? null,
  }
}

export const getAllBooks = async (params?: Omit<GetBooksParams, 'page'>): Promise<Book[]> => {
  const resultMap = new Map<number, Book>()
  let currentPage = 1
  let lastPage = 1

  do {
    const response = await getBooks({
      ...params,
      page: currentPage,
      per_page: params?.per_page ?? 100,
    })

    response.items.forEach((item) => {
      resultMap.set(item.id, item)
    })

    lastPage = response.lastPage
    currentPage += 1
  } while (currentPage <= lastPage)

  return Array.from(resultMap.values())
}

export const getBookById = async (bookId: number, token?: string): Promise<Book> => {
  const response = await fetch(buildUrl(`/books/${bookId}`), {
    headers: createHeaders(token),
  })

  const data = await parseResponse<BookDetailsResponseDto>(response)

  if (!data.data) {
    throw new Error(data.message || 'Книга не найдена')
  }

  return mapBook(data.data, 0)
}

export const getFavorites = async (
  token: string,
  params?: GetFavoritesParams
): Promise<BooksListResult> => {
  const response = await fetch(buildUrl(`/favorites${createQueryString(params)}`), {
    headers: createHeaders(token),
  })

  const data = await parseResponse<FavoritesResponseDto>(response)
  const items = Array.isArray(data.data?.data) ? data.data.data.map(mapBook) : []

  return {
    items,
    currentPage: data.data?.current_page ?? 1,
    lastPage: data.data?.last_page ?? 1,
    perPage: data.data?.per_page ?? items.length,
    total: data.data?.total ?? items.length,
    nextPageUrl: data.data?.next_page_url ?? null,
    prevPageUrl: data.data?.prev_page_url ?? null,
    message: null,
  }
}

export const getAllFavorites = async (token: string): Promise<Book[]> => {
  const response = await getFavorites(token, { all: true })
  return response.items
}

export const addToFavorites = async (bookId: number, token: string): Promise<void> => {
  const response = await fetch(buildUrl(`/favorites/${bookId}`), {
    method: 'POST',
    headers: createHeaders(token),
  })

  await parseResponse<void>(response)
}

export const removeFromFavorites = async (bookId: number, token: string): Promise<void> => {
  const response = await fetch(buildUrl(`/favorites/${bookId}`), {
    method: 'DELETE',
    headers: createHeaders(token),
  })

  await parseResponse<void>(response)
}

export const getBookFileForReading = async (
  fileId: number,
  token: string
): Promise<{ blob: Blob; fileName: string; contentType: string }> => {
  return getProtectedFileBlob(`/books/file/${fileId}/read`, token, `book-file-${fileId}`)
}

export const readBookFile = async (fileId: number, token: string): Promise<string> => {
  const { blob } = await getProtectedFileBlob(
    `/books/file/${fileId}/read`,
    token,
    `book-file-${fileId}`
  )

  return URL.createObjectURL(blob)
}

export const downloadBookFile = async (fileId: number, token: string): Promise<void> => {
  const { blob, fileName } = await getProtectedFileBlob(
    `/books/file/${fileId}/download`,
    token,
    `book-file-${fileId}`
  )

  const blobUrl = URL.createObjectURL(blob)

  const link = document.createElement('a')
  link.href = blobUrl
  link.download = fileName
  document.body.appendChild(link)
  link.click()
  link.remove()

  URL.revokeObjectURL(blobUrl)
}

const mapBookFromDetailsResponse = (data: BookDetailsResponseDto): Book => {
  if (!data.data) {
    throw new Error(data.message || 'Книга не найдена')
  }

  return mapBook(data.data, 0)
}

export const createGenre = async (
  payload: GenreFormPayload,
  token: string
): Promise<Genre> => {
  const formData = new FormData()

  formData.append('genre_name', payload.genre_name)

  const response = await fetch(buildUrl('/admin/genres'), {
    method: 'POST',
    headers: createHeaders(token),
    body: formData,
  })

  const data = await parseResponse<GenreDto>(response)
  return mapGenre(data, 0)
}

export const updateGenre = async (
  genreId: number,
  payload: GenreFormPayload,
  token: string
): Promise<Genre> => {
  const formData = new FormData()

  formData.append('genre_name', payload.genre_name)

  const response = await fetch(buildUrl(`/admin/genres/${genreId}`), {
    method: 'PUT',
    headers: createHeaders(token),
    body: formData,
  })

  const data = await parseResponse<GenreDto>(response)
  return mapGenre(data, 0)
}

export const deleteGenre = async (
  genreId: number,
  token: string
): Promise<void> => {

  await fetch(buildUrl(`/admin/genres/${genreId}`), {
    method: 'DELETE',
    headers: createHeaders(token)
  })
}

export const createAuthor = async (
  payload: AuthorFormPayload,
  token: string
): Promise<Author> => {
  const formData = new FormData()

  formData.append('first_name', payload.first_name)
  formData.append('middle_name', payload.middle_name ? payload.middle_name : '')
  formData.append('last_name', payload.last_name)

  const response = await fetch(buildUrl('/admin/authors'), {
    method: 'POST',
    headers: createHeaders(token),
    body: formData,
  })

  const data = await parseResponse<AuthorDto>(response)
  return mapAuthor(data, 0)
}

export const updateAuthor = async (
  authorId: number,
  payload: AuthorFormPayload,
  token: string
): Promise<Author> => {
  const formData = new FormData()

  formData.append('first_name', payload.first_name)
  formData.append('middle_name', payload.middle_name ? payload.middle_name : '')
  formData.append('last_name', payload.last_name)

  const response = await fetch(buildUrl(`/admin/authors/${authorId}`), {
    method: 'PUT',
    headers: createHeaders(token),
    body: formData,
  })

  const data = await parseResponse<AuthorDto>(response)
  return mapAuthor(data, 0)
}

export const deleteAuthor = async (
  authorId: number,
  token: string
): Promise<void> => {


  await fetch(buildUrl(`/admin/authors/${authorId}`), {
    method: 'DELETE',
    headers: createHeaders(token)
  })
}

export const createPublisher = async (
  payload: PublisherFormPayload,
  token: string
): Promise<Publisher> => {
  const formData = new FormData()

  formData.append('publisher_name', payload.publisher_name)

  const response = await fetch(buildUrl('/admin/publishers'), {
    method: 'POST',
    headers: createHeaders(token),
    body: formData,
  })

  const data = await parseResponse<PublisherDto>(response)
  return mapPublisher(data, 0)
}

export const updatePublisher = async (
  publisherId: number,
  payload: PublisherFormPayload,
  token: string
): Promise<Publisher> => {
  const formData = new FormData()

  formData.append('publisher_name', payload.publisher_name)

  const response = await fetch(buildUrl(`/admin/publishers/${publisherId}`), {
    method: 'PUT',
    headers: createHeaders(token),
    body: formData,
  })

  const data = await parseResponse<PublisherDto>(response)
  return mapPublisher(data, 0)
}

export const deletePublisher = async (
  publisherId: number,
  token: string
): Promise<void> => {


  await fetch(buildUrl(`/admin/publishers/${publisherId}`), {
    method: 'DELETE',
    headers: createHeaders(token)
  })
}

export const createBook = async (
  payload: BookFormPayload,
  token: string,
  coverFile?: File,
  files?: File[]
): Promise<Book> => {
  const formData = new FormData()

  formData.append('book_title', payload.book_title)
  formData.append('description', payload.description ?? '')

  payload.authors.forEach((author) => {
    formData.append('author_ids[]', author)
  })

  if (payload.published_year !== undefined && payload.published_year !== null) {
    formData.append('published_year', String(payload.published_year))
  }

  payload.genres.forEach((genre) => {
    formData.append('genre_ids[]', genre)
  })

  if (payload.publisher) {
    formData.append('publisher_id', payload.publisher)
  }

  if (coverFile) {
    formData.append('cover', coverFile)
  }

  if (files && files.length > 0) {
    var i = 0;
    files.forEach((file) => {
      var file_name_split = file.name.split('.')
      var format_name = file_name_split[file_name_split.length - 1]
      var format_id = ''
      if (format_name == 'pdf') {
        format_id = '1'
      } else if (format_name == 'txt') {
        format_id = '2'
      } else {
        format_id = '3'
      }
      formData.append(`files[${i}][file]`, file)
      formData.append(`files[${i}][format_id]`, format_id)
      i+=1;
    })
  }

  const response = await fetch(buildUrl('/admin/books'), {
    method: 'POST',
    headers: createHeaders(token),
    body: formData,
  })

  const data = await parseResponse<BookDetailsResponseDto>(response)
  return mapBookFromDetailsResponse(data)
}

export const updateBook = async (
  bookId: number,
  payload: BookFormPayload,
  token: string,
  coverFile?: File,
  files?: File[]
): Promise<Book> => {
  const formData = new FormData()

  formData.append('book_title', payload.book_title)
  formData.append('description', payload.description ?? '')

  payload.authors.forEach((author) => {
    formData.append('authors[]', author)
    formData.append('author_ids[]', author)
  })

  if (payload.published_year !== undefined && payload.published_year !== null) {
    formData.append('published_year', String(payload.published_year))
  }

  payload.genres.forEach((genre) => {
    formData.append('genres[]', genre)
    formData.append('genre_ids[]', genre)
  })

  if (payload.publisher) {
    formData.append('publisher', payload.publisher)
  }

  if (coverFile) {
    formData.append('cover', coverFile)
  }

  if (files && files.length > 0) {
    var i = 0;
    files.forEach((file) => {
      var file_name_split = file.name.split('.')
      var format_name = file_name_split[file_name_split.length - 1]
      var format_id = ''
      if (format_name == 'pdf') {
        format_id = '1'
      } else if (format_name == 'txt') {
        format_id = '2'
      } else {
        format_id = '3'
      }
      formData.append(`files[${i}][file]`, file)
      formData.append(`files[${i}][format_id]`, format_id)
      i+=1;
    })
  }

  const response = await fetch(buildUrl(`/admin/books/${bookId}`), {
    method: 'PUT',
    headers: createHeaders(token, { Accept: 'application/json' }),
    body: formData,
  })

  const data = await parseResponse<BookDetailsResponseDto>(response)
  return mapBookFromDetailsResponse(data)
}

export const deleteBook = async (bookId: number, token: string): Promise<void> => {
  const response = await fetch(buildUrl(`/admin/books/${bookId}`), {
    method: 'DELETE',
    headers: createHeaders(token),
  })

  await parseResponse<void>(response)
}

// Интерфейсы для ответов с пагинацией
export interface PaginatedResponse<T> {
  items: T[]
  total: number
  page: number
  per_page: number
  lastPage: number
}

// Методы для получения данных с пагинацией
export const getAdminGenresPaginated = async (
  token: string,
  params: {
    page?: number
    per_page?: number
    search?: string
  } = {}
): Promise<PaginatedResponse<Genre>> => {
  const queryParams = new URLSearchParams()
  if (params.page) queryParams.append('page', params.page.toString())
  if (params.per_page) queryParams.append('per_page', params.per_page.toString())
  if (params.search) queryParams.append('search', params.search)
  
  const response = await fetch(buildUrl(`/admin/genres?${queryParams}`), {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  })
  
  if (!response.ok) {
    throw new Error('Ошибка загрузки жанров')
  }
  
  return response.json()
}

export const getAdminAuthorsPaginated = async (
  token: string,
  params: {
    page?: number
    per_page?: number
    search?: string
  } = {}
): Promise<PaginatedResponse<Author>> => {
  const queryParams = new URLSearchParams()
  if (params.page) queryParams.append('page', params.page.toString())
  if (params.per_page) queryParams.append('per_page', params.per_page.toString())
  if (params.search) queryParams.append('search', params.search)
  
  const response = await fetch(buildUrl('/admin/authors?${queryParams}'), {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  })
  
  if (!response.ok) {
    throw new Error('Ошибка загрузки авторов')
  }
  
  return response.json()
}

export const getAdminPublishersPaginated = async (
  token: string,
  params: {
    page?: number
    per_page?: number
    search?: string
  } = {}
): Promise<PaginatedResponse<Publisher>> => {
  const queryParams = new URLSearchParams()
  if (params.page) queryParams.append('page', params.page.toString())
  if (params.per_page) queryParams.append('per_page', params.per_page.toString())
  if (params.search) queryParams.append('search', params.search)
  
  const response = await fetch(buildUrl('/admin/publishers?${queryParams}'), {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  })
  
  if (!response.ok) {
    throw new Error('Ошибка загрузки издательств')
  }
  
  return response.json()
}