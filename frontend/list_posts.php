<?php

/**
 * publisher.mod
 * list posts
 *
 */


/* individual settings from $page_contents */
$addon_data = str_replace('&quot;', '"', $page_contents['page_addon_string']);
$addon_data = utf8_encode($addon_data);
$addon_data = json_decode($addon_data,true, 512, JSON_UNESCAPED_UNICODE);

/* overwrite entries per page */
if(is_numeric($addon_data['entries_per_page'])) {
	$pb_posts_limit = $addon_data['entries_per_page'];
}

/* overwrite filter by type if one of them is not empty */
if($addon_data['type_filter_messages'] != '' OR $addon_data['type_filter_image'] != '' OR
		$addon_data['type_filter_link'] != '' OR $addon_data['type_filter_video'] != '' OR
		$addon_data['type_filter_events'] != '' OR $addon_data['type_filter_product'] != '' OR 
		$addon_data['type_filter_gallery'] != '' ){
			
		$pb_posts_filter['types'] =
		$addon_data['type_filter_messages'].'-'.$addon_data['type_filter_image'].'-'.$addon_data['type_filter_link'].'-'.$addon_data['type_filter_video'].'-'.$addon_data['type_filter_events'].'-'.$addon_data['type_filter_product'].'-'.$addon_data['type_filter_gallery'];
		$pb_posts_filter['types'] = implode('-',array_unique(explode('-', $pb_posts_filter['types'])));
}
		
$sql_start = ($pb_posts_start*$pb_posts_limit)-$pb_posts_limit;
if($sql_start < 0) {
	$sql_start = 0;
}
$get_posts = pub_get_entries($sql_start,$pb_posts_limit,$pb_posts_filter);
$cnt_filter_posts = $get_posts[0]['cnt_posts'];
$cnt_get_posts = count($get_posts);
//$cnt_posts = pub_cnt_entries();

$nextPage = $pb_posts_start+$pb_posts_limit;
$prevPage = $pb_posts_start-$pb_posts_limit;
$cnt_pages = ceil($cnt_filter_posts / $pb_posts_limit);


/* pagination */

$pag_list_tpl = file_get_contents("modules/publisher.mod/$pub_tpl_dir/templates/pagination_list.tpl");
$pag_list = '';
$arr_pag = array();

for($i=0;$i<$cnt_pages;$i++) {
	
	$active_class = '';
	$set_start = $i+1;
	
	if($i == 0 && $pb_posts_start < 1) {
		$set_start = 1;
		$active_class = 'active';
	}
	
	
	if($set_start == $pb_posts_start) {
		$active_class = 'active';
		$current_page = $set_start;
	}
	
	$pagination_link = set_pagination_query($pub_display_mode,$set_start);
	
	$pag_list_item = $pag_list_tpl;
	$pag_list_item = str_replace("{pag_href}", $pagination_link, $pag_list_item);
	$pag_list_item = str_replace("{pag_nbr}", $set_start, $pag_list_item);
	$pag_list_item = str_replace("{pag_active_class}", $active_class, $pag_list_item);
	$arr_pag[] = $pag_list_item;
	
}

$pag_start = $current_page-4;

if($pag_start < 0) { $pag_start = 0; }
$arr_pag = array_slice($arr_pag, $pag_start, 5);

foreach($arr_pag as $pag) {
	$pag_list .= $pag;
}

$nextstart = $pb_posts_start+1;
$prevstart = $pb_posts_start-1;

$older_link_query = set_pagination_query($pub_display_mode,$nextstart);
$newer_link_query = set_pagination_query($pub_display_mode,$prevstart);

if($prevstart < 1) {
	$prevstart = 1;
	$newer_link_query = '#';
}

if($nextstart > $cnt_pages) {
	$older_link_query = '#';
}


$pagination = file_get_contents("modules/publisher.mod/$pub_tpl_dir/templates/pagination.tpl");
$pagination = str_replace("{pag_prev_href}", $newer_link_query, $pagination);
$pagination = str_replace("{pag_next_href}", $older_link_query, $pagination);
$pagination = str_replace("{pagination_list}", $pag_list, $pagination);

$show_start = $sql_start+1;
$show_end = $show_start+($pb_posts_limit-1);

if($show_end > $cnt_filter_posts) {
	$show_end = $cnt_filter_posts;
}

//eol pagination


/* get the intro, if needed */
$intro_snippet = '';
if(($pub_preferences['intro_snippet'] != 'use_standard') AND ($pub_preferences['intro_snippet'] != '') AND ($show_start == '1')) {
	
	$intro_snippet = basename($pub_preferences['intro_snippet']);
	$db = new PDO("sqlite:$fc_db_content");
	$sql = "SELECT * FROM fc_textlib WHERE textlib_name = '$intro_snippet' AND textlib_lang LIKE '%$languagePack%'";
	$snippet = $db->query($sql);
	$snippet = $snippet->fetch(PDO::FETCH_ASSOC);	
	$intro_snippet = $snippet['textlib_content'];
}







/* generate list of posts */

$post_list = '';

for($i=0;$i<$cnt_get_posts;$i++) {
	
	$tpl = 'list_post_message.tpl';
	
	$post_releasedate = date('Y-m-d',$get_posts[$i]['releasedate']);
	$post_releasedate_year = date('Y',$get_posts[$i]['releasedate']);
	$post_releasedate_month = date('m',$get_posts[$i]['releasedate']);
	$post_releasedate_day = date('d',$get_posts[$i]['releasedate']);
	$post_releasedate_time = date('H:i:s',$get_posts[$i]['releasedate']);
	
	$post_id = $get_posts[$i]['id'];
	$post_slug = $get_posts[$i]['slug'];
	

	/* event dates */
	$event_start_day = date('d',$get_posts[$i]['startdate']);
	$event_start_month = date('m',$get_posts[$i]['startdate']);
	$event_start_month_text = $pub_lang["m$event_start_month"];
	$event_start_year = date('Y',$get_posts[$i]['startdate']);
	$event_end_day = date('d',$get_posts[$i]['enddate']);
	$event_end_month = date('m',$get_posts[$i]['enddate']);
	$event_end_year = date('Y',$get_posts[$i]['enddate']);
	
	/* entry date */
	$entrydate_year = date('Y',$get_posts[$i]['date']);
	
	
	/* post images */
	$first_post_image = '';
	$post_images = explode("<->", $get_posts[$i]['images']);

	if($post_images[1] != "") {
		$first_post_image = '/' . $img_path . '/' . str_replace('../content/images/','',$post_images[1]);
	} else if($pub_preferences['default_banner'] == "" OR $pub_preferences['default_banner'] == "use_standard") {
		$first_post_image = FC_INC_DIR ."/modules/publisher.mod/$pub_tpl_dir/images/no-image.png";
	} else if($pub_preferences['default_banner'] == "without_image") {
		$tpl = 'list_post_message_wo_image.tpl';
	} else {
		$first_post_image = "/$img_path/" . $pub_preferences['default_banner'];
	}
		
	/* type of post */
	if($get_posts[$i]['type'] == 'event') {
		$tpl = 'list_post_event.tpl';
	} else if($get_posts[$i]['type'] == 'link') {
		$tpl = 'list_post_link.tpl';
	} else if($get_posts[$i]['type'] == 'product') {
		$tpl = 'list_post_product.tpl';
	} else if($get_posts[$i]['type'] == 'video') {
		$tpl = 'list_post_video.tpl';
		$vURL = parse_url($get_posts[$i]['video_url']);
		parse_str($vURL['query'],$video);
	} else if($get_posts[$i]['type'] == 'image') {
		$tpl = 'list_post_image.tpl';
	} else if($get_posts[$i]['type'] == 'gallery') {
		$tpl = 'list_post_gallery.tpl';
		
		$gallery_dir = 'content/publisher/galleries/'.$entrydate_year.'/gallery'.$get_posts[$i]['id'].'/';	
		$fp = $gallery_dir.'*_tmb.jpg';
		$tmb_tpl = file_get_contents("modules/publisher.mod/$pub_tpl_dir/templates/thumbnail.tpl");
		$thumbs_array = glob("$fp");
		arsort($thumbs_array);
		$cnt_thumbs_array = count($thumbs_array);
		if($cnt_thumbs_array > 0) {
			$thumbnails_str = '';
			$x = 0;
			foreach($thumbs_array as $tmb) {
				$x++;
				$tmb_str = $tmb_tpl;
				
				$tmb_src = '/'.$tmb;
				$img_src = str_replace('_tmb','_img',$tmb_src);
				$tmb_str = str_replace('{tmb_src}', $tmb_src, $tmb_str);
				$tmb_str = str_replace('{img_src}', $img_src, $tmb_str);
				$thumbnails_str .= $tmb_str;
				
				if($x == 5) {
					break;
				}
				
			}
		}
	}
	
	$get_tpl = file_get_contents("modules/publisher.mod/$pub_tpl_dir/templates/$tpl");
	
	
	/* post categories */
	$tpl_link = file_get_contents("modules/publisher.mod/$pub_tpl_dir/templates/link_categories.tpl");
	$array_categories = explode("<->", $get_posts[$i]['categories']);
	$cat_links = '';
	$cat_links_string = '';
	foreach ($array_categories as $value) {
		unset($cat_link_href,$category_name);
		$category_name = $all_cats[$value][0];
		$cat_link_href = FC_INC_DIR . "/$fct_slug" . $pub_preferences['url_separator_categories'] . "/$value/";
		if($value != '') {
	    $cat_links = $tpl_link;
	    $cat_links = str_replace('{href}',$cat_link_href,$cat_links);
	    $cat_links = str_replace('{text}',$category_name,$cat_links);
	    $cat_links_string .= $cat_links;
	  }
	}
	
	if($pub_preferences['url_pattern'] == "by_filename") {
		$post_slug = basename($post_slug);
		$post_href = FC_INC_DIR . "/$fct_slug" . "$post_slug-$post_id.html";
	} else {
		$post_href = FC_INC_DIR . "/$fct_slug" . "$post_slug";
	}
	
	
	$post_price_gross = $get_posts[$i]['product_price_net']*($get_posts[$i]['product_tax']+100)/100;;
	$post_price_gross = pub_print_currency($post_price_gross);
	
	$post_tpl = str_replace("{post_title}", $get_posts[$i]['title'], $get_tpl);
	$post_tpl = str_replace("{post_author}", $get_posts[$i]['author'], $post_tpl);
	$post_tpl = str_replace("{post_teaser}", $get_posts[$i]['teaser'], $post_tpl);
	$post_tpl = str_replace("{post_type}", $get_posts[$i]['type'], $post_tpl);
	$post_tpl = str_replace("{post_img_src}", $first_post_image, $post_tpl);
	$post_tpl = str_replace("{video_id}", $video['v'], $post_tpl);
	
	$post_tpl = str_replace("{post_releasedate_ts}", $get_posts[$i]['releasedate'], $post_tpl); /* timestring */
	$post_tpl = str_replace("{post_releasedate}", $post_releasedate, $post_tpl);
	$post_tpl = str_replace("{post_releasedate_year}", $post_releasedate_year, $post_tpl);
	$post_tpl = str_replace("{post_releasedate_month}", $post_releasedate_month, $post_tpl);
	$post_tpl = str_replace("{post_releasedate_day}", $post_releasedate_day, $post_tpl);
	$post_tpl = str_replace("{post_releasedate_time}", $post_releasedate_time, $post_tpl);
	$post_tpl = str_replace("{event_start_day}", $event_start_day, $post_tpl);
	$post_tpl = str_replace("{event_start_month}", $event_start_month, $post_tpl);
	$post_tpl = str_replace("{event_start_month_text}", $event_start_month_text, $post_tpl);
	$post_tpl = str_replace("{event_start_year}", $event_start_year, $post_tpl);
	$post_tpl = str_replace("{event_end_day}", $event_end_day, $post_tpl);
	$post_tpl = str_replace("{event_end_month}", $event_end_month, $post_tpl);
	$post_tpl = str_replace("{event_end_year}", $event_end_year, $post_tpl);
	
	
	$post_tpl = str_replace("{post_href}", $post_href, $post_tpl);
	$post_tpl = str_replace("{post_cats}", $cat_links_string, $post_tpl);
	$post_tpl = str_replace("{read_more_text}", $pub_lang['btn_read_more'], $post_tpl);
	$post_tpl = str_replace("{post_external_link}", $get_posts[$i]['link'], $post_tpl);
	
	$post_tpl = str_replace("{post_price_gross}", $post_price_gross, $post_tpl);
	$post_tpl = str_replace("{post_currency}", $get_posts[$i]['product_currency'], $post_tpl);
	$post_tpl = str_replace("{post_product_unit}", $get_posts[$i]['product_unit'], $post_tpl);
	$post_tpl = str_replace("{post_product_price_label}", $get_posts[$i]['product_price_label'], $post_tpl);
	
	$post_tpl = str_replace("{post_thumbnails}", $thumbnails_str, $post_tpl);
	
	
	$post_list .= $post_tpl;
	
}


$index_tpl = file_get_contents("modules/publisher.mod/$pub_tpl_dir/templates/index.tpl");
$index_tpl = str_replace("{post_list}", $post_list, $index_tpl);
$index_tpl = str_replace("{intro_snippet}", $intro_snippet, $index_tpl);
$index_tpl = str_replace("{pagination}", $pagination, $index_tpl);

$index_tpl = str_replace("{post_start_nbr}", $show_start, $index_tpl);
$index_tpl = str_replace("{post_end_nbr}", $show_end, $index_tpl);
$index_tpl = str_replace("{post_cnt}", $cnt_filter_posts, $index_tpl);
$index_tpl = str_replace("{lang_entries}", $pub_lang['entries'], $index_tpl);
$index_tpl = str_replace("{lang_entries_cnt}", $pub_lang['entries_cnt'], $index_tpl);
$index_tpl = str_replace("{category_filter}", $cat_name, $index_tpl);


if($cnt_filter_posts < 1) {
	$index_tpl = '<div class="alert alert-info">'.$pub_lang['msg_no_posts_to_show'].'</div>';
}


$modul_content = $index_tpl;

?>