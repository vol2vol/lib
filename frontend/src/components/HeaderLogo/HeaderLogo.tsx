import { Link } from 'react-router-dom'
import { Icon } from '@components/Icon'
import styles from './HeaderLogo.module.css'

type HeaderLogoProps = {
  className?: string
}

export const HeaderLogo = ({ className }: HeaderLogoProps) => {
  return (
    <Link
      to="/library"
      className={`${styles.logoLink} ${className ?? ''}`}
      aria-label="Перейти на главную страницу библиотеки"
    >
      <Icon name="Logo" className={styles.logo} />
    </Link>
  )
}