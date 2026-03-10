import { Icon } from '@components/Icon'
import styles from './FilterButton.module.css'

type FilterButtonProps = {
  onClick?: () => void
}

export const FilterButton = ({ onClick }: FilterButtonProps) => {
  return (
    <button className={styles.filterButton} type="button" onClick={onClick}>
      <Icon name="Filter" size={24} />
    </button>
  )
}