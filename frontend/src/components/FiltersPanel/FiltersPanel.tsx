import { MultiSelect } from '@components/MultiSelect'
import type { Author, Genre, Publisher } from '@models/library'
import styles from './FiltersPanel.module.css'

type FiltersPanelProps = {
  authors: Author[]
  genres: Genre[]
  publishers: Publisher[]
  authorIds: number[]
  genreIds: number[]
  publisherId: number | null
  yearFrom: string
  yearTo: string
  onAuthorChange: (value: number[]) => void
  onGenreChange: (value: number[]) => void
  onPublisherChange: (value: number | null) => void
  onYearFromChange: (value: string) => void
  onYearToChange: (value: string) => void
  onClear: () => void
}

const normalizeYearValue = (value: string) => value.replace(/\D+/g, '').slice(0, 4)

export const FiltersPanel = ({
  authors,
  genres,
  publishers,
  authorIds,
  genreIds,
  publisherId,
  yearFrom,
  yearTo,
  onAuthorChange,
  onGenreChange,
  onPublisherChange,
  onYearFromChange,
  onYearToChange,
  onClear,
}: FiltersPanelProps) => {
  return (
    <section className={styles.panel} aria-label="Фильтры каталога">
      <div className={styles.grid}>
        <label className={styles.field}>
          <span className={styles.label}>Автор</span>
          <MultiSelect
            items={authors.map((author) => ({
              id: author.id,
              name: author.fullName,
            }))}
            selectedIds={authorIds}
            onSelectionChange={onAuthorChange}
            placeholder="Все авторы"
          />
        </label>

        <label className={styles.field}>
          <span className={styles.label}>Жанр</span>
          <MultiSelect
            items={genres.map((genre) => ({
              id: genre.id,
              name: genre.name,
            }))}
            selectedIds={genreIds}
            onSelectionChange={onGenreChange}
            placeholder="Все жанры"
          />
        </label>

        <label className={styles.field}>
          <span className={styles.label}>Издательство</span>
          <MultiSelect
            items={publishers.map((publisher) => ({
              id: publisher.id,
              name: publisher.name,
            }))}
            selectedIds={publisherId !== null ? [publisherId] : []}
            onSelectionChange={(value) => onPublisherChange(value[0] ?? null)}
            placeholder="Все издательства"
            multiple={false}
          />
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