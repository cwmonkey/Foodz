<?php

error_reporting(E_ALL);

if ( file_exists('../../../monkake/monkake.php') ) {
	require_once('../../../monkake/monkake.php');
} else {
	require_once('../monkake/monkake.php');
}

M::AddConfig('app/config.php');

M::Run();
