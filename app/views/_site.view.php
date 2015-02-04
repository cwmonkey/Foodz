<?php

class view__site {
	public $content = '';
	public $title = '';
	public $body_id = '';
	public $body_class = '';
	public $site_wrapper;
	public $use_wrapper = true;

	public $url = '';
	public $share_url = NULL;
	public $disqus_url = NULL;
	public $base_url = 'http://foodz.mysmilies.com/';
	public $show_share = true;

	public $older_link = NULL;
	public $newer_link = NULL;
	public $canonical = NULL;

	public $debug = false;
	public $show_comments = false;
	public $show_secondary = true;

	public $ajax = false;
	public $xml = false;
	public $json = false;
	public $Value = null;
	public $do_cache;
	public $JsFiles = array();
	public $CssFiles = array();
	public $Metas = array();

	public $header301;
	public $is_404 = false;

	public function init($request) {
	}

	public function action_show($request) {
		return $this;
	}

	public function render($template, $vars = array()) {
		$view = $this;
		extract($vars);
		require(M::Get('monkake_dir') . M::Get('template_dir') . $template . M::Get('template_append'));
	}

	public function AddMeta($name, $content, $id = null) {
		if ( !$id ) $id = 'meta-' . $name;
		$this->Metas[$name] = htmlspecialchars($content);
	}

	public function RenderJsFiles($js_files) {
		if ( !is_array($js_files) ) $js_files = array($js_files);

		if ( !M::Get('minify_js') ) {
			for ( $i=0; $i < count($js_files); $i++ ) {
				$file = $js_files[$i];
				if ( strstr($file, '//') ) {
					echo '<script type="text/javascript" src="' . $file . '" defer="defer"></script>';
				} else {
					echo '<script type="text/javascript" src="/js/' . $file . '" defer="defer"></script>';
				}
			}
			return;
		}

		$output = '';
		$lastmod = 0;
		$files = array();

		foreach ( $js_files as $file ) {
			$path = M::Get('js_dir') . $file;
			if ( strstr($file, '//') !== false ) {
				echo '<script type="text/javascript" src="' . $file . '" defer="defer"></script>';
			} elseif ( ($mtime = filemtime($path)) ) {
				if ( $lastmod < $mtime ) $lastmod = $mtime;
				$files[] = urlencode($file);
			}
		}

		if ( count($files) ) {
			$filename = M::Get('js_compressed_dir') . md5(implode(',', $files)) . '.js';

			if ( intval(@filemtime($filename)) < $lastmod ) {
				foreach ( $files as $file ) {
					$path = M::Get('js_dir') . urldecode($file);
					$output .= "\n" . file_get_contents($path);
				}

				$fp = fopen($filename, 'w+');
				$output = $this->GetCompressedJs($output);
				fwrite($fp, $output);
				fclose($fp);
				chmod($filename, 0777);
			}
			echo '<script type="text/javascript" src="/js/' . $lastmod . '/' . md5(implode(',', $files)) . '.js" defer="defer"></script>';
		}
	}

	public function GetCompressedJs($output) {
		//$jsp = new JavaScriptPacker($output, 0);
		//$output = $jsp->pack();
		$output = JSMin::minify($output);
		return $output;
	}

	public function IncludeJsFile($file) {
		echo '<script type="text/javascript">';
		$path = M::Get('js_dir') . $file;
		$output = file_get_contents($path);
		echo $this->GetCompressedJs($output);
		echo '</script>';
	}

	public function RenderCssFiles($css_files) {
		if ( !is_array($css_files) ) $css_files = array($css_files);

		if ( !M::Get('minify_css') ) {
			$cssextra = ( M::Get('debug') ) ? '?' . time() : '';
			foreach ( $css_files as $file ) {
				if ( strstr($file, '//') ) {
					echo '<link rel="stylesheet" type="text/css" href="' . $file . $cssextra . '" />';
				} else {
					echo '<link rel="stylesheet" type="text/css" href="/css/' . $file . $cssextra . '" />';
				}
			}
			return;
		}

		$output = '';
		$lastmod = 0;
		$files = array();

		foreach ( $css_files as $file ) {
			$path = M::Get('css_dir') . $file;
			if ( ($mtime = filemtime($path)) ) {
				if ( $lastmod < $mtime ) $lastmod = $mtime;
				$files[] = $file;
			}
		}

		if ( count($files) ) {
			$filename = M::Get('css_compressed_dir') . md5(implode(',', $files)) . '.css';

			if ( intval(@filemtime($filename)) < $lastmod ) {
				foreach ( $files as $file ) {
					$path = M::Get('css_dir') . $file;
					$output .= "\n" . file_get_contents($path);
				}

				$fp = fopen($filename, 'w+');
				fwrite($fp, Minify_CSS_Compressor::process($output));
				fclose($fp);
				chmod($filename, 0777);
			}
			echo '<link rel="stylesheet" type="text/css" href="/css/' . $lastmod . '/' . md5(implode(',', $files)) . '.css" />';
		}
	}
}
