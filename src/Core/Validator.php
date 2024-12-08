<?php

namespace Streamline\Core;

use Exception;
use Streamline\Helpers\Validations;
use Streamline\Routing\Request;

/**
 * Method responsible for managing pre-defined validations
 * 
 * @package Streamline\Core
 */
class Validator
{
  use Validations;

  /**
   * Validation rules separator character
   * 
   * @var string
   */
  private const RULE_SEPARATOR = '|';

  /**
   * Parameter separator character
   * 
   * @var string
   */
  private const PARAMETER_SEPARATOR = ':';

  /**
   * Request class instance
   * 
   * @var Request
   */
  private Request $request;

  /**
   * List of data after validation
   * 
   * @var array
   */
  private array $processedData = [];

  /**
   * Method responsible for instantiating the validation class
   * 
   * @param \Streamline\Routing\Request $request
   */
  public function __construct(Request $request)
  {
    $this->request = $request;
  }

  /**
   * Method responsible for returning the name of the method 
   * and the list of parameters defined in the validation rules 
   * placeholder
   * 
   * @param string $rule
   * @return array
   */
  private function methodAndParams(string $rule): array
  {
    $items = array_values(array_filter(explode(self::PARAMETER_SEPARATOR, $rule)));

    $method = $items[0] ?? '';
    $params = array_slice($items, 1);

    return [$method, $params];
  }

  /**
   * Method responsible for performing validations for each passed rule
   * 
   * @param array $rules
   * @throws \Exception
   * @return array
   */
  public function validations(array $rules): array
  {
    foreach ($rules as $field => $placeholders) {
      $listOfRules = array_values(array_filter(explode(self::RULE_SEPARATOR, $placeholders)));

      if (empty($listOfRules)) {
        throw new Exception("Placeholders not defined for field '{$field}'", 500);
      }

      foreach ($listOfRules as $rule) {
        [$method, $params] = $this->methodAndParams($rule);

        if (!method_exists(self::class, $method)) {
          throw new Exception("Validation method '{$method}' does not exist", 500);
        }

        $result = $this->$method($field, $params);

        if ($result === false) {
          break;
        }
      }
    }

    return $this->processedData;
  }

  /**
   * Method responsible for sanitizing a text field
   * 
   * @param string $var
   * @return string
   */
  private function processedVar(string $var): string
  {
    return htmlspecialchars(filter_var(strip_tags($var), FILTER_SANITIZE_STRING), ENT_QUOTES, 'UTF-8');
  }
}