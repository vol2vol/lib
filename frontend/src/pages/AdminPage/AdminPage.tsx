import { useEffect, useMemo, useState, useCallback } from 'react'
import { useNavigate } from 'react-router-dom'
import { getCurrentUser } from '@api/auth'
import {
  getAllBooks,
  getAdminGenres,
  getAdminAuthors,
  getAdminPublishers,
  createPublisher,
  updatePublisher,
  deletePublisher,
  createBook,
  updateBook,
  deleteBook,
  getBookById,
  createAuthor,
  updateAuthor,
  deleteAuthor,
  createGenre,
  updateGenre,
  deleteGenre,
} from '@api/library'
import { Header } from '@components/Header'
import type { Book, Genre, Author, Publisher, PublisherFormPayload, AuthorFormPayload, GenreFormPayload } from '@models/library'
import type { BookFormPayload } from '@models/library'
import type { User } from '@models/auth'
import styles from './AdminPage.module.css'

type GenreFormState = {
  name: string;
}

type AuthorFormState = {
  first_name: string;
  last_name: string;
  middle_name: string | null;
}

type PublisherFormState = {
  name: string;
}

type BookFormState = {
  title: string
  description: string
  authors: string[]
  genres: string[]
  publisher: string
  publishedYear: string
  coverFile: File | null
  files: File[]
}

const initialGenreFormState: GenreFormState = {
  name: '',
}

const initialAuthorFormState: AuthorFormState = {
  first_name: '',
  middle_name: '',
  last_name: '',
}

const initialPublisherFormState: PublisherFormState = {
  name: '',
}

const initialFormState: BookFormState = {
  title: '',
  description: '',
  authors: [],
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
  const [publishers, setPublishers] = useState<Publisher[]>([])
  const [books, setBooks] = useState<Book[]>([])
  const [search, setSearch] = useState('')
  const [selectedGenre, setSelectedGenre] = useState<Genre | null>(null)
  const [selectedAuthor, setSelectedAuthor] = useState<Author | null>(null)
  const [selectedPublisher, setSelectedPublisher] = useState<Publisher | null>(null)
  const [selectedBook, setSelectedBook] = useState<Book | null>(null)
  const [genreForm, setGenreForm] = useState<GenreFormState>(initialGenreFormState)
  const [authorForm, setAuthorForm] = useState<AuthorFormState>(initialAuthorFormState)
  const [publisherForm, setPublisherForm] = useState<PublisherFormState>(initialPublisherFormState)
  const [form, setForm] = useState<BookFormState>(initialFormState)
  const [error, setError] = useState('')
  const [successMessage, setSuccessMessage] = useState('')
  const [isLoading, setIsLoading] = useState(true)
  const [isSaving, setIsSaving] = useState(false)
  const [isFilterOpen, setIsFilterOpen] = useState(false)

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
      const [genresData, booksData, authorsData, publishersData] = await Promise.all([
        getAdminGenres(token),
        getAllBooks(),
        getAdminAuthors(token),
        getAdminPublishers(token),
      ])

      setUser(currentUser)
      setGenres(genresData)
      setBooks(booksData)
      setAuthors(authorsData)
      setPublishers(publishersData)
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

  const handleSelectBook = async (book: Book) => {
    if (!ensureAdminAccess()) {
      return
    }
    
    if (!token) {
      return
    }

    setSelectedBook(book)
    setForm({
      title: book.title,
      description: (await getBookById(book.id, token)).description,
      authors: book.authors.map((author) => author.id.toString()),
      genres: book.genres.map((genre) => genre.id.toString()),
      publisher: book.publisher.id.toString(),
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

  const handleSelectAuthor = (author: Author) => {
    if (!ensureAdminAccess()) {
      return
    }
    
    if (!token) {
      return
    }

    setSelectedAuthor(author)
    setAuthorForm({
      first_name: author.firstName,
      middle_name: author.middleName,
      last_name: author.lastName,

    })
    setSuccessMessage('')
    setError('')
  }

  const handleDeleteAuthor = async (authorId: number) => {
    if (!ensureAdminAccess()) {
      return
    }

    if (!token) {
      return
    }

    if (!window.confirm('Удалить автора?')) {
      return
    }

    try {
      setIsLoading(true)
      setError('')
      setSuccessMessage('')

      await deleteAuthor(authorId, token)
      setSuccessMessage('Автор успешно удален.')
      await loadData()
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Произошла ошибка при удалении')
    } finally {
      setIsLoading(false)
    }
  }

  const handleSelectGenre = (genre: Genre) => {
    if (!ensureAdminAccess()) {
      return
    }
    
    if (!token) {
      return
    }

    setSelectedGenre(genre)
    setGenreForm({
      name: genre.name 
    })
    setSuccessMessage('')
    setError('')
  }

  const handleDeleteGenre = async (genreId: number) => {
    if (!ensureAdminAccess()) {
      return
    }

    if (!token) {
      return
    }

    if (!window.confirm('Удалить жанр?')) {
      return
    }

    try {
      setIsLoading(true)
      setError('')
      setSuccessMessage('')

      await deleteGenre(genreId, token)
      setSuccessMessage('Жанр успешно удален.')
      await loadData()
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Произошла ошибка при удалении')
    } finally {
      setIsLoading(false)
    }
  }

  const handleSelectPublisher = (publisher: Genre) => {
    if (!ensureAdminAccess()) {
      return
    }
    
    if (!token) {
      return
    }

    setSelectedPublisher(publisher)
    setPublisherForm({
      name: publisher.name 
    })
    setSuccessMessage('')
    setError('')
  }

  const handleDeletePublisher = async (publisherId: number) => {
    if (!ensureAdminAccess()) {
      return
    }

    if (!token) {
      return
    }

    if (!window.confirm('Удалить издательство?')) {
      return
    }

    try {
      setIsLoading(true)
      setError('')
      setSuccessMessage('')

      await deletePublisher(publisherId, token)
      setSuccessMessage('Издательство успешно удалено.')
      await loadData()
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Произошла ошибка при удалении')
    } finally {
      setIsLoading(false)
    }
  }

  const handleSubmitGenre = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault()

    if (!ensureAdminAccess()) {
      return
    }

    if (!token) {
      return
    }

    if (!genreForm.name.trim()) {
      setError('Название жанра обязательно')
      return
    }

    const payload: GenreFormPayload = {
      genre_name: genreForm.name.trim(),
    }

    try {
      setIsSaving(true)
      setError('')
      setSuccessMessage('')
      if (selectedGenre) {
        await updateGenre(selectedGenre.id, payload, token)
        setSuccessMessage('Жанр успешно обновлено.')
      } else {
        await createGenre(payload, token)
        setSuccessMessage('Жанр успешно обнавлен.')
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

  const handleSubmitAuthor = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault()

    if (!ensureAdminAccess()) {
      return
    }

    if (!token) {
      return
    }

    if (!authorForm.first_name.trim()) {
      setError('Имя обязательно')
      return
    }

    if (!authorForm.last_name.trim()) {
      setError('Фамилия обязательна')
      return
    }

    const payload: AuthorFormPayload = {
      first_name: authorForm.first_name.trim(),
      middle_name: authorForm.middle_name ? authorForm.middle_name.trim() : null,
      last_name: authorForm.last_name.trim(),
    }

    try {
      setIsSaving(true)
      setError('')
      setSuccessMessage('')
      if (selectedAuthor) {
        await updateAuthor(selectedAuthor.id, payload, token)
        setSuccessMessage('Автор успешно обновлен.')
      } else {
        await createAuthor(payload, token)
        setSuccessMessage('Автор успешно добавлен.')
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

  const handleSubmitPublisher = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault()

    if (!ensureAdminAccess()) {
      return
    }

    if (!token) {
      return
    }

    if (!publisherForm.name.trim()) {
      setError('Название издательства обязательно')
      return
    }

    const payload: PublisherFormPayload = {
      publisher_name: publisherForm.name.trim(),
    }

    try {
      setIsSaving(true)
      setError('')
      setSuccessMessage('')
      console.log(form.files)
      if (selectedPublisher) {
        await updatePublisher(selectedPublisher.id, payload, token)
        setSuccessMessage('Издательство успешно обновлено.')
      } else {
        await createPublisher(payload, token)
        setSuccessMessage('Издательство успешно добавлено.')
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

    if (form.authors.length === 0) {
      setError('Укажите хотя бы одного автора')
      return
    }

    if (form.genres.length === 0) {
      setError('Выберите хотя бы один жанр')
      return
    }
    console.log(form.publisher);
    const payload: BookFormPayload = {
      book_title: form.title.trim(),
      description: form.description.trim(),
      published_year: form.publishedYear ? Number(form.publishedYear) : undefined,
      authors: form.authors,
      genres: form.genres,
      publisher: form.publisher.trim(),
    }

    try {
      setIsSaving(true)
      setError('')
      setSuccessMessage('')
      console.log(form.files)
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
        centerVariant="search"
        rightVariant="profile"
        onBackClick={() => navigate('/profile')}
        onFilterClick={() => setIsFilterOpen((current) => !current)}
        onProfileClick={() => navigate('/profile')}
      />

      <section className={styles.container}>
        {isLoading ? <p className={styles.status}>Загрузка...</p> : null}
        {error ? <p className={styles.error}>{error}</p> : null}
        {successMessage ? <p className={styles.success}>{successMessage}</p> : null}

        <div className={styles.grid}>
          <aside className={styles.sidebar}>
            <h2>Жанры ({genres.length})</h2>
            <div className={styles.tableWrap}>
              <table className={styles.table}>
                <thead>
                  <tr>
                    <th>Название</th>
                    <th>Действия</th>
                  </tr>
                </thead>
                <tbody>
                  {genres.map((genre) => (
                    <tr key={genre.id}>
                      <td>{genre.name}</td>
                      <td>
                        <button
                          className={styles.actionButton}
                          type="button"
                          onClick={() => handleSelectGenre(genre)}
                        >
                          Редактировать
                        </button>
                        <button
                          className={styles.actionButtonDanger}
                          type="button"
                          onClick={() => handleDeleteGenre(genre.id)}
                        >
                          Удалить
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </aside>

          <aside className={styles.sidebar}>
            <h2>Авторы ({authors.length})</h2>
            <div className={styles.tableWrap}>
              <table className={styles.table}>
                <thead>
                  <tr>
                    <th>ФИО</th>
                    <th>Действия</th>
                  </tr>
                </thead>
                <tbody>
                  {authors.map((author) => (
                    <tr key={author.id}>
                      <td>{author.fullName}</td>
                      <td>
                        <button
                          className={styles.actionButton}
                          type="button"
                          onClick={() => handleSelectAuthor(author)}
                        >
                          Редактировать
                        </button>
                        <button
                          className={styles.actionButtonDanger}
                          type="button"
                          onClick={() => handleDeleteAuthor(author.id)}
                        >
                          Удалить
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </aside>

          <aside className={styles.sidebar}>
            <h2>Издательства ({publishers.length})</h2>
            <div className={styles.tableWrap}>
              <table className={styles.table}>
                <thead>
                  <tr>
                    <th>Название</th>
                    <th>Действия</th>
                  </tr>
                </thead>
                <tbody>
                  {publishers.map((publisher) => (
                    <tr key={publisher.id}>
                      <td>{publisher.name}</td>
                      <td>
                        <button
                          className={styles.actionButton}
                          type="button"
                          onClick={() => handleSelectPublisher(publisher)}
                        >
                          Редактировать
                        </button>
                        <button
                          className={styles.actionButtonDanger}
                          type="button"
                          onClick={() => handleDeletePublisher(publisher.id)}
                        >
                          Удалить
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
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
                      <td>{book.publisher.name}</td>
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
            <h2>{selectedGenre ? 'Редактирование жанра' : 'Добавление жанра'}</h2>
            <form className={styles.form} onSubmit={handleSubmitGenre}>
              <label className={styles.label}>
                Название
                <input
                  className={styles.input}
                  value={genreForm.name}
                  onChange={(event) => setGenreForm((prev) => ({ ...prev, name: event.target.value }))}
                />
              </label>
              <button className={styles.saveButton} type="submit" disabled={isSaving}>
                {isSaving ? 'Сохранение...' : selectedGenre ? 'Сохранить' : 'Добавить'}
              </button>
            </form>
          </section>

          <section className={styles.formSection}>
            <h2>{selectedAuthor ? 'Редактирование автора' : 'Добавление автора'}</h2>
            <form className={styles.form} onSubmit={handleSubmitAuthor}>
              <label className={styles.label}>
                Имя
                <input
                  className={styles.input}
                  value={authorForm.first_name}
                  onChange={(event) => setAuthorForm((prev) => ({ ...prev, first_name: event.target.value }))}
                />
              </label>
              <label className={styles.label}>
                Отчество
                <input
                  className={styles.input}
                  value={authorForm.middle_name ? authorForm.middle_name : ''}
                  onChange={(event) => setAuthorForm((prev) => ({ ...prev, middle_name: event.target.value }))}
                />
              </label>
              <label className={styles.label}>
                Фамилия
                <input
                  className={styles.input}
                  value={authorForm.last_name}
                  onChange={(event) => setAuthorForm((prev) => ({ ...prev, last_name: event.target.value }))}
                />
              </label>
              <button className={styles.saveButton} type="submit" disabled={isSaving}>
                {isSaving ? 'Сохранение...' : selectedAuthor ? 'Сохранить' : 'Добавить'}
              </button>
            </form>
          </section>

          <section className={styles.formSection}>
            <h2>{selectedPublisher ? 'Редактирование издательства' : 'Добавление издательства'}</h2>
            <form className={styles.form} onSubmit={handleSubmitPublisher}>
              <label className={styles.label}>
                Название
                <input
                  className={styles.input}
                  value={publisherForm.name}
                  onChange={(event) => setPublisherForm((prev) => ({ ...prev, name: event.target.value }))}
                />
              </label>
              <button className={styles.saveButton} type="submit" disabled={isSaving}>
                {isSaving ? 'Сохранение...' : selectedPublisher ? 'Сохранить' : 'Добавить'}
              </button>
            </form>
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
                Авторы
                <div className={styles.genresContainer}>
                  <select
                    className={styles.select}
                    multiple
                    value={form.authors}
                    onChange={(event) => {
                      const selected = Array.from(
                        event.target.selectedOptions,
                        (option) => option.value,
                      )
                      setForm((prev) => ({ ...prev, authors: selected }))
                    }}
                  >
                    {authors.map((author) => (
                      <option key={author.id} value={author.id}>
                        {author.fullName}
                      </option>
                    ))}
                  </select>

                  <div className={styles.selectedGenres}>
                    {form.authors.map((author) => (
                      <div key={author} className={styles.genreTag}>
                        <span>{author}</span>
                        <button
                          type="button"
                          className={styles.genreTagRemove}
                          onClick={() =>
                            setForm((prev) => ({
                              ...prev,
                              authors: prev.authors.filter((a) => a !== author),
                            }))
                          }
                        >
                          ×
                        </button>
                      </div>
                    ))}
                  </div>
                </div>
              </label>

              <label className={styles.label}>
                Издательство
                <div className={styles.genresContainer}>
                  <select
                    className={styles.select}
                    value={form.publisher}
                    onChange={(event) => {
                      const selected = Array.from(
                        event.target.selectedOptions,
                        (option) => option.value,
                      )
                      setForm((prev) => ({ ...prev, publisher: selected[0] }))
                    }}
                  >
                    {publishers.map((publisher) => (
                      <option key={publisher.id} value={publisher.id}>
                        {publisher.name}
                      </option>
                    ))}
                  </select>
                </div>
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
                Жанры
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
                      <option key={genre.id} value={genre.id}>
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