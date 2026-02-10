import { Link } from 'react-router-dom'

export const Header = () => {
  return (
    <header>
      <Link to="/">Электронная библиотека</Link>

      <nav>
        <Link to="/profile">Профиль</Link>
      </nav>
    </header>
  )
}
