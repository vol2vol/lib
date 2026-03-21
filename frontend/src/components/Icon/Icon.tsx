import styles from './Icon.module.css'

type IconProps = {
  name: string
  size?: number | string
  className?: string
}

export const Icon = ({ name, size, className }: IconProps) => {
  const src = `/src/assets/icons/${name}.svg`

  const inlineStyle =
    size !== undefined
      ? {
          width: typeof size === 'number' ? `${size}px` : size,
          height: typeof size === 'number' ? `${size}px` : size,
        }
      : undefined

  return (
    <img
      src={src}
      alt={name}
      style={inlineStyle}
      className={`${styles.icon} ${className ?? ''}`}
    />
  )
}