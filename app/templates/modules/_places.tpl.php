<? foreach ( $places as $place ): ?>
	<article class="place">
		<a href="<?=$place->url ?>" target="_blank">
			<div class="place_content">
				<h1 class="name"><?=$place->name ?></h1>
				<p class="rating"><img src="<?=$place->rating ?>" width="50" height="10"></p>
				<p class="last_visited">Last visit: <strong><?=$place->last_visited ?></strong></p>
				<p class="info"><?=$place->info ?></p>
			</div>
		</a>
		<? if ( $menu ): ?>
			<menu class="menu">
				<? if ( !$saved ): ?><a class="action save" href="<?=$place->save_url ?>">Save</a><? endif ?>
				<? if ( $saved ): ?>
					<a class="action visited" href="<?=$place->visited_url ?>">Visited</a>
					<a class="action unsave" href="<?=$place->unsave_url ?>">Unsave</a>
				<? endif ?>
			</menu>
		<? endif ?>
	</article>
<? endforeach ?>
