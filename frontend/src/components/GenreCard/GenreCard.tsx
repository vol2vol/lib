import styles from './GenreCard.module.css'

type GenreCardProps = {
  name: string
}

export const GenreCard = ({ name }: GenreCardProps) => {
  return (
    <article className={styles.genreCard}>
      <span className={styles.title}>{name}</span>
    </article>
  )
}