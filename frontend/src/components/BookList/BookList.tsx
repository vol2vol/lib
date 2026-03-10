import { BookCard } from '@components/BookCard'
import type { Book } from '@models/library'
import styles from './BookList.module.css'

type BookListProps = {
  books: Book[]
}

export const BookList = ({ books }: BookListProps) => {
  return (
    <div className={styles.grid}>
      {books.map((book) => (
        <BookCard key={book.id} book={book} />
      ))}
    </div>
  )
}