import { buildUrl, createHeaders, parseResponse, ApiError } from './http'
import type {
  Book,
  BookDetailsResponseDto,
  BookDto,
  BookFile,
  BookFileDto,
  BookListResponseDto,
  BooksListResult,
  FavoritesResponseDto,
  Genre,
  GenreDto,
  GetBooksParams,
} from '@models/library'

const createQueryString = (params?: GetBooksParams) => {
  if (!params) {
    return ''
  }

  const searchParams = new URLSearchParams()

  Object.entries(params).forEach(([key, value]) => {
    if (value !== undefined && value !== null && value !== '') {
      searchParams.set(key, String(value))
    }
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

const mapGenre = (genre: GenreDto, index: number): Genre => ({
  id: genre.genre_id ?? index,
  name: genre.genre_name ?? `Жанр ${index + 1}`,
})

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
  publisher:
    typeof book.publisher === 'string'
      ? book.publisher
      : book.publisher?.publisher_name ?? 'Издательство не указано',
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

export const getFavorites = async (token: string): Promise<BooksListResult> => {
  const response = await fetch(buildUrl('/favorites'), {
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