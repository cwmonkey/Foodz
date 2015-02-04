<?php

class controller_save extends controller__site {
	public function action_show($request) {
		$view = new view__site();

		$orm = R::dispense('place');
		$orm->user_id = $_SESSION['id'];
		$orm->name = $_GET['name'];
		$orm->yelp_id = $_GET['yelp_id'];
		$orm->rating = $_GET['rating'];
		$orm->phone = $_GET['phone'];
		$orm->address = $_GET['address'];

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