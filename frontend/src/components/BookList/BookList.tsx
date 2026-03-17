import { BookCard } from '@components/BookCard'
import type { Book } from '@models/library'
import styles from './BookList.module.css'

type BookListProps = {
  books: Book[]
  onBookClick?: (book: Book) => void
}

export const BookList = ({ books, onBookClick }: BookListProps) => {
  return (
    <div className={styles.grid}>
      {books.map((book) => (
        <BookCard
          key={book.id}
          book={book}
          onClick={onBookClick ? () => onBookClick(book) : undefined}
        />
      ))}
    </div>
  )
}