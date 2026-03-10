import { useState } from 'react'
import type { FormEvent } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { Icon } from '@components/Icon'
import { ApiError, registerUser } from '@api/auth'
import styles from './SignUpPage.module.css'

type FieldErrors = {
  login?: string
  password?: string
  password_confirmation?: string
}

export const SignUpPage = () => {
  const navigate = useNavigate()

  const [login, setLogin] = useState('')
  const [password, setPassword] = useState('')
  const [passwordConfirmation, setPasswordConfirmation] = useState('')
  const [error, setError] = useState('')
  const [fieldErrors, setFieldErrors] = useState<FieldErrors>({})
  const [isLoading, setIsLoading] = useState(false)
  const [showPassword, setShowPassword] = useState(false)
  const [showPasswordConfirmation, setShowPasswordConfirmation] = useState(false)

  const resetErrors = () => {
    setError('')
    setFieldErrors({})
  }

  const handleSubmit = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault()
    resetErrors()

    if (!login.trim()) {
      setFieldErrors({ login: 'Введите логин' })
      return
    }

    if (!password.trim()) {
      setFieldErrors({ password: 'Введите пароль' })
      return
    }

    if (password !== passwordConfirmation) {
      setFieldErrors({ password_confirmation: 'Пароли не совпадают' })
      return
    }

    try {
      setIsLoading(true)

      const data = await registerUser({
        login,
        password,
        password_confirmation: passwordConfirmation,
      })

      const token = data.access_token

      if (token) {
        localStorage.setItem('token', token)
      }

      navigate('/library')
    } catch (err) {
      if (err instanceof ApiError) {
        if (err.fieldErrors) {
          setFieldErrors({
            login: err.fieldErrors.login?.[0],
            password: err.fieldErrors.password?.[0],
            password_confirmation: err.fieldErrors.password_confirmation?.[0],
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
    <main className={styles.signUpPage}>
      <section className={styles.content}>
        <Icon name="Logo" className={styles.logo} />

        <p className={styles.subtitle}>Чтобы присоединиться, зарегистрируйтесь</p>

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
              Придумайте пароль

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
                  autoComplete="new-password"
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

            <label className={styles.label}>
              Повторите пароль

              <div className={styles.passwordField}>
                <input
                  className={`${styles.input} ${fieldErrors.password_confirmation ? styles.inputError : ''}`}
                  type={showPasswordConfirmation ? 'text' : 'password'}
                  value={passwordConfirmation}
                  onChange={(event) => {
                    setPasswordConfirmation(event.target.value)
                    setError('')
                    setFieldErrors((prev) => ({
                      ...prev,
                      password_confirmation: undefined,
                    }))
                  }}
                  autoComplete="new-password"
                />

                <button
                  className={styles.passwordToggle}
                  type="button"
                  onMouseDown={() => setShowPasswordConfirmation(true)}
                  onMouseUp={() => setShowPasswordConfirmation(false)}
                  onMouseLeave={() => setShowPasswordConfirmation(false)}
                  onTouchStart={() => setShowPasswordConfirmation(true)}
                  onTouchEnd={() => setShowPasswordConfirmation(false)}
                >
                  Показать
                </button>
              </div>

              {fieldErrors.password_confirmation ? (
                <span className={styles.fieldError}>
                  {fieldErrors.password_confirmation}
                </span>
              ) : null}
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
            {isLoading ? '...' : <Icon name="ForwardButton" size={20} />}
        </button>
        </form>
      </section>
    </main>
  )
}