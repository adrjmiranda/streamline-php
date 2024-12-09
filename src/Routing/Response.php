<?php

namespace Streamline\Routing;

use InvalidArgumentException;
use RuntimeException;

/**
 * 
 * This class is responsible for constructing and sending HTTP responses.
 * It allows setting the status code, headers, body, cookies, and content type of the response.
 * The class also provides functionality for redirecting to a different URI and sending cookies.
 * By default, the response uses the HTTP/1.1 protocol and a 200 OK status code.
 * 
 * @package Streamline\Routing
 */
class Response
{
  /**
   * The HTTP status code of the response.
   * 
   * @var int
   */
  private int $statusCode;

  /**
   * The headers sent in the response.
   * 
   * @var array
   */
  private array $headers = [];

  /**
   * The body of the content sent in the response.
   * 
   * @var mixed
   */
  private mixed $body = '';

  /**
   * Response cookies.
   * 
   * @var array
   */
  private array $cookies = [];

  /**
   * HTTP protocol version of the response.
   * 
   * @var string
   */
  private string $version;

  /**
   * Response content type.
   * 
   * @var string
   */
  private string $contentType;

  /**
   * Constructor for the Response class.
   *
   * This method initializes the `Response` class instance with default values ​​for
   * the HTTP protocol version (HTTP/1.1) and the status code (200 - OK).
   *
   * The protocol version is set to "HTTP/1.1", and the default status code
   * is 200, which indicates success. These settings can be modified later
   * through the class-specific methods.
   *
   * @return void
   */
  public function __construct()
  {
    $this->version = "HTTP/1.1";
    $this->contentType = 'text/html';
    $this->statusCode = 200;
  }

  /**
   * Define the response body
   * 
   * @param mixed $body
   * @return Response
   */
  public function setBody(mixed $body): static
  {
    $this->body = $body;

    return $this;
  }

  /**
   * Method responsible for returning the 
   * content of the response body
   * 
   * @return mixed
   */
  public function getBody(): mixed
  {
    return $this->body;
  }

  /**
   * Adds a value to the cookie list
   * 
   * @param string $name
   * @param string $value
   * @param int $expires
   * @param string $path
   * @param string $domain
   * @param bool $secure
   * @param mixed $httpOnly
   * @return Response
   */
  public function addCookie(string $name, string $value, int $expires, string $path = '/', string $domain = '', bool $secure = false, $httpOnly = true): static
  {
    $this->cookies[$name] = [
      'value' => $value,
      'expires' => $expires,
      'path' => $path,
      'domain' => $domain,
      'secure' => $secure,
      'httpOnly' => $httpOnly
    ];

    setcookie($name, $value, $expires, $path, $domain, $secure, $httpOnly);

    return $this;
  }

  /**
   * Sets the HTTP protocol version of the response.
   * 
   * @param string $version
   * @return Response
   */
  public function setVersion(string $version): static
  {
    $this->version = $version;

    return $this;
  }

  /**
   * Sets the response status code.
   * 
   * @param int $statusCode
   * @return Response
   */
  public function setStatus(int $statusCode): static
  {
    $this->statusCode = $statusCode;

    return $this;
  }

  /**
   * Sets the content type of the response.
   * 
   * @param string $contentType
   * @return Response
   */
  public function setContentType(string $contentType): static
  {
    $this->contentType = $contentType;
    $this->addHeader("Content-Type", $contentType);

    return $this;
  }

  /**
   * Adds an item to the response headers list.
   * 
   * @param string $key
   * @param mixed $value
   * @return void
   */
  private function addHeader(string $key, mixed $value): void
  {
    $this->headers[$key] = $value;
  }

  /**
   * Redirects to a specific route.
   * 
   * @param string $uri
   * @param int $statusCode
   * @return Response
   */
  public function redirect(string $uri, int $statusCode = 302): static
  {
    $this->setStatus($statusCode);
    $this->addHeader('Location', $uri);

    return $this;
  }

  /**
   * Gets the status message corresponding to the status code.
   * 
   * @param int $statusCode
   * @return string
   */
  private function getStatusMessage(int $statusCode): string
  {
    return match ($statusCode) {
      200 => 'OK',
      201 => 'Created',
      202 => 'Accepted',
      204 => 'No Content',
      301 => 'Moved Permanently',
      302 => 'Found',
      304 => 'Not Modified',
      400 => 'Bad Request',
      401 => 'Unauthorized',
      403 => 'Forbidden',
      404 => 'Not Found',
      405 => 'Method Not Allowed',
      500 => 'Internal Server Error',
      501 => 'Not Implemented',
      502 => 'Bad Gateway',
      503 => 'Service Unavailable',
      504 => 'Gateway Timeout',
      default => 'Unknown Status'
    };
  }

  /**
   * Send the response.
   * 
   * @return never
   */
  public function send(): never
  {
    header("HTTP/{$this->version} {$this->statusCode} " . $this->getStatusMessage($this->statusCode));

    foreach ($this->headers as $key => $value) {
      header("$key: $value");
    }

    $bodyContent = match ($this->contentType) {
      'text/html' => $this->body,
      'application/json' => $this->encodeJsonBody($this->body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
      default => throw new InvalidArgumentException("Unsupported content type: {$this->contentType}"),
    };

    echo $bodyContent;
    exit;
  }

  /**
   * Method responsible for returning content in json format after processing
   * 
   * @param mixed $body
   * @param int $options
   * @throws \RuntimeException
   * @return string
   */
  private function encodeJsonBody(mixed $body, int $options = 0): string
  {
    $json = json_encode($body, $options);

    if ($json === false) {
      throw new RuntimeException("Failed to encode JSON: " . json_last_error_msg());
    }

    return $json;
  }
}