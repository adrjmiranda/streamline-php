<?php

namespace Streamline\Routing;

use Exception;

class Route
{
  private string $method;
  private string $uri;
  private string $controllerNamespace;
  private string $action;
  private array $middlewares = [];

  public function __construct(string $method, string $uri, string $controllerNamespace, string $action)
  {
    $this->method = $method;
    $this->uri = $uri;
    $this->controllerNamespace = $controllerNamespace;
    $this->action = $action;
  }

  public function getMethod(): string
  {
    return $this->method;
  }

  public function getUri(): string
  {
    return $this->uri;
  }

  public function getControllerNamespace(): string
  {
    return $this->controllerNamespace;
  }

  public function getAction(): string
  {
    return $this->action;
  }

  public function getMiddlewares(): array
  {
    return $this->middlewares;
  }

  public function addMiddleware(string $middlewareNamespance): static
  {
    if (in_array($middlewareNamespance, $this->middlewares)) {
      throw new Exception("Trying to add existing {$middlewareNamespance} middleware", 500);
    }

    $this->middlewares[] = $middlewareNamespance;

    return $this;
  }
}