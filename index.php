<?php
include "app/core/loader.php";

$themeBase = "themes/".$Lando->config["theme"]."/";
$template = "404";
$url = current_path();
$Current = new Page();

if($url == "/") {
	$Current = $Lando->get_content();
	$template = $Current ? "home" : "404";
}

if(preg_match('~^/([\w-]+)$~', $url, $matches)) {
	switch($matches[1]) {
		case "posts": 
			$template = "posts_all";
			break;
		case "drafts":
			$template = "drafts_all";
			break;
		case "rss":
			$template = "rss";
			break;
		default: 
			$Current = $Lando->get_content();
		
			if(!$Current)
				$template = "404";
			elseif(include_exists($themeBase.$matches[1].".php"))
				$template = $matches[1];
			else
				$template = "page";
	}
}

if(preg_match('~^/posts/from/(\d{4})(?:/(\d{2}))?(?:/(\d{2}))?$~', $url))
	$template = "posts_by_date";

elseif(preg_match('~^/posts/tagged/([\w\s\+-,]+)$~', $url))
	$template = "posts_by_tag";

elseif(preg_match('~^/([\w-]+)(?:/([\w-]+))+$~', $url, $matches)) {
	$Current = $Lando->get_content();

	if(!$Current)
		$template = "404";
	else {
		switch($matches[1]) {
			case "posts": 
				$template = "post";
				break;
			case "drafts":
				$template = "draft";
				break;
			default: 
				if(include_exists($themeBase.$matches[2].".php"))
					$template = $matches[2];
				else
					$template = "page";
		}
	}
}
	
//kick out to login if trying to view drafts
if(in_array($template, array("draft", "drafts_all")) && !admin_cookie())
	header("Location: ".$Lando->config["site_root"]."/admin/login.php?redirect=drafts");

$helper_file = $themeBase."theme_functions.php";
if(include_exists($helper_file))
	include $helper_file;

if(!include_exists($themeBase.$template.".php")) {
	if(include_exists("app/templates/$template.php"))
		$themeBase = "app/templates/"; //fallback for missing optional custom templates
	else
		system_error("Missing Theme/Template", "The template file <strong>$template.php</strong> could not be found in <strong>$theme_dir</strong>.");
}

//for 404 page, serve blank content
if(!$Current)
	$Current = new Page();

include_once $themeBase.$template.".php";