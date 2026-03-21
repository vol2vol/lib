import { SearchBar } from '@components/SearchBar'
import { HeaderLogo } from '@components/HeaderLogo'
import { HeaderActionButton } from '@components/HeaderActionButton'
import styles from './Header.module.css'

type HeaderLeftVariant = 'logo' | 'back' | 'none'
type HeaderCenterVariant = 'search' | 'logo' | 'title' | 'none'
type HeaderRightVariant = 'profile' | 'exit' | 'settings' | 'none'

type HeaderProps = {
  leftVariant?: HeaderLeftVariant
  centerVariant?: HeaderCenterVariant
  rightVariant?: HeaderRightVariant
  title?: string
  searchValue?: string
  onSearchChange?: (value: string) => void
  onBackClick?: () => void
  onProfileClick?: () => void
  onExitClick?: () => void
  onSettingsClick?: () => void
}

export const Header = ({
  leftVariant = 'none',
  centerVariant = 'none',
  rightVariant = 'none',
  title = '',
  searchValue = '',
  onSearchChange,
  onBackClick,
  onProfileClick,
  onExitClick,
  onSettingsClick,
}: HeaderProps) => {
  const headerClassName = [
    styles.header,
    centerVariant === 'search' ? styles.headerSearch : styles.headerFixedCenter,
  ].join(' ')

  const renderLeft = () => {
    switch (leftVariant) {
      case 'logo':
        return <HeaderLogo />
      case 'back':
        return (
          <HeaderActionButton
            iconName="BackButton"
            onClick={onBackClick}
            ariaLabel="Назад"
          />
        )
      default:
        return null
    }
  }

  const renderCenter = () => {
    switch (centerVariant) {
      case 'search':
        return (
          <div className={styles.searchWrap}>
            <SearchBar value={searchValue} onChange={onSearchChange ?? (() => {})} />
          </div>
        )
      case 'logo':
        return <HeaderLogo />
      case 'title':
        return <h1 className={styles.title}>{title}</h1>
      default:
        return null
    }
  }

  const renderRight = () => {
    switch (rightVariant) {
      case 'profile':
        return (
          <HeaderActionButton
            iconName="Avatar"
            onClick={onProfileClick}
            ariaLabel="Профиль"
          />
        )
      case 'exit':
        return (
          <HeaderActionButton
            iconName="Exit"
            onClick={onExitClick}
            ariaLabel="Выход"
          />
        )
      case 'settings':
        return (
          <HeaderActionButton
            iconName="Settings"
            onClick={onSettingsClick}
            ariaLabel="Настройки"
          />
        )
      default:
        return null
    }
  }

  return (
    <header className={headerClassName}>
      <div className={styles.left}>{renderLeft()}</div>
      <div className={styles.center}>{renderCenter()}</div>
      <div className={styles.right}>{renderRight()}</div>
    </header>
  )
}