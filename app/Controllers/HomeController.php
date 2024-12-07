<?php

namespace App\Controllers;

use App\Models\UserModel;
use Streamline\Routing\Request;
use Streamline\Routing\Response;

class HomeController extends Controller
{
  public function index(Request $request, Response $response, array $args = []): Response
  {
    $userModel = new UserModel();
    $users = $userModel->query("SELECT id, name, email FROM users");

    $response->setBody($this->view('home', [
      'pageTitle' => 'All Users',
      'users' => $users
    ]));

    return $response;
  }
}