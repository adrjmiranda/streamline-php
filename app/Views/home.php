<?php $this->extends('master', ['title' => 'Home Page']); ?>

<?php $this->ssection('css'); ?>
<link rel="stylesheet" href="<?= ($call->baseUrl)() ?>/assets/css/styles.css">
<?php $this->esection(); ?>

<div class="container">
  <h1><?= $name ?> Page</h1>
</div>