import { useNavigate } from 'react-router-dom'
import { Logo } from '@components/Logo'
import styles from './EnterPage.module.css'

export const EnterPage = () => {
  const navigate = useNavigate()

  return (
    <main className={styles.enterPage}>
      <section className={styles.hero}>
        <Logo className={styles.logo} />

        <p className={styles.subtitle}>
          Добро пожаловать в онлайн-библиотеку — здесь вы можете:
        </p>

        <div className={styles.cards}>
          <article className={styles.card}>
            <h2 className={styles.cardTitle}>Читать книги</h2>
            <p className={styles.cardText}>
              прямо в нашем онлайн-ридере
            </p>
          </article>

          <article className={styles.card}>
            <h2 className={styles.cardTitle}>Скачивать книги</h2>
            <p className={styles.cardText}>
              в форматах pdf, txt и fb2
            </p>
          </article>

          <article className={styles.card}>
            <h2 className={styles.cardTitle}>Огромная библиотека</h2>
            <p className={styles.cardText}>
              тысячи страниц ждут, чтобы вы их прочитали
            </p>
          </article>
        </div>

        <button
          className={styles.button}
          type="button"
          onClick={() => navigate('/signup')}
        >
          НАЧАТЬ ЧТЕНИЕ
        </button>
      </section>
    </main>
  )
}