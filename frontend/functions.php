<?php

/**
 * publisher functions for frontend
 *
 */

function set_pagination_query($display_mode,$start) {
	
	global $fct_slug;
	global $pb_posts_filter;
	global $pub_preferences;
	global $array_mod_slug;
	
	if($display_mode == 'list_posts_by_category') {
		$pagination_link = "/$fct_slug".$pub_preferences['url_separator_categories'].'/'.$pb_posts_filter['categories'].'/'.$pub_preferences['url_separator_pages'].'/'."$start/";
	} else if($display_mode == 'list_archive_year') {
		$pagination_link = "/$fct_slug".$array_mod_slug[0].'/'.$pub_preferences['url_separator_pages'].'/'."$start/";
	} else if($display_mode == 'list_archive_month') {
		$pagination_link = "/$fct_slug".$array_mod_slug[0].'/'.$array_mod_slug[1].'/'.$pub_preferences['url_separator_pages'].'/'."$start/";
	} else if($display_mode == 'list_archive_day') {
		$pagination_link = "/$fct_slug".$array_mod_slug[0].'/'.$array_mod_slug[1].'/'.$array_mod_slug[2].'/'.$pub_preferences['url_separator_pages'].'/'."$start/";
	} else {
		$pagination_link = "/$fct_slug".$pub_preferences['url_separator_pages'].'/'."$start/";
	}

	
	return $pagination_link;
}



function get_post_categories() {
	
	global $mod;
	
	$dbh = new PDO("sqlite:".$mod['database']);	
	$sql = "SELECT name_safe, name FROM categories ORDER BY id ASC";
	$stmt = $dbh -> query($sql);
	$cats = $stmt -> fetchAll(PDO::FETCH_GROUP|PDO::FETCH_COLUMN);
	
	$dbh = null;
	return($cats);
}


function pub_list_gallery_thumbs($fp,$nbr='all') {
	
	$fp = $fp.'*_tmb.jpg';

	$thumbs_array = glob("$fp");
	arsort($thumbs_array);
	print_r($thumbs_array);

	$thumbs = '';
	foreach($thumbs_array as $tmb) {
		$thumbs .= '<div class="tmb">';
		$thumbs .= '<div class="tmb-preview"><img src="'.$tmb.'" class="img-fluid"></div>';
		$thumbs .= '</div>';
	}
	
	
	$str = '';
	$str .= $thumbs;
	
	return $str;
		
}



?>