import { useEffect, useMemo, useState, useCallback } from 'react'
import { useNavigate } from 'react-router-dom'
import { getCurrentUser } from '@api/auth'
import {
  getBooks,
  getAdminGenres,
  getAdminAuthors,
  createBook,
  updateBook,
  deleteBook,
} from '@api/library'
import { Header } from '@components/Header'
import type { Book, Genre, Author } from '@models/library'
import type { BookFormPayload } from '@models/library'
import type { User } from '@models/auth'
import styles from './AdminPage.module.css'

type BookFormState = {
  title: string
  description: string
  author: string
  genres: string[]
  publisher: string
  publishedYear: string
  coverFile: File | null
  files: File[]
}

const initialFormState: BookFormState = {
  title: '',
  description: '',
  author: '',
  genres: [],
  publisher: '',
  publishedYear: '',
  coverFile: null,
  files: [],
}

export const AdminPage = () => {
  const navigate = useNavigate()

  const [user, setUser] = useState<User | null>(null)
  const [authors, setAuthors] = useState<Author[]>([])
  const [genres, setGenres] = useState<Genre[]>([])
  const [books, setBooks] = useState<Book[]>([])
  const [search, setSearch] = useState('')
  const [selectedBook, setSelectedBook] = useState<Book | null>(null)
  const [form, setForm] = useState<BookFormState>(initialFormState)
  const [error, setError] = useState('')
  const [successMessage, setSuccessMessage] = useState('')
  const [isLoading, setIsLoading] = useState(true)
  const [isSaving, setIsSaving] = useState(false)

  const token = localStorage.getItem('token')

  const loadData = useCallback(async () => {
    if (!token) {
      navigate('/signin', { replace: true })
      return
    }

    try {
      setIsLoading(true)
      setError('')

      const currentUser = await getCurrentUser(token)

      if (!currentUser) {
        localStorage.removeItem('token')
        navigate('/signin', { replace: true })
        return
      }

      // Если пользователь не админ — отправляем в профиль
      if (currentUser.roleId !== 1) {
        navigate('/profile', { replace: true })
        return
      }

      // Загружаем админ-данные только для админов
      const [genresData, booksData, authorsData] = await Promise.all([
      const [genresData, booksData] = await Promise.all([
        getAdminGenres(token),
        getBooks(),
        getAdminAuthors(token),
      ])

      setUser(currentUser)
      setGenres(genresData)
      setBooks(booksData.items)
      setAuthors(authorsData)
    } catch (err) {
      const errorMessage =
        err instanceof Error ? err.message : 'Произошла ошибка при загрузке данных'

      if (
        errorMessage.includes('401') ||
        errorMessage.includes('Unauthorized') ||
        errorMessage.includes('токен')
      ) {
        localStorage.removeItem('token')
        navigate('/signin', { replace: true })
      } else {
        setError(errorMessage)
      }
    } finally {
      setIsLoading(false)
    }
  }, [navigate, token])

  useEffect(() => {
    void loadData()
  }, [loadData])

  const filteredBooks = useMemo(() => {
    const query = search.trim().toLowerCase()

    if (!query) {
      return books
    }

    return books.filter((book) =>
      [book.title, book.author, book.genre, book.publisher]
        .join(' ')
        .toLowerCase()
        .includes(query),
    )
  }, [books, search])

  const ensureAdminAccess = () => {
    if (!token) {
      navigate('/signin', { replace: true })
      return false
    }

    if (user?.roleId !== 1) {
      navigate('/profile', { replace: true })
      return false
    }

    return true
  }

  const handleSelectBook = (book: Book) => {
    if (!ensureAdminAccess()) {
      return
    }

    setSelectedBook(book)
    setForm({
      title: book.title,
      description: book.description,
      author: book.author,
      genres: book.genre ? [book.genre] : [],
      publisher: book.publisher,
      publishedYear: book.publishedYear ? String(book.publishedYear) : '',
      coverFile: null,
      files: [],
    })
    setSuccessMessage('')
    setError('')
  }

  const handleCreateNew = () => {
    if (!ensureAdminAccess()) {
      return
    }

    setSelectedBook(null)
    setForm(initialFormState)
    setSuccessMessage('')
    setError('')
  }

  const handleDelete = async (bookId: number) => {
    if (!ensureAdminAccess()) {
      return
    }

    if (!token) {
      return
    }

    if (!window.confirm('Удалить книгу?')) {
      return
    }

    try {
      setIsLoading(true)
      setError('')
      setSuccessMessage('')

      await deleteBook(bookId, token)
      setSuccessMessage('Книга успешно удалена.')
      await loadData()
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Произошла ошибка при удалении')
    } finally {
      setIsLoading(false)
    }
  }

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault()

    if (!ensureAdminAccess()) {
      return
    }

    if (!token) {
      return
    }

    if (!form.title.trim()) {
      setError('Название книги обязательно')
      return
    }

    if (!form.author.trim()) {
      setError('Укажите автора')
      return
    }

    if (form.genres.length === 0) {
      setError('Выберите хотя бы один жанр')
      return
    }

    const payload: BookFormPayload = {
      book_title: form.title.trim(),
      description: form.description.trim(),
      published_year: form.publishedYear ? Number(form.publishedYear) : undefined,
      author: form.author.trim(),
      genres: form.genres,
      publisher: form.publisher.trim() || undefined,
    }

    try {
      setIsSaving(true)
      setError('')
      setSuccessMessage('')

      if (selectedBook) {
        await updateBook(selectedBook.id, payload, token, form.coverFile ?? undefined, form.files)
        setSuccessMessage('Книга успешно обновлена.')
      } else {
        await createBook(payload, token, form.coverFile ?? undefined, form.files)
        setSuccessMessage('Книга успешно добавлена.')
      }

      setForm(initialFormState)
      setSelectedBook(null)
      await loadData()
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Произошла ошибка при сохранении')
    } finally {
      setIsSaving(false)
    }
  }

  return (
    <main className={styles.adminPage}>
      <Header
        leftVariant="back"
        centerVariant="title"
        title="Админ-панель"
        rightVariant="none"
        onBackClick={() => navigate('/profile')}
      />

      <section className={styles.container}>
        {user ? <p className={styles.userGreeting}>Вы вошли как: {user.login}</p> : null}
        {isLoading ? <p className={styles.status}>Загрузка...</p> : null}
        {error ? <p className={styles.error}>{error}</p> : null}
        {successMessage ? <p className={styles.success}>{successMessage}</p> : null}

        <div className={styles.controls}>
          <label className={styles.searchLabel}>
            Найти книгу
            <input
              className={styles.searchInput}
              value={search}
              onChange={(event) => setSearch(event.target.value)}
              placeholder="Название, автор, жанр, издательство"
            />
          </label>
          <button className={styles.newButton} type="button" onClick={handleCreateNew}>
            Добавить книгу
          </button>
        </div>

        <div className={styles.grid}>
          <aside className={styles.sidebar}>
            <h2>Жанры ({genres.length})</h2>
            <ul className={styles.genreList}>
              {genres.map((genre) => (
                <li key={genre.id}>{genre.name}</li>
              ))}
            </ul>
          </aside>

          <aside className={styles.sidebar}>
            <h2>Авторы ({authors.length})</h2>
            <ul className={styles.genreList}>
              {authors.map((author) => (
                <li key={author.id}>{author.firstName} {author.middleName} {author.lastName}</li>
              ))}
            </ul>
          </aside>

          <section className={styles.listSection}>
            <h2>Книги ({filteredBooks.length})</h2>
            <div className={styles.tableWrap}>
              <table className={styles.table}>
                <thead>
                  <tr>
                    <th>Название</th>
                    <th>Автор</th>
                    <th>Жанр</th>
                    <th>Издательство</th>
                    <th>Год</th>
                    <th>Файлы</th>
                    <th>Действия</th>
                  </tr>
                </thead>
                <tbody>
                  {filteredBooks.map((book) => (
                    <tr key={book.id}>
                      <td>{book.title}</td>
                      <td>{book.author}</td>
                      <td>{book.genre}</td>
                      <td>{book.publisher}</td>
                      <td>{book.publishedYear ?? ''}</td>
                      <td>{book.filesCount}</td>
                      <td>
                        <button
                          className={styles.actionButton}
                          type="button"
                          onClick={() => handleSelectBook(book)}
                        >
                          Редактировать
                        </button>
                        <button
                          className={styles.actionButtonDanger}
                          type="button"
                          onClick={() => handleDelete(book.id)}
                        >
                          Удалить
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </section>

          <section className={styles.formSection}>
            <h2>{selectedBook ? 'Редактирование книги' : 'Добавление книги'}</h2>
            <form className={styles.form} onSubmit={handleSubmit}>
              <label className={styles.label}>
                Название
                <input
                  className={styles.input}
                  value={form.title}
                  onChange={(event) => setForm((prev) => ({ ...prev, title: event.target.value }))}
                />
              </label>

              <label className={styles.label}>
                Описание
                <textarea
                  className={styles.textarea}
                  value={form.description}
                  onChange={(event) =>
                    setForm((prev) => ({ ...prev, description: event.target.value }))
                  }
                />
              </label>

              <label className={styles.label}>
                Автор
                <input
                  className={styles.input}
                  value={form.author}
                  onChange={(event) => setForm((prev) => ({ ...prev, author: event.target.value }))}
                  placeholder="Введите имя автора"
                />
              </label>

              <label className={styles.label}>
                Издательство
                <input
                  className={styles.input}
                  value={form.publisher}
                  onChange={(event) =>
                    setForm((prev) => ({ ...prev, publisher: event.target.value }))
                  }
                />
              </label>

              <label className={styles.label}>
                Год издания
                <input
                  className={styles.input}
                  type="number"
                  value={form.publishedYear}
                  onChange={(event) =>
                    setForm((prev) => ({ ...prev, publishedYear: event.target.value }))
                  }
                />
              </label>

              <label className={styles.label}>
                Жанр(ы) - выбрать из списка или добавить свой
                <div className={styles.genresContainer}>
                  <select
                    className={styles.select}
                    multiple
                    value={form.genres}
                    onChange={(event) => {
                      const selected = Array.from(
                        event.target.selectedOptions,
                        (option) => option.value,
                      )
                      setForm((prev) => ({ ...prev, genres: selected }))
                    }}
                  >
                    {genres.map((genre) => (
                      <option key={genre.id} value={genre.name}>
                        {genre.name}
                      </option>
                    ))}
                  </select>

                  <div className={styles.selectedGenres}>
                    {form.genres.map((genre) => (
                      <div key={genre} className={styles.genreTag}>
                        <span>{genre}</span>
                        <button
                          type="button"
                          className={styles.genreTagRemove}
                          onClick={() =>
                            setForm((prev) => ({
                              ...prev,
                              genres: prev.genres.filter((g) => g !== genre),
                            }))
                          }
                        >
                          ×
                        </button>
                      </div>
                    ))}
                  </div>

                  <input
                    type="text"
                    className={styles.input}
                    placeholder="Введите свой жанр и нажмите Enter"
                    onKeyPress={(event) => {
                      if (event.key === 'Enter') {
                        event.preventDefault()
                        const value = (event.target as HTMLInputElement).value.trim()

                        if (value && !form.genres.includes(value)) {
                          setForm((prev) => ({
                            ...prev,
                            genres: [...prev.genres, value],
                          }))
                          ;(event.target as HTMLInputElement).value = ''
                        }
                      }
                    }}
                  />
                </div>
              </label>

              <label className={styles.label}>
                Обложка (опция)
                <input
                  type="file"
                  accept="image/*"
                  className={styles.inputFile}
                  onChange={(event) => {
                    const file = event.target.files?.[0] ?? null
                    setForm((prev) => ({ ...prev, coverFile: file }))
                  }}
                />
              </label>

              <label className={styles.label}>
                Прикрепить файл(ы) (pdf, fb2, txt)
                <input
                  type="file"
                  accept=".pdf,.fb2,.txt"
                  className={styles.inputFile}
                  multiple
                  onChange={(event) => {
                    const files = event.target.files ? Array.from(event.target.files) : []
                    setForm((prev) => ({ ...prev, files }))
                  }}
                />
              </label>

              <button className={styles.saveButton} type="submit" disabled={isSaving}>
                {isSaving ? 'Сохранение...' : selectedBook ? 'Сохранить' : 'Добавить'}
              </button>
            </form>
          </section>
        </div>
      </section>
    </main>
  )
}