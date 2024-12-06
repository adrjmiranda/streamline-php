<?php

namespace App\Models;

use Streamline\Core\Model;

class UserModel extends Model
{
  protected function getTableName(): string
  {
    return 'users';
  }
}