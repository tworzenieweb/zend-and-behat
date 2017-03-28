Feature: Creation of books
  As a book company i should be able to manage book store

  Scenario: Creating book with given author and publisher
    Given There is a book author "John Doe"
    And There is a book publisher "ORelly"
    When I create a new book "DDD in PHP" with isbn "12345" for given author and publisher
    Then It should be in the database