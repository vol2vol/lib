import styles from './Icon.module.css'

type IconProps = {
  name: string
  size?: number | string
  className?: string
}

export const Icon = ({ name, size = 24, className }: IconProps) => {
  const src = `/src/assets/icons/${name}.svg`

  return (
    <img
      src={src}
      alt={name}
      width={size}
      height={size}
      className={`${styles.icon} ${className ?? ''}`}
    />
  )
}