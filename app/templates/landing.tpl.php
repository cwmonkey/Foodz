<?php

$view->body_id = 'landing';
$view->title = 'Make Your List';

?>

<? if ( $view->saved ): ?>
	<section id="saved" class="places">
		<h2 class="headline">Saved Places</h2>
		<? $view->render('modules/_places', array('menu' => true, 'saved' => true, 'places' => $view->saved)) ?>
	</section>
<? endif ?>

<? if ( $view->searched ): ?>
	<section id="searched" class="places">
		<h2 class="headline">Searched Places</h2>
		<? $view->render('modules/_places', array('menu' => true, 'saved' => false, 'places' => $view->searched)) ?>
	</section>
<? endif ?>

<section>
	<h2 class="headline">Search</h2>

	<form action="/#searched" id="search" method="get" class="cms">
		<p>
			<input type="text" name="location" id="location" placeholder="Location" value="<?=$view->location ?>" required>
		</p>
		<p>
			<input type="text" name="term" id="term" placeholder="Search Term (Default: lunch)" value="<?=$view->term ?>">
		</p>
		<p>
			<input type="number" name="radius_filter" id="radius_filter" placeholder="Radius in Miles (Default: 1)" value="<?=$view->radius_filter ?>" min="1" max="25">
		</p>
		<p>
			<input type="submit" value="Find">
		</p>
	</form>
</section>