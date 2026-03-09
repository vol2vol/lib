import { useState, type FormEvent } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { Logo } from '@components/Logo'
import { loginUser } from '@api/auth'
import styles from './SignInPage.module.css'

export const SignInPage = () => {
  const navigate = useNavigate()

  const [login, setLogin] = useState('')
  const [password, setPassword] = useState('')
  const [error, setError] = useState('')
  const [isLoading, setIsLoading] = useState(false)

  const handleSubmit = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault()
    setError('')

    if (!login.trim()) {
      setError('Введите логин')
      return
    }

    if (!password.trim()) {
      setError('Введите пароль')
      return
    }

    try {
      setIsLoading(true)

      const data = await loginUser({
        login,
        password,
      })

      const token = data.token || data.access_token

      if (token) {
        localStorage.setItem('token', token)
      }

      navigate('/library')
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Произошла ошибка')
    } finally {
      setIsLoading(false)
    }
  }

  return (
    <main className={styles.signInPage}>
      <section className={styles.content}>
        <Logo className={styles.logo} />

        <p className={styles.subtitle}>Чтобы войти, введите данные аккаунта</p>

        <form className={styles.form} onSubmit={handleSubmit}>
          <div className={styles.fields}>
            <label className={styles.label}>
              Логин
              <input
                className={styles.input}
                type="text"
                value={login}
                onChange={(event) => setLogin(event.target.value)}
                autoComplete="username"
              />
            </label>

            <label className={styles.label}>
              Пароль
              <input
                className={styles.input}
                type="password"
                value={password}
                onChange={(event) => setPassword(event.target.value)}
                autoComplete="current-password"
              />
            </label>
          </div>

          {error ? <p className={styles.error}>{error}</p> : null}

          <p className={styles.note}>Восстановление пароля пока недоступно</p>

          <div className={styles.footer}>
            <span className={styles.footerText}>Нет аккаунта?</span>
            <Link className={styles.link} to="/signup">
              Зарегистрироваться
            </Link>
          </div>

          <button className={styles.submit} type="submit" disabled={isLoading}>
            {isLoading ? '...' : '→'}
          </button>
        </form>
      </section>
    </main>
  )
}