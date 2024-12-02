<?php

namespace Src\Routing;


/**
 * 
 * Responsible for encapsulating the details of an HTTP request received by the server, 
 * providing a clean and organized interface to access the request's relevant information.
 *
 * @package Src\Routing
 */
class Request
{
  /**
   * The HTTP method of the request (e.g., GET, POST).
   */
  private string $method;

  /**
   * The requested URI.
   */
  private string $uri;

  /**
   * The headers sent with the request.
   */
  private array $headers = [];

  /**
   * The query parameters from the URL.
   */
  private array $query = [];

  /**
   * The body of the request (POST data).
   */
  private array $body = [];

  /**
   * The cookies sent with the request.
   */
  private array $cookies = [];

  /**
   * The files sent in the request.
   */
  private array $files = [];

  /**
   * The server variables for the request.
   */
  private array $server = [];

  /**
   * The client's IP address.
   */
  private string $ip;

  /**
   * Custom attributes for storing additional data.
   */
  private array $attributes = [];

  /**
   * Whether the request is an Ajax request.
   */
  private bool $isAjax;

  /**
   * Whether the request contains JSON data.
   */
  private bool $isJson;

  /**
   * Constructor for the Request class.
   *
   * Initializes the parameters of the HTTP request object based on PHP superglobals and other relevant information.
   * This includes the HTTP method, URI, headers, query parameters, request body, cookies, files,
   * server data, client IP, and information about the type of request (whether it is AJAX or JSON).
   *
   * The properties are initialized as follows:
   * - **method**: Defines the HTTP method of the request (GET, POST, etc.) using `$_SERVER['REQUEST_METHOD']`.
   * - **uri**: Extracts the path from the request URI using `$_SERVER['REQUEST_URI']` and `parse_url`.
   * - **headers**: Calls the `parseHeaders()` method to process the request headers from `$_SERVER`. * - **query**: Accesses the query string parameters via `$_GET`.
   * - **body**: Accesses the request body parameters via `$_POST`.
   * - **cookies**: Accesses the cookies sent in the request via `$_COOKIE`.
   * - **files**: Accesses the files sent via forms with `$_FILES`.
   * - **server**: Contains information about the request server accessed via `$_SERVER`.
   * - **ip**: Obtains the client's IP address via the `getClientIp()` method.
   * - **isAjax**: Checks if the request is an AJAX request using the `checkIsAjax()` method.
   * - **isJson**: Checks if the request content type is JSON using the `checkIsJson()` method.
   *
   * If an expected value is not available in the superglobals or server variables, default values ​​are used. 
   * 
   */

  public function __construct()
  {
    $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
    $this->headers = $this->parseHeaders();
    $this->query = $_GET ?? [];
    $this->body = $_POST ?? [];
    $this->cookies = $_COOKIE ?? [];
    $this->files = $_FILES ?? [];
    $this->server = $_SERVER ?? [];
    $this->ip = $this->getClientIp();
    $this->isAjax = $this->checkIsAjax();
    $this->isJson = $this->checkIsJson();
  }

  /**
   * Fills the list of header items from within the global variable $_SERVER.
   * 
   * @return array
   */
  private function parseHeaders(): array
  {
    $headers = [];
    foreach ($_SERVER as $key => $value) {
      if (preg_match('/^HTTP_(.+)$/', $key, $matches)) {
        $headers[str_replace('_', '-', $matches[1])] = $value;
      }
    }
    return $headers;
  }

  /**
   * Returns the client IP.
   * 
   * @return string
   */
  private function getClientIp(): string
  {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      return $_SERVER['HTTP_CLIENT_IP'];
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    }

    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
  }


  /**
   * Checks if the request is of the ajax type.
   * 
   * @return bool
   */
  private function checkIsAjax(): bool
  {
    return isset($this->headers['X-Requested-With']) && strtolower($this->headers['X-Requested-With']) === 'xmlhttprequest';
  }

  /**
   * Checks if the request is of type json.
   * 
   * @return bool
   */
  private function checkIsJson(): bool
  {
    return isset($this->headers['Content-Type']) && strpos($this->headers['Content-Type'], 'application/json') === 0;
  }

  /**
   * Returns the HTTP method used in the request.
   * 
   * @return string
   */
  public function getMethod(): string
  {
    return $this->method;
  }

  /**
   * Returns the request uri.
   * 
   * @return string
   */
  public function getUri(): string
  {
    return $this->uri;
  }

  /**
   * Returns the request headers.
   * 
   * @return array
   */
  public function getHeaders(): array
  {
    return $this->headers;
  }

  /**
   * Returns an array containing the parameters passed 
   * in the query string.
   * 
   * @return array
   */
  public function getQuery(): array
  {
    return $this->query;
  }

  /**
   * Returns the list of parameters passed in the request body.
   * 
   * @return array
   */
  public function getBody(): array
  {
    return $this->body;
  }

  /**
   * Returns the cookies passed in the request.
   * 
   * @return array
   */
  public function getCookies(): array
  {
    return $this->cookies;
  }

  /**
   * Returns the list of file items passed in the request.
   * 
   * @return array
   */
  public function getFiles(): array
  {
    return $this->files;
  }

  /**
   * Returns the contents of the super global server.
   * 
   * @return array
   */
  public function getServer(): array
  {
    return $this->server;
  }

  /**
   * Returns the client IP of the request.
   * @return string
   */
  public function getIp(): string
  {
    return $this->ip;
  }

  /**
   * Returns a specific file passed in the request.
   * 
   * @param string $name
   * @return array
   */
  public function file(string $name): array
  {
    return $this->files[$name] ?? [];
  }

  /**
   * Method responsible for extracting only specific parameters from a given dataset. 
   * It can handle either a single parameter key or a set of keys. If a field or key 
   * is not present, the method returns null for that parameter.
   * 
   * @param string|array $fields
   * @param array $data
   * @return mixed
   */
  private function getOnlyParameters(string|array $fields, array $data): mixed
  {
    $parameters = [];
    if (is_string($fields)) {
      $parameters = $data[$fields] ?? null;
    } else {
      foreach ($fields as $name) {
        $parameters[$name] = $data[$name] ?? null;
      }
    }

    return $parameters;
  }

  /**
   * Method responsible for returning data from a set, excluding the specific parameters 
   * provided. It can remove a single parameter or multiple parameters, depending on how 
   * the $fields parameter is provided (string or array).
   * 
   * @param string|array $fields
   * @param array $data
   * @return mixed
   */
  private function getExceptParameters(string|array $fields, array $data): mixed
  {
    $parameters = $data;
    if (is_string($fields)) {
      unset($parameters[$fields]);
    } else {
      foreach ($fields as $name) {
        unset($parameters[$name]);
      }
    }

    return $parameters;
  }

  /**
   * Method responsible for extracting specific parameters from the request body ($_POST), 
   * using the getOnlyParameters method. It allows you to select one or more parameters from 
   * the request body.
   * 
   * @param string|array $fields
   * @return mixed
   */
  public function getOnlyBodyParameters(string|array $fields): mixed
  {
    return $this->getOnlyParameters($fields, $this->body);
  }

  /**
   * Method responsible for extracting specific parameters from the request query ($_GET), 
   * using the getOnlyParameters method. It allows you to select one or more parameters from 
   * the request query string.
   * 
   * @param string|array $fields
   * @return mixed
   */
  public function getOnlyQueryParameters(string|array $fields): mixed
  {
    return $this->getOnlyParameters($fields, $this->query);
  }

  /**
   * Method responsible for excluding specific parameters from the request body ($_POST), 
   * using the getExceptParameters method. It can remove one or more parameters from the 
   * request body.
   * 
   * @param string|array $fields
   * @return mixed
   */
  public function getExceptBodyParameters(string|array $fields): mixed
  {
    return $this->getExceptParameters($fields, $this->body);
  }

  /**
   * Method responsible for excluding specific parameters from the request query string ($_GET), 
   * using the getExceptParameters method. It can remove one or more parameters from the 
   * request query string.
   * 
   * @param string|array $fields
   * @return mixed
   */
  public function getExceptQueryParameters(string|array $fields): mixed
  {
    return $this->getExceptParameters($fields, $this->query);
  }
}