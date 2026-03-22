export type GenreDto = {
  genre_id?: number
  genre_name?: string
  created_at?: string | null
  updated_at?: string | null
}

export type AuthorDto = {
  author_id?: number
  last_name?: string
  first_name?: string
  middle_name?: string
  birth_date?: string | null
}

export type PublisherDto = {
  publisher_id?: number
  publisher_name?: string
}

export type FormatDto = {
  format_id?: number
  format_name?: string
}

export type BookFileDto = {
  file_id?: number
  format_id?: number
  format_name?: string
  file_size_bytes?: number
  file_size_mb?: number
  read_url?: string
  download_url?: string
}

export type BookDto = {
  book_id?: number
  book_title?: string
  description?: string
  published_year?: number
  cover_url?: string
  is_favorited?: boolean
  genres?: GenreDto[]
  authors?: AuthorDto[]
  publisher?: PublisherDto | string
  files_count?: number
  files?: BookFileDto[]
}

export type PaginatedResponseDto<T> = {
  success?: boolean
  current_page?: number
  data?: T[]
  first_page_url?: string | null
  from?: number | null
  last_page?: number
  last_page_url?: string | null
  next_page_url?: string | null
  path?: string
  per_page?: number
  prev_page_url?: string | null
  to?: number | null
  total?: number
  message?: string
}

export type BookListResponseDto = PaginatedResponseDto<BookDto>

export type BookDetailsResponseDto = {
  success?: boolean
  data?: BookDto
  message?: string
}

export type FavoritesResponseDto = {
  success?: boolean
  data?: PaginatedResponseDto<BookDto>
}

export type Author = {
  id: number
  lastName: string
  firstName: string
  middleName: string | null
  fullName: string
}

export type Genre = {
  id: number
  name: string
}

export type Publisher = {
  id: number
  name: string
}

export type BookFile = {
  id: number
  formatId: number | null
  formatName: string
  sizeBytes: number
  sizeMb: number
  readUrl: string | null
  downloadUrl: string | null
}

export type Book = {
  id: number
  title: string
  description: string
  genre: string
  author: string
  genres: Genre[]
  authors: Author[]
  publisher: string
  publishedYear: number | null
  coverUrl: string | null
  isFavorited: boolean
  filesCount: number
  files: BookFile[]
}

export type BooksListResult = {
  items: Book[]
  currentPage: number
  lastPage: number
  perPage: number
  total: number
  nextPageUrl: string | null
  prevPageUrl: string | null
  message: string | null
}

export type GetBooksParams = {
  page?: number
  per_page?: number
  search?: string
  genre_id?: number
  author_id?: number
  genre_ids?: number[]
  author_ids?: number[]
  publisher_id?: number
  year_from?: number
  year_to?: number
  sort?: 'book_title' | 'published_year' | 'created_at'
  order?: 'asc' | 'desc'
}
export type GetFavoritesParams = {
  page?: number
  per_page?: number
  all?: boolean
}
export type BookFormPayload = {
  book_title: string
  description?: string
  published_year?: number
  author: string
  genres: string[]
  publisher?: string
}