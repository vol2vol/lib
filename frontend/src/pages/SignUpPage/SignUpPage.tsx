import { useMemo, useState } from 'react'
import type { FormEvent } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { Icon } from '@components/Icon'
import { registerUser } from '@api/auth'
import { ApiError } from '@api/http'
import styles from './SignUpPage.module.css'

type FieldErrors = {
  login?: string
  password?: string
  password_confirmation?: string
}

const LOGIN_MAX_LENGTH = 255
const PASSWORD_MIN_LENGTH = 8
const PASSWORD_MAX_LENGTH = 255
const LOGIN_ALLOWED_TEXT = 'Разрешены латинские буквы, цифры, точка, дефис и подчёркивание'
const LOGIN_PATTERN = /^[A-Za-z0-9._-]+$/

const sanitizeLogin = (value: string) => value.replace(/\s+/g, '').replace(/[^A-Za-z0-9._-]/g, '')

const getLoginError = (value: string) => {
  if (!value) {
    return 'Введите логин'
  }

  if (!LOGIN_PATTERN.test(value)) {
    return LOGIN_ALLOWED_TEXT
  }

  if (value.length > LOGIN_MAX_LENGTH) {
    return `Логин должен содержать не более ${LOGIN_MAX_LENGTH} символов`
  }

  return undefined
}

const getPasswordError = (value: string) => {
  if (!value.trim()) {
    return 'Введите пароль'
  }

  if (value.length < PASSWORD_MIN_LENGTH) {
    return `Пароль должен содержать минимум ${PASSWORD_MIN_LENGTH} символов`
  }

  if (value.length > PASSWORD_MAX_LENGTH) {
    return `Пароль должен содержать не более ${PASSWORD_MAX_LENGTH} символов`
  }

  return undefined
}

const getPasswordConfirmationError = (password: string, confirmation: string) => {
  if (!confirmation.trim()) {
    return 'Повторите пароль'
  }

  if (confirmation.length < PASSWORD_MIN_LENGTH) {
    return `Пароль должен содержать минимум ${PASSWORD_MIN_LENGTH} символов`
  }

  if (confirmation.length > PASSWORD_MAX_LENGTH) {
    return `Пароль должен содержать не более ${PASSWORD_MAX_LENGTH} символов`
  }

  if (password !== confirmation) {
    return 'Пароли не совпадают'
  }

  return undefined
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

  const loginCounterText = useMemo(() => `Символов: ${login.length} / ${LOGIN_MAX_LENGTH}`, [login.length])
  const passwordCounterText = useMemo(
    () => `Символов: ${password.length} / минимум ${PASSWORD_MIN_LENGTH}`,
    [password.length],
  )
  const confirmationCounterText = useMemo(
    () => `Символов: ${passwordConfirmation.length} / минимум ${PASSWORD_MIN_LENGTH}`,
    [passwordConfirmation.length],
  )
  const isPasswordLengthValid = password.length >= PASSWORD_MIN_LENGTH && password.length <= PASSWORD_MAX_LENGTH
  const isConfirmationLengthValid =
    passwordConfirmation.length >= PASSWORD_MIN_LENGTH && passwordConfirmation.length <= PASSWORD_MAX_LENGTH
  const passwordMatchState = !passwordConfirmation
    ? ''
    : password === passwordConfirmation
      ? 'Пароли совпадают'
      : 'Пароли не совпадают'

  const resetErrors = () => {
    setError('')
    setFieldErrors({})
  }

  const handleSubmit = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault()
    resetErrors()

    const nextFieldErrors: FieldErrors = {
      login: getLoginError(login),
      password: getPasswordError(password),
      password_confirmation: getPasswordConfirmationError(password, passwordConfirmation),
    }

    if (nextFieldErrors.login || nextFieldErrors.password || nextFieldErrors.password_confirmation) {
      setFieldErrors(nextFieldErrors)
      return
    }

    try {
      setIsLoading(true)

      const data = await registerUser({
        login,
        password,
        password_confirmation: passwordConfirmation,
      })

      const token = data.accessToken

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
        <button
          className={styles.back}
          type="button"
          onClick={() => navigate('/library')}
          aria-label="Назад"
        >
          <Icon name="BackButton" className={styles.backIcon} />
        </button>

        <Icon name="Logo" className={styles.logo} />

        <p className={styles.subtitle}>Чтобы присоединиться, зарегистрируйтесь</p>

        <form className={styles.form} onSubmit={handleSubmit} noValidate>
          <div className={styles.fields}>
            <label className={styles.label}>
              Логин
              <div className={styles.metaRow}>
                <span className={styles.hint}>{LOGIN_ALLOWED_TEXT}</span>
              </div>
              <input
                className={`${styles.input} ${fieldErrors.login ? styles.inputError : ''}`}
                type="text"
                value={login}
                onChange={(event) => {
                  const nextValue = sanitizeLogin(event.target.value).slice(0, LOGIN_MAX_LENGTH)
                  setLogin(nextValue)
                  setError('')
                  setFieldErrors((prev) => ({ ...prev, login: undefined }))
                }}
                autoComplete="username"
                spellCheck={false}
                inputMode="text"
                maxLength={LOGIN_MAX_LENGTH}
                pattern="[A-Za-z0-9._-]+"
                required
                title={LOGIN_ALLOWED_TEXT}
                aria-describedby="signup-login-help"
              />
              <div className={styles.fieldFooter}>
                <span className={styles.counter}>{loginCounterText}</span>
              </div>
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
                    setPassword(event.target.value.slice(0, PASSWORD_MAX_LENGTH))
                    setError('')
                    setFieldErrors((prev) => ({
                      ...prev,
                      password: undefined,
                      password_confirmation: undefined,
                    }))
                  }}
                  autoComplete="new-password"
                  minLength={PASSWORD_MIN_LENGTH}
                  maxLength={PASSWORD_MAX_LENGTH}
                  required
                  title={`Пароль должен содержать минимум ${PASSWORD_MIN_LENGTH} символов`}
                  aria-describedby="signup-password-help"
                />

                <button
                  className={styles.passwordToggle}
                  type="button"
                  onClick={() => setShowPassword((prev) => !prev)}
                  aria-label={showPassword ? 'Скрыть пароль' : 'Показать пароль'}
                  aria-pressed={showPassword}
                >
                  {showPassword ? 'Скрыть' : 'Показать'}
                </button>
              </div>
              <div className={styles.fieldFooter}>
                <span
                  className={`${styles.counter} ${password && !isPasswordLengthValid ? styles.counterWarning : ''}`}
                >
                  {passwordCounterText}
                </span>
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
                    setPasswordConfirmation(event.target.value.slice(0, PASSWORD_MAX_LENGTH))
                    setError('')
                    setFieldErrors((prev) => ({
                      ...prev,
                      password_confirmation: undefined,
                    }))
                  }}
                  autoComplete="new-password"
                  minLength={PASSWORD_MIN_LENGTH}
                  maxLength={PASSWORD_MAX_LENGTH}
                  required
                  title={`Пароль должен содержать минимум ${PASSWORD_MIN_LENGTH} символов`}
                  aria-describedby="signup-password-confirmation-help"
                />

                <button
                  className={styles.passwordToggle}
                  type="button"
                  onClick={() => setShowPasswordConfirmation((prev) => !prev)}
                  aria-label={showPasswordConfirmation ? 'Скрыть пароль' : 'Показать пароль'}
                  aria-pressed={showPasswordConfirmation}
                >
                  {showPasswordConfirmation ? 'Скрыть' : 'Показать'}
                </button>
              </div>
              <div className={styles.fieldFooter}>
                {passwordMatchState ? (
                  <span className={`${styles.hint} ${passwordConfirmation && password !== passwordConfirmation ? styles.helperWarning : ''}`}>
                    {passwordMatchState}
                  </span>
                ) : <span />}
                <span
                  className={`${styles.counter} ${passwordConfirmation && !isConfirmationLengthValid ? styles.counterWarning : ''}`}
                >
                  {confirmationCounterText}
                </span>
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
            {isLoading ? '...' : <Icon name="ForwardButton" className={styles.submitIcon} />}
          </button>
        </form>
      </section>
    </main>
  )
}
