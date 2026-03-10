import { API_BASE_URL } from '@api/api'
import type { Book, BookDto, Genre, GenreDto } from 'models/library'

const getGenreName = (genre: BookDto['genre'], genres?: BookDto['genres']) => {
  if (typeof genre === 'string') {
    return genre
  }

  if (genre && typeof genre === 'object') {
    return genre.name || genre.title || 'Без жанра'
  }

  if (Array.isArray(genres) && genres.length > 0) {
    return genres[0].name || genres[0].title || 'Без жанра'
  }

  return 'Без жанра'
}

const getAuthorName = (authors?: BookDto['authors']) => {
  if (!Array.isArray(authors) || authors.length === 0) {
    return 'Автор неизвестен'
  }

  const firstAuthor = authors[0]

  return firstAuthor.full_name || firstAuthor.name || 'Автор неизвестен'
}

const mapGenre = (genre: GenreDto, index: number): Genre => ({
  id: genre.genre_id ?? genre.id ?? index,
  name: genre.name ?? genre.title ?? `Жанр ${index + 1}`,
})

const mapBook = (book: BookDto, index: number): Book => ({
  id: book.book_id ?? book.id ?? index,
  title: book.title ?? book.name ?? `Книга ${index + 1}`,
  genre: getGenreName(book.genre, book.genres),
  author: getAuthorName(book.authors),
  publisher: book.publisher?.name ?? 'Издательство не указано',
})

export const getGenres = async (): Promise<Genre[]> => {
  const response = await fetch(`${API_BASE_URL}/genres`, {
    headers: {
      Accept: 'application/json',
    },
  })

  if (!response.ok) {
    throw new Error('Не удалось загрузить жанры')
  }

  const data: GenreDto[] = await response.json()

  return data.map(mapGenre)
}

export const getBooks = async (): Promise<Book[]> => {
  const response = await fetch(`${API_BASE_URL}/books`, {
    headers: {
      Accept: 'application/json',
    },
  })

  if (!response.ok) {
    throw new Error('Не удалось загрузить книги')
  }

  const data: BookDto[] = await response.json()

  return data.map(mapBook)
}