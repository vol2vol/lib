import logo from '@assets/logo/onlib.svg'
import styles from './Logo.module.css'

type LogoProps = {
  className?: string
}

export const Logo = ({ className }: LogoProps) => {
  return (
    <img
      src={logo}
      alt="OnLib"
      className={`${styles.logo} ${className ?? ''}`}
    />
  )
}