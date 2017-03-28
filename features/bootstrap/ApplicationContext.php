<?php

use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\ApplicationInterface;
use Zend\Stdlib\Parameters;

/**
 * Defines application features from the specific context.
 */
class ApplicationContext
    implements Context, \Alteris\BehatZendframeworkExtension\Context\ContextAwareInterface
{
    /** @var ApplicationInterface */
    private $application;

    /**
     * @When /^I visit homepage$/
     * @When /^I visit "([^"]+)"$/
     * @param string $routeName
     */
    public function iVisit($routeName = 'home')
    {
        $this->dispatch($this->urlHelper($routeName));
    }



    /**
     * @Then It should contain :needle
     * @param $needle
     */
    public function itShouldContain($needle)
    {
        Assert::assertContains($needle, $this->application->getResponse()->getContent());
    }

    /**
     * @param ApplicationInterface $application
     * @return void
     */
    public function setApplication(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * Set the request URL
     *
     * @param  string $url
     * @param  string|null $method
     * @param  array|null $params
     */
    public function url($url, $method = Request::METHOD_GET, $params = [])
    {
        $request = $this->application->getRequest();

        $query = $request->getQuery()->toArray();
        $post = $request->getPost()->toArray();
        $uri = new \Zend\Uri\Http($url);
        $queryString = $uri->getQuery();
        if ($queryString) {
            parse_str($queryString, $query);
        }
        if ($method == Request::METHOD_POST) {
            if (count($params) != 0) {
                $post = $params;
            }
        } elseif ($method == Request::METHOD_GET) {
            $query = array_merge($query, $params);
        } elseif ($method == Request::METHOD_PUT || $method == Request::METHOD_PATCH) {
            if (count($params) != 0) {
                $content = http_build_query($params);
                $request->setContent($content);
            }
        } elseif ($params) {
            trigger_error(
                'Additional params is only supported by GET, POST, PUT and PATCH HTTP method',
                E_USER_NOTICE
            );
        }
        $request->setMethod($method);
        $request->setQuery(new Parameters($query));
        $request->setPost(new Parameters($post));
        $request->setUri($uri);
        $request->setRequestUri($uri->getPath());
    }

    /**
     * Dispatch the MVC with a URL
     * Accept a HTTP (simulate a customer action) or console route.
     *
     * The URL provided set the request URI in the request object.
     *
     * @param  string $url
     * @param  string|null $method
     * @param  array|null $params
     * @throws \Exception
     */
    public function dispatch($url, $method = null, $params = [], $isXmlHttpRequest = false)
    {
        if (!isset($method)
            && $this->application->getRequest() instanceof Request
            && $requestMethod = $this->application->getRequest()->getMethod()
        ) {
            $method = $requestMethod;
        } elseif (!isset($method)) {
            $method = Request::METHOD_GET;
        }
        if ($isXmlHttpRequest) {
            $headers = $this->application->getRequest()->getHeaders();
            $headers->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        }
        $this->url($url, $method, $params);

        ob_start();
        $this->application->run();
        ob_clean();
    }

    /**
     * @param string $routingName
     * @return string
     */
    private function urlHelper($routingName)
    {
        /** @var \Zend\Mvc\Controller\PluginManager $manager */
        $manager = $this->application->getServiceManager()->get('ViewHelperManager');

        /** @var \Zend\View\Helper\Url $url */
        $url = $manager->get('url');

        return $url($routingName);
    }
}

;