import { useEffect, useMemo, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import {
  addToFavorites,
  downloadBookFile,
  getBookById,
  getFavorites,
  removeFromFavorites,
} from '@api/library'
import { Header } from '@components/Header'
import { Icon } from '@components/Icon'
import type { Book, BookFile } from '@models/library'
import { ApiError } from '@api/http'
import styles from './BookPage.module.css'

export const BookPage = () => {
  const navigate = useNavigate()
  const { bookId } = useParams<{ bookId: string }>()

  const [book, setBook] = useState<Book | null>(null)
  const [selectedFileId, setSelectedFileId] = useState<number | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState('')
  const [isFavoriteLoading, setIsFavoriteLoading] = useState(false)
  const [isFileLoading, setIsFileLoading] = useState(false)

  useEffect(() => {
    const parsedBookId = Number(bookId)

    if (!bookId || Number.isNaN(parsedBookId)) {
      navigate('/library', { replace: true })
      return
    }

    const loadBook = async () => {
      const token = localStorage.getItem('token')

      try {
        setIsLoading(true)
        setError('')

        const data = await getBookById(parsedBookId, token ?? undefined)

        let isFavorited = Boolean(data.isFavorited)

        if (token) {
          try {
            const favoritesData = await getFavorites(token)
            isFavorited = favoritesData.items.some((item) => item.id === parsedBookId)
          } catch {
            isFavorited = Boolean(data.isFavorited)
          }
        }

        setBook({
          ...data,
          isFavorited,
        })
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
    if (!selectedFile) {
      return
    }

    const token = localStorage.getItem('token')

    if (!token) {
      navigate('/signin', { replace: true })
      return
    }

    navigate(`/library/read/${selectedFile.id}`)
  }

  const handleDownload = async () => {
    if (!selectedFile) {
      return
    }

    const token = localStorage.getItem('token')

    if (!token) {
      navigate('/signin', { replace: true })
      return
    }

    try {
      setIsFileLoading(true)
      setError('')

      await downloadBookFile(selectedFile.id, token)
    } catch (err) {
      if (err instanceof ApiError && err.status === 401) {
        localStorage.removeItem('token')
        navigate('/signin', { replace: true })
        return
      }

      setError(err instanceof Error ? err.message : 'Не удалось скачать файл')
    } finally {
      setIsFileLoading(false)
    }
  }

  const handleFavoriteClick = async () => {
    if (!book) {
      return
    }

    const token = localStorage.getItem('token')

    if (!token) {
      navigate('/signin', { replace: true })
      return
    }

    try {
      setIsFavoriteLoading(true)
      setError('')

      if (book.isFavorited) {
        await removeFromFavorites(book.id, token)
      } else {
        await addToFavorites(book.id, token)
      }

      setBook((prev) =>
        prev
          ? {
              ...prev,
              isFavorited: !prev.isFavorited,
            }
          : prev
      )
    } catch (err) {
      if (err instanceof ApiError && err.status === 401) {
        localStorage.removeItem('token')
        navigate('/signin', { replace: true })
        return
      }

      setError(err instanceof Error ? err.message : 'Не удалось обновить избранное')
    } finally {
      setIsFavoriteLoading(false)
    }
  }

  return (
    <main className={styles.bookPage}>
      <Header
        leftVariant="back"
        centerVariant="logo"
        rightVariant="profile"
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
                <img className={styles.cover} src={book.coverUrl} alt={book.title} />
              ) : (
                <div className={styles.coverPlaceholder} />
              )}
            </div>

            <div className={styles.content}>
              <div className={styles.head}>
                <div className={styles.titleRow}>
                  <div className={styles.titleBlock}>
                    <h1 className={styles.title}>{book.title}</h1>
                    <p className={styles.author}>{book.author}</p>
                  </div>

                  <button
                    type="button"
                    className={styles.favoriteButton}
                    onClick={handleFavoriteClick}
                    disabled={isFavoriteLoading}
                    aria-label={
                      book.isFavorited ? 'Убрать из избранного' : 'Добавить в избранное'
                    }
                  >
                    <Icon
                      name={book.isFavorited ? 'FavoriteActive' : 'Favorite'}
                      className={styles.favoriteIcon}
                    />
                  </button>
                </div>
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
                  <dd className={styles.metaValue}>{book.publishedYear ?? 'Не указан'}</dd>
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
                        disabled={!selectedFile}
                      >
                        Читать
                      </button>

                      <button
                        type="button"
                        className={styles.secondaryButton}
                        onClick={handleDownload}
                        disabled={!selectedFile || isFileLoading}
                      >
                        {isFileLoading ? 'Загрузка...' : 'Скачать'}
                      </button>
                    </div>
                  </div>
                ) : (
                  <p className={styles.empty}>Для этой книги пока нет доступных файлов.</p>
                )}
              </section>
            </div>
          </div>
        ) : null}
      </section>
    </main>
  )
}