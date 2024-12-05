<?php $this->extends('master', ['title' => 'Register User']); ?>

<?php $this->ssection('css'); ?>
<link
rel="stylesheet" href="<?= ($call->baseUrl)() ?>/assets/css/styles.css"> <?php $this->esection(); ?>

<h1 class="text-center mb-3"><?= $this->escape($pageTitle) ?></h1>

<form action="/user/edit/<?= $this->escape($user->id) ?>" method="post">
  <div class="mb-3">
    <label for="name" class="form-label">Full name:</label>
    <input type="text" name="name" class="form-control" id="name" placeholder="Your name" value="<?= $this->escape($user->name) ?>">
  </div>

  <div class="mb-3">
    <label for="email" class="form-label">Email address:</label>
    <input type="email" name="email" class="form-control" id="email" placeholder="name@example.com" value="<?= $this->escape($user->email) ?>">
  </div>

  <div class="mb-3">
    <label for="password" class="form-label">Password:</label>
    <input type="password" name="password" class="form-control" id="password" placeholder="Your password">
  </div>

  <div class="mb-3">
    <button type="submit" class="btn btn-primary">Atualizar</button>
  </div>
</form>

