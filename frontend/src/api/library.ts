import { buildUrl, createHeaders, parseResponse } from './http'
import type {
  Book,
  BookDetailsResponseDto,
  BookDto,
  BookFile,
  BookFileDto,
  BookListResponseDto,
  BooksListResult,
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

  return buildUrl(url.startsWith('/') ? url : `/${url}`)
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
  publisher: book.publisher?.publisher_name ?? 'Издательство не указано',
  publishedYear: book.published_year ?? null,
  coverUrl: normalizeUrl(book.cover_url),
  isFavorited: Boolean(book.is_favorited),
  filesCount: book.files_count ?? book.files?.length ?? 0,
  files: Array.isArray(book.files) ? book.files.map(mapBookFile) : [],
})

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

export const getBookById = async (bookId: number): Promise<Book> => {
  const response = await fetch(buildUrl(`/books/${bookId}`), {
    headers: createHeaders(),
  })

  const data = await parseResponse<BookDetailsResponseDto>(response)

  if (!data.data) {
    throw new Error(data.message || 'Книга не найдена')
  }

  return mapBook(data.data, 0)
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