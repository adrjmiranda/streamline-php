<?php

namespace Streamline\Core;

use Exception;

class Template
{
  private string $viewsPath;
  private string $masterTemplate = '';
  private array $masterData;
  private null|string $templateContent = null;

  public function __construct(string $viewsPath)
  {
    $this->viewsPath = $viewsPath;
  }

  private function getTemplateFile(string $templateName): string
  {
    $tempaltePath = "{$this->viewsPath}/{$templateName}.php";

    if (!file_exists($tempaltePath)) {
      throw new Exception("Template {$templateName} does not exist", 500);
    }

    return $tempaltePath;
  }

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

  private function extends(string $masterTemplate, array $masterData = []): void
  {
    $this->masterTemplate = $masterTemplate;
    $this->masterData = $masterData;
  }

  private function block(): string
  {
    return $this->templateContent ?? '';
  }
}