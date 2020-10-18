<?php

namespace Voxus\core\routing;

use mysql_xdevapi\Exception;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * Class route
 *
 * @package Voxus\Kernel\routing Define a route unity
 */
class Route
{

    /**
     * @var string
     */
    private string $url;

    /**
     * @var String Any ClassName that extend a controller
     */
    private string $controller;

    /**
     * @var string a method callback from your controller
     */
    private string $method;

    /**
     * @var bool Check if the route object is valid
     */
    private bool $validRoute = false;

    public function __construct(string $url, string $controller, string $method)
    {
        $this->url = $url;
        $this->controller = $controller;
        $this->method = $method;

        $this->verifyClass();
    }

    /**
     * @return string get current url to your route
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string get current url to your route
     */
    public function setURL(string $url)
    {
       $this->url = $url;
    }

    /**
     * @return string get the current controller to the route
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return string get the current method to the controller in the route
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return bool return a statement if the controller is valid
     */
    public function isValidRoute()
    {
        return $this->validRoute;
    }

    /**
     * verify the controller to the route
     *
     * @throws \Exception
     */
    protected function verifyClass()
    {
        $namespace = 'Voxus\App\controller';
        if(!class_exists($this->controller)){
            throw new \Exception('Controller doest not exist');
        }
        $reflection = new \ReflectionClass($this->controller);


        $parent = $reflection->getParentClass();
        if (!$parent || $parent->getName() !== $namespace.'\Controller') {
            throw new \Exception('This object it is not a instance of controller');
        }

        if (!$reflection->hasMethod($this->method)) {
            throw new \Exception('Invalid Method');
        }

        $this->validRoute = true;

    }
}
