import { useState } from 'react'
import styles from './MultiSelect.module.css'

export type MultiSelectItem = {
  id: number | string
  name: string
}

type MultiSelectProps = {
  items: MultiSelectItem[]
  selectedIds: (number | string)[]
  onSelectionChange: (ids: (number | string)[]) => void
  placeholder?: string
}

export const MultiSelect = ({
  items,
  selectedIds,
  onSelectionChange,
  placeholder = 'Выберите или введите...',
}: MultiSelectProps) => {
  const [isOpen, setIsOpen] = useState(false)
  const [inputValue, setInputValue] = useState('')

  const selected = items.filter((item) => selectedIds.includes(item.id))
  const filtered = items.filter((item) =>
    item.name.toLowerCase().includes(inputValue.toLowerCase()) && !selectedIds.includes(item.id)
  )

  const handleToggleItem = (id: number | string) => {
    if (selectedIds.includes(id)) {
      onSelectionChange(selectedIds.filter((selected) => selected !== id))
    } else {
      onSelectionChange([...selectedIds, id])
    }
  }

  const handleRemoveSelected = (id: number | string) => {
    onSelectionChange(selectedIds.filter((selected) => selected !== id))
  }

  return (
    <div className={styles.container}>
      <div className={styles.inputWrapper}>
        <div className={styles.selectedTags}>
          {selected.map((item) => (
            <div key={item.id} className={styles.tag}>
              <span>{item.name}</span>
              <button
                type="button"
                className={styles.tagRemove}
                onClick={() => handleRemoveSelected(item.id)}
                aria-label={`Удалить ${item.name}`}
              >
                ×
              </button>
            </div>
          ))}
          <input
            type="text"
            className={styles.input}
            value={inputValue}
            onChange={(e) => setInputValue(e.target.value)}
            onFocus={() => setIsOpen(true)}
            onBlur={() => setTimeout(() => setIsOpen(false), 200)}
            placeholder={selected.length === 0 ? placeholder : ''}
          />
        </div>
      </div>

      {isOpen && filtered.length > 0 && (
        <ul className={styles.dropdown}>
          {filtered.map((item) => (
            <li key={item.id}>
              <button
                type="button"
                className={styles.option}
                onClick={() => handleToggleItem(item.id)}
              >
                {item.name}
              </button>
            </li>
          ))}
        </ul>
      )}
    </div>
  )
}
