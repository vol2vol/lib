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
}

export type PublisherDto = {
  publisher_id?: number
  publisher_name?: string
}

export type BookDto = {
  book_id?: number
  book_title?: string
  genres?: GenreDto[]
  authors?: AuthorDto[]
  publisher?: PublisherDto
}

export type Genre = {
  id: number
  name: string
}

export type Book = {
  id: number
  title: string
  genre: string
  author: string
  publisher: string
}