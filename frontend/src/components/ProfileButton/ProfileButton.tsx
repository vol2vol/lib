import { Icon } from '@components/Icon'
import styles from './ProfileButton.module.css'

type ProfileButtonProps = {
  onClick?: () => void
}

export const ProfileButton = ({ onClick }: ProfileButtonProps) => {
  return (
    <button className={styles.profileButton} type="button" onClick={onClick}>
      <Icon name="Avatar" size={28} />
    </button>
  )
}