import type { Author, Genre } from '@models/library'
import styles from './FiltersPanel.module.css'

type FiltersPanelProps = {
  authors: Author[]
  genres: Genre[]
  authorId: string
  genreId: string
  yearFrom: string
  yearTo: string
  onAuthorChange: (value: string) => void
  onGenreChange: (value: string) => void
  onYearFromChange: (value: string) => void
  onYearToChange: (value: string) => void
  onClear: () => void
}

const normalizeYearValue = (value: string) => value.replace(/\D+/g, '').slice(0, 4)

export const FiltersPanel = ({
  authors,
  genres,
  authorId,
  genreId,
  yearFrom,
  yearTo,
  onAuthorChange,
  onGenreChange,
  onYearFromChange,
  onYearToChange,
  onClear,
}: FiltersPanelProps) => {
  return (
    <section className={styles.panel} aria-label="Фильтры каталога">
      <div className={styles.grid}>
        <label className={styles.field}>
          <span className={styles.label}>Автор</span>
          <select
            className={styles.select}
            value={authorId}
            onChange={(event) => onAuthorChange(event.target.value)}
          >
            <option value="">Все авторы</option>
            {authors.map((author) => (
              <option key={author.id} value={author.id}>
                {author.fullName}
              </option>
            ))}
          </select>
        </label>

        <label className={styles.field}>
          <span className={styles.label}>Жанр</span>
          <select
            className={styles.select}
            value={genreId}
            onChange={(event) => onGenreChange(event.target.value)}
          >
            <option value="">Все жанры</option>
            {genres.map((genre) => (
              <option key={genre.id} value={genre.id}>
                {genre.name}
              </option>
            ))}
          </select>
        </label>

        <div className={styles.field}>
          <span className={styles.label}>Год публикации</span>

          <div className={styles.range}>
            <label className={styles.rangeField}>
              <span className={styles.rangeLabel}>От</span>
              <input
                type="text"
                inputMode="numeric"
                className={styles.input}
                value={yearFrom}
                placeholder="1900"
                onChange={(event) => onYearFromChange(normalizeYearValue(event.target.value))}
              />
            </label>

            <label className={styles.rangeField}>
              <span className={styles.rangeLabel}>До</span>
              <input
                type="text"
                inputMode="numeric"
                className={styles.input}
                value={yearTo}
                placeholder="2026"
                onChange={(event) => onYearToChange(normalizeYearValue(event.target.value))}
              />
            </label>
          </div>
        </div>
      </div>

      <div className={styles.actions}>
        <button type="button" className={styles.clearButton} onClick={onClear}>
          Очистить поля
        </button>
      </div>
    </section>
  )
}