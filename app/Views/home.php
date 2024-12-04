<?php $this->extends('master', ['title' => 'Home Page']) ?>

<div class="container">
  <h1><?= $name ?> Page</h1>
  <h2><?= ($call->sum)(2, 3) ?></h2>
</div>