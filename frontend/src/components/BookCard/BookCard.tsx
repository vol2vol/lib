import { Link } from 'react-router-dom'
import type { Book } from '@models/book'


type Props = {
  book: Book
}

export const BookCard = ({ book }: Props) => {
  return (
    <article>
      <h3>{book.title}</h3>
      <p>{book.author}</p>

      <Link to={`/book/${book.id}`}>
        Подробнее
      </Link>
    </article>
  )
}
