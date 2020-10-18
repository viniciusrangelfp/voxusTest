<?php

namespace Voxus\Core\routing;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class Router
{
    /**
     * @var array array of all routes
     */
    protected array $routes;

    /**
     * @var string
     */
    private string $serverBasePath = '';

    /**
     * @var array|string[] Default matches to the route
     */
    protected array $matches = [
        'i'  => '[0-9]++',
        'a'  => '[0-9A-Za-z]++',
        'h'  => '[0-9A-Fa-f]++',
        '*'  => '.+?',
        '**' => '.++',
        ''   => '[^/\.]++'
    ];


    /**
     * Get the request method used, taking overrides into account.
     *
     * @return string The Request method to handle
     */
    private function getRequestMethod()
    {
        // Take the method as found in $_SERVER
        $method = $_SERVER['REQUEST_METHOD'];

        // If it's a HEAD request override it to being GET and prevent any output, as per HTTP Specification
        // @url http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.4
        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_start();
            $method = 'GET';
        }

        // If it's a POST request, check for a method override header
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $headers = $this->getRequestHeaders();
            if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], ['PUT', 'DELETE', 'PATCH'])) {
                $method = $headers['X-HTTP-Method-Override'];
            }
        }

        return $method;
    }

    private function match($methods,$pattern,$route){
        $pattern = $this->baseRoute . '/' . trim($pattern, '/');
        $pattern = $this->baseRoute ? rtrim($pattern, '/') : $pattern;

        $route->setUrl($pattern);

        foreach (explode('|', $methods) as $method) {
            $this->addRoute($method,$route);
        }
    }

    /**
     * @param string $route
     * @param string $controller
     * @param string $method
     */
    public function get(string $route, string $controller, string $method){
        $routeObj = new Route($route,$controller,$method);
        $this->match('GET',$route,$routeObj);
    }

    /**
     * @param string $route
     * @param string $controller
     * @param string $method
     */
    public function post(string $route, string $controller, string $method){
        $routeObj = new Route($route,$controller,$method);
        $this->match('POST',$route,$routeObj);
    }

    /**
     * @param string $route
     * @param string $controller
     * @param string $method
     */
    public function put(string $route, string $controller, string $method){
        $routeObj = new Route($route,$controller,$method);
        $this->match('PUT',$route,$routeObj);
    }

    /**
     * @param string $method
     * @param Route  $route
     */
    private function addRoute(string $method, Route $route){
        $this->routes[$method][] = $route;
    }

    public function register()
    {
        $request = $this->getRequestMethod();

        // Handle all routes
        $numHandled = 0;
        if (isset($this->routes[$request])) {
            $numHandled = $this->handle($this->routes[$request], true);
        }

        // If no route was handled, trigger the 404 (if any)
        if($numHandled === 0){
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
        }

        // If it originally was a HEAD request, clean up after ourselves by emptying the output buffer
        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_end_clean();
        }

        return $numHandled !== 0;
    }

    /**
     * Handle a set of routes if a match it is found.
     *
     * @param      $routes
     * @param bool $quitAfterRun
     */
    private function handle($routes, $quitAfterRun=false){
        // Counter to keep track of the number of routes we've handled
        $numHandled = 0;

        $uri = $this->getCurrentUri();

        foreach($routes as $route){
            //replace braches matches
            $route->setUrl(preg_replace('/\/{(.*?)}/', '/(.*?)', $route->getUrl()));

            if(preg_match_all('#^' . $route->getUrl() . '$#', $uri, $matches, PREG_OFFSET_CAPTURE)){
                // Rework matches to only contain the matches, not the orig string
                $matches = array_slice($matches, 1);

                // Extract the matched URL parameters (and only the parameters)
                $params = array_map(function ($match, $index) use ($matches) {
                    //  take the substring from the current param position until the next one's position
                    if (isset($matches[$index + 1]) && isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
                        return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
                    } // We have no following parameters: return the whole lot

                    return isset($match[0][0]) ? trim($match[0][0], '/') : null;
                }, $matches, array_keys($matches));

                $request = Request::createFromGlobals();
                $response = new JsonResponse();
                $newResponse = call_user_func([$route->getController(),$route->getMethod()],$params,$request,$response);
                if($newResponse){
                    $newResponse->send();
                }

                ++$numHandled;

                // If we need to quit, then quit
                if ($quitAfterRun) {
                    break;
                }
            }
        }


        // Return the number of routes handled
        return $numHandled;
    }

    public function getRequestHeaders()
    {
        $headers = [];

        // If getallheaders() is available, use that
        if (function_exists('getallheaders')) {
            $headers = getallheaders();

            // getallheaders() can return false if something went wrong
            if ($headers !== false) {
                return $headers;
            }
        }

        // Method getallheaders() not available or went wrong: manually extract 'm
        foreach ($_SERVER as $name => $value) {
            if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
                $headers[str_replace([' ', 'Http'], ['-', 'HTTP'], ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }

    /**
     * Return server base Path, and define it if isn't defined.
     *
     * @return string
     */
    public function getBasePath()
    {
        // Check if server base path is defined, if not define it.
        if ($this->serverBasePath === null) {
            $this->serverBasePath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        }

        return $this->serverBasePath;
    }

    /**
     * Define the current relative URI.
     *
     * @return string
     */
    public function getCurrentUri()
    {
        // Get the current Request URI and remove rewrite base path from it (= allows one to run the router in a sub folder)
        $uri = substr(rawurldecode($_SERVER['REQUEST_URI']), strlen($this->getBasePath()));

        // Don't take query params into account on the URL
        if (strstr($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        // Remove trailing slash + enforce a slash at the start
        return '/' . trim($uri, '/');
    }
}
