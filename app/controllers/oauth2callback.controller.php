<?php

class controller_oauth2callback extends controller__site {
	public $requires_login = false;

	public function action_show($request) {
		$view = new view__site();

		$client = $this->get_client();

		if ( isset($_GET['code']) ) {
			$client->authenticate($_GET['code']);
			$_SESSION['access_token'] = $client->getAccessToken();

			$token_data = $client->verifyIdToken()->getAttributes();
			$email = $token_data['payload']['email'];
			$_SESSION['email'] = $email;

			$user = model_user::GetByEmail($email);

			$_SESSION['id'] = $user->id;

			if ( isset($_SESSION['return_url']) ) {
				$view->header301 = $_SESSION['return_url'];
				unset($_SESSION['return_url']);
			} else {
				$view->header301 = '/';
			}
		}

		return $view;
	}
}