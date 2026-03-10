import { GenreCard } from '@components/GenreCard'
import type { Genre } from 'models/library'
import styles from './GenreList.module.css'

type GenreListProps = {
  genres: Genre[]
}

export const GenreList = ({ genres }: GenreListProps) => {
  return (
    <div className={styles.grid}>
      {genres.map((genre) => (
        <GenreCard key={genre.id} name={genre.name} />
      ))}
    </div>
  )
}