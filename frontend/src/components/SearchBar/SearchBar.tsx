import { FilterButton } from './FilterButton'
import { SearchButton } from './SearchButton'
import { SearchInput } from './SearchInput'
import styles from './SearchBar.module.css'

type SearchBarProps = {
  value: string
  onChange: (value: string) => void
  placeholder?: string
  onFilterClick?: () => void
}

export const SearchBar = ({
  value,
  onChange,
  placeholder = 'Поиск...',
  onFilterClick,
}: SearchBarProps) => {
  return (
    <div className={styles.searchBar}>
      <SearchButton />
      <SearchInput
        value={value}
        onChange={onChange}
        placeholder={placeholder}
      />
      <FilterButton onClick={onFilterClick} />
    </div>
  )
}