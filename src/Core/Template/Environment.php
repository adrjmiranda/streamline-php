<?php

namespace Streamline\Core\Template;

/**
 * Class responsible for initializing the
 *  template system environment
 * 
 * @package Streamline\Core\Template
 */
class Environment
{
  /**
   * Method responsible for initializing the template environment 
   * and returning an instance of the template class
   * 
   * @param string $viewsPath
   * @param mixed $functionsFilePath
   * @return \Streamline\Core\Template\Template
   */
  public static function init(string $viewsPath, ?string $functionsFilePath = null): Template
  {
    $template = new Template($viewsPath);
    $functionsData = $functionsFilePath !== null ? require $functionsFilePath : [];

    $template->addFunctions($functionsData);

    return $template;
  }
}