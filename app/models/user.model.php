<?php

class model_user extends model__base {
	/* Static */
	public static $table = 'user';

	public static function GetById($id) {
		$orm = R::load(self::$table, $id);
		if ( $orm ) return new self($orm);
	}

	public static function GetByEmail($email) {
		$orm = R::findOne(self::$table, ' WHERE email = ? ',
			array($email)
		);

		if ( !$orm ) {
			$orm = R::dispense('user');
			$orm->email = $email;
			R::store($orm);
		}

		return new self($orm);
	}

	/* Instanced */
	public $href;

	public function __construct($orm) {
		$this->_orm = $orm;
		$this->href = '/' . urlencode($orm->id);
	}
}
