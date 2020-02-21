<?php
/**
 * @modul	publisher
 * backend
 */

//error_reporting(E_ALL ^E_NOTICE);

if(!defined('FC_INC_DIR')) {
	die("No access");
}

include __DIR__.'/include.php';

/* set modus */

if((isset($_REQUEST['post_id'])) && is_numeric($_REQUEST['post_id'])) {
	
	$post_id = (int) $_REQUEST['post_id'];
	$modus = 'update';
	$post_data = pub_get_post_data($post_id);
	$submit_btn = '<input type="submit" class="btn btn-save btn-block" name="save_post" value="'.$lang['update'].'">';
	
} else {
	$post_id = '';
	$modus = 'new';
	$submit_btn = '<input type="submit" class="btn btn-save btn-block" name="save_post" value="'.$lang['save'].'">';

}


/* save or update post data */

if(isset($_POST['save_post']) OR isset($_POST['del_tmb']) OR isset($_POST['sort_tmb'])) {

	//$post_date = time();
	$post_releasedate = time();
	$post_lastedit = time();
	$post_lastedit_from = $_SESSION['user_nick'];
	
	if($_POST['post_date'] == "") {
		$_POST['post_date'] = time();
	}
		
	if($_POST['post_releasedate'] != "") {
		$post_releasedate = strtotime($_POST['post_releasedate']);
	}
	
	if($_POST['event_start'] != "") {
		$event_start = strtotime($_POST['event_start']);
	}
	
	if($_POST['event_end'] != "") {
		$event_end = strtotime($_POST['event_end']);
		if($event_end < $event_start) {
			$event_end = $event_start;
		}
	}
	
	$clean_title = clean_filename($_POST['post_title']);
	$post_date_year = date("Y",$post_releasedate);
	$post_date_month = date("m",$post_releasedate);
	$post_date_day = date("d",$post_releasedate);


	if($_POST['post_slug'] == "") {
		$post_slug = "$post_date_year/$post_date_month/$post_date_day/$clean_title/";
	} else {
		$post_slug = "$post_date_year/$post_date_month/$post_date_day/".$_POST['post_slug']."/";
		$post_slug = preg_replace('#\/{2,}#','/',$post_slug);
	}

	$post_languages = @implode("<->", $_POST['post_languages']);
	$post_categories = @implode("<->", $_POST['post_categories']);
	$post_images_string = @implode("<->", $_POST['post_images']);
	$post_images_string = "<->$post_images_string<->";
	
	$product_price_net = str_replace('.', '', $_POST['post_product_price_net']);
	$product_price_net = str_replace(',', '.', $product_price_net);
	
	/* gallery thumbnails */
	if($_POST['del_tmb'] != '') {
		$del_tmb = $_POST['del_tmb'];
		$del_img = str_replace('_tmb','_img',$del_tmb);
		unlink($del_tmb);
		unlink($del_img);
	}
	
	if($_POST['sort_tmb'] != '') {
		pub_rename_image($_POST['sort_tmb']);
	}
	
	$dbh = new PDO("sqlite:$mod_db");

	/* build url for rss feed */
	if($_POST['rss_url'] == '') {
		
		if($pub_preferences['url_pattern'] == 'by_filename') {
			
			$file_suffix = '-'.$post_id;
			
			if($post_id == '') {
				// we have no id, so we get the last id from db and add +1
				$sql_id = 'SELECT id FROM posts ORDER BY id DESC LIMIT 0 , 1';
				$last_id = $dbh->query($sql_id)->fetch();
				$next_id = $last_id['id']+1;
				$file_suffix = '-'.$next_id;
			}
			
			$rss_url = $pub_preferences['url'] . substr("$post_slug", 11,-1) .$file_suffix.'.html';
			
			
		} else {
			$rss_url = $pub_preferences['url'].$post_slug;
		}
		
	} else {
		$rss_url = $_POST['rss_url'];
	}






	
	require '../modules/publisher.mod/install/tpl-posts.php';
	
	if($modus == "update")	{
		
		
		$sql = generate_sql_update_str($cols,$table_name,'WHERE id = :id');
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':id', $_POST['post_id'], PDO::PARAM_INT);
		
	} else {
		
		$sql = generate_sql_insert_str($cols,$table_name);
		$sth = $dbh->prepare($sql);
		
	}
	
	foreach($cols as $k => $v) {
  	$par = ":$k";
  	$var = 'post_'.$k;
  	
  	if($k == 'id') {
	  	continue;
  	}
  	
  	$sth->bindParam($par, $_POST[$var], PDO::PARAM_STR);
  }
	

	$sth->bindParam(':releasedate', $post_releasedate, PDO::PARAM_STR);
	$sth->bindParam(':startdate', $event_start, PDO::PARAM_STR);
	$sth->bindParam(':enddate', $event_end, PDO::PARAM_STR);
	$sth->bindParam(':images', $post_images_string, PDO::PARAM_STR);
	$sth->bindParam(':lang', $post_languages, PDO::PARAM_STR);
	$sth->bindParam(':categories', $post_categories, PDO::PARAM_STR);
	$sth->bindParam(':slug', $post_slug, PDO::PARAM_STR);
	$sth->bindParam(':rss_url', $rss_url, PDO::PARAM_STR);
	$sth->bindParam(':lastedit', $post_lastedit, PDO::PARAM_STR);
	$sth->bindParam(':lastedit_from', $post_lastedit_from, PDO::PARAM_STR);
	$sth->bindParam(':product_price_net', $product_price_net, PDO::PARAM_STR);
	
	$cnt_changes = $sth->execute();
	
	if($modus == "new")	{
		$post_id = $dbh->lastInsertId();
	} else {
		$post_id = $_POST['post_id'];
	}
	
	if($cnt_changes == TRUE){
		$sys_message = '{OKAY} ' . $lang['db_changed'];
		record_log($_SESSION['user_nick'],"article ($modus) <i>".$_POST['post_title']."</i>",'0');
		$post_data = pub_get_post_data($post_id);
		$modus = 'update';		
	} else {
		$sys_message = '{ERROR} ' . $lang['db_not_changed'];
		print_r($dbh->errorInfo());
	}
	
	$dbh = NULL;
	
	print_sysmsg("$sys_message");

	if($_POST['rss'] == "on") {	
		add_feed($_POST['post_title'],$_POST['post_teaser'],"$rss_url","publisher$post_id","",$post_releasedate);
	}

}


if(isset($_GET['new'])) {
	$show_type = $_GET['new'];
}

if($post_data['type'] != '') {
	$show_type = $post_data['type'];
}




echo '<h3>'.$mod_name.' '.$mod_version.' <small>| '.$modus.' '.$show_type.'</small></h3>';


if($modus != 'update' && !isset($_GET['new'])) {
	echo '<fieldset class="mt-3">';
	echo '<legend>'.$pub_lang['select_post_type'].'</legend>';
	echo '<div class="btn-group text-right" role="group">';
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=edit&new=message" class="btn btn-fc '.$btn_type['message'].'">'.$pub_lang['type_message'].'</a>';
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=edit&new=event" class="btn btn-fc '.$btn_type['event'].'">'.$pub_lang['type_event'].'</a>';
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=edit&new=image" class="btn btn-fc '.$btn_type['image'].'">'.$pub_lang['type_image'].'</a>';
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=edit&new=gallery" class="btn btn-fc '.$btn_type['gallery'].'">'.$pub_lang['type_gallery'].'</a>';
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=edit&new=video" class="btn btn-fc '.$btn_type['video'].'">'.$pub_lang['type_video'].'</a>';
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=edit&new=link" class="btn btn-fc '.$btn_type['link'].'">'.$pub_lang['type_link'].'</a>';
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=edit&new=product" class="btn btn-fc '.$btn_type['product'].'">'.$pub_lang['type_product'].'</a>';
	echo '</div>';
	echo '</fieldset>';
}



$images = fc_scandir_rec('../'.FC_CONTENT_DIR.'/images');
$arr_lang = get_all_languages();
$cats = pub_get_categories();

foreach($images as $img) {
	$filemtime = date ("Y", filemtime("$img"));
	$all_images[] = array('name' => $img, 'dateY' => $filemtime);
}

foreach ($all_images as $key => $row) {
	$date[$key]  = $row['dateY'];
  $name[$key] = $row['name'];
}

/* we sort the images from new to old and from a to z */
array_multisort($date, SORT_DESC, $name, SORT_ASC, $all_images);

/* images */
$array_images = explode("<->", $post_data['images']);
$choose_images = '<select multiple="multiple" class="image-picker show-html" name="post_images[]">';

/* if we have selected images, show them first */
if(count($array_images)>1) {
	$choose_images .= '<optgroup label="'.$pub_lang['label_image_selected'].'">';
	foreach($array_images as $sel_images) {
		if(is_file("$sel_images")) {
			$choose_images .= '<option data-img-src="'.$sel_images.'" value="'.$sel_images.'" selected>'.basename($sel_images).'</option>'."\r\n";
		}
	}
	$choose_images .= '</optgroup>'."\r\n";
}

for($i=0;$i<count($all_images);$i++) {
	
	$img_filename = basename($all_images[$i]['name']);
	$image_name = $all_images[$i]['name'];
	$imgsrc = "../$img_path/$all_images[$i][name]";	
	$filemtime = $all_images[$i]['dateY'];
	
	if($ft_prefs_image_prefix != "") {
		if((strpos($image_name, $ft_prefs_image_prefix)) === false) {
			continue;
		}
	}
	/* new label for each year */
	if($all_images[$i-1]['dateY'] != $filemtime) {	
		if($i == 0) {
			$choose_images .= '<optgroup label="'.$filemtime.'">'."\r\n";
		} else {
			$choose_images .= '</optgroup><optgroup label="'.$filemtime.'">'."\r\n";
		}
	}
	
	if(!in_array($image_name, $array_images)) {
		$choose_images .= '<option data-img-src="'.$image_name.'" value="'.$image_name.'">'.$img_filename.'</option>'."\r\n";
	}
	
}
$choose_images .= '</optgroup>'."\r\n";
$choose_images .= '</select>'."\r\n";


/* categories */
for($i=0;$i<count($cats);$i++) {
	$category = $cats[$i]['name'];
	$category_safe = $cats[$i]['name_safe'];
	$array_categories = explode("<->", $post_data['categories']);
	$checked = "";
	if(in_array("$category_safe", $array_categories)) {
	    $checked = "checked";
	}
	$checkboxes_cat .= '<div class="form-check">';
	$checkboxes_cat .= '<input class="form-check-input" id="cat'.$i.'" type="checkbox" name="post_categories[]" value="'.$category_safe.'" '.$checked.'>';
	$checkboxes_cat .= '<label class="form-check-label" for="cat'.$i.'">'.$category.'</label>';
	$checkboxes_cat .= '</div>';
}


/* release date */
if($post_data['releasedate'] > 0) {
	$post_releasedate = date('Y-m-d H:i:s', $post_data['releasedate']);
} else {
	$post_releasedate = date('Y-m-d H:i:s', time());
}

/* event dates */
if($post_data['startdate'] > 0) {
	$post_startdate = date('Y-m-d H:i:s', $post_data['startdate']);
} else {
	$post_startdate = date('Y-m-d H:i:s', time());
}
if($post_data['enddate'] > 0) {
	$post_enddate = date('Y-m-d H:i:s', $post_data['enddate']);
} else {
	$post_enddate = date('Y-m-d H:i:s', time());
}

/* slug */
$post_slug = substr($post_data['slug'], 11);

/* author */
if($post_data['author'] == "") {
	$post_data['author'] = $_SESSION['user_firstname'] .' '.$_SESSION['user_lastname'];
}


/* priority */
$select_priority = "<select name='post_priority' class='form-control custom-select'>";
for($i=1;$i<11;$i++) {
	$option_add = '';
	$sel_prio = '';
	if($i == 1) {
		$option_add = ' ('.$pub_lang['label_priority_bottom'].')';
	}
	if($i == 10) {
		$option_add = ' ('.$pub_lang['label_priority_top'].')';
	}
	if($post_data['priority'] == $i) {
		$sel_prio = 'selected';
	}
	$select_priority .= '<option value="'.$i.'" '.$sel_prio.'>'.$i.' '.$option_add.'</option>';
}
$select_priority .= '</select>';



if($post_data['fixed'] == 'fixed') {
	$checked_fixed = 'checked';
}
$checkbox_fixed  = '<div class="form-check">';
$checkbox_fixed .= '<input class="form-check-input" id="fix" type="checkbox" name="post_fixed" value="fixed" '.$checked_fixed.'>';
$checkbox_fixed .= '<label class="form-check-label" for="fix">'.$pub_lang['label_fixed'].'</label>';
$checkbox_fixed .= '</div>';




/* status | draft or published */
if($post_data['status'] == "draft") {
	$sel_status1 = "selected";
} else {
	$sel_status2 = "selected";
}
$select_status = "<select name='post_status' class='form-control custom-select'>";
if($_SESSION['drm_can_publish'] == "true") {
	$select_status .= '<option value="draft" '.$sel_status1.'>'.$pub_lang['status_draft'].'</option>';
	$select_status .= '<option value="published" '.$sel_status2.'>'.$pub_lang['status_public'].'</option>';
} else {
	/* user can not publish */
	$select_status .= '<option value="draft" selected>'.$pub_lang['status_draft'].'</option>';
}
$select_status .= '</select>';




/* RSS */
if($post_data['rss'] == "on") {
	$sel1 = "selected";
} else {
	$sel2 = "selected";
}
$select_rss = "<select name='post_rss' class='form-control custom-select'>";
$select_rss .= '<option value="on" '.$sel1.'>'.$lang['yes'].'</option>';
$select_rss .= '<option value="off" '.$sel2.'>'.$lang['no'].'</option>';
$select_rss .=	'</select>';

/* languages */
for($i=0;$i<count($arr_lang);$i++) {
	$lang_folder = $arr_lang[$i]['lang_folder'];
	
	if(strpos($post_data['lang'], "$lang_folder") !== false) {
		$checked_lang = "checked";
	} else {
		$checked_lang = "";
	}
	
	if($post_data['lang'] == "" AND $lang_folder == "$_SESSION[lang]") {
		$checked_lang = "checked";
	}
	
	$checkboxes_lang .= '<div class="form-check form-check-inline">';
	$checkboxes_lang .= '<input class="form-check-input" id="'.$lang_folder.'" type="checkbox" name="post_languages[]" value="'.$lang_folder.'" '.$checked_lang.'>';
	$checkboxes_lang .= '<label class="form-check-label" for="'.$lang_folder.'">'.$lang_folder.'</label>';
	$checkboxes_lang .= '</div>';
} // eo $i




if($post_data['product_tax'] == '') {
	$set_tax = $pub_preferences['products_default_tax'];
} else {
	$set_tax = $post_data['product_tax'];
}

if($post_data['product_currency'] == '') {
	$set_currency = $pub_preferences['products_default_currency'];
} else {
	$set_currency = $post_data['product_currency'];
}

if(!empty($post_data['product_price_net'])) {
	$product_price_net = number_format($post_data['product_price_net'], 4, ',', '.');
}



if($post_data['type'] == 'message') {
	$form_tpl = file_get_contents(__DIR__.'/tpl/post_message.tpl');
} else if ($post_data['type'] == 'video') {
	$form_tpl = file_get_contents(__DIR__.'/tpl/post_video.tpl');
} else if ($post_data['type'] == 'image') {
	$form_tpl = file_get_contents(__DIR__.'/tpl/post_image.tpl');
} else if ($post_data['type'] == 'link') {
	$form_tpl = file_get_contents(__DIR__.'/tpl/post_link.tpl');
} else if ($post_data['type'] == 'event') {
	$form_tpl = file_get_contents(__DIR__.'/tpl/post_event.tpl');
} else if ($post_data['type'] == 'product') {
	$form_tpl = file_get_contents(__DIR__.'/tpl/post_product.tpl');
} else if ($post_data['type'] == 'gallery') {
	$form_tpl = file_get_contents(__DIR__.'/tpl/post_gallery.tpl');
	
	$form_upload_tpl = file_get_contents(__DIR__.'/tpl/gallery_upload_form.tpl');
	$form_upload_tpl = str_replace('{token}',$_SESSION['token'], $form_upload_tpl);
	$form_upload_tpl = str_replace('{post_id}',$post_data['id'], $form_upload_tpl);
	$form_upload_tpl = str_replace('{disabled_upload_btn}','disabled', $form_upload_tpl);
	
	$form_sort_tpl = file_get_contents(__DIR__.'/tpl/gallery_sort_form.tpl');
	
	$tmb_list = pub_list_gallery_thumbs($post_data['id']);
	$form_sort_tpl = str_replace('{thumbnail_list}',$tmb_list, $form_sort_tpl);
	$form_sort_tpl = str_replace('{post_id}',$post_data['id'], $form_sort_tpl);
}

if($_GET['new'] == 'message') {
	$form_tpl = file_get_contents(__DIR__.'/tpl/post_message.tpl');
	$post_data['type'] = 'message';
} else if ($_GET['new'] == 'video') {
	$form_tpl = file_get_contents(__DIR__.'/tpl/post_video.tpl');
	$post_data['type'] = 'video';
} else if ($_GET['new'] == 'image') {
	$form_tpl = file_get_contents(__DIR__.'/tpl/post_image.tpl');
	$post_data['type'] = 'image';
} else if ($_GET['new'] == 'link') {
	$form_tpl = file_get_contents(__DIR__.'/tpl/post_link.tpl');
	$post_data['type'] = 'link';
} else if ($_GET['new'] == 'event') {
	$form_tpl = file_get_contents(__DIR__.'/tpl/post_event.tpl');
	$post_data['type'] = 'event';
} else if ($_GET['new'] == 'product') {
	$form_tpl = file_get_contents(__DIR__.'/tpl/post_product.tpl');
	$post_data['type'] = 'product';
} else if ($_GET['new'] == 'gallery') {
	$form_tpl = file_get_contents(__DIR__.'/tpl/post_gallery.tpl');
	$post_data['type'] = 'gallery';
}

$post_data['text'] = htmlentities(stripslashes($post_data['text']), ENT_QUOTES, "UTF-8");
$post_data['teaser'] = htmlentities(stripslashes($post_data['teaser']), ENT_QUOTES, "UTF-8");

$form_tpl = str_replace('{post_title}', $post_data['title'], $form_tpl);
$form_tpl = str_replace('{post_teaser}', $post_data['teaser'], $form_tpl);
$form_tpl = str_replace('{post_text}', $post_data['text'], $form_tpl);
$form_tpl = str_replace('{post_link}', $post_data['link'], $form_tpl);
$form_tpl = str_replace('{post_type}', $post_data['type'], $form_tpl);
$form_tpl = str_replace('{post_author}', $post_data['author'], $form_tpl);
$form_tpl = str_replace('{post_slug}', $post_slug, $form_tpl);
$form_tpl = str_replace('{post_tags}', $post_data['tags'], $form_tpl);
$form_tpl = str_replace('{post_video_url}', $post_data['video_url'], $form_tpl);
$form_tpl = str_replace('{post_rss_url}', $post_data['rss_url'], $form_tpl);
$form_tpl = str_replace('{post_hidden}', $post_data['hidden'], $form_tpl);
$form_tpl = str_replace('{post_source}', $post_data['source'], $form_tpl);
$form_tpl = str_replace('{widget_images}', $choose_images, $form_tpl);
$form_tpl = str_replace('{submit_button}', $submit_btn, $form_tpl);
$form_tpl = str_replace('{token}', $_SESSION['token'], $form_tpl);
$form_tpl = str_replace('{post_id}', $post_id, $form_tpl);
$form_tpl = str_replace('{post_releasedate}', $post_releasedate, $form_tpl);
$form_tpl = str_replace('{event_start}', $post_startdate, $form_tpl);
$form_tpl = str_replace('{event_end}', $post_enddate, $form_tpl);
$form_tpl = str_replace('{post_date}', $post_data['date'], $form_tpl);

$form_tpl = str_replace('{post_product_number}', $post_data['product_number'], $form_tpl);
$form_tpl = str_replace('{post_product_manufacturer}', $post_data['product_manufacturer'], $form_tpl);
$form_tpl = str_replace('{post_product_supplier}', $post_data['product_supplier'], $form_tpl);
$form_tpl = str_replace('{post_product_tax}', $set_tax, $form_tpl);
$form_tpl = str_replace('{post_product_currency}', $set_currency, $form_tpl);
$form_tpl = str_replace('{post_product_price_net}', $product_price_net, $form_tpl);
$form_tpl = str_replace('{post_product_price_gross}', $product_price_gross, $form_tpl);
$form_tpl = str_replace('{post_product_unit}', $post_data['product_unit'], $form_tpl);

$form_tpl = str_replace('{select_priority}', $select_priority, $form_tpl);
$form_tpl = str_replace('{checkbox_fixed}', $checkbox_fixed, $form_tpl);
$form_tpl = str_replace('{select_status}', $select_status, $form_tpl);

$form_tpl = str_replace('{select_rss}', $select_rss, $form_tpl);
$form_tpl = str_replace('{checkboxes_lang}', $checkboxes_lang, $form_tpl);
$form_tpl = str_replace('{widget_categories}', $checkboxes_cat, $form_tpl);

$form_tpl = str_replace('{modal_upload_form}', $form_upload_tpl, $form_tpl);
$form_tpl = str_replace('{thumbnail_list_form}', $form_sort_tpl, $form_tpl);

foreach($pub_lang as $k => $v) {
	$form_tpl = str_replace('{'.$k.'}', $pub_lang[$k], $form_tpl);
}

$form_tpl = str_replace('{tab_intro}', $pub_lang['tab_intro'], $form_tpl);
$form_tpl = str_replace('{tab_content}', $pub_lang['tab_content'], $form_tpl);
$form_tpl = str_replace('{tab_preferences}', $pub_lang['tab_preferences'], $form_tpl);

$form_tpl = str_replace('{formaction}', 'acp.php?tn=moduls&sub=publisher.mod&a=edit', $form_tpl);


echo $form_tpl;


?>