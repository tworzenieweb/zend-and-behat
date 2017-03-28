<?php
use Application\Model\Author;
use Application\Model\Book;
use Application\Model\BookQuery;
use Application\Model\Publisher;
use Application\Service\BookCreator;
use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;


class PropelContext implements Context, \Alteris\BehatZendframeworkExtension\Context\ContextAwareInterface
{
    /** @var Publisher */
    private $publisher;

    /** @var Author */
    private $author;

    /** @var string */
    private $isbn;

    /** @var Book */
    private $book;

    /** @var \Zend\Mvc\ApplicationInterface */
    private $application;

    public function __construct()
    {
        Propel::init($this->getConfig());
        Propel::getConnection()->exec(file_get_contents(__DIR__ . '/propel/schema.sql'));
    }

    private function getConfig()
    {
        return __DIR__ . '/propel/config.php';
    }

    /**
     * @Given There is a book author :author
     * @param string $author
     */
    public function thereIsABookAuthor($author)
    {
        list($firstName, $lastName) = explode(' ', $author);

        $author = new Author();
        $author->setFirstName($firstName);
        $author->setLastName($lastName);
        $author->save();

        $this->author = $author;
    }

    /**
     * @Given There is a book publisher :publisher
     * @param $publisherName
     */
    public function thereIsABookPublisher($publisherName)
    {
        $publisher = new Publisher();
        $publisher->setName($publisherName);
        $publisher->save();

        $this->publisher = $publisher;
    }

    /**
     * @When I create a new book :title with isbn :isbn for given author and publisher
     * @param $title
     * @param $isbn
     */
    public function iCreateANewBookForGivenAuthorAndPublisher($title, $isbn)
    {
        $bookCreator = $this->application->getServiceManager()->get(BookCreator::class);

        $this->book = $bookCreator->create($title, $isbn, $this->author, $this->publisher);
        $this->isbn = $isbn;
    }

    /**
     * @Then It should be in the database
     */
    public function itShouldBeInTheDatabase()
    {
        $book = BookQuery::create()->findOneByISBN($this->isbn);
        Assert::assertEquals($book->getId(), $this->book->getId());
    }

    /**
     * @param \Zend\Mvc\ApplicationInterface $application
     * @return void
     */
    public function setApplication(\Zend\Mvc\ApplicationInterface $application)
    {
        $this->application = $application;
    }
}
