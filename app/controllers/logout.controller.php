<?php

class controller_logout extends controller__site {
	public $requires_login = false;

	public function action_show($request) {
		$r = $request->route;

		$view = new view__site();

		session_destroy();

		$view->header301 = '/';

		return $view;
	}
}