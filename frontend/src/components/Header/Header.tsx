import { Icon } from '@components/Icon'
import { ProfileButton } from '@components/ProfileButton'
import { ExitButton } from '@components/ExitButton'
import { SearchBar } from '@components/SearchBar'
import styles from './Header.module.css'

type HeaderProps = {
  searchValue?: string
  onSearchChange?: (value: string) => void
  onProfileClick?: () => void
  onBackClick?: () => void
  onExitClick?: () => void
  showBackButton?: boolean
  showSearch?: boolean
  showExit?: boolean
}

export const Header = ({
  searchValue = '',
  onSearchChange,
  onProfileClick,
  onBackClick,
  onExitClick,
  showBackButton = false,
  showSearch = true,
  showExit = false,
}: HeaderProps) => {
  return (
    <header className={styles.header}>
      <div className={styles.left}>
        {showBackButton ? (
          <button
            type="button"
            className={styles.iconButton}
            onClick={onBackClick}
          >
            <Icon name="BackButtun" size={28} />
          </button>
        ) : (
          <Icon name="Logo" className={styles.logo} />
        )}
      </div>

      <div className={styles.center}>
        {showSearch ? (
          <div className={styles.searchWrap}>
            <SearchBar value={searchValue} onChange={onSearchChange!} />
          </div>
        ) : (
          <Icon name="Logo" className={styles.logo} />
        )}
      </div>

      <div className={styles.right}>
        {showExit ? (
          <ExitButton onClick={onExitClick} />
        ) : (
          <ProfileButton onClick={onProfileClick} />
        )}
      </div>
    </header>
  )
}