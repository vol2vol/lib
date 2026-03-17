import type { Book } from '@models/library'
import styles from './BookCard.module.css'

type BookCardProps = {
  book: Book
}

export const BookCard = ({ book }: BookCardProps) => {
  return (
    <article className={styles.bookCard}>
      {book.coverUrl ? (
        <img
          className={styles.cover}
          src={book.coverUrl}
          alt={book.title}
          loading="lazy"
        />
      ) : (
        <div className={styles.coverPlaceholder} />
      )}

      <div className={styles.info}>
        <h3 className={styles.title}>{book.title}</h3>
        <p className={styles.meta}>{book.author}</p>
        <p className={styles.meta}>{book.genre}</p>
        <p className={styles.meta}>{book.publisher}</p>
        {book.publishedYear ? <p className={styles.meta}>{book.publishedYear}</p> : null}
      </div>
    </article>
  )
}