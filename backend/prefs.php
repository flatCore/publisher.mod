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

echo '<h3>'.$mod_name.' '.$mod_version.' <small>| '.$pub_lang['nav_preferences'].'</small></h3>';


if($_POST['saveprefs'] == 'save') {
	
	$dbh = new PDO("sqlite:$mod_db");
	$sql = "UPDATE preferences
					SET entries_per_page = :entries_per_page,
							images_prefix = :images_prefix,
							default_banner = :default_banner,
							default_template = :default_template,
							default_page_template_entries = :default_page_template_entries,
							ignore_inline_css = :ignore_inline_css,
							intro_snippet = :intro_snippet,
							url = :url,
							url_separator_categories = :url_separator_categories,
							url_separator_pages = :url_separator_pages,
							url_pattern = :url_pattern,
							products_default_tax = :products_default_tax,
							products_default_currency = :products_default_currency,
							event_time_offset = :event_time_offset
					WHERE status = 'active' ";
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':entries_per_page', $_POST['entries_per_page'], PDO::PARAM_STR);
	$sth->bindParam(':images_prefix', $_POST['images_prefix'], PDO::PARAM_STR);
	$sth->bindParam(':default_banner', $_POST['default_banner'], PDO::PARAM_STR);
	$sth->bindParam(':default_template', $_POST['default_template'], PDO::PARAM_STR);
	$sth->bindParam(':default_page_template_entries', $_POST['default_page_template_entries'], PDO::PARAM_STR);
	$sth->bindParam(':ignore_inline_css', $_POST['ignore_inline_css'], PDO::PARAM_STR);
	$sth->bindParam(':intro_snippet', $_POST['intro_snippet'], PDO::PARAM_STR);
	$sth->bindParam(':url', $_POST['url'], PDO::PARAM_STR);
	$sth->bindParam(':url_pattern', $_POST['url_pattern'], PDO::PARAM_STR);
	$sth->bindParam(':url_separator_categories', $_POST['url_separator_categories'], PDO::PARAM_STR);
	$sth->bindParam(':url_separator_pages', $_POST['url_separator_pages'], PDO::PARAM_STR);
	$sth->bindParam(':products_default_tax', $_POST['products_default_tax'], PDO::PARAM_STR);
	$sth->bindParam(':products_default_currency', $_POST['products_default_currency'], PDO::PARAM_STR);
	$sth->bindParam(':event_time_offset', $_POST['event_time_offset'], PDO::PARAM_STR);
	$cnt_changes = $sth->execute();
	$dbh = null;
	
	$pub_preferences = pub_get_preferences(); /* load again */
		
}



/* PRINT THE FORM */
echo'<form action="acp.php?tn=moduls&sub=publisher.mod&a=prefs" method="POST">';

echo '<fieldset>';
echo '<legend>' . $pub_lang['label_entries'] . '</legend>';
echo '<div class="form-group">';
echo '<label>' . $pub_lang['label_entries_per_page'] . '</label>
				<input type="text" class="form-control" name="entries_per_page" value="'.$pub_preferences['entries_per_page'].'">
			</div>';
echo '</fieldset>';


echo'<fieldset>';
echo'<legend>'.$pub_lang['label_images'].'</legend>';
echo '<div class="form-group">';
echo '<label>'.$pub_lang['label_images_prefix'].'</label>
			<input type="text" class="form-control" name="images_prefix" value="'.$pub_preferences['images_prefix'].'">
			</div>';
$all_images = fc_get_all_images();
echo '<div class="form-group">';
echo '<label>'.$pub_lang['label_default_image'].'</label>';
				
echo '<select class="form-control custom-select" name="default_banner">';
echo '<option value="use_standard">'.$pub_lang['use_standard'].'</option>';

if($pub_preferences['default_banner'] == 'without_image') { $sel_without_image = 'selected'; }
echo '<option value="without_image" '.$sel_without_image.'>'.$pub_lang['without_image'].'</option>';
foreach ($all_images as $img) {
	unset($sel);
	if($pub_preferences['default_banner'] == $img) {
		$sel = "selected";
	}
	echo "<option $sel value='$img'>$img</option>";
}
				
echo '</select>';
				
echo '</div>';
echo '</fieldset>';
echo '<fieldset>';
echo '<legend>'.$pub_lang['label_design'].'</legend>';
echo '<div class="form-group">';
echo '<label>'.$pub_lang['label_template'].'</label>';
				
$tpl_folders = pub_list_template_folders();
				
echo '<select class="form-control custom-select" name="default_template">';
echo '<option value="use_standard">'.$pub_lang['use_standard'].'</option>';
				
foreach ($tpl_folders as $tpl) {
	unset($sel);
	if($pub_preferences['default_template'] == $tpl) {
		$sel = "selected";
	}					
	echo "<option $sel value='$tpl'>$tpl</option>";
}
echo '</select>';
echo '</div>';


$arr_Styles = get_all_templates();
		
echo '<div class="form-group">';
echo '<label>'.$pub_lang['label_page_template'].'</label>';
echo '<select class="form-control custom-select" name="default_page_template_entries">';
echo '<option value="use_standard">'.$pub_lang['use_standard'].'</option>';

foreach($arr_Styles as $template) {

	$arr_layout_tpl = glob("../styles/$template/templates/layout*.tpl");	
	$select_prefs_template .= "<optgroup label='$template'>";
	
	foreach($arr_layout_tpl as $layout_tpl) {
		$tpl = $template.'/templates/'.basename($layout_tpl);
		
		$selected = "";
		if($tpl == $pub_preferences['default_page_template_entries']) {
			$selected = 'selected';
		}
		$select_prefs_template .= "<option $selected value='$tpl'>$tpl</option>";
	}
	$select_prefs_template .= '</optgroup>';
}
$select_prefs_template .= '</select>';
echo $select_prefs_template;
echo '</select>';
echo '<span class="form-text text-muted">'.$pub_lang['page_template_text'].'</span>';
echo '</div>';

echo '<div class="form-group">';
echo '<label>CSS</label>';
if($pub_preferences['ignore_inline_css'] == 'ignore') {
	$ckeck_ignore_inline_css = 'checked';
} else {
	$ckeck_ignore_inline_css = '';
}
echo '<div class="form-check">';
echo '<input class="form-check-input" id="ignore_css" type="checkbox" name="ignore_inline_css" value="ignore" '.$ckeck_ignore_inline_css.'>';
echo '<label for="ignore_css">'.$pub_lang['ignore_inline_css'].'</label>';
echo '</div>';


/* intro snippet */
echo '<div class="form-group">';
echo '<label>' . $pub_lang['label_intro_snippet'] . '</label>';
echo '<select class="form-control custom-select" name="intro_snippet">';
echo '<option value="use_standard">'.$pub_lang['without_snippet'].'</option>';
$dbh = new PDO("sqlite:".CONTENT_DB);
$sql = "SELECT * FROM fc_textlib ORDER BY textlib_name ASC";
foreach ($dbh->query($sql) as $row) {
	$snippets_list[] = $row;
}
$dbh = null;
foreach($snippets_list as $snippet) {
	$selected = "";
	if($snippet['textlib_name'] == $pub_preferences['intro_snippet']) {
		$selected = 'selected';
	}
	echo '<option '.$selected.' value='.$snippet['textlib_name'].'>'.$snippet['textlib_name'].'</option>';
}
echo '</select>';
echo '<span class="form-text text-muted">'.$pub_lang['intro_snippet_text'].'</span>';
echo '</div>';
echo '</fieldset>';


/* URL and Permalinks */

echo '<fieldset>';
echo '<legend>URL</legend>';

if($pub_preferences['url_pattern'] == "by_date") {
	$select_modus_date = "checked";
} else {
	$select_modus_title = "checked";
}

echo '<div class="form-check">
				<input class="form-check-input" type="radio" name="url_pattern" value="by_date" '.$select_modus_date.'>
				<label>' . $pub_lang['url_by_date'] . '</label>
	 		</div>';
echo '<div class="form-check">
				<input class="form-check-input" type="radio" name="url_pattern" value="by_filename" '.$select_modus_title.'>
				<label>' . $pub_lang['url_by_title'] . '</label>
	 		</div>';
	 		
echo '<div class="row">';
echo '<div class="col-md-4">';
echo '<div class="form-group">
				<label>' . $pub_lang['label_rss_adress'] . '</label>
				<input type="text" class="form-control" name="url" value="'.$pub_preferences['url'].'">
			</div>';
echo '</div>';
echo '<div class="col-md-4">';
echo '<div class="form-group">
				<label>' . $pub_lang['url_separator_categories'] . '</label>
				<input type="text" class="form-control" name="url_separator_categories" value="'.$pub_preferences['url_separator_categories'].'">
			</div>';
echo '</div>';
echo '<div class="col-md-4">';
echo '<div class="form-group">
				<label>' . $pub_lang['url_separator_pages'] . '</label>
				<input type="text" class="form-control" name="url_separator_pages" value="'.$pub_preferences['url_separator_pages'].'">
			</div>';

echo '</div>';
echo '</div>';

echo'</fieldset>';


/* products */

echo '<fieldset>';
echo '<legend>'.$pub_lang['type_product'].'</legend>';

echo '<div class="form-group">
				<label>' . $pub_lang['products_default_tax'] . '</label>
				<input type="text" class="form-control" name="products_default_tax" value="'.$pub_preferences['products_default_tax'].'">
			</div>';
echo '<div class="form-group">
				<label>' . $pub_lang['products_default_currency'] . '</label>
				<input type="text" class="form-control" name="products_default_currency" value="'.$pub_preferences['products_default_currency'].'">
			</div>';
echo'</fieldset>';



/* events */

echo '<fieldset>';
echo '<legend>'.$pub_lang['type_event'].'</legend>';
echo '<div class="form-group">
				<label>' . $pub_lang['label_event_time_offset'] . '</label>
				<input type="text" class="form-control" name="event_time_offset" value="'.$pub_preferences['event_time_offset'].'">
				<small class="form-text text-muted">'.$pub_lang['event_time_offset_help_text'].'</small>
			</div>';
echo'</fieldset>';


echo'<div class="well well-sm">';
echo'<button type="submit" class="btn btn-success" name="saveprefs" value="save">'.$lang['save'].'</button>';
echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo'</div>';
echo'</form>';

echo '<hr class="shadow">';

/**
 * IMPORTING
 */

echo '<fieldset>';
echo '<legend>Import flatNews.mod</legend>';
$fn_db = '../content/SQLite/flatNews.sqlite3';
if(is_file($fn_db)) {
	
	
	if($_POST['import_fn_entries'] == 'import') {
		/* start import entries */
		
		$dbh = new PDO("sqlite:$fn_db");
		$sql = "SELECT * FROM fc_news ORDER BY news_id ASC";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		$entries = $sth->fetchAll();
		$dbh = null;
		
		$cnt_entries = count($entries);
		
		echo '<p class="alert alert-info">start importing '.$cnt_entries.' entries</p>';
		
		$dbh = new PDO("sqlite:$mod_db");
		
		$sql_insert = "INSERT INTO posts (
			id, type, date, releasedate, title, teaser, text, images, link, video_url, categories, author, status, lang, slug,
			rss, rss_url, priority, tags, startdate, enddate, hits
				) VALUES (
			NULL, :type, :date, :releasedate, :title, :teaser, :text, :images, :link, :video_url, :categories, :author, :status, :lang, :slug,
			:rss, :rss_url, :priority, :tags, :startdate, :enddate, :hits	)";
		
		for($i=0;$i<$cnt_entries;$i++) {
			
			/**
			 * importing columns
			 * news_date | news_releasedate | news_title | news_teaser | news_text | news_categories | news_author | news_status | news_rss | news_slug | 
			 * news_images | news_type | news_link | news_video_url | news_priority | news_hits | event_startdate | event_enddate
			 */
			 
			$sth = $dbh->prepare($sql_insert);
			$sth->bindParam(':type', $entries[$i]['news_type'], PDO::PARAM_STR);
			$sth->bindParam(':date', $entries[$i]['news_date'], PDO::PARAM_STR);
			$sth->bindParam(':releasedate', $entries[$i]['news_releasedate'], PDO::PARAM_STR);
			$sth->bindParam(':title', $entries[$i]['news_title'], PDO::PARAM_STR);
			$sth->bindParam(':teaser', $entries[$i]['news_teaser'], PDO::PARAM_STR);
			$sth->bindParam(':text', $entries[$i]['news_text'], PDO::PARAM_STR);
			$sth->bindParam(':images', $entries[$i]['news_images'], PDO::PARAM_STR);
			$sth->bindParam(':link', $entries[$i]['news_link'], PDO::PARAM_STR);
			$sth->bindParam(':video_url', $entries[$i]['news_video_url'], PDO::PARAM_STR);
			$sth->bindParam(':categories', $entries[$i]['news_categories'], PDO::PARAM_STR);
			$sth->bindParam(':author', $entries[$i]['news_author'], PDO::PARAM_STR);
			$sth->bindParam(':status', $entries[$i]['news_status'], PDO::PARAM_STR);
			$sth->bindParam(':lang', $entries[$i]['news_lang'], PDO::PARAM_STR);
			$sth->bindParam(':slug', $entries[$i]['news_slug'], PDO::PARAM_STR);
			$sth->bindParam(':rss', $entries[$i]['news_rss'], PDO::PARAM_STR);
			$sth->bindParam(':rss_url', $entries[$i]['news_rss_url'], PDO::PARAM_STR);
			$sth->bindParam(':priority', $entries[$i]['news_priority'], PDO::PARAM_STR);
			$sth->bindParam(':tags', $entries[$i]['news_tags'], PDO::PARAM_STR);
			$sth->bindParam(':startdate', $entries[$i]['event_startdate'], PDO::PARAM_STR);
			$sth->bindParam(':enddate', $entries[$i]['event_enddate'], PDO::PARAM_STR);
			$sth->bindParam(':hits', $entries[$i]['news_hits'], PDO::PARAM_STR);
			$cnt_changes = $sth->execute();
			
		}
		
		$dbh = null;
		
		
	}
	
	
	if($_POST['import_fn_categories'] == 'import') {
		/* start import categories */
		
		$dbh = new PDO("sqlite:$fn_db");
		$sql = "SELECT * FROM fc_newscats ORDER BY cat_id ASC";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		$entries = $sth->fetchAll();
		$dbh = null;

		$cnt_entries = count($entries);
		
		echo '<p class="alert alert-info">start importing '.$cnt_entries.' entries from category table</p>';		
		
		$dbh = new PDO("sqlite:$mod_db");
		
		$sql_insert = "INSERT INTO categories (
			id, name, name_safe, hash, description, thumbnail, sort, counter
				) VALUES (
			NULL, :name, :name_safe, :hash, :description, :thumbnail, :sort, :counter	)";	
		
		
		
		for($i=0;$i<$cnt_entries;$i++) {
			$sth = $dbh->prepare($sql_insert);
			$sth->bindParam(':name', $entries[$i]['cat_name'], PDO::PARAM_STR);
			$sth->bindParam(':name_safe', $entries[$i]['cat_name_safe'], PDO::PARAM_STR);
			$sth->bindParam(':hash', $entries[$i]['cat_hash'], PDO::PARAM_STR);
			$sth->bindParam(':description', $entries[$i]['cat_description'], PDO::PARAM_STR);
			$sth->bindParam(':thumbnail', $entries[$i]['cat_thumbnail'], PDO::PARAM_STR);
			$sth->bindParam(':sort', $entries[$i]['cat_sort'], PDO::PARAM_STR);
			$sth->bindParam(':counter', $entries[$i]['cat_counter'], PDO::PARAM_STR);
			$cnt_changes = $sth->execute();
		}
		
		
		$dbh = null;
				
	}
	


	if($_POST['import_fn_preferences'] == 'import') {
		/* start import preferences */
		
		$dbh = new PDO("sqlite:$fn_db");
		$sql = "SELECT * FROM fc_newsprefs ORDER BY fn_prefs_id ASC";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		$entries = $sth->fetchAll();
		$dbh = null;

		$cnt_entries = count($entries);
		echo '<p class="alert alert-info">start importing '.$cnt_entries.' entries from preferences table</p>';
	
		$dbh = new PDO("sqlite:$mod_db");
		
		$sql = "UPDATE preferences
				SET	url = :url,
					url_pattern = :url_pattern,
					images_prefix = :images_prefix,
					default_banner = :default_banner,
					default_template = :default_template,
					ignore_inline_css = :ignore_inline_css,
					default_page_template_entries = :default_page_template_entries,
					intro_snippet = :intro_snippet,
					entries_per_page = :entries_per_page
				WHERE status = 'active' ";
	

		for($i=0;$i<$cnt_entries;$i++) {
			$sth = $dbh->prepare($sql);
			$sth->bindParam(':url', $entries[$i]['fn_prefs_url'], PDO::PARAM_STR);
			$sth->bindParam(':url_pattern', $entries[$i]['fn_prefs_url_pattern'], PDO::PARAM_STR);
			$sth->bindParam(':images_prefix', $entries[$i]['fn_prefs_images_prefix'], PDO::PARAM_STR);
			$sth->bindParam(':default_banner', $entries[$i]['fn_prefs_standard_banner'], PDO::PARAM_STR);
			$sth->bindParam(':default_template', $entries[$i]['fn_prefs_template'], PDO::PARAM_STR);
			$sth->bindParam(':ignore_inline_css', $entries[$i]['fn_prefs_ignore_inline_css'], PDO::PARAM_STR);
			$sth->bindParam(':default_page_template_entries', $entries[$i]['fn_prefs_page_template_entries'], PDO::PARAM_STR);
			$sth->bindParam(':intro_snippet', $entries[$i]['fn_prefs_intro_snippet'], PDO::PARAM_STR);
			$sth->bindParam(':entries_per_page', $entries[$i]['fn_prefs_entries_per_page'], PDO::PARAM_STR);
			$cnt_changes = $sth->execute();
		}
		
		
		$dbh = null;		
	
	}
	
	
	
	echo '<p>'.$pub_lang['msg_import_fn'].'</p>';
	echo '<form action="acp.php?tn=moduls&sub=publisher.mod&a=prefs" method="POST">';
	echo '<button type="submit" name="import_fn_entries" value="import" class="btn btn-primary">'.$pub_lang['btn_start_import_entries'].'</button> ';
	echo '<button type="submit" name="import_fn_categories" value="import" class="btn btn-primary">'.$pub_lang['btn_start_import_categories'].'</button> ';
	echo '<button type="submit" name="import_fn_preferences" value="import" class="btn btn-primary">'.$pub_lang['btn_start_import_preferences'].'</button> ';
	echo '</form>';
}

echo '</fieldset>';

echo '<fieldset id="importcals">';
echo '<legend>Import flatCal.mod</legend>';
$fc_db = '../content/SQLite/flatCal.sqlite3';
if(is_file($fc_db)) {
	
	if($_POST['import_fc_categories'] == 'import') {
		/* start import categories */
		
		$dbh = new PDO("sqlite:$fc_db");
		$sql = "SELECT * FROM fc_calscats ORDER BY cat_id ASC";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		$entries = $sth->fetchAll();
		$dbh = null;

		$cnt_entries = count($entries);
		
		echo '<p class="alert alert-info">start importing '.$cnt_entries.' entries from category table</p>';		
		
		
		$dbh = new PDO("sqlite:$mod_db");
		
		$sql_insert = "INSERT INTO categories (
			id, name, name_safe, description
				) VALUES (
			NULL, :name, :name_safe, :description	)";	
		
		
		
		for($i=0;$i<$cnt_entries;$i++) {
			
			$name_safe = clean_filename($entries[$i]['cat_name']);
			
			$sth = $dbh->prepare($sql_insert);
			$sth->bindParam(':name', $entries[$i]['cat_name'], PDO::PARAM_STR);
			$sth->bindParam(':name_safe', $name_safe, PDO::PARAM_STR);
			$sth->bindParam(':description', $entries[$i]['cat_description'], PDO::PARAM_STR);
			$cnt_changes = $sth->execute();
		}
		
		
		$dbh = null;
		
	}
	
	if($_POST['import_fc_entries'] == 'import') {
		/* start import entries */
		
		$time = time();
		$type = 'event';
		$status = 'published';
		$priority = 1;
		
		$dbh = new PDO("sqlite:$fc_db");
		$sql = "SELECT * FROM fc_cals WHERE cal_startdate > :time ORDER BY cal_id ASC";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':time', $time, PDO::PARAM_STR);
		$sth->execute();
		$entries = $sth->fetchAll();
		$dbh = null;
		
		$cnt_entries = count($entries);
		
		echo '<p class="alert alert-info">start importing '.$cnt_entries.' entries</p>';
		
		
		$dbh = new PDO("sqlite:$mod_db");
		
		$sql_insert = "INSERT INTO posts (
			id, type, date, releasedate, title, teaser, images, categories, author, status, lang,
			priority, startdate, enddate
				) VALUES (
			NULL, :type, :date, :releasedate, :title, :teaser, :images, :categories, :author, :status, :lang,
			:priority, :startdate, :enddate	)";
		
		for($i=0;$i<$cnt_entries;$i++) {
			

			$cat_str = '';
			$cal_cats = explode('<->',$entries[$i]['cal_categories']);
			foreach($cal_cats as $cats) {
				$cat_str .= clean_filename($cats) . '<->';
			}
						 
			$sth = $dbh->prepare($sql_insert);
			$sth->bindParam(':type', $type, PDO::PARAM_STR);
			$sth->bindParam(':date', $time, PDO::PARAM_STR);
			$sth->bindParam(':releasedate', $time, PDO::PARAM_STR);
			$sth->bindParam(':title', $entries[$i]['cal_title'], PDO::PARAM_STR);
			$sth->bindParam(':teaser', $entries[$i]['cal_text'], PDO::PARAM_STR);
			$sth->bindParam(':images', $entries[$i]['cal_image'], PDO::PARAM_STR);
			$sth->bindParam(':categories', $cat_str, PDO::PARAM_STR);
			$sth->bindParam(':author', $entries[$i]['cal_author'], PDO::PARAM_STR);
			$sth->bindParam(':status', $status, PDO::PARAM_STR);
			$sth->bindParam(':lang', $languagePack, PDO::PARAM_STR);
			$sth->bindParam(':priority', $priority, PDO::PARAM_STR);
			$sth->bindParam(':startdate', $entries[$i]['cal_startdate'], PDO::PARAM_STR);
			$sth->bindParam(':enddate', $entries[$i]['cal_enddate'], PDO::PARAM_STR);
			$cnt_changes = $sth->execute();

			
		}
		
		$dbh = null;
		
	}


	echo '<p>'.$pub_lang['msg_import_fc'].'</p>';
	echo '<form action="acp.php?tn=moduls&sub=publisher.mod&a=prefs#importcals" method="POST">';
	echo '<button type="submit" name="import_fc_entries" value="import" class="btn btn-primary">'.$pub_lang['btn_start_import_entries'].'</button> ';
	echo '<button type="submit" name="import_fc_categories" value="import" class="btn btn-primary">'.$pub_lang['btn_start_import_categories'].'</button> ';
	echo '</form>';	
}
echo '</fieldset>';




echo '<fieldset id="importgalleries">';
echo '<legend>Import fcGallery.mod</legend>';

$fcg_db = '../content/SQLite/flatPix.sqlite3';
if(is_file($fcg_db)) {

	if($_POST['import_fcg_galleries'] == 'import') {
		/* start import galleries */

		$type = 'gallery';
		$status = 'published';
		$priority = 1;
		
		$dbh = new PDO("sqlite:$fcg_db");
		$sql = "SELECT * FROM fp_galleries ORDER BY fp_gal_id ASC";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		$entries = $sth->fetchAll();
		$dbh = null;

		$cnt_entries = count($entries);
		
		echo '<p class="alert alert-info">start importing '.$cnt_entries.' entries from gallery table</p>';

		$dbh = new PDO("sqlite:$mod_db");
		
		$sql_insert = "INSERT INTO posts (
			id, type, date, releasedate, title, teaser, status, priority, slug
				) VALUES (
			NULL, :type, :date, :releasedate, :title, :teaser, :status, :priority, :slug )";
					
		
		for($i=0;$i<$cnt_entries;$i++) {
			
			$time = time();
			$gal_id = $entries[$i]['fp_gal_id'];
			$gal_dir = '../content/flatPix/gal'.$gal_id;
			$gal_imgs = glob("$gal_dir/$gal_id_img*.jpg");
			
			$clean_title = clean_filename($entries[$i]['fp_gal_title']);
			$post_date_year = date("Y",$entries[$i]['fp_gal_date']);
			$post_date_month = date("m",$entries[$i]['fp_gal_date']);
			$post_date_day = date("d",$entries[$i]['fp_gal_date']);
			$slug = "$post_date_year/$post_date_month/$post_date_day/$clean_title/";

			$sth = $dbh->prepare($sql_insert);
			$sth->bindParam(':type', $type, PDO::PARAM_STR);
			$sth->bindParam(':date', $entries[$i]['fp_gal_date'], PDO::PARAM_STR);
			$sth->bindParam(':releasedate', $entries[$i]['fp_gal_date'], PDO::PARAM_STR);
			$sth->bindParam(':title', $entries[$i]['fp_gal_title'], PDO::PARAM_STR);
			$sth->bindParam(':teaser', $entries[$i]['fp_gal_description'], PDO::PARAM_STR);
			$sth->bindParam(':status', $status, PDO::PARAM_STR);
			$sth->bindParam(':priority', $priority, PDO::PARAM_STR);
			$sth->bindParam(':slug', $slug, PDO::PARAM_STR);
			$cnt_changes = $sth->execute();
			
			$post_id = $dbh->lastInsertId();
			
			//echo $entries[$i]['fp_gal_id'] . ' - ' .$entries[$i]['fp_gal_title'].' - '. $entries[$i]['fp_gal_id'].'<br>';
			
			$new_dir = '../content/publisher/galleries/'.date('Y',$entries[$i]['fp_gal_date']).'/gallery'.$post_id;
			if(mkdir($new_dir, 0777, true)) {
				
				/* copy and rename images to new directory */
				foreach($gal_imgs as $old_img) {
					$timestring = microtime(true);
					if(stripos($old_img,"_img_") !== false) {
						/* copy image */
						$new_img = $new_dir.'/'.$timestring.'_img.jpg';
						$new_tmb = $new_dir.'/'.$timestring.'_tmb.jpg';
						$old_tmb = str_replace('_img_','_thumb_',$old_img);
						
						copy($old_img ,$new_img);
						copy($old_tmb ,$new_tmb);
						
					}
				}	
			}
		}
		
		$dbh = null;

	}

	echo '<p>'.$pub_lang['import_fcg_galleries'].'</p>';
	echo '<form action="acp.php?tn=moduls&sub=publisher.mod&a=prefs#importcals" method="POST">';
	echo '<button type="submit" name="import_fcg_galleries" value="import" class="btn btn-primary">'.$pub_lang['btn_start_import_entries'].'</button> ';
	echo '</form>';	
}
echo '</fieldset>';


?>