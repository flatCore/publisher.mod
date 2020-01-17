<?php

/**
 * publisher.mod
 * show post
 *
 * $get_post - id or string
 *
 */

//error_reporting(E_ALL ^E_NOTICE);

$post_data = pub_get_post_data($get_post);
$post_images = explode("<->", $post_data['images']);
$post_type = $post_data['type'];
$post_releasedate = date('Y-m-d H:i',$post_data['releasedate']);
$post_lastedit = date('Y-m-d H:i',$post_data['lastedit']);
$post_lastedit_from = $post_data['lastedit_from'];

/* event dates */

$event_start_day = date('d',$post_data['startdate']);
$event_start_month = date('m',$post_data['startdate']);
$event_start_month_text = $pub_lang["m$event_start_month"];
$event_start_year = date('Y',$post_data['startdate']);
$event_end_day = date('d',$post_data['enddate']);
$event_end_month = date('m',$post_data['enddate']);
$event_end_year = date('Y',$post_data['enddate']);

/* entry date */
$entrydate_year = date('Y',$post_data['date']);


/* images */

if($post_images[1] != "") {
	$first_post_image = '/' . $img_path . '/' . str_replace('../content/images/','',$post_images[1]);
} elseif($pub_preferences['default_banner'] == "" OR $pub_preferences['default_banner'] == "use_standard") {
	$first_post_image = FC_INC_DIR ."/modules/publisher.mod/$pub_tpl_dir/images/no-image.png";
} else {
	$first_post_image = "/$img_path/" . $pub_preferences['default_banner'];
}



if($post_type == 'image') {
	$tpl = file_get_contents("modules/publisher.mod/$pub_tpl_dir/templates/post_image.tpl");
} else if ($post_type == 'gallery') {
	
	$tpl = file_get_contents("modules/publisher.mod/$pub_tpl_dir/templates/post_gallery.tpl");
	$gallery_dir = 'content/publisher/galleries/'.$entrydate_year.'/gallery'.$post_data['id'].'/';
	$fp = $gallery_dir.'*_tmb.jpg';
	$tmb_tpl = file_get_contents("modules/publisher.mod/$pub_tpl_dir/templates/thumbnail.tpl");
	$thumbs_array = glob("$fp");
	arsort($thumbs_array);
	$cnt_thumbs_array = count($thumbs_array);
	if($cnt_thumbs_array > 0) {
		$thumbnails_str = '';
		
		foreach($thumbs_array as $tmb) {
			$tmb_str = $tmb_tpl;
			
			$tmb_src = '/'.$tmb;
			$img_src = str_replace('_tmb','_img',$tmb_src);
			$tmb_str = str_replace('{tmb_src}', $tmb_src, $tmb_str);
			$tmb_str = str_replace('{img_src}', $img_src, $tmb_str);
			$thumbnails_str .= $tmb_str;
			
		}
	}
	
} else if ($post_type == 'video') {
	$tpl = file_get_contents("modules/publisher.mod/$pub_tpl_dir/templates/post_video.tpl");
	$vURL = parse_url($post_data['video_url']);
	parse_str($vURL['query'],$video);
	//$video['v'] -> youtube video id
} elseif($post_type == 'link') {
	$tpl = file_get_contents("modules/publisher.mod/$pub_tpl_dir/templates/post_link.tpl");
} else if($post_type == 'event') {
	$tpl = file_get_contents("modules/publisher.mod/$pub_tpl_dir/templates/post_event.tpl");
} else if ($post_type == 'product') {
	$tpl = file_get_contents("modules/publisher.mod/$pub_tpl_dir/templates/post_product.tpl");
} else {
	$tpl = file_get_contents("modules/publisher.mod/$pub_tpl_dir/templates/post_message.tpl");
	if($pub_preferences['default_banner'] == "without_image" && $post_images[1] == "") {
		$tpl = file_get_contents("modules/publisher.mod/$pub_tpl_dir/templates/post_message_wo_image.tpl");
	}
}


/* post categories */
$tpl_link = file_get_contents("modules/publisher.mod/$pub_tpl_dir/templates/link_categories.tpl");
$array_categories = explode("<->", $post_data['categories']);
$cat_links = '';
$cat_links_string = '';
foreach ($array_categories as $value) {
	unset($cat_link_href,$cat_name);
	$cat_name = $all_cats[$value][0];
	$cat_link_href = FC_INC_DIR . "/$fct_slug" . $pub_preferences['url_separator_categories'] . "/$value/";
	if($value != '') {
    $cat_links = $tpl_link;
    $cat_links = str_replace('{href}',$cat_link_href,$cat_links);
    $cat_links = str_replace('{text}',$cat_name,$cat_links);
    $cat_links_string .= $cat_links;
  }
}



$post_price_gross = $post_data['product_price_net']*($post_data['product_tax']+100)/100;;
$post_price_gross = pub_print_currency($post_price_gross);



/* increase hits */

$hits = (int) $post_data['hits'];
$hits++;
$sql_update = "UPDATE posts SET	hits = :hits WHERE id = :id ";

$dbh = new PDO("sqlite:".$mod['database']);	
$sth = $dbh->prepare($sql_update);
$sth->bindParam(':id', $post_data['id'], PDO::PARAM_INT);
$sth->bindParam(':hits', $hits, PDO::PARAM_INT);
$sth->execute();
$dbh = NULL;



$post_tpl = str_replace("{post_author}", $post_data['author'], $tpl);
$post_tpl = str_replace("{post_title}", $post_data['title'], $post_tpl);
$post_tpl = str_replace("{post_teaser}", $post_data['teaser'], $post_tpl);
$post_tpl = str_replace("{post_text}", $post_data['text'], $post_tpl);
$post_tpl = str_replace("{post_type}", $post_data['type'], $post_tpl);
$post_tpl = str_replace("{post_img_src}", $first_post_image, $post_tpl);
$post_tpl = str_replace("{post_releasedate}", $post_releasedate, $post_tpl);
$post_tpl = str_replace("{post_lastedit}", $post_lastedit, $post_tpl);
$post_tpl = str_replace("{post_lastedit_from}", $post_lastedit_from, $post_tpl);
$post_tpl = str_replace("{event_start_day}", $event_start_day, $post_tpl);
$post_tpl = str_replace("{event_start_month}", $event_start_month, $post_tpl);
$post_tpl = str_replace("{event_start_month_text}", $event_start_month_text, $post_tpl);
$post_tpl = str_replace("{event_start_year}", $event_start_year, $post_tpl);
$post_tpl = str_replace("{event_end_day}", $event_end_day, $post_tpl);
$post_tpl = str_replace("{event_end_month}", $event_end_month, $post_tpl);
$post_tpl = str_replace("{event_end_year}", $event_end_year, $post_tpl);
$post_tpl = str_replace("{video_id}", $video['v'], $post_tpl);
$post_tpl = str_replace("{post_external_link}", $post_data['link'], $post_tpl);
$post_tpl = str_replace("{post_cats}", $cat_links_string, $post_tpl);
$post_tpl = str_replace("{back_to_overview}", $pub_lang['back_to_overview'], $post_tpl);
$post_tpl = str_replace("{back_link}", "/$fct_slug", $post_tpl);

$post_tpl = str_replace("{post_price_gross}", $post_price_gross, $post_tpl);
$post_tpl = str_replace("{post_currency}", $post_data['product_currency'], $post_tpl);
$post_tpl = str_replace("{post_product_unit}", $post_data['product_unit'], $post_tpl);

$post_tpl = str_replace("{post_thumbnails}", $thumbnails_str, $post_tpl);

$modul_content = "$post_tpl $tests";

$mod['page_title'] = $post_data['title'];
$mod['page_description'] = substr(strip_tags($post_data['teaser']),0,160);
$mod['page_keywords'] = $post_data['tags'];
$mod['page_thumbnail'] = '/'.$img_path.'/'.basename($first_post_image);

/* overwrite pages template */

if($pub_preferences['default_page_template_entries'] != 'use_standard') {
	$prefs_template_layout = basename($pub_preferences['default_page_template_entries']);
	$urlParts = explode('/', $pub_preferences['default_page_template_entries']);
	$prefs_template = $urlParts[0];
}

?>