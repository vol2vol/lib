import { BookList } from '@components/BookList/BookList'
import { books } from '@data/books'

export const HomePage = () => {
  return (
    <main>
      <h1>Каталог книг</h1>
      <BookList books={books} pageSize={10} />
    </main>
  )
}
