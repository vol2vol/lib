import styles from './Pagination.module.css'

type PaginationProps = {
  currentPage: number
  lastPage: number
  perPage: number
  total: number
  onPageChange: (page: number) => void
  onPerPageChange: (value: number) => void
}

const PER_PAGE_OPTIONS = [5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60]

const getVisiblePages = (currentPage: number, lastPage: number) => {
  if (lastPage <= 1) {
    return [1]
  }

  const pages = new Set<number>([1, lastPage, currentPage])

  for (let page = currentPage - 1; page <= currentPage + 1; page += 1) {
    if (page > 1 && page < lastPage) {
      pages.add(page)
    }
  }

  return Array.from(pages).sort((first, second) => first - second)
}

export const Pagination = ({
  currentPage,
  lastPage,
  perPage,
  total,
  onPageChange,
  onPerPageChange,
}: PaginationProps) => {
  const visiblePages = getVisiblePages(currentPage, lastPage)
  const from = total === 0 ? 0 : (currentPage - 1) * perPage + 1
  const to = Math.min(currentPage * perPage, total)

  return (
    <div className={styles.wrapper}>
      <div className={styles.metaBlock}>
        <p className={styles.meta}>
          Показано {from}-{to} из {total}
        </p>

        <label className={styles.selectWrap}>
          <span className={styles.selectLabel}>Книг на странице</span>
          <select
            className={styles.select}
            value={perPage}
            onChange={(event) => onPerPageChange(Number(event.target.value))}
          >
            {PER_PAGE_OPTIONS.map((option) => (
              <option key={option} value={option}>
                {option}
              </option>
            ))}
          </select>
        </label>
      </div>

      {lastPage > 1 ? (
        <nav className={styles.navigation} aria-label="Пагинация">
          <button
            type="button"
            className={styles.button}
            onClick={() => onPageChange(currentPage - 1)}
            disabled={currentPage <= 1}
          >
            Назад
          </button>

          <div className={styles.pages}>
            {visiblePages.map((page, index) => {
              const previousPage = visiblePages[index - 1]
              const shouldShowDots = previousPage !== undefined && page - previousPage > 1

              return (
                <div key={page} className={styles.pageItem}>
                  {shouldShowDots ? <span className={styles.dots}>...</span> : null}
                  <button
                    type="button"
                    className={`${styles.button} ${page === currentPage ? styles.buttonActive : ''}`}
                    onClick={() => onPageChange(page)}
                    aria-current={page === currentPage ? 'page' : undefined}
                  >
                    {page}
                  </button>
                </div>
              )
            })}
          </div>

          <button
            type="button"
            className={styles.button}
            onClick={() => onPageChange(currentPage + 1)}
            disabled={currentPage >= lastPage}
          >
            Вперед
          </button>
        </nav>
      ) : null}
    </div>
  )
}