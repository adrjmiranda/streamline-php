<?php

namespace App\Controllers;

use App\Models\UserModel;
use Streamline\Core\SessionManager;
use Streamline\Routing\Request;
use Streamline\Routing\Response;

class UserController extends Controller
{
  public function register(Request $request, Response $response, array $args = []): Response
  {
    $response->setBody($this->view('register', [
      'pageTitle' => 'Register Now'
    ]));

    return $response;
  }

  public function store(Request $request, Response $response, array $args = []): Response
  {
    $name = $request->getOnlyBodyParameters('name');
    $email = $request->getOnlyBodyParameters('email');
    $password = $request->getOnlyBodyParameters('password');

    $userModel = new UserModel();
    if (
      $userModel->create([
        'name' => $name,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT)
      ])
    ) {
      dd('User created successfully!');
    } else {
      dd('Error');
    }

    return $response;
  }

  public function edit(Request $request, Response $response, array $args = []): Response
  {
    $id = $args['id'];
    $userModel = new UserModel();

    $userData = $userModel->find((int) $id);

    $response->setBody($this->view('edit', [
      'pageTitle' => 'Edit User',
      'user' => $userData
    ]));

    return $response;
  }

  public function update(Request $request, Response $response, array $args = []): Response
  {
    $id = $args['id'];
    $userModel = new UserModel();

    $userData = $userModel->find((int) $id);

    $name = $request->getOnlyBodyParameters('name');
    $email = $request->getOnlyBodyParameters('email');
    $password = $request->getOnlyBodyParameters('password');

    if (
      $userModel->update((int) $id, [
        'name' => $name,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT)
      ])
    ) {
      dd('User updated successfully!');
    } else {
      dd('Error trying to update user!');
    }

    return $response;
  }

  public function show(Request $request, Response $response, array $args = []): Response
  {
    $id = $args['id'];
    $userModel = new UserModel();

    $userData = $userModel->find((int) $id);

    $response->setBody($this->view('user', [
      'pageTitle' => 'User Data',
      'user' => $userData
    ]));

    return $response;
  }

  public function delete(Request $request, Response $response, array $args = []): Response
  {
    $id = (int) $args['id'];
    $userModel = new UserModel();

    if ($userModel->delete($id)) {
      dd('User removed successfully!');
    } else {
      dd('Error');
    }

    return $response;
  }

  public function login(Request $request, Response $response, array $args = []): Response
  {
    $response->setBody($this->view('login', [
      'pageTitle' => 'Login User'
    ]));

    return $response;
  }

  public function auth(Request $request, Response $response, array $args = []): Response
  {
    $email = $request->getOnlyBodyParameters('email');
    $password = $request->getOnlyBodyParameters('password');

    $userModel = new UserModel();
    $userByEmail = $userModel->query('SELECT * FROM users WHERE email = :email LIMIT 1', [
      'email' => $email
    ]);

    if (!empty($userByEmail)) {
      $user = $userByEmail[0];

      if (password_verify($password, $user->password)) {
        $session = new SessionManager();
        $session->set('user', [
          'id' => $user->id,
          'name' => $user->name,
          'email' => $user->email
        ]);

        $response->redirect('/')->send();
      }
    }

    $response->redirect('/user/login')->send();

    return $response;
  }
}