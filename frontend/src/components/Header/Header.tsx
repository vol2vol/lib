import { useEffect, useRef, useState } from 'react'
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
  onSearchClick?: () => void
  onFilterClick?: () => void
  onBackClick?: () => void
  onProfileClick?: () => void
  onExitClick?: () => void
  onSettingsClick?: () => void
}

const MOBILE_HEADER_MEDIA_QUERY = '(max-width: 768px)'
const SCROLL_DELTA_THRESHOLD = 12

export const Header = ({
  leftVariant = 'none',
  centerVariant = 'none',
  rightVariant = 'none',
  title = '',
  searchValue = '',
  onSearchChange,
  onSearchClick,
  onFilterClick,
  onBackClick,
  onProfileClick,
  onExitClick,
  onSettingsClick,
}: HeaderProps) => {
  const headerRef = useRef<HTMLElement | null>(null)
  const [headerHeight, setHeaderHeight] = useState(0)
  const [isHiddenOnMobile, setIsHiddenOnMobile] = useState(false)

  useEffect(() => {
    const element = headerRef.current

    if (!element) {
      return
    }

    const updateHeaderHeight = () => {
      setHeaderHeight(element.getBoundingClientRect().height)
    }

    updateHeaderHeight()

    const resizeObserver = new ResizeObserver(() => {
      updateHeaderHeight()
    })

    resizeObserver.observe(element)

    window.addEventListener('resize', updateHeaderHeight)

    return () => {
      resizeObserver.disconnect()
      window.removeEventListener('resize', updateHeaderHeight)
    }
  }, [centerVariant, leftVariant, rightVariant, title])

  useEffect(() => {
    let lastScrollY = window.scrollY

    const mediaQuery = window.matchMedia(MOBILE_HEADER_MEDIA_QUERY)

    const handleScroll = () => {
      const currentScrollY = window.scrollY

      if (!mediaQuery.matches) {
        setIsHiddenOnMobile(false)
        lastScrollY = currentScrollY
        return
      }

      if (currentScrollY <= 0) {
        setIsHiddenOnMobile(false)
        lastScrollY = currentScrollY
        return
      }

      const scrollDiff = currentScrollY - lastScrollY

      if (Math.abs(scrollDiff) < SCROLL_DELTA_THRESHOLD) {
        return
      }

      if (scrollDiff > 0 && currentScrollY > headerHeight) {
        setIsHiddenOnMobile(true)
      } else if (scrollDiff < 0) {
        setIsHiddenOnMobile(false)
      }

      lastScrollY = currentScrollY
    }

    const handleMediaQueryChange = () => {
      if (!mediaQuery.matches) {
        setIsHiddenOnMobile(false)
      }
    }

    window.addEventListener('scroll', handleScroll, { passive: true })
    mediaQuery.addEventListener('change', handleMediaQueryChange)

    return () => {
      window.removeEventListener('scroll', handleScroll)
      mediaQuery.removeEventListener('change', handleMediaQueryChange)
    }
  }, [headerHeight])

  const headerClassName = [
    styles.header,
    centerVariant === 'search' ? styles.headerSearch : styles.headerFixedCenter,
    isHiddenOnMobile ? styles.headerHiddenOnMobile : '',
  ]
    .filter(Boolean)
    .join(' ')

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
            <SearchBar
              value={searchValue}
              onChange={onSearchChange ?? (() => {})}
              onSearchClick={onSearchClick}
              onFilterClick={onFilterClick}
            />
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
    <>
      <header ref={headerRef} className={headerClassName}>
        <div className={styles.left}>{renderLeft()}</div>
        <div className={styles.center}>{renderCenter()}</div>
        <div className={styles.right}>{renderRight()}</div>
      </header>
      <div className={styles.spacer} style={{ height: headerHeight }} aria-hidden="true" />
    </>
  )
}
