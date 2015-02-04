<?php

if ( get_magic_quotes_gpc() ) {
	function stripslashes_array($array) {
		return is_array($array) ? array_map('stripslashes_array', $array) : stripslashes($array);
	}

	$_GET = stripslashes_array($_GET);
	$_POST = stripslashes_array($_POST);
	$_COOKIE = stripslashes_array($_COOKIE);
}

function weightedshuffle ($a, $b) {
    $a_rank = ( $a->last_visited == 'Never' ) ? 100 : intval($a->last_visited);
    $b_rank = ( $b->last_visited == 'Never' ) ? 100 : intval($b->last_visited);
    return rand(0, $a_rank + $b_rank) >= $a_rank;
}

class controller__site {
	public $ajax = false;
	public $requires_login = true;

	public function Init($request) {
		require_once realpath(M::Get('monkake_dir') . M::Get('class_dir') . 'google-api-php-client/autoload.php');

		$debugger = ( @$_COOKIE[M::Get('helper_name')] == M::Get('helper_value') ) ? true : false;
		if ( $debugger ) {
			$cookie_debug = @$_COOKIE['debug'];

			if ( isset($_GET['debug']) ) {
				if ( $_GET['debug'] == 1 ) {
					setcookie('debug', 1, time()+60*60*24*365, '/');
					$cookie_debug = 1;
				} else {
					setcookie('debug', NULL, -1, '/');
				}
			}

			if ( $cookie_debug != 1 ) {
				M::Set('debug', false);
			} else {
				M::Set('debug', true);
			}
		}

		if ( M::Get('debug') ) {
			error_reporting(E_ALL);
			M::Set('minify_js', false);
			M::Set('minify_css', false);
		} else {
			error_reporting(0);
		}

		if ( (isset($_GET['ajax']) && $_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ) {
			$this->ajax = true;
		}

		if ( $this->requires_login && !isset($_SESSION['id']) ) {
			if ( $this->ajax ) {
				header('HTTP/1.0 403 Forbidden');
				header('Content-type: application/json');
				$var = new stdClass();
				$var->success = false;
				echo FastJSON::encode($var);
				exit;
			} else {
				$_SESSION['return_url'] = $_SERVER['REQUEST_URI'];
				header("Location: /login");
				exit;
			}
		}

		$action = M::Get('action_prepend') . $request->route['action'];

		define('REDBEAN_MODEL_PREFIX', 'RedBeanPHP_Model_');
		require_once(M::Get('lib_dir') . 'RedBean/rb.php');

		R::setup('mysql:host=' . M::Get('db_address') . '; dbname=' . M::Get('db_database'), M::Get('db_user'), M::Get('db_password'));
		R::freeze( true );

		// View
		$view = $this->$action($request);

		if ( !$view ) return;

		$view->debug = M::Get('debug');
		if ( isset($_GET['debug']) && !$_GET['debug'] ) $view->debug = false;
		if ( @$request->route['is_404'] ) {
			$view->is_404 = true;
		}
		if ( $_SERVER['REQUEST_URI'] == '/' ) $_SESSION['referer'] = '';

		if ( !isset($_SESSION['referer']) ) $_SESSION['referer'] = ( isset($_SERVER['HTTP_REFERER']) ) ? $_SERVER['HTTP_REFERER'] : '';

		if ( $view->header301 ) {
			header("Location: " . $view->header301, true, 301);
			exit;
		}

		if ( $view->json ) {
			self::RenderJson($view, $view->Value);
		} else {
			$view->site_wrapper = M::Get('template_wrapper');

			self::RenderView($view);
		}
	}

	private $_cache;
	public function get_cache() {
		if ( $this->_cache ) {
			return $this->_cache;
		}

		require_once(M::Get('monkake_dir') . M::Get('class_dir') . 'phpfastcache/phpfastcache_v2.1_release/phpfastcache/phpfastcache.php');
		phpFastCache::setup('storage','auto');
		$cache = phpFastCache();
		$this->_cache = $cache;
		return $cache;
	}

	public function yelp_search($user, $offset = 0) {
		$term = ( isset($_GET['term']) ) ? $_COOKIE['term'] = $_GET['term'] : 'lunch';
		$location = ( isset($_GET['location']) ) ? $_SESSION['location'] = $_GET['location'] : '';
		$radius_filter = ( isset($_GET['radius_filter']) ) ? $_SESSION['radius_filter'] = $_GET['radius_filter'] : '1';
		$sort = ( isset($_GET['sort']) ) ? $_SESSION['sort'] = $_GET['sort'] : '';
		//$offset = ( isset($_GET['offset']) ) ? $_SESSION['offset'] = $_GET['offset'] : '';

		$user->term = $term;
		$user->radius = $radius_filter;
		$user->location = $location;
		R::store($user);

		$cache_key = 'yelp_search:' . $term . '|' . $location . '|' . $radius_filter . '|' . $sort . '|' . $offset;
		$cache = $this->get_cache();

		if ( ($data = $cache->get($cache_key)) ) {
		} else {
			require_once(M::Get('monkake_dir') . M::Get('class_dir') . 'yelp-api/v2/php/lib/OAuth.php');

			// Set your OAuth credentials here  
			// These credentials can be obtained from the 'Manage API Access' page in the
			// developers documentation (http://www.yelp.com/developers)

			$CONSUMER_KEY = M::Get('yelp_consumer_key');
			$CONSUMER_SECRET = M::Get('yelp_consumer_secret');
			$TOKEN = M::Get('yelp_token');
			$TOKEN_SECRET = M::Get('yelp_token_secret');

			$API_HOST = 'api.yelp.com';
			$DEFAULT_TERM = 'dinner';
			$DEFAULT_LOCATION = 'San Francisco, CA';
			$DEFAULT_RADIUS = 1;
			$DEFAULT_SORT = 0;
			$SEARCH_LIMIT = 20;
			$SEARCH_PATH = '/v2/search/';
			$BUSINESS_PATH = '/v2/business/';

			$url_params = array();

			$url_params['term'] = $term ?: $DEFAULT_TERM;
			$url_params['radius_filter'] = $radius_filter ?: $DEFAULT_RADIUS;
			$url_params['radius_filter'] *= 1609.34;
			$url_params['location'] = $location?: $DEFAULT_LOCATION;
			$url_params['limit'] = $SEARCH_LIMIT;
			$url_params['sort'] = ($sort !== '') ? $sort : $DEFAULT_SORT;
			if ( $offset ) $url_params['offset'] = $offset;
			$search_path = $SEARCH_PATH . "?" . http_build_query($url_params);

			$host = $API_HOST;
			$path = $search_path;

			$unsigned_url = "http://" . $host . $path;

			// Token object built using the OAuth library
			$token = new OAuthToken($TOKEN, $TOKEN_SECRET);

			// Consumer object built using the OAuth library
			$consumer = new OAuthConsumer($CONSUMER_KEY, $CONSUMER_SECRET);

			// Yelp uses HMAC SHA1 encoding
			$signature_method = new OAuthSignatureMethod_HMAC_SHA1();

			$oauthrequest = OAuthRequest::from_consumer_and_token(
				$consumer, 
				$token, 
				'GET', 
				$unsigned_url
			);

			// Sign the request
			$oauthrequest->sign_request($signature_method, $consumer, $token);

			// Get the signed URL
			$signed_url = $oauthrequest->to_url();

			// Send Yelp API Call
			$ch = curl_init($signed_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$data = curl_exec($ch);
			curl_close($ch);

			$cache->set($cache_key, $data, 3600);
		}

		$json_obj = json_decode($data);

		$businesses = array();
		foreach ( $json_obj->businesses as $biz ) {
			$businesses[] = model_place::MakeYelp($biz, $_SESSION['id']);
		}

		return $businesses;
	}

	public function get_client() {
		$client_id = M::Get('google_client_id');
		$client_secret = M::Get('google_client_secret');
		$redirect_uri = M::Get('google_redirect_uri');

		$client = new Google_Client();
		$client->setClientId($client_id);
		$client->setClientSecret($client_secret);
		$client->setRedirectUri($redirect_uri);
		$client->addScope("https://www.googleapis.com/auth/userinfo.profile");
		$client->addScope("https://www.googleapis.com/auth/userinfo.email");

		return $client;
	}

	public function action_show($request) {
		if ( isset($request->route['view']) ) {
			$view_name = M::Get('view_prepend') . $request->route['view'];
			$view = new $view_name();
		} else {
			$view = new view__site();
		}

		$view->template = $request->route['template'];

		return $view;
	}

	private function is_mobile() {
		$wurflDir = dirname(__FILE__) . '/../lib/WURFL';
		$resourcesDir = dirname(__FILE__) . '/../resources';
		 
		require_once $wurflDir.'/Application.php';
		 
		$persistenceDir = $resourcesDir.'/storage/persistence';
		$cacheDir = $resourcesDir.'/storage/cache';
		 
		// Create WURFL Configuration
		$wurflConfig = new WURFL_Configuration_InMemoryConfig();
		 
		// Set location of the WURFL File
		$wurflConfig->wurflFile($resourcesDir.'/wurfl.zip');
		 
		// Set the match mode for the API ('performance' or 'accuracy')
		$wurflConfig->matchMode('performance');
		 
		// Automatically reload the WURFL data if it changes
		$wurflConfig->allowReload(true);
		 
		// Optionally specify which capabilities should be loaded
		$wurflConfig->capabilityFilter(array(
			// "device_os",
			// "device_os_version",
			// "is_tablet",
			"is_wireless_device",
			// "mobile_browser",
			// "mobile_browser_version",
			// "pointing_method",
			// "preferred_markup",
			// "resolution_height",
			// "resolution_width",
			// "ux_full_desktop",
			// "xhtml_support_level",
		));
		 
		// Setup WURFL Persistence
		$wurflConfig->persistence('file', array('dir' => $persistenceDir));
		 
		// Setup Caching
		$wurflConfig->cache('file', array('dir' => $cacheDir, 'expiration' => 36000));
		 
		// Create a WURFL Manager Factory from the WURFL Configuration
		$wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);
		 
		// Create a WURFL Manager
		/* @var $wurflManager WURFL_WURFLManager */
		$wurflManager = $wurflManagerFactory->create();

		$requestingDevice = $wurflManager->getDeviceForHttpRequest($_SERVER);

		return ($requestingDevice->getCapability('is_wireless_device') == 'true');
	}

	public static function RenderView($view) {
		if ( $view->use_wrapper && !$view->ajax ) {
			ob_start();
		}

		if ( $view->xml ) header('Content-type: text/xml');
		require_once(M::Get('monkake_dir') . M::Get('template_dir') . $view->template . M::Get('template_append'));

		if ( $view->use_wrapper && !$view->ajax ) {
			$view->content = ob_get_clean();

			if ( $view->do_cache && M::Get('do_cache') && !M::Get('debug') && !$view->is_404 ) ob_start();
			require_once(M::Get('monkake_dir') . M::Get('template_dir') . $view->site_wrapper . M::Get('template_append'));
			if ( $view->do_cache && M::Get('do_cache') && !M::Get('debug') && !$view->is_404 ) {
				$content = ob_get_clean();
				file_put_contents(self::$cache_file, $content);
				//echo $content;
				include(self::$cache_file);
			}
		}
	}

	public function RenderJson($view, $var) {
		header('Content-type: application/json');
		echo FastJSON::encode($var);
	}
}