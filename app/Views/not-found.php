<?php $this->extends('master', ['title' => 'Error 404']); ?>

<?php $this->ssection('css'); ?>
<link
rel="stylesheet" href="<?= ($call->baseUrl)() ?>/assets/css/styles.css"> <?php $this->esection(); ?>

<h1 class="text-center mb-3"><?= $this->escape($pageTitle) ?></h1>
