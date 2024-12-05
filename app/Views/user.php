<?php $this->extends('master', ['title' => 'Register User']); ?>

<?php $this->ssection('css'); ?>
<link
rel="stylesheet" href="<?= ($call->baseUrl)() ?>/assets/css/styles.css"> <?php $this->esection(); ?>

<h1 class="text-center mb-3"><?= $this->escape($pageTitle) ?></h1>

<div class="card" style="width: 18rem;">
  <img src="https://cdn.pixabay.com/photo/2017/09/13/04/32/girl-2744387_640.png" class="card-img-top" alt="...">
  <div class="card-body">
    <h5 class="card-title"><?= $this->escape($user->name) ?></h5>
    <p class="card-text"><?= $this->escape($user->email) ?></p>
    <a href="/user/edit/<?= $this->escape($user->id) ?>" class="btn btn-primary">Edit</a>
  </div>
</div>

