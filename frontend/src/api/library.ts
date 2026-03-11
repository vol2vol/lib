import { API_BASE_URL } from '@api/api'
import type { Book, BookDto, Genre, GenreDto } from 'models/library'

const getGenreName = (genres?: BookDto['genres']) => {
  if (!Array.isArray(genres) || genres.length === 0) {
    return 'Без жанра'
  }

  return (
    genres
      .map((item) => item.genre_name)
      .filter(Boolean)
      .join(', ') || 'Без жанра'
  )
}

const getAuthorName = (authors?: BookDto['authors']) => {
  if (!Array.isArray(authors) || authors.length === 0) {
    return 'Автор неизвестен'
  }

  return (
    authors
      .map((author) =>
        [author.last_name, author.first_name, author.middle_name]
          .filter(Boolean)
          .join(' ')
      )
      .filter(Boolean)
      .join(', ') || 'Автор неизвестен'
  )
}

const mapGenre = (genre: GenreDto, index: number): Genre => ({
  id: genre.genre_id ?? index,
  name: genre.genre_name ?? `Жанр ${index + 1}`,
})

const mapBook = (book: BookDto, index: number): Book => ({
  id: book.book_id ?? index,
  title: book.book_title ?? `Книга ${index + 1}`,
  genre: getGenreName(book.genres),
  author: getAuthorName(book.authors),
  publisher: book.publisher?.publisher_name ?? 'Издательство не указано',
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