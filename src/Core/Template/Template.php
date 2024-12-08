<?php

namespace Streamline\Core\Template;

use Exception;
use Streamline\Helpers\Logger;
use Streamline\Helpers\Utilities;

/**
 * Class responsible for managing the template system
 * 
 * @package Streamline\Core
 */
class Template
{
  /**
   * Base path to view files
   * 
   * @var string
   */
  private string $viewsPath;

  /**
   * Name of the main template that 
   * will be extended
   * 
   * @var string
   */
  private string $masterTemplate = '';

  /**
   * List of data to be passed to 
   * the main template
   * 
   * @var array
   */
  private array $masterData;

  /**
   * Template content
   * 
   * @var null|string
   */
  private null|string $templateContent = null;

  /**
   * List of custom functions that can be 
   * used in the template
   * 
   * @var array
   */
  private array $customFunctions = [];

  /**
   * List of sections called within 
   * the template
   * 
   * @var array
   */
  private array $sections = [];

  /**
   * The name of the current section
   * 
   * @var null|string
   */
  private ?string $currentSection = null;

  /**
   * Logger instance
   * 
   * @var null|Logger
   */
  private ?Logger $logger = null;

  /**
   * Constructor of the class 
   * responsible for managing templates
   * 
   * @param string $viewsPath
   */
  public function __construct(string $viewsPath)
  {
    $this->viewsPath = $viewsPath;
    $this->logger = new Logger(Utilities::rootPath() . '/logs/template.log');
  }

  private function getFunctionContext(): object
  {
    return (object) $this->customFunctions;
  }

  /**
   * Method responsible for adding a function to 
   * the template's list of custom functions
   * 
   * @param array $functions
   * @return void
   */
  public function addFunctions(array $functions): void
  {
    foreach ($functions as $functionName => $functionInstance) {
      $this->customFunctions[$functionName] = $functionInstance;
    }
  }

  /**
   * Method responsible for returning 
   * the template file path if it exists
   * 
   * @param string $templateName
   * @throws \Exception
   * @return string|null
   */
  private function getTemplateFile(string $templateName): ?string
  {
    $templateNamePattern = '/^[a-z._-]+$/';
    if (!preg_match($templateNamePattern, $templateName)) {
      throw new Exception("The name of a template must contain only lowercase letters, underscores, hyphens or periods", 500);
    }

    $templateName = str_replace('.', '/', $templateName);
    $tempaltePath = "{$this->viewsPath}/{$templateName}.php";

    if (!file_exists($tempaltePath)) {
      throw new Exception("Template {$templateName} does not exist", 500);
    }

    return $tempaltePath;
  }

  /**
   * Method responsible for returning the 
   * rendered content of the template
   * 
   * @param string $template
   * @param array $data
   * @return null|string
   */
  public function render(string $template, array $data = []): ?string
  {
    try {
      $templateFile = $this->getTemplateFile($template);

      ob_start();

      $call = $this->getFunctionContext();

      extract($data);

      require_once $templateFile;

      $content = ob_get_contents();

      ob_end_clean();

      if (!empty($this->masterTemplate)) {
        $this->templateContent = $content;
        $allData = array_merge($data, $this->masterData);
        $masterTemplateName = $this->masterTemplate;

        $this->masterTemplate = '';

        return $this->render($masterTemplateName, $allData);
      }

      return $content !== false ? $content : '';
    } catch (Exception $exception) {
      $this->logger->error($exception->getMessage());
      throw new Exception("Error Processing Request", 500);
    }
  }

  /**
   * Method responsible for defining the name 
   * of the main template that will be extended
   * 
   * @param string $masterTemplate
   * @param array $masterData
   * @return void
   */
  public function extends(string $masterTemplate, array $masterData = []): void
  {
    $this->masterTemplate = $masterTemplate;
    $this->masterData = $masterData;
  }

  /**
   * Method responsible for returning the 
   * template content within the main template
   * 
   * @return string
   */
  public function block(): string
  {
    return $this->templateContent ?? '';
  }

  /**
   * Method responsible for including template 
   * snippets within a template
   * 
   * @param string $partialsName
   * @return string
   */
  public function include(string $partialsName): string
  {
    return $this->render($partialsName);
  }

  /**
   * Method responsible for returning the content 
   * of a text with escaped special characters
   * 
   * @param string $content
   * @param int $flags
   * @param mixed $encoding
   * @return string
   */
  public function escape(string $content, int $flags = ENT_QUOTES, $encoding = 'UTF-8'): string
  {
    return htmlspecialchars($content, $flags, $encoding);
  }

  /**
   * Method responsible for starting a 
   * section block within the template
   * 
   * @param string $name
   * @throws \Exception
   * @return void
   */
  public function ssection(string $name): void
  {
    if (!empty($this->currentSection)) {
      throw new Exception("A section is already being started: {$this->currentSection}", 500);
    }
    $this->currentSection = $name;
    ob_start();
  }

  /**
   * Method responsible for finalizing the 
   * block of a section within the template
   * 
   * @throws \Exception
   * @return void
   */
  public function esection(): void
  {
    if (empty($this->currentSection)) {
      throw new Exception("No section is currently being started", 500);
    }

    $content = ob_get_clean();
    $this->sections[$this->currentSection] = $content;
    $this->currentSection = null;
  }

  /**
   * Method responsible for returning 
   * the content of a section
   * 
   * @param string $name
   * @param string $default
   * @return string
   */
  public function section(string $name, string $default = ''): string
  {
    return $this->sections[$name] ?? $default;
  }
}