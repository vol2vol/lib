import { Icon } from '@components/Icon'
import styles from './ExitButton.module.css'

type ExitButtonProps = {
  onClick?: () => void
}

export const ExitButton = ({ onClick }: ExitButtonProps) => {
  return (
    <button
      type="button"
      className={styles.exitButton}
      onClick={onClick}
      aria-label="Выход"
    >
      <Icon name="Exit" size={28} />
    </button>
  )
}