import type { KeyboardEvent } from 'react'
import type { Book } from '@models/library'
import styles from './BookCard.module.css'

type BookCardProps = {
  book: Book
  onClick?: () => void
}

export const BookCard = ({ book, onClick }: BookCardProps) => {
  const isInteractive = Boolean(onClick)

  const handleKeyDown = (event: KeyboardEvent<HTMLElement>) => {
    if (!onClick) {
      return
    }

    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault()
      onClick()
    }
  }

  return (
    <article
      className={`${styles.bookCard} ${isInteractive ? styles.interactive : ''}`}
      onClick={onClick}
      onKeyDown={handleKeyDown}
      role={isInteractive ? 'button' : undefined}
      tabIndex={isInteractive ? 0 : undefined}
    >
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