Feature: Homepage
  As a user
  I should be able to access homepage
  To determine that application is working

  Scenario: Accessing homepage
    When I visit homepage
    Then It should contain '<h1>Welcome to <span class="zf-green">Zend Framework</span></h1>'


  Scenario: Accessing by routing name
    When I visit "home"
    Then It should contain '<h1>Welcome to <span class="zf-green">Zend Framework</span></h1>'
    