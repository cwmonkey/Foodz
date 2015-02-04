<?php

class controller_visited extends controller__site {
	public function action_show($request) {
		$view = new view__site();

		$orm = R::findOne('place', ' WHERE user_id = ? AND id = ? ',
			array($_SESSION['id'], $_GET['id'])
		);

		$orm->visited = date('Y-m-d');
		R::store($orm);

		$cache = $this->get_cache();
		$places_by_user_key = 'places_by_user_' . $_SESSION['id'];
		$cache->delete($places_by_user_key);

		/*if ( $this->ajax ) {
			$this->json = true;
			$this->Value = new stdClass();
			$this->Value->success = true;
		} else {*/
			$view->header301 = '/';
		//}

		return $view;
	}
}