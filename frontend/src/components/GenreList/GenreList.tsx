import { useEffect, useMemo, useRef } from 'react'
import { GenreCard } from '@components/GenreCard'
import type { Genre } from 'models/library'
import styles from './GenreList.module.css'

type GenreListProps = {
  genres: Genre[]
  onGenreClick?: (genre: Genre) => void
}

export const GenreList = ({ genres, onGenreClick }: GenreListProps) => {
  const viewportRef = useRef<HTMLDivElement | null>(null)

  const columns = useMemo(() => {
    const result: Genre[][] = []

    for (let i = 0; i < genres.length; i += 2) {
      result.push(genres.slice(i, i + 2))
    }

    return result
  }, [genres])

  useEffect(() => {
    const element = viewportRef.current

    if (!element) {
      return
    }

    const handleWheel = (event: WheelEvent) => {
      if (Math.abs(event.deltaY) <= Math.abs(event.deltaX)) {
        return
      }

      const maxScrollLeft = element.scrollWidth - element.clientWidth
      const canScrollHorizontally = maxScrollLeft > 0

      if (!canScrollHorizontally) {
        return
      }

      const isScrollingDown = event.deltaY > 0
      const isAtStart = element.scrollLeft <= 0
      const isAtEnd = element.scrollLeft >= maxScrollLeft

      if ((!isScrollingDown && isAtStart) || (isScrollingDown && isAtEnd)) {
        return
      }

      event.preventDefault()
      element.scrollBy({
        left: event.deltaY,
        behavior: 'auto',
      })
    }

    element.addEventListener('wheel', handleWheel, { passive: false })

    return () => {
      element.removeEventListener('wheel', handleWheel)
    }
  }, [])

  return (
    <div ref={viewportRef} className={styles.viewport}>
      <div className={styles.track}>
        {columns.map((column, index) => (
          <div key={index} className={styles.column}>
            {column.map((genre) => (
              <GenreCard
                key={genre.id}
                name={genre.name}
                onClick={onGenreClick ? () => onGenreClick(genre) : undefined}
              />
            ))}
          </div>
        ))}
      </div>
    </div>
  )
}