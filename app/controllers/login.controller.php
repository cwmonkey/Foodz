<?php

class controller_login extends controller__site {
	public $requires_login = false;

	public function action_show($request) {
		$r = $request->route;

		$client = $this->get_client();

		$view = new view__site();

		$view->auth_url = $client->createAuthUrl();

		$view->template = $request->route['template'];

		return $view;
	}
}