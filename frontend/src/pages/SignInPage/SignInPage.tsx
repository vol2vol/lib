import { useState, type FormEvent } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { Icon } from '@components/Icon'
import { loginUser } from '@api/auth'
import { ApiError } from '@api/http'
import styles from './SignInPage.module.css'

type FieldErrors = {
  login?: string
  password?: string
}

export const SignInPage = () => {
  const navigate = useNavigate()

  const [login, setLogin] = useState('')
  const [password, setPassword] = useState('')
  const [error, setError] = useState('')
  const [fieldErrors, setFieldErrors] = useState<FieldErrors>({})
  const [isLoading, setIsLoading] = useState(false)
  const [showPassword, setShowPassword] = useState(false)

  const handleSubmit = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault()
    setError('')
    setFieldErrors({})

    if (!login.trim()) {
      setFieldErrors({ login: 'Введите логин' })
      return
    }

    if (!password.trim()) {
      setFieldErrors({ password: 'Введите пароль' })
      return
    }

    try {
      setIsLoading(true)

      const data = await loginUser({
        login,
        password,
      })

      const token = data.accessToken

      if (token) {
        localStorage.setItem('token', token)
      }

      // Админы идут в админ-панель, остальные на главный экран
      const isAdmin = data.user?.roleId === 1
      navigate(isAdmin ? '/admin' : '/library')
    } catch (err) {
      if (err instanceof ApiError) {
        if (err.fieldErrors) {
          setFieldErrors({
            login: err.fieldErrors.login?.[0],
            password: err.fieldErrors.password?.[0],
          })
          setError('')
        } else {
          setError(err.message)
        }
      } else {
        setError(err instanceof Error ? err.message : 'Произошла ошибка')
      }
    } finally {
      setIsLoading(false)
    }
  }

  return (
    <main className={styles.signInPage}>
      <section className={styles.content}>
        <button
          className={styles.back}
          type="button"
          onClick={() => navigate('/library')}
          aria-label="Назад"
        >
          <Icon name="BackButton" className={styles.backIcon} />
        </button>

        <Icon name="Logo" className={styles.logo} />

        <p className={styles.subtitle}>Чтобы войти, введите данные аккаунта</p>

        <form className={styles.form} onSubmit={handleSubmit}>
          <div className={styles.fields}>
            <label className={styles.label}>
              Логин
              <input
                className={`${styles.input} ${fieldErrors.login ? styles.inputError : ''}`}
                type="text"
                value={login}
                onChange={(event) => {
                  setLogin(event.target.value)
                  setError('')
                  setFieldErrors((prev) => ({ ...prev, login: undefined }))
                }}
                autoComplete="username"
              />
              {fieldErrors.login ? (
                <span className={styles.fieldError}>{fieldErrors.login}</span>
              ) : null}
            </label>

            <label className={styles.label}>
              Пароль

              <div className={styles.passwordField}>
                <input
                  className={`${styles.input} ${fieldErrors.password ? styles.inputError : ''}`}
                  type={showPassword ? 'text' : 'password'}
                  value={password}
                  onChange={(event) => {
                    setPassword(event.target.value)
                    setError('')
                    setFieldErrors((prev) => ({ ...prev, password: undefined }))
                  }}
                  autoComplete="current-password"
                />

                <button
                  className={styles.passwordToggle}
                  type="button"
                  onMouseDown={() => setShowPassword(true)}
                  onMouseUp={() => setShowPassword(false)}
                  onMouseLeave={() => setShowPassword(false)}
                  onTouchStart={() => setShowPassword(true)}
                  onTouchEnd={() => setShowPassword(false)}
                >
                  Показать
                </button>
              </div>

              {fieldErrors.password ? (
                <span className={styles.fieldError}>{fieldErrors.password}</span>
              ) : null}
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
            {isLoading ? '...' : <Icon name="ForwardButton" className={styles.submitIcon} />}
          </button>
        </form>
      </section>
    </main>
  )
}