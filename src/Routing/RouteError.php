<?php

namespace Streamline\Routing;

use Exception;

/**
 * Class responsible for managing error content in the route system
 * 
 * @package Streamline\Routing
 */
class RouteError
{
  /**
   * List of data for handling error routes
   * 
   * @var array
   */
  private static array $errorRoutes = [];

  /**
   * Method responsible for adding a new handler 
   * to the list of error code handlers
   * 
   * @param int $errorCode
   * @param string $errorContentControllerNamespace
   * @param string $errorContentControllerAction
   * @throws \Exception
   * @return void
   */
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

  /**
   * Method responsible for returning the 
   * response content based on the error code
   * 
   * @param int $errorCode
   * @param \Streamline\Routing\Request $request
   * @param \Streamline\Routing\Response $response
   * @param array $args
   * @return \Streamline\Routing\Response
   */
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