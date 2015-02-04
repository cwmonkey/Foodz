<?php

$view->body_id = 'list';
$view->title = 'Saved Places';

?>

<div id="saved" class="places">
	<? $view->render('modules/_places', array('menu' => false, 'places' => $view->saved)) ?>
</div>
