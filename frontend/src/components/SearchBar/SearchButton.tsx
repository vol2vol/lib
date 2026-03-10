import { Icon } from '@components/Icon'
import styles from './SearchButton.module.css'

type SearchButtonProps = {
  onClick?: () => void
}

export const SearchButton = ({ onClick }: SearchButtonProps) => {
  return (
    <button
      type="button"
      className={styles.searchButton}
      onClick={onClick}
      aria-label="Поиск"
    >
      <Icon name="Search" size={18} className={styles.icon} />
    </button>
  )
}