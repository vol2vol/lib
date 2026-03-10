export type GenreDto = {
  genre_id?: number
  id?: number
  name?: string
  title?: string
}

export type BookDto = {
  book_id?: number
  id?: number
  title?: string
  name?: string
  genre?: string | { name?: string; title?: string }
  genres?: Array<{ name?: string; title?: string }>
  authors?: Array<{ name?: string; full_name?: string }>
  publisher?: { name?: string }
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