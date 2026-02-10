import { useState } from 'react'
import type { Book } from '@models/book'
import { BookCard } from '@components/BookCard/BookCard'

type Props = {
  books: Book[]
  pageSize?: number
}

export const BookList = ({ books, pageSize = 10 }: Props) => {
  const [page, setPage] = useState(1)

  const start = (page - 1) * pageSize
  const end = start + pageSize

  const paginatedBooks = books.slice(start, end)
  const totalPages = Math.ceil(books.length / pageSize)

  return (
    <div>
      <div>
        {paginatedBooks.map(book => (
          <BookCard key={book.id} book={book} />
        ))}
      </div>

      {totalPages > 1 && (
        <div>
          <button
            onClick={() => setPage(p => Math.max(p - 1, 1))}
            disabled={page === 1}
          >
            Назад
          </button>

          <span>
            {page} / {totalPages}
          </span>

          <button
            onClick={() => setPage(p => Math.min(p + 1, totalPages))}
            disabled={page === totalPages}
          >
            Вперёд
          </button>
        </div>
      )}
    </div>
  )
}
