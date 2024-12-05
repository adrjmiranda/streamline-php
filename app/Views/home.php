<?php $this->extends('master', ['title' => 'Home Page']); ?>

<?php $this->ssection('css'); ?>
<link
rel="stylesheet" href="<?= ($call->baseUrl)() ?>/assets/css/styles.css"> <?php $this->esection(); ?>

<h1 class="text-center mb-3"><?= $this->escape($pageTitle) ?></h1>

<?php if (!empty($users)): ?>
  <table class="table">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">Name</th>
        <th scope="col">E-mail</th>
        <th scope="col">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): ?>
        <tr>
          <td scope="row"><?= $this->escape($user->id) ?></td>
          <td>
            <a
              href="/user/<?= $this->escape($user->id) ?>"><?= $this->escape($user->name) ?>
            </a>
          </td>
          <td><?= $this->escape($user->email) ?></td>
          <td>
            <a href="/user/delete/<?= $this->escape($user->id) ?>" class="btn btn-danger">
              <i class="bi bi-trash3"></i>
            </a>
            <a href="/user/edit/<?= $this->escape($user->id) ?>" class="btn btn-warning">
              <i class="bi bi-pencil-square"></i>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php else: ?>
  <strong>No registered users!</strong>
<?php endif; ?>

