import { Routes, Route } from 'react-router-dom'
import { EnterPage } from '@pages/EnterPage'
import { SignUpPage } from '@pages/SignUpPage'
import { SignInPage } from '@pages/SignInPage'
import { HomePage } from '@pages/HomePage'
import { ProfilePage } from '@pages/ProfilePage'
import { GenrePage } from '@pages/GenrePage'

export const App = () => {
  return (
    <Routes>
      <Route path="/" element={<EnterPage />} />
      <Route path="/signup" element={<SignUpPage />} />
      <Route path="/signin" element={<SignInPage />} />
      <Route path="/library" element={<HomePage />} />
      <Route path="/profile" element={<ProfilePage />} />
      <Route path="/library/genres/:genreId" element={<GenrePage />} />
    </Routes>
  )
}