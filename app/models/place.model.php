<?php

class model_place extends model__base {
	/* Static */
	public static $table = 'place';

	public static function GetById($id) {
		$orm = R::findOne(self::$table, ' id = ? ', array(
			$id
		));
		if ( $orm ) return new self($orm);
	}

	public static function GetByUserId($id) {
		$orms = R::find(self::$table, ' WHERE user_id = ? ',
			array($id)
		);

		$objs = array();
		foreach ( $orms as $orm ) {
			$objs[] = new self($orm);
		}

		return $objs;
	}

	public static function MakeYelp($obj, $user_id) {
		$params = array();

		$rplace = R::dispense('place');
		$params['name'] = $rplace->name = $obj->name;
		$params['yelp_id'] = $rplace->yelp_id = $obj->id;
		$params['rating'] = $rplace->rating = $obj->rating_img_url_small;
		if ( isset($obj->display_phone) ) $params['phone'] = $rplace->phone = $obj->display_phone;
		$params['address'] = $rplace->address = (( $obj->location->address ) ? implode(' - ', $obj->location->address) . ', ' : '') . $obj->location->city;
		$rplace->user_id = $user_id;

		$mplace = new model_place($rplace);
		$mplace->save_url = '/save/?' . http_build_query($params);

		return $mplace;
	}

	/* Instanced */
	public $info;
	public $url;
	public $save_url;
	public $unsave_url;
	public $visited_url;
	public $last_visited;

	public function __construct($orm) {
		$this->_orm = $orm;
		$this->info = $orm->address . ' (' . $orm->phone . ')';
		$this->url = 'http://yelp.com/biz/' . $orm->yelp_id;
		$this->unsave_url = '/unsave/?id=' . $this->id;
		$this->visited_url = '/visited/?id=' . $this->id;

		if ( !$orm->visited ) {
			$this->last_visited = 'Never';
		} else {
			$day = floor((time() - strtotime($orm->visited . ' 00:00:00'))/60/60/24);
			$this->last_visited = $day . ' day' . (($day == 1)?'':'s') . ' ago';
		}
	}
}
