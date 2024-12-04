<?php

namespace Streamline\Middlewares;

use Streamline\Routing\Route;
use Streamline\Routing\Request;
use Streamline\Routing\Response;

/**
 * Class responsible for managing the middleware 
 * queues for each route in the system
 * 
 * @package Streamline\Middlewares
 */
class Queue
{
  /**
   * List of middleware to be 
   * executed on the route
   * 
   * @var array
   */
  private array $middlewares;

  private Route $route;
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
   * Method responsible for starting an instance of the class 
   * that manages the middleware queue of a route
   * 
   * @param \Streamline\Routing\Route $route
   * @param \Streamline\Routing\Request $request
   * @param \Streamline\Routing\Response $response
   * @param array $args
   * @param array $middlewares
   */
  public function __construct(Route $route, Request $request, Response $response, array $args, array $middlewares)
  {
    $this->route = $route;
    $this->request = $request;
    $this->response = $response;
    $this->args = $args;

    $this->middlewares = $middlewares;
  }

  /**
   * Method responsible for executing the middleware queue
   * 
   * @return mixed
   */
  public function handle(): mixed
  {
    $runner = array_reduce(array_reverse($this->middlewares), function ($next, $middleware) {
      return function ($request) use ($next, $middleware): mixed {
        return (new $middleware())->handle($request, $next);
      };
    }, function ($request) {
      return $this->route->executeControllerAction($this->request, $this->response, $this->args);
    });

    return $runner($this->request);
  }
}
