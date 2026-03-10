import { Icon } from '@components/Icon'
import { ProfileButton } from '@components/ProfileButton'
import { SearchBar } from '@components/SearchBar'
import styles from './Header.module.css'

type HeaderProps = {
  searchValue: string
  onSearchChange: (value: string) => void
  onProfileClick?: () => void
}

export const Header = ({
  searchValue,
  onSearchChange,
  onProfileClick,
}: HeaderProps) => {
  return (
    <header className={styles.header}>
      <div className={styles.left}>
        <Icon name="Logo" className={styles.logo} />
      </div>

      <div className={styles.center}>
        <div className={styles.searchWrap}>
          <SearchBar value={searchValue} onChange={onSearchChange} />
        </div>
      </div>

      <div className={styles.right}>
        <ProfileButton onClick={onProfileClick} />
      </div>
    </header>
  )
}