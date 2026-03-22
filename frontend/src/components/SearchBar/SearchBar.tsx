import { Icon } from '@components/Icon'
import styles from './SearchBar.module.css'

type SearchBarProps = {
  value: string
  onChange: (value: string) => void
  onSearchClick?: () => void
  onFilterClick?: () => void
  placeholder?: string
}

export const SearchBar = ({
  value,
  onChange,
  onSearchClick,
  onFilterClick,
  placeholder = 'Поиск',
}: SearchBarProps) => {
  const className = [
    styles.searchBar,
    onFilterClick ? styles.withFilter : styles.withoutFilter,
  ].join(' ')

  return (
    <div className={className}>
      <button
        type="button"
        className={styles.searchButton}
        onClick={onSearchClick}
        aria-label="Поиск"
      >
        <Icon name="Search" className={styles.icon} />
      </button>

      <div className={styles.inputWrap}>
        <input
          type="text"
          value={value}
          onChange={(event) => onChange(event.target.value)}
          onKeyDown={(event) => {
            if (event.key === 'Enter') {
              onSearchClick?.()
            }
          }}
          className={styles.input}
          placeholder={placeholder}
        />
      </div>

      {onFilterClick ? (
        <button
          type="button"
          className={styles.filterButton}
          onClick={onFilterClick}
          aria-label="Фильтр"
        >
          <Icon name="Filter" className={styles.icon} />
        </button>
      ) : null}
    </div>
  )
}