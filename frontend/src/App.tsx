import { Routes, Route, Outlet } from 'react-router-dom'
import { Header } from '@components/Header/Header'
import { HomePage } from '@pages/HomePage'
import { EnterPage } from '@pages/EnterPage'

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

      <Route element={<Layout />}>
        <Route path="/library" element={<HomePage />} />
      </Route>
    </Routes>
  )
}