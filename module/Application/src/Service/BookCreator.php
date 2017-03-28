<?php

namespace Application\Service;


use Application\Model\Author;
use Application\Model\Book;
use Application\Model\Publisher;

class BookCreator
{
    /**
     * @param string $title
     * @param string $isbn
     * @param Author $author
     * @param Publisher $publisher
     * @return Book
     */
    public function create($title, $isbn, Author $author, Publisher $publisher)
    {
        $book = new Book();
        $book->setTitle($title);
        $book->setISBN($isbn);
        $book->setPublisher($publisher);
        $book->setAuthor($author);
        $book->save();

        return $book;
    }
}