<?php

namespace Streamline\Core;

use Exception;
use ReflectionFunction;
use Streamline\Helpers\Logger;
use Streamline\Middlewares\Queue;
use Streamline\Routing\DynamicRouteValidator;
use Streamline\Routing\Request;
use Streamline\Routing\Response;
use Streamline\Routing\Route;
use Streamline\Routing\RouteCollection;
use Streamline\Routing\RouteError;
use Streamline\Routing\RouteRules;
use Streamline\Routing\StaticRouteValidator;

/**
 * Class responsible for managing all application routes and performing 
 * the final processing of the request and response.
 * 
 * @package Streamline\Core
 */
class Router
{
  /**
   * Request instance containing the request information
   * 
   * @var Request
   */
  private Request $request;

  /**
   * Response instance containing all response configurations
   * 
   * @var Response
   */
  private Response $response;

  /**
   * List of arguments that will be passed to the 
   * controller action
   * 
   * @var array
   */
  private array $args = [];

  /**
   * The uri prefix that will be added to 
   * uri when defining route groups
   * 
   * @var string
   */
  private string $uriPrefix = '';

  /**
   * List of middleware added to a route group
   * 
   * @var array
   */
  private array $groupMiddlewares = [];

  /**
   * List of alias for each route
   * 
   * @var array
   */
  private static array $aliasList = [];

  /**
   * Logger instance
   * 
   * @var null|Logger
   */
  private ?Logger $logger = null;

  /**
   * Code for error content
   * 
   * @var int
   */
  private int $errorContentCode;

  /**
   * The error controller namespace
   * 
   * @var int
   */
  private string $errorContentControllerNamespace;

  /**
   * The error handler method
   * 
   * @var string
   */
  private string $errorContentControllerAction;

  /**
   * Method responsible for instantiating the Router 
   * class and initializing its properties
   */
  public function __construct()
  {
    $this->request = new Request();
    $this->response = new Response();
    $this->args = [];

    $this->logger = new Logger(rootPath() . '/logs/route.log');
  }

  /**
   * Method responsible for checking whether a route 
   * with a given name already exists (alias)
   * 
   * @param string $name
   * @return bool
   */
  public static function aliasAlreadyRegistered(string $name): bool
  {
    return in_array($name, self::$aliasList);
  }

  /**
   * Method responsible for adding a route name 
   * to the list of route names (alias)
   * 
   * @param string $name
   * @return void
   */
  public static function addAlias(string $name): void
  {
    self::$aliasList[] = $name;
  }

  /**
   * Method responsible for creating Route instance
   * 
   * @param string $method
   * @param string $uri
   * @param string $handle
   * @throws \Exception
   * @return Route
   */
  private function createRoute(string $method, string $uri, string $handle): ?Route
  {
    [$controllerNamespace, $action] = explode(':', $handle);

    if (!class_exists($controllerNamespace)) {
      throw new Exception("Controller {$controllerNamespace} does not exist", 500);
    }

    if (!method_exists($controllerNamespace, $action)) {
      throw new Exception("The {$action} method does not exist in the {$controllerNamespace} controller", 500);
    }

    $route = new Route($method, $uri, $controllerNamespace, $action, $this->groupMiddlewares);

    return $route;
  }

  /**
   * Method responsible for checking whether all middleware 
   * passed in the middleware list for the route group are valid
   * 
   * @param array $middlewares
   * @return bool|string
   */
  private function groupMiddlewareListIsValid(array $middlewares): bool|string
  {
    foreach ($middlewares as $middlewareNamespace) {
      if (!class_exists($middlewareNamespace)) {
        return $middlewareNamespace;
      }
    }

    return true;
  }

  /**
   * Method responsible for validating whether the function 
   * passed to the route group is the expected function
   * 
   * @param callable $callback
   * @return bool
   */
  private function isValidCallbackInGroup(callable $callback): bool
  {
    $reflection = new ReflectionFunction($callback);
    $parameters = $reflection->getParameters();

    if (count($parameters) !== 1) {
      return false;
    }

    if ($parameters[0]->getType()->getName() !== 'Streamline\Core\Router') {
      return false;
    }

    return $reflection->getReturnType() && $reflection->getReturnType()->getName() === 'void';
  }

  /**
   * Method responsible for defining group 
   * routes based on a prefix for the uri
   * 
   * @param string $uri
   * @param callable $callback
   * @return void
   */
  public function group(string $uri, callable $callback, array $middlewares = []): void
  {
    if (!$this->isValidCallbackInGroup($callback)) {
      throw new Exception("Callback must be a function that accepts Router and returns void", 500);
    }

    $groupMiddlewareListIsValidated = $this->groupMiddlewareListIsValid($middlewares);
    if ($groupMiddlewareListIsValidated !== true) {
      throw new Exception("The {$groupMiddlewareListIsValidated} middleware does not exist. Aded to the list of middlewares in the route group with uri prefix '{$uri}'", 500);
    }

    if (count($middlewares) !== count(array_unique($middlewares))) {
      throw new Exception("The middleware list added to the route group must have middleware added only once each", 500);
    }

    $this->groupMiddlewares = $middlewares;

    $this->uriPrefix = $uri;
    $callback($this);
    $this->uriPrefix = '';
  }

  /**
   * Method responsible for adding a Route instance to the route list
   * 
   * @param string $method
   * @param string $uri
   * @param string $handle
   * @return \Streamline\Routing\Route
   */
  private function add(string $method, string $uri, string $handle): Route
  {
    $route = $this->createRoute($method, $uri, $handle);

    if (DynamicRouteValidator::containsDynamicSegment($uri)) {
      DynamicRouteValidator::validateAndAddRoute($uri, $method, $route);
    } else {
      StaticRouteValidator::validateAndAddRoute($uri, $method, $route);
    }

    return $route;
  }

  /**
   * Method responsible for extracting the uri and handle 
   * from the http function arguments
   * 
   * @param array $args
   * @throws \Exception
   * @return array
   */
  private function getUriAndHandle(array $args): array
  {
    if (!isset($args[0]) || !isset($args[1])) {
      throw new Exception("Two parameters are expected when defining the request method. Ex: \$app->get(string \$uri, string \$handle)", 500);
    }

    $uri = preg_replace('/\s*/', '', $args[0]);
    $uri = $this->uriPrefix . $uri;
    $handle = $args[1];

    return [$uri, $handle];
  }

  /**
   * Method responsible for handling route definition http methods. 
   * Ex: get, post, delete
   * 
   * @param string $method
   * @param array $args
   * @throws \Exception
   * @return Route
   */
  public function __call(string $method, array $args): ?Route
  {
    $method = strtoupper($method);
    if (in_array($method, RouteRules::getEnabledMethods())) {
      [$uri, $handle] = $this->getUriAndHandle($args);

      return $this->add($method, $uri, $handle);
    } else {
      throw new Exception("Method Not Enabled", 400);
    }
  }

  public function setErrorContent(int $errorCode, string $handle): void
  {
    [$errorContentControllerNamespace, $errorContentControllerAction] = explode(':', $handle);

    if (!class_exists($errorContentControllerNamespace)) {
      throw new Exception("Error controller {$errorContentControllerNamespace} does not exist", 500);
    }

    if (!method_exists($errorContentControllerNamespace, $errorContentControllerAction)) {
      throw new Exception("The {$errorContentControllerAction} method does not exist in the {$errorContentControllerNamespace} controller", 500);
    }

    $this->errorContentControllerNamespace = $errorContentControllerNamespace;
    $this->errorContentControllerAction = $errorContentControllerAction;

    $this->errorContentCode = $errorCode;

    RouteError::addErrorRoute($errorCode, $errorContentControllerNamespace, $errorContentControllerAction);
  }

  private function getErrorContent(int $errorCode): Response
  {
    $content = RouteError::getErrorContent($errorCode, $this->request, $this->response, $this->args);
    return $content;
  }

  /**
   * Method responsible for executing the route 
   * system sending a response
   * 
   * @throws \Exception
   * @return void
   */
  public function run(): void
  {
    try {
      $requestUri = $this->request->getUri();
      $requestMethod = $this->request->getMethod();
      $uriKey = StaticRouteValidator::staticRouteAlreadyExists($requestUri, $requestMethod) ? $requestUri : DynamicRouteValidator::uriMatchesWithDynamicRoute($requestUri, $requestMethod);

      if ($uriKey === null) {
        throw new Exception("Route {$requestUri} not found", 404);
      } else if (DynamicRouteValidator::containsDynamicSegment($uriKey)) {
        $args = DynamicRouteValidator::getDynamicRouteArguments($uriKey, $requestUri);
        $route = RouteCollection::getDynamicRoutes($requestMethod)[$uriKey];

        $response = (new Queue($route, $this->request, $this->response, $args, $route->getMiddlewares()))->handle();
      } else {
        $route = RouteCollection::getStaticRoutes($requestMethod)[$uriKey];

        $response = (new Queue($route, $this->request, $this->response, [], $route->getMiddlewares()))->handle();
      }

      $response->send();
    } catch (Exception $exception) {
      $this->logger->error($exception->getMessage());
      $this->getErrorContent($exception->getCode())->send();
    }
  }
}