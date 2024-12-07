<?php

namespace App\Controllers;

use Streamline\Core\Cache;
use Streamline\Core\Template\Environment;

abstract class Controller
{
  protected Cache $cache;

  public function __construct()
  {
    $this->cache = new Cache(rootPath() . '/cache');
  }

  protected function view(string $templateName, array $templateData = []): string
  {
    $template = Environment::init(rootPath() . '/app/Views', rootPath() . '/app/helpers/template.php');

    return $template->render($templateName, $templateData);
  }
}