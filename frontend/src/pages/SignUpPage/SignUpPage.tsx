import { useState } from 'react'
import type { FormEvent } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { Logo } from '@components/Logo'
import { registerUser } from '@api/auth'
import styles from './SignUpPage.module.css'

export const SignUpPage = () => {
  const navigate = useNavigate()

  const [login, setLogin] = useState('')
  const [password, setPassword] = useState('')
  const [passwordConfirmation, setPasswordConfirmation] = useState('')
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

    if (password !== passwordConfirmation) {
      setError('Пароли не совпадают')
      return
    }

    try {
      setIsLoading(true)

      const data = await registerUser({
        login,
        password,
        password_confirmation: passwordConfirmation,
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
    <main className={styles.signUpPage}>
      <section className={styles.content}>
        <Logo className={styles.logo} />

        <p className={styles.subtitle}>Чтобы присоединиться, зарегистрируйтесь</p>

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
              Придумайте пароль
              <input
                className={styles.input}
                type="password"
                value={password}
                onChange={(event) => setPassword(event.target.value)}
                autoComplete="new-password"
              />
            </label>

            <label className={styles.label}>
              Повторите пароль
              <input
                className={styles.input}
                type="password"
                value={passwordConfirmation}
                onChange={(event) => setPasswordConfirmation(event.target.value)}
                autoComplete="new-password"
              />
            </label>
          </div>

          {error ? <p className={styles.error}>{error}</p> : null}

          <div className={styles.footer}>
            <span className={styles.footerText}>Уже есть аккаунт?</span>
            <Link className={styles.link} to="/signin">
                Войти
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