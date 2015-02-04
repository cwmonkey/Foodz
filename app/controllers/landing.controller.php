<?php

class controller_landing extends controller__site {
	public function action_show($request) {
		$r = $request->route;

		$view = new view__site();

		$cache = $this->get_cache();
		$places_by_user_key = 'places_by_user_' . $_SESSION['id'];
		if ( ($view->saved = $cache->get($places_by_user_key)) ) {
		} else {
			$view->saved = model_place::GetByUserId($_SESSION['id']);
			$cache->set($places_by_user_key, $view->saved);
		}

		if ( $view->saved ) {
			//shuffle($view->saved);
			usort($view->saved, 'weightedshuffle');
		}

		$view->searched = array();

		$filter = array();
		foreach ( $view->saved as $saved ) {
			$filter[$saved->yelp_id] = true;
		}

		$user = R::load('user', $_SESSION['id']);

		if ( isset($_GET['location']) ) {
			$businesses = array();
			for ( $i = 0; $i < 5; $i++ ) {
				$bs = $this->yelp_search($user, $i * 20);
				foreach ( $bs as $b ) {
					if ( !isset($filter[$b->yelp_id]) ) $businesses[] = $b;
				}

				if ( count($bs) < 20 ) break;
			}

			$view->searched = $businesses;
		}

		$view->location = htmlspecialchars($user->location);
		$view->radius_filter = htmlspecialchars($user->radius);
		if ( !$view->radius_filter ) $view->radius_filter = 1;
		$view->term = htmlspecialchars($user->term);

		$view->template = $request->route['template'];

		return $view;
	}
}