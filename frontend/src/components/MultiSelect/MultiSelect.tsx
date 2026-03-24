import { useEffect, useId, useMemo, useRef, useState } from 'react'
import styles from './MultiSelect.module.css'

type MultiSelectItem = {
  id: number
  name: string
}

type MultiSelectProps = {
  items: MultiSelectItem[]
  selectedIds: number[]
  onSelectionChange: (ids: number[]) => void
  placeholder?: string
  multiple?: boolean
}

export const MultiSelect = ({
  items,
  selectedIds,
  onSelectionChange,
  placeholder = 'Выберите значения',
  multiple = true,
}: MultiSelectProps) => {
  const [isOpen, setIsOpen] = useState(false)
  const rootRef = useRef<HTMLDivElement | null>(null)
  const radioGroupName = useId()

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (!rootRef.current) {
        return
      }

      if (!rootRef.current.contains(event.target as Node)) {
        setIsOpen(false)
      }
    }

    document.addEventListener('mousedown', handleClickOutside)
    return () => {
      document.removeEventListener('mousedown', handleClickOutside)
    }
  }, [])

  const selectedLabels = useMemo(() => {
    const selectedSet = new Set(selectedIds)

    return items
      .filter((item) => selectedSet.has(item.id))
      .map((item) => item.name)
  }, [items, selectedIds])

  const toggleItem = (id: number) => {
    if (multiple) {
      if (selectedIds.includes(id)) {
        onSelectionChange(selectedIds.filter((itemId) => itemId !== id))
        return
      }

      onSelectionChange([...selectedIds, id])
      return
    }

    if (selectedIds[0] === id) {
      onSelectionChange([])
      setIsOpen(false)
      return
    }

    onSelectionChange([id])
    setIsOpen(false)
  }

  const clearSelection = () => {
    onSelectionChange([])
  }

  return (
    <div className={styles.root} ref={rootRef}>
      <button
        type="button"
        className={styles.control}
        onClick={() => setIsOpen((current) => !current)}
        aria-expanded={isOpen}
      >
        <span className={styles.value}>
          {selectedLabels.length > 0 ? selectedLabels.join(', ') : placeholder}
        </span>
        <span className={styles.arrow}>{isOpen ? '▲' : '▼'}</span>
      </button>

      {isOpen ? (
        <div className={styles.dropdown}>
          <div className={styles.actions}>
            <button type="button" className={styles.actionButton} onClick={clearSelection}>
              Очистить
            </button>
          </div>

          <div className={styles.list}>
            {items.map((item) => {
              const checked = selectedIds.includes(item.id)

              return (
                <label key={item.id} className={styles.option}>
                  <input
                    type={multiple ? 'checkbox' : 'radio'}
                    name={multiple ? undefined : radioGroupName}
                    checked={checked}
                    onChange={() => toggleItem(item.id)}
                  />
                  <span>{item.name}</span>
                </label>
              )
            })}
          </div>
        </div>
      ) : null}
    </div>
  )
}

export type { MultiSelectItem }