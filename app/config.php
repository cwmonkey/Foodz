<?php

$debug = false;
$dev = false;
$minify_js = true;
$minify_css = true;

if (
	__DIR__ == 'E:\wamp\www\msnu\app'
	|| __DIR__ == '/home/monkey/sites/mysmilies_dev'
) {
	//$debug = true;
	$minify_js = true;
	$minify_css = true;
	$dev = true;
}

$do_cache = true;

$routes = array(
	// Regular pages
	'/save' => array(
		'controller' => 'save',
		'action' => 'show',
	),
	'/unsave' => array(
		'controller' => 'unsave',
		'action' => 'show',
	),
	'/visited' => array(
		'controller' => 'visited',
		'action' => 'show',
	),
	'/search' => array(
		'controller' => 'search',
		'action' => 'show',
	),
	'/login' => array(
		'controller' => 'login',
		'action' => 'show',
		'template' => 'login',
	),
	'/logout' => array(
		'controller' => 'logout',
		'action' => 'show',
	),
	'/oauth2callback' => array(
		'controller' => 'oauth2callback',
		'action' => 'show',
		'template' => 'oauth2callback',
	),
	'/:id' => array(
		'constraints' => array(
			'id' => '/^[0-9]+$/',
		),
		'controller' => 'list',
		'action' => 'show',
		'template' => 'list',
	),
	'' => array(
		'controller' => 'landing',
		'action' => 'show',
		'view' => '_site',
		'template' => 'landing',
		'url' => '/',
		'landing' => true,
	),
);

$route_404_config = array(
	'controller' => '_site',
	'action' => 'show',
	'view' => '_site',
	'template' => '404',
	'is_404' => true,
	'url' => '/',
);

$monkake_dir = dirname(__FILE__) . '/';

$controller_dir = 'controllers/';
$model_dir = 'models/';
$template_dir = 'templates/';
$view_dir = 'views/';
$lib_dir = '../../../_shared/lib/';
$class_dir = 'classes/';
$cache_dir = '_cache/';

$logs_dir = $monkake_dir . 'logs/';

$doc_root_dir = $monkake_dir . '../';
$css_dir = $doc_root_dir . 'css/';
$css_compressed_dir = $css_dir . 'compressed/';
$js_dir = $doc_root_dir . 'js/';
$js_compressed_dir = $js_dir . 'compressed/';

$controller_prepend = 'controller_';
$model_prepend = 'model_';
$action_prepend = 'action_';
$view_prepend = 'view_';

$controller_append = '.controller.php';
$template_append = '.tpl.php';
$view_append = '.view.php';
$model_append = '.model.php';
$class_append = '.class.php';

$template_wrapper = '_site';

$url = 'http://mysmilies.com';

$smiley_count = 75;

@include('config.local.php');