<?php

class model__base {
	/* Site Stuff
	-------------------------------------------------------- */
	public static $table;
	public $_orm;

	public function urlize($val) {
		$val = preg_replace('/[^a-z0-9]+/', '-', strtolower($val));
		return $val;
	}

	public function __get($name) {
		if ( method_exists($this, $name) ) return $this->$name();
		if ( isset($this->_orm->{$name}) ) return $this->_orm->{$name};
		return null;
	}

	public $_smiley;
	public function smiley() {
		if ( isset($this->_smiley) ) return $this->_smiley;
		if ( !$this->_orm->smiley_id ) return $this->_smiley = NULL;
		$smiley = model_smiley::GetById($this->_orm->smiley_id);
		return $this->_smiley = $smiley;
	}

	public $_user;
	public function user() {
		if ( isset($this->_user) ) return $this->_user;
		if ( !$this->_orm->usr_id ) return $this->_user = NULL;

		$user = model_user::GetById($this->_orm->usr_id);
		if ( !$user ) return $this->_user = NULL;

		return $this->_user = $user;
	}

	/* Admin Stuff
	-------------------------------------------------------- */
	public $_form;
	public $_mapping;
	public $_values;
	public $_table;
	public static $_display;
	public function form() {
		if ( $this->_form ) return $this->_form;
		$this->_form = new helper_form($this->_table . '_form', $this->_mapping, $this->_orm, $this->_values);
		return $this->_form;
	}

	public function save() {
		if ( !$this->_orm ) $this->_orm = R::dispense($this->_table);
		if ( $this->_values ) {
			foreach ( $this->_mapping as $key => $val ) {
				$allow_null = false;
				if ( is_array($val) && isset($val['default']) ) {
					if ( isset($val['null']) ) $allow_null = $val['null'];
					$val = $val['default'];
				}

				if ( substr($key, 0, 1) == '#' ) continue;

				if ( substr($key, 0, 1) == '_' || substr($key, 0, 1) == '!' ) {
					$name = substr($key, 1);
				} else {
					$name = $key;
				}

				if ( isset($this->_values[$name]) ) {
					if ( $allow_null && !$this->_values[$name] ) {
						$this->_orm->{$name} = null;
					} else {
						$this->_orm->{$name} = $this->_values[$name];
					}
				} elseif ( is_bool($val) ) {
					$this->_orm->{$name} = false;
				}
			}
		}

		R::store($this->_orm);
	}
}
