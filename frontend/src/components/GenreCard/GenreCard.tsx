import styles from './GenreCard.module.css'

type GenreCardProps = {
  name: string
  onClick?: () => void
}

export const GenreCard = ({ name, onClick }: GenreCardProps) => {
  return (
    <button type="button" className={styles.genreCard} onClick={onClick}>
      <span className={styles.title}>{name}</span>
    </button>
  )
}