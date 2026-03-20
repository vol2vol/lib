import { Routes, Route } from 'react-router-dom'
import { EnterPage } from '@pages/EnterPage'
import { SignUpPage } from '@pages/SignUpPage'
import { SignInPage } from '@pages/SignInPage'
import { HomePage } from '@pages/HomePage'
import { ProfilePage } from '@pages/ProfilePage'
import { GenrePage } from '@pages/GenrePage'
import { BookPage } from '@pages/BookPage'
import { RenderPage } from '@pages/RenderPage'

export const App = () => {
  return (
    <Routes>
      <Route path="/" element={<EnterPage />} />
      <Route path="/signup" element={<SignUpPage />} />
      <Route path="/signin" element={<SignInPage />} />
      <Route path="/library" element={<HomePage />} />
      <Route path="/library/genres/:genreId" element={<GenrePage />} />
      <Route path="/library/books/:bookId" element={<BookPage />} />
      <Route path="/library/read/:fileId" element={<RenderPage />} />
      <Route path="/profile" element={<ProfilePage />} />
    </Routes>
  )
}