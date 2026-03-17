import { useEffect, useMemo, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { getBookById } from '@api/library'
import { Header } from '@components/Header'
import type { Book, BookFile } from '@models/library'
import styles from './BookPage.module.css'

export const BookPage = () => {
  const navigate = useNavigate()
  const { bookId } = useParams<{ bookId: string }>()

  const [book, setBook] = useState<Book | null>(null)
  const [selectedFileId, setSelectedFileId] = useState<number | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState('')

  useEffect(() => {
    const parsedBookId = Number(bookId)

    if (!bookId || Number.isNaN(parsedBookId)) {
      navigate('/library', { replace: true })
      return
    }

    const loadBook = async () => {
      try {
        setIsLoading(true)
        setError('')

        const data = await getBookById(parsedBookId)

        setBook(data)
        setSelectedFileId(data.files[0]?.id ?? null)
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Не удалось загрузить книгу')
      } finally {
        setIsLoading(false)
      }
    }

    void loadBook()
  }, [bookId, navigate])

  const selectedFile = useMemo<BookFile | null>(() => {
    if (!book) {
      return null
    }

    if (selectedFileId === null) {
      return book.files[0] ?? null
    }

    return book.files.find((file) => file.id === selectedFileId) ?? book.files[0] ?? null
  }, [book, selectedFileId])

  const handleBack = () => {
    navigate(-1)
  }

  const handleRead = () => {
    if (!selectedFile?.readUrl) {
      return
    }

    window.open(selectedFile.readUrl, '_blank', 'noopener,noreferrer')
  }

  const handleDownload = () => {
    if (!selectedFile?.downloadUrl) {
      return
    }

    window.open(selectedFile.downloadUrl, '_blank', 'noopener,noreferrer')
  }

  return (
    <main className={styles.bookPage}>
      <Header
        showBackButton
        showSearch={false}
        onBackClick={handleBack}
        onProfileClick={() => navigate('/profile')}
      />

      <section className={styles.container}>
        {isLoading ? <p className={styles.state}>Загрузка...</p> : null}
        {error ? <p className={styles.error}>{error}</p> : null}

        {!isLoading && !error && book ? (
          <div className={styles.layout}>
            <div className={styles.coverColumn}>
              {book.coverUrl ? (
                <img
                  className={styles.cover}
                  src={book.coverUrl}
                  alt={book.title}
                />
              ) : (
                <div className={styles.coverPlaceholder} />
              )}
            </div>

            <div className={styles.content}>
              <div className={styles.head}>
                <h1 className={styles.title}>{book.title}</h1>
                <p className={styles.author}>{book.author}</p>
              </div>

              <dl className={styles.metaList}>
                <div className={styles.metaItem}>
                  <dt className={styles.metaLabel}>Жанр</dt>
                  <dd className={styles.metaValue}>{book.genre}</dd>
                </div>

                <div className={styles.metaItem}>
                  <dt className={styles.metaLabel}>Издательство</dt>
                  <dd className={styles.metaValue}>{book.publisher}</dd>
                </div>

                <div className={styles.metaItem}>
                  <dt className={styles.metaLabel}>Год</dt>
                  <dd className={styles.metaValue}>
                    {book.publishedYear ?? 'Не указан'}
                  </dd>
                </div>
              </dl>

              <section className={styles.section}>
                <h2 className={styles.sectionTitle}>Описание</h2>
                <p className={styles.description}>
                  {book.description || 'Описание пока отсутствует.'}
                </p>
              </section>

              <section className={styles.section}>
                <h2 className={styles.sectionTitle}>Формат</h2>

                {book.files.length > 0 ? (
                  <div className={styles.actions}>
                    <label className={styles.selectWrap}>
                      <span className={styles.selectLabel}>Выберите файл</span>

                      <select
                        className={styles.select}
                        value={selectedFile?.id ?? ''}
                        onChange={(event) => setSelectedFileId(Number(event.target.value))}
                      >
                        {book.files.map((file) => (
                          <option key={file.id} value={file.id}>
                            {file.formatName}
                            {file.sizeMb > 0 ? ` • ${file.sizeMb.toFixed(2)} MB` : ''}
                          </option>
                        ))}
                      </select>
                    </label>

                    <div className={styles.buttons}>
                      <button
                        type="button"
                        className={styles.primaryButton}
                        onClick={handleRead}
                        disabled={!selectedFile?.readUrl}
                      >
                        Читать
                      </button>

                      <button
                        type="button"
                        className={styles.secondaryButton}
                        onClick={handleDownload}
                        disabled={!selectedFile?.downloadUrl}
                      >
                        Скачать
                      </button>
                    </div>
                  </div>
                ) : (
                  <p className={styles.empty}>Файлы для чтения пока не добавлены.</p>
                )}
              </section>
            </div>
          </div>
        ) : null}
      </section>
    </main>
  )
}