import type { KeyboardEvent } from 'react'
import { useState, useRef, useEffect } from 'react'
import type { Book } from '@models/library'
import styles from './BookCard.module.css'

type BookCardProps = {
  book: Book
  onClick?: () => void
}

// Хук для проверки переполнения текста
const useTextOverflow = () => {
  const containerRef = useRef<HTMLDivElement>(null)
  const contentRef = useRef<HTMLDivElement>(null)
  const [isOverflowing, setIsOverflowing] = useState(false)

  useEffect(() => {
    const checkOverflow = () => {
      if (containerRef.current && contentRef.current) {
        const containerWidth = containerRef.current.clientWidth
        const contentWidth = contentRef.current.scrollWidth
        setIsOverflowing(contentWidth > containerWidth)
      }
    }

    checkOverflow()
    
    window.addEventListener('resize', checkOverflow)
    return () => window.removeEventListener('resize', checkOverflow)
  }, [])

  return { containerRef, contentRef, isOverflowing }
}

export const BookCard = ({ book, onClick }: BookCardProps) => {
  const isInteractive = Boolean(onClick)
  const [isHovered, setIsHovered] = useState(false)
  const { 
    containerRef: authorContainerRef, 
    contentRef: authorContentRef, 
    isOverflowing: isAuthorOverflowing 
  } = useTextOverflow()
  const { 
    containerRef: genreContainerRef, 
    contentRef: genreContentRef, 
    isOverflowing: isGenreOverflowing 
  } = useTextOverflow()
  const { 
    containerRef: titleContainerRef, 
    contentRef: titleContentRef, 
    isOverflowing: isTitleOverflowing 
  } = useTextOverflow()
  const { 
    containerRef: publisherContainerRef, 
    contentRef: publisherContentRef, 
    isOverflowing: isPublisherOverflowing 
  } = useTextOverflow()

  const handleKeyDown = (event: KeyboardEvent<HTMLElement>) => {
    if (!onClick) {
      return
    }

    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault()
      onClick()
    }
  }

  const handleMouseEnter = () => setIsHovered(true)
  const handleMouseLeave = () => setIsHovered(false)

  return (
    <article
      className={`${styles.bookCard} ${isInteractive ? styles.interactive : ''}`}
      onClick={onClick}
      onKeyDown={handleKeyDown}
      role={isInteractive ? 'button' : undefined}
      tabIndex={isInteractive ? 0 : undefined}
      onMouseEnter={handleMouseEnter}
      onMouseLeave={handleMouseLeave}
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
        {/* Заголовок - с бегущей строкой */}
        <div 
          className={styles.titleContainer}
          ref={titleContainerRef}
        >
          <h3 
            className={`${styles.title} ${isTitleOverflowing && isHovered ? styles.marquee : ''}`}
            ref={titleContentRef}
          >
            <span>{book.title}</span>
            {isTitleOverflowing && isHovered && (
              <span className={styles.marqueeSpacer} aria-hidden="true" />
            )}
            {isTitleOverflowing && isHovered && (
              <span aria-hidden="true">{book.title}</span>
            )}
          </h3>
        </div>
        
        {/* Автор - отдельная строка с бегущей строкой */}
        <div 
          className={styles.metaContainer}
          ref={authorContainerRef}
        >
          <div 
            className={`${styles.metaContent} ${isAuthorOverflowing && isHovered ? styles.marquee : ''}`}
            ref={authorContentRef}
          >
            <span className={styles.author}>{book.author}</span>
            {isAuthorOverflowing && isHovered && (
              <span className={styles.marqueeSpacer} aria-hidden="true" />
            )}
            {isAuthorOverflowing && isHovered && (
              <span className={styles.author} aria-hidden="true">{book.author}</span>
            )}
          </div>
        </div>

        {/* Жанр - отдельная строка с бегущей строкой */}
        {book.genre && (
          <div 
            className={styles.metaContainer}
            ref={genreContainerRef}
          >
            <div 
              className={`${styles.metaContent} ${isGenreOverflowing && isHovered ? styles.marquee : ''}`}
              ref={genreContentRef}
            >
              <span className={styles.genre}>{book.genre}</span>
              {isGenreOverflowing && isHovered && (
                <span className={styles.marqueeSpacer} aria-hidden="true" />
              )}
              {isGenreOverflowing && isHovered && (
                <span className={styles.genre} aria-hidden="true">{book.genre}</span>
              )}
            </div>
          </div>
        )}

        {/* Издательство - с бегущей строкой */}
        <div 
          className={styles.metaContainer}
          ref={publisherContainerRef}
        >
          <div 
            className={`${styles.metaContent} ${isPublisherOverflowing && isHovered ? styles.marquee : ''}`}
            ref={publisherContentRef}
          >
            <span>{book.publisher.name}</span>
            {isPublisherOverflowing && isHovered && (
              <span className={styles.marqueeSpacer} aria-hidden="true" />
            )}
            {isPublisherOverflowing && isHovered && (
              <span aria-hidden="true">{book.publisher.name}</span>
            )}
          </div>
        </div>

        {book.publishedYear ? <p className={styles.meta}>{book.publishedYear}</p> : null}
      </div>
    </article>
  )
}