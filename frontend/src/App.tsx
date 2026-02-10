import { Routes, Route, Outlet } from 'react-router-dom'
import { Header } from '@components/Header/Header'
import { HomePage } from '@pages/HomePage'
// import { BookPage } from '@pages/BookPage/BookPage'
// import { ProfilePage } from '@pages/ProfilePage/ProfilePage'
// import { LoginPage } from '@pages/LoginPage/LoginPage'

const Layout = () => {
  return (
    <>
      <Header />
      <Outlet />
    </>
  )
}

export const App = () => {
  return (
    <Routes>
      <Route element={<Layout />}>
        <Route path="/" element={<HomePage />} />
        {/* <Route path="/book/:id" element={<BookPage />} />
        <Route path="/profile" element={<ProfilePage />} />
        <Route path="/login" element={<LoginPage />} /> */}
      </Route>
    </Routes>
  )
}
