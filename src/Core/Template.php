<?php

namespace Streamline\Core;

use Exception;

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
   * Constructor of the class 
   * responsible for managing templates
   * 
   * @param string $viewsPath
   */
  public function __construct(string $viewsPath)
  {
    $this->viewsPath = $viewsPath;
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
   * @return string
   */
  public function render(string $template, array $data = []): string
  {
    $templateFile = $this->getTemplateFile($template);

    ob_start();

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
  }

  /**
   * Method responsible for defining the name 
   * of the main template that will be extended
   * 
   * @param string $masterTemplate
   * @param array $masterData
   * @return void
   */
  private function extends(string $masterTemplate, array $masterData = []): void
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
  private function block(): string
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
  private function include(string $partialsName): string
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
  private function escape(string $content, int $flags = ENT_QUOTES, $encoding = 'UTF-8'): string
  {
    return htmlspecialchars($content, $flags, $encoding);
  }
}