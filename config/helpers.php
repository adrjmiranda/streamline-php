<?php

function rootPath(): string
{
  return dirname(dirname(__FILE__));
}

function dd(mixed ...$values): never
{
  foreach ($values as $key => $value) {
    echo '<pre>';
    print_r($value);
    echo '</pre>';
  }

  exit;
}
;
