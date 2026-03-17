import { useMemo, type WheelEvent } from 'react'
import { GenreCard } from '@components/GenreCard'
import type { Genre } from 'models/library'
import styles from './GenreList.module.css'

type GenreListProps = {
  genres: Genre[]
  onGenreClick?: (genre: Genre) => void
}

export const GenreList = ({ genres, onGenreClick }: GenreListProps) => {
  const columns = useMemo(() => {
    const result: Genre[][] = []

    for (let i = 0; i < genres.length; i += 2) {
      result.push(genres.slice(i, i + 2))
    }

    return result
  }, [genres])

  const handleWheel = (event: WheelEvent<HTMLDivElement>) => {
    const element = event.currentTarget

    if (Math.abs(event.deltaY) <= Math.abs(event.deltaX)) {
      return
    }

    element.scrollLeft += event.deltaY
    event.preventDefault()
  }

  return (
    <div className={styles.viewport} onWheel={handleWheel}>
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