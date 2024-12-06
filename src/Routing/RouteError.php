<?php

namespace Streamline\Routing;

use Exception;

class RouteError
{
  private static array $errorRoutes = [];

  public static function addErrorRoute(int $errorCode, string $errorContentControllerNamespace, string $errorContentControllerAction): void
  {
    $errorCodesAlreadyRegistered = array_keys(self::$errorRoutes);

    if (in_array($errorCode, $errorCodesAlreadyRegistered)) {
      throw new Exception("Error content already set to code {$errorCode}", 500);
    }

    self::$errorRoutes[$errorCode] = [
      'controller' => $errorContentControllerNamespace,
      'action' => $errorContentControllerAction
    ];
  }

  public static function getErrorContent(int $errorCode, Request $request, Response $response, array $args = []): Response
  {
    $errorContentData = self::$errorRoutes[$errorCode] ?? '';

    if (!empty($errorContentData)) {
      $controllerNamespace = $errorContentData['controller'];
      $controllerInstance = new $controllerNamespace();
      $action = $errorContentData['action'];

      $response->setStatus($errorCode);
      $response = $controllerInstance->$action($request, $response, $args);
    }

    return $response;
  }
}