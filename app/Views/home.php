<?php $this->extends('master', ['title' => 'Home Page']); ?>

<?php $this->ssection('css'); ?>
<link rel="stylesheet" href="/assets/css/styles.css">
<?php $this->esection(); ?>

<div class="container">
  <h1><?= $name ?> Page</h1>
  <h2><?= ($call->sum)(2, 3) ?></h2>
</div>