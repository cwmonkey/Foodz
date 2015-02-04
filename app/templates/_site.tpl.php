<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]> <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]> <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<title><?=($view->title)?$view->title . ' - ':''?>Foodz</title>

	<meta name="viewport" content="width=device-width">
	<? /* <link rel="shortcut icon" href="http://wrestlingwithtext.com/favicon.ico" /> */ ?>
	<!--[if !IE 6]><!-->
	<link href='http://fonts.googleapis.com/css?family=Ranchers' rel='stylesheet' type='text/css' media="screen" />

	<? $view->RenderCssFiles(array_merge(array(
		'reset.css',
		'cms-normalize.css',
		/*'colorbox.css',*/
		'jquery.message.css',
		'style.css',
	), $view->CssFiles)) ?>
	<!--<![endif]-->

	<link rel="apple-touch-icon" href="/apple-touch-icon.png" />

	<!--[if lt IE 9]>
	<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<? foreach ( $view->Metas as $name => $content ): ?>
		<meta name="<?=$name ?>" content="<?=$content ?>" id="meta-<?=$name ?>" />
	<? endforeach ?>

	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-59179184-1', 'auto');
		ga('send', 'pageview');
	</script>
</head>
<body <?=($view->body_id)?'id="'.$view->body_id.'"':''?> class="<?=$view->body_class ?>">

<? if ( $view->debug ): ?>
	<p>
		<a href="?debug=0">Debug off</a>
	</p>
<? endif ?>

	<div id="wrapper">
		<header id="header" role="banner"><div class="content">
			<h4 id="title">
				<a href="/">
					Foodz
				</a>
			</h4>

			<nav id="header_nav">
				<? if ( isset($_SESSION['id']) ): ?>
					<a href="/logout">Logout</a>
				<? endif ?>
			</nav>
		</div></header>

		<div id="content"><div class="content">
			<main id="main">
				<? /* <h1 id="main_headline"><a href="<?=$view->url?>">
					<strong><?=$view->title?></strong>
				</a></h1> */ ?>

<div id="sub">
	<?=$view->content ?>
</div>

			</main>
		</div></div>

		<footer id="footer"><div class="content cms">
			<p>
				Powered by <a href="http://yelp.com"><img src="http://s3-media1.fl.yelpcdn.com/assets/2/www/img/14f29ad24935/map/miniMapLogo.png" width="40" height="20"></a>
			</p>
			<p>
				And also monkeys.
			</p>
		</div></footer>
	</div>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

	<? $view->RenderJsFiles(array_merge(array(
		'lib/floatLabels.js',
		'script.js'
	), $view->JsFiles)) ?>

	<? if ( $view->debug ): ?>
		<? $view->RenderJsFiles(array(
			'debug_script.js',
		)) ?>
	<? endif ?>
</body>
</html>