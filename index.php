<?php
/**
 * publisher.mod main include file
 *
 * $mod_slug -> for example
 *		/20XY/XY/XY/article_title/ -> show article
 *		/20XY/XY/ -> show archive from date
 *		/$mod[url_pages]/2/ -> show archive, page 2
 *		/$pub_preferences[url_separator_pages]/example/ -> show archive from category
 *		/category/example/$pub_preferences[url_separator_pages]/2/ -> go to category, page 2
 */
 
include 'modules/publisher.mod/info.inc.php';
include 'modules/publisher.mod/global/functions.php';
include 'modules/publisher.mod/frontend/functions.php';


/* defaults */
$pb_posts_start = 0;
$pb_posts_limit = 10;
$pb_posts_filter = array();

$pb_posts_filter['languages'] = $languagePack;
$pb_posts_filter['categories'] = 'all';
$pb_posts_filter['status'] = 'published';
$pb_posts_filter['types'] = 'messages-event-image-video-link-product';

$time_string_now = time();

$pub_preferences = pub_get_preferences();
/**
 * [url_pattern] [entries_per_page] [intro_snippet]
 * [default_page_template_entries] [ignore_inline_css] [default_template] [default_banner]
 * [url_separator_categories] [url_separator_pages]
 */

$pb_posts_limit = $pub_preferences['entries_per_page'];

/* set the template directory */
if($pub_preferences['default_template'] == 'use_standard') {
	$pub_tpl_dir = 'styles/default';
} else {
	$pub_tpl_dir = 'styles/' . basename($pub_preferences['default_template']);
}

$array_mod_slug = explode("/", $mod_slug);

/* default mode - list archive */
$pub_display_mode = 'list_posts';

$all_cats = get_post_categories();

/* pagination */

if(!isset($pb_posts_start)) {
	$pb_posts_start = 0;
}
/*
if(is_numeric($array_mod_slug[0])) {
	$pb_posts_start = $array_mod_slug[0];
}
*/




/* Mode: Categories */
if($array_mod_slug[0] == $pub_preferences['url_separator_categories']) {
		if($array_mod_slug[1] != "") {
			$pub_display_mode = 'list_posts_by_category';
			$cat = strip_tags($array_mod_slug[1]);
			$cat_name = $all_cats[$cat][0];
			$pb_posts_filter['categories'] = $cat;
			if($array_mod_slug[2] == $pub_preferences['url_separator_pages']) {
				$pb_posts_start = (int) $array_mod_slug[3];
			}
		} else {
			header("HTTP/1.1 301 Moved Permanently"); 
			header("Location: ". FC_INC_DIR . "/$fct_slug"); 
			header("Connection: close"); 
		}
}


/* pagination f.e. /p/2/ or /p/3/ .... */
if($array_mod_slug[0] == $pub_preferences['url_separator_pages'] && is_numeric($array_mod_slug[1])) {
	$pb_posts_start = $array_mod_slug[1];
}






/* Mode: post or archive by Date */
if(is_numeric($array_mod_slug[0])) {
	$pub_display_mode = 'list_archive_year';
	$time_string_start = strtotime("$array_mod_slug[0]-01-01");
	$time_string_end = strtotime("$array_mod_slug[0]-01-01" . '+12 month');
	$pb_posts_start = (int) $array_mod_slug[2];
	
	if(is_numeric($array_mod_slug[1])) {
		$pub_display_mode = 'list_archive_month';
		$time_string_start = strtotime("$array_mod_slug[0]-$array_mod_slug[1]");
		$time_string_end = strtotime(date("Y-m", strtotime("$array_mod_slug[0]-$array_mod_slug[1]")) . " +1 month");
		$pb_posts_start = (int) $array_mod_slug[3];
		
		if(is_numeric($array_mod_slug[2])) {
			$pub_display_mode = 'list_archive_day';
			$time_string_start = strtotime("$array_mod_slug[0]-$array_mod_slug[1]-$array_mod_slug[2]");
			$time_string_end = strtotime(date("Y-m-d", strtotime("$array_mod_slug[0]-$array_mod_slug[1]-$array_mod_slug[2]")) . " +1 day");
			$pb_posts_start = (int) $array_mod_slug[4];
			
			if($array_mod_slug[3] != "" && $array_mod_slug[3] != $pub_preferences['url_separator_pages']) {
				$pub_display_mode = 'show_post';
				$get_post = $mod_slug;
			}
		}
	}
}




/* Load frontend inline css and commit to theme */
if($pub_preferences['ignore_inline_css'] != 'ignore') {
	$styles_tpl = file_get_contents('modules/publisher.mod/'.$pub_tpl_dir.'/css/frontend.css');
	$styles_tpl = str_replace(array("\r", "\r\n", "\n", "\t"), '', $styles_tpl);
	$styles = '<style type="text/css">'.$styles_tpl.'</style>';
	$modul_head_enhanced = $styles;
}





/* show post by filename */
if(substr("$mod_slug", -5) == '.html') {
	$get_post = (int) basename(end(explode("-", $mod_slug)));
}

if($get_post != "") {
	$pub_display_mode = 'show_post';
}

switch ($pub_display_mode) {
    case "show_post":
        include 'frontend/show_post.php';
        break;
    case "list_posts_by_category":
        include 'frontend/list_posts.php';
        break;
   default:
        include 'frontend/list_posts.php';
}
	


?>