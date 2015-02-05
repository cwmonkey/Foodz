<?php

class controller_list extends controller__site {
	public $requires_login = false;

	public function action_show($request) {
		$view = new view__site();

		$cache = $this->get_cache();
		$places_by_user_key = 'places_by_user_' . $request->route['id'];
		if ( ($view->saved = $cache->get($places_by_user_key)) ) {
		} else {
			$view->saved = model_place::GetByUserId($request->route['id']);
			$cache->set($places_by_user_key, $view->saved);
		}

		if ( $view->saved ) {
			//shuffle($view->saved);
			usort($view->saved, 'weightedshuffle');
		}


		$view->template = $request->route['template'];

		return $view;
	}
}