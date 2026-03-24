import { Icon } from '@components/Icon'
import styles from './HeaderActionButton.module.css'

type HeaderActionButtonProps = {
  iconName: string
  onClick?: () => void
  ariaLabel: string
  className?: string
}

export const HeaderActionButton = ({
  iconName,
  onClick,
  ariaLabel,
  className,
}: HeaderActionButtonProps) => {
  return (
    <button
      type="button"
      className={`${styles.button} ${className ?? ''}`}
      onClick={onClick}
      aria-label={ariaLabel}
    >
      <Icon name={iconName} className={styles.icon} />
    </button>
  )
}