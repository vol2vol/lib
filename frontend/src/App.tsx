import { Routes, Route, Outlet } from 'react-router-dom'
import { Header } from '@components/Header/Header'
import { HomePage } from '@pages/HomePage'
import { EnterPage } from '@pages/EnterPage'
import { SignUpPage } from '@pages/SignUpPage'
import { SignInPage } from '@pages/SignInPage'

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
      <Route path="/" element={<EnterPage />} />
      <Route path="/signup" element={<SignUpPage />} />
      <Route path="/signin" element={<SignInPage />} />

      <Route element={<Layout />}>
        <Route path="/library" element={<HomePage />} />
      </Route>
    </Routes>
  )
}