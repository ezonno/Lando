<?php

function current_path() {
	$path_info = isset($_SERVER['PATH_INFO']) ? trim_slashes($_SERVER['PATH_INFO']) : "";
	return strtolower("/$path_info");
}

function path_segments() {
	global $Lando;

	$segs = explode("/", current_path());
	
	if(!$Lando->config["pretty_urls"])
		$segs[0] = "index.php";
	
	return $segs;
}

function path_segment($n) {
	$segs = path_segments();
	return isset($segs[$n]) ? $segs[$n] : false;
}

function current_url() {
	return implode("/", url_segments());
}

function url_segments() {
	$segs[0] = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
	
	if($_SERVER["SERVER_PORT"] != "80")
    $segs[0] .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
	else 
    $segs[0] .= $_SERVER["SERVER_NAME"];
    
  $path = trim_slashes(str_replace("?".$_SERVER["QUERY_STRING"], "", $_SERVER["REQUEST_URI"]));
   
  return array_merge($segs, explode("/", $path));
}

function url_segment($n) {
	$segs = url_segments();
	return isset($segs[$n]) ? $segs[$n] : false;
}