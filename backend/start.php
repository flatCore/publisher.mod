<?php

/**
 * @modul	publisher
 * backend
 */

error_reporting(E_ALL ^E_NOTICE);

if(!defined('FC_INC_DIR')) {
	die("No access");
}

include '../modules/publisher.mod/install/installer.php';
include __DIR__.'/include.php';


echo '<h3>'.$mod_name.' '.$mod_version.' <small>| '.$mod['description'].'</small></h3>';


/* delete post */

if((isset($_POST['delete_id'])) && is_numeric($_POST['delete_id'])) {
	$dbh = new PDO("sqlite:$mod_db");
	$sql = "DELETE FROM posts WHERE id = :id";
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':id', $_POST['delete_id'], PDO::PARAM_INT);
	$cnt_changes = $sth->execute();
	$dbh = NULL;
	if($cnt_changes == TRUE) {
		echo"<div class='alert alert-success'>ARTIKEL WURDE GELÖSCHT</div>";
	}
	
}

/* remove fixed */

if(is_numeric($_REQUEST['rfixed'])) {
	$dbh = new PDO("sqlite:$mod_db");
	$change_id = (int) $_REQUEST['rfixed'];
	$sql_change = "UPDATE posts SET fixed = NULL WHERE id = $change_id";
	$cnt_change = $dbh->exec($sql_change);
	$dbh = NULL;
}

/* set fixed */

if(is_numeric($_REQUEST['sfixed'])) {
	$dbh = new PDO("sqlite:$mod_db");
	$change_id = (int) $_REQUEST['sfixed'];
	$sql_change = "UPDATE posts SET fixed = 'fixed' WHERE id = $change_id";
	$cnt_change = $dbh->exec($sql_change);
	$dbh = NULL;
}

/* change priority */

if(isset($_POST['post_priority'])) {
	$dbh = new PDO("sqlite:$mod_db");
	$change_id = (int) $_POST['prio_id'];
	$sql = "UPDATE posts SET priority = :priority WHERE id = :id";
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':id', $change_id, PDO::PARAM_INT);
	$sth->bindParam(':priority', $_POST['post_priority'], PDO::PARAM_STR);
	$cnt_changes = $sth->execute();
	$dbh = NULL;	
}


// defaults
$pb_posts_start = 0;
$pb_posts_limit = 25;
$pb_posts_order = 'id';
$pb_posts_direction = 'DESC';
$pb_posts_filter = array();

$arr_status = array('draft','published');
$arr_types = array('message','image','video','link','event');
$arr_lang = get_all_languages();
$arr_categories = pub_get_categories();

/* default: check all languages */
if(!isset($_SESSION['checked_lang_string'])) {	
	foreach($arr_lang as $langstring) {
		$checked_lang_string .= "$langstring[lang_folder]-";
	}
	$_SESSION['checked_lang_string'] = "$checked_lang_string";
}

/* change status of $_GET['switchLang'] */
if($_GET['switchLang']) {
	if(strpos("$_SESSION[checked_lang_string]", "$_GET[switchLang]") !== false) {
		$checked_lang_string = str_replace("$_GET[switchLang]-", '', $_SESSION['checked_lang_string']);
	} else {
		$checked_lang_string = $_SESSION['checked_lang_string'] . "$_GET[switchLang]-";
	}
	$_SESSION['checked_lang_string'] = "$checked_lang_string";
}

/* filter buttons for languages */
$lang_btn_group = '<div class="btn-group">';
for($i=0;$i<count($arr_lang);$i++) {
	$lang_desc = $arr_lang[$i]['lang_desc'];
	$lang_folder = $arr_lang[$i]['lang_folder'];
	
	$this_btn_status = '';
	if(strpos("$_SESSION[checked_lang_string]", "$lang_folder") !== false) {
		$this_btn_status = 'active';
	}
	
	$lang_btn_group .= '<a href="acp.php?tn=moduls&sub=publisher.mod&a=start&switchLang='.$lang_folder.'" class="btn btn-sm btn-fc '.$this_btn_status.'">'.$lang_folder.'</a>';
}
$lang_btn_group .= '</div>';


/* default: check all types */
if(!isset($_SESSION['checked_type_string'])) {		
	$_SESSION['checked_type_string'] = 'message-image-video-link-event';
}
/* change status of selected types */
if($_GET['type']) {
	if(strpos("$_SESSION[checked_type_string]", "$_GET[type]") !== false) {
		$checked_type_string = str_replace("$_GET[type]", '', $_SESSION['checked_type_string']);
	} else {
		$checked_type_string = $_SESSION['checked_type_string'] . '-' . $_GET['type'];
	}
	$checked_type_string = str_replace('--', '-', $checked_type_string);
	$_SESSION['checked_type_string'] = "$checked_type_string";
}
/* default: check all status types */
if(!isset($_SESSION['checked_status_string'])) {		
	$_SESSION['checked_status_string'] = 'draft-published';
}
/* change status types */
if($_GET['status']) {
	if(strpos("$_SESSION[checked_status_string]", "$_GET[status]") !== false) {
		$checked_status_string = str_replace("$_GET[status]", '', $_SESSION['checked_status_string']);
	} else {
		$checked_status_string = $_SESSION['checked_status_string'] . '-' . $_GET['status'];
	}
	$checked_status_string = str_replace('--', '-', $checked_status_string);
	$_SESSION['checked_status_string'] = "$checked_status_string";
}


/* default: check all categories */
if(!isset($_SESSION['checked_cat_string'])) {	
	$_SESSION['checked_cat_string'] = 'all';
}
/* filter by categories */
if($_GET['cat']) {
	$_SESSION['checked_cat_string'] = $_GET['cat'];
}

$cat_all_active = '';
if($_SESSION['checked_cat_string'] == 'all') {
	$cat_all_active = 'active';
}

$cat_btn_group = '<div class="card">';
$cat_btn_group .= '<div class="list-group list-group-flush">';
$cat_btn_group .= '<a href="acp.php?tn=moduls&sub=publisher.mod&a=start&cat=all" class="list-group-item list-group-item-ghost p-1 px-2 '.$cat_all_active.'">'.$pub_lang['btn_all_categories'].'</a>';
foreach($arr_categories as $c) {
	$cat_active = '';
	if(strpos($_SESSION['checked_cat_string'], $c['name_safe']) !== false) {
		$cat_active = 'active';
	}
	
	$cat_btn_group .= '<a href="acp.php?tn=moduls&sub=publisher.mod&a=start&cat='.$c['name_safe'].'" class="list-group-item list-group-item-ghost p-1 px-2 '.$cat_active.'">'.$c['name'].'</a>';
}

$cat_btn_group .= '</div>';
$cat_btn_group .= '</div>';


if((isset($_GET['pb_posts_start'])) && is_numeric($_GET['pb_posts_start'])) {
	$pb_posts_start = (int) $_GET['pb_posts_start'];
}

if((isset($_POST['setPage'])) && is_numeric($_POST['setPage'])) {
	$pb_posts_start = (int) $_POST['setPage'];
}


$pb_posts_filter['languages'] = $_SESSION['checked_lang_string'];
$pb_posts_filter['types'] = $_SESSION['checked_type_string'];
$pb_posts_filter['status'] = $_SESSION['checked_status_string'];
$pb_posts_filter['categories'] = $_SESSION['checked_cat_string'];


$get_posts = pub_get_entries($pb_posts_start,$pb_posts_limit,$pb_posts_filter);
$cnt_filter_posts = $get_posts[0]['cnt_posts'];
$cnt_get_posts = count($get_posts);
$cnt_posts = pub_cnt_entries();

$nextPage = $pb_posts_start+$pb_posts_limit;
$prevPage = $pb_posts_start-$pb_posts_limit;
$cnt_pages = ceil($cnt_filter_posts / $pb_posts_limit);



echo '<div class="row">';
echo '<div class="col-md-9">';

echo '<h4>'.$cnt_filter_posts.'/'.$cnt_posts['All'].'</h4>';

echo '<table class="table table-sm table-hover">';

echo '<thead><tr>';
echo '<th>#</th>';
echo '<th>'.$icon['star'].'</th>';
echo '<th>'.$pub_lang['label_priority'].'</th>';
echo '<th nowrap>'.$pub_lang['label_date'].'</th>';
echo '<th>'.$pub_lang['label_post_type'].'</th>';
echo '<th></th>';
echo '<th>'.$pub_lang['label_post_title'].'</th>';
echo '<th></th>';
echo '</tr></thead>';

for($i=0;$i<$cnt_get_posts;$i++) {
	
	$type_class = 'label-type label-'.$get_posts[$i]['type'];
	$icon_fixed = '';
	$draft_class = '';
	
	if($get_posts[$i]['fixed'] == 'fixed') {
		$icon_fixed = '<a href="acp.php?tn=moduls&sub=publisher.mod&a=start&rfixed='.$get_posts[$i]['id'].'">'.$icon['star'].'</a>';
	} else {
		$icon_fixed = '<a href="acp.php?tn=moduls&sub=publisher.mod&a=start&sfixed='.$get_posts[$i]['id'].'">'.$icon['star_outline'].'</a>';
	}
	
	if($get_posts[$i]['status'] == 'draft') {
		$draft_class = 'item_is_draft';
	}
	
	/* trim teaser to $trim chars */
	$trim = 150;
	$teaser = strip_tags($get_posts[$i]['teaser']);
	if(strlen($teaser) > $trim) {
		$ellipses = ' <small><i>(...)</i></small>';
	  $last_space = strrpos(substr($teaser, 0, $trim), ' ');
	  if($last_space !== false) {
		  $trimmed_teaser = substr($teaser, 0, $last_space);
		} else {
			$trimmed_teaser = substr($teaser, 0, $trim);
		}
		$trimmed_teaser = $trimmed_teaser.$ellipses;
	} else {
		$trimmed_teaser = $teaser;
	}
	
	
	$post_image = explode("<->", $get_posts[$i]['images']);
	$show_thumb = '';
	if($post_image[1] != "") {
		$image_src = $post_image[1];
		/* older version of flatNews stored only basename of images */
		if(stripos($post_image[1],'/content/') === FALSE) {
			$image_src = "/$img_path/" . $post_image[1];
		}
	
		$show_thumb  = '<a data-toggle="popover" data-trigger="hover" data-html="true" data-content="<img src=\''.$image_src.'\'>">';
		$show_thumb .= '<div class="show-thumb" style="background-image: url('.$image_src.');">';
		$show_thumb .= '</div>';
	
	
	}

	
	$select_priority = '<select name="post_priority" class="form-control custom-select" onchange="this.form.submit()">';
	for($x=1;$x<11;$x++) {
		$option_add = '';
		$sel_prio = '';
		if($get_posts[$i]['priority'] == $x) {
			$sel_prio = 'selected';
		}
		$select_priority .= '<option value="'.$x.'" '.$sel_prio.'>'.$x.'</option>';
	}
	$select_priority .= '</select>';
	
	
	
	$prio_form  = '<form action="acp.php?tn=moduls&sub=publisher.mod&a=start" method="POST">';
	$prio_form .= $select_priority;
	$prio_form .= '<input type="hidden" name="prio_id" value="'.$get_posts[$i]['id'].'">';
	$prio_form .= '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
	$prio_form .= '</form>';
	
	$published_date = '<span title="'.date('Y-m-d h:i:s',$get_posts[$i]['date']).'">E: '.date('Y-m-d',$get_posts[$i]['date']).'</span>';
	$release_date = '<span title="'.date('Y-m-d h:i:s',$get_posts[$i]['releasedate']).'">R: '.date('Y-m-d',$get_posts[$i]['releasedate']).'</span>';
	$lastedit_date = '';
	if($get_posts[$i]['lastedit'] != '') {
		$lastedit_date = '<span title="'.date('Y-m-d h:i:s',$get_posts[$i]['lastedit']).' ('.$get_posts[$i]['lastedit_from'].')">L: '.date('Y-m-d',$get_posts[$i]['lastedit']).'</span>';
	}
	
	
	echo '<tr class="'.$draft_class.'">';
	echo '<td>'.$get_posts[$i]['id'].'</td>';
	echo '<td>'.$icon_fixed.'</td>';
	echo '<td>'.$prio_form.'</td>';
	echo '<td nowrap><small>'.$published_date.'<br>'.$release_date.'<br>'.$lastedit_date.'</small></td>';
	echo '<td><span class="'.$type_class.'">'.$get_posts[$i]['type'].'</span></td>';
	echo '<td>'.$show_thumb.'</td>';
	echo '<td><h5 class="mb-0">'.$get_posts[$i]['title'].'</h5><small>'.$trimmed_teaser.'</small></td>';
	echo '<td style="min-width: 150px;">';
	echo '<nav class="nav justify-content-end">';
	echo '<a class="btn btn-fc btn-sm text-success mx-1" href="acp.php?tn=moduls&sub=publisher.mod&a=edit&post_id='.$get_posts[$i]['id'].'">'.$lang['edit'].'</a>';
	echo '<form class="form-inline" action="acp.php?tn=moduls&sub=publisher.mod&a=start" method="POST"><button class="btn btn-danger btn-sm" type="submit" name="delete_id" value="'.$get_posts[$i]['id'].'">'.$icon['trash_alt'].'</button></form>';
	echo '</nav>';
	echo '</td>';
	echo '</tr>';


}

echo '</table>';

echo '</div>';
echo '<div class="col-md-3">';



echo '<div class="row">';
echo '<div class="col-md-2">';
if($prevPage < 0) {
	echo '<a class="btn btn-fc btn-block disabled" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>';
} else {
	echo '<a class="btn btn-fc btn-block" href="acp.php?tn=moduls&sub=publisher.mod&a=start&pb_posts_start='.$prevPage.'" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>';
}

echo '</div>';
echo '<div class="col-md-8">';
echo '<form action="acp.php?tn=moduls&sub=publisher.mod&a=start" method="POST">';
echo '<select class="form-control custom-select" name="setPage" onchange="this.form.submit()">';
for($i=0;$i<$cnt_pages;$i++) {
	$x = $i+1;
	$thisPage = ($x*$pb_posts_limit)-$pb_posts_limit;
	$sel = '';
	if($thisPage == $pb_posts_start) {
		$sel = 'selected';
	}
	echo '<option value="'.$thisPage.'" '.$sel.'>'.$x.' ('.$thisPage.')</option>';
}
echo '</select>';
echo '</form>';
echo '</div>';
echo '<div class="col-md-2">';
if($nextPage < ($cnt_filter_posts-$pb_posts_limit)+$pb_posts_limit) {
	echo '<a class="btn btn-fc btn-block" href="acp.php?tn=moduls&sub=publisher.mod&a=start&pb_posts_start='.$nextPage.'" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>';
} else {
	echo '<a class="btn btn-fc btn-block disabled" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>';
}
echo '</div>';
echo '</div>';

echo '<hr>';

echo '<div class="card mt-2">';
echo '<div class="list-group list-group-flush">';
echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=edit&new=message" class="list-group-item list-group-item-ghost p-1 px-2">'.$icon['plus'].' '.$pub_lang['type_message'].'</a>';
echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=edit&new=event" class="list-group-item list-group-item-ghost p-1 px-2">'.$icon['plus'].' '.$pub_lang['type_event'].'</a>';
echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=edit&new=image" class="list-group-item list-group-item-ghost p-1 px-2">'.$icon['plus'].' '.$pub_lang['type_image'].'</a>';
echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=edit&new=video" class="list-group-item list-group-item-ghost p-1 px-2">'.$icon['plus'].' '.$pub_lang['type_video'].'</a>';
echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=edit&new=link" class="list-group-item list-group-item-ghost p-1 px-2">'.$icon['plus'].' '.$pub_lang['type_link'].'</a>';
echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=edit&new=product" class="list-group-item list-group-item-ghost p-1 px-2">'.$icon['plus'].' '.$pub_lang['type_product'].'</a>';
echo '</div>';
echo '</div>';

/* Filter Options */
echo '<fieldset class="mt-4">';
echo '<legend>'.$icon['filter'].' '.$pub_lang['label_language'].'</legend>';

echo '<div class="text-center">'.$lang_btn_group.'</div>';

echo '</fieldset>';

echo '<fieldset class="mt-4">';
echo '<legend>'.$icon['filter'].' '.$pub_lang['label_post_type'].'</legend>';

/* type filter */
echo '<div class="btn-group d-flex">';
if(strpos("$_SESSION[checked_type_string]", "message") !== false) {
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=start&type=message" class="btn btn-sm btn-fc active w-100">'.$pub_lang['type_message'].'</a>';
} else {
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=start&type=message" class="btn btn-sm btn-fc w-100">'.$pub_lang['type_message'].'</a>';
}
if(strpos("$_SESSION[checked_type_string]", "event") !== false) {
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=start&type=event" class="btn btn-sm btn-fc active w-100">'.$pub_lang['type_event'].'</a>';
} else {
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=start&type=event" class="btn btn-sm btn-fc w-100">'.$pub_lang['type_event'].'</a>';
}
if(strpos("$_SESSION[checked_type_string]", "image") !== false) {
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=start&type=image" class="btn btn-sm btn-fc active w-100">'.$pub_lang['type_image'].'</a>';
} else {
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=start&type=image" class="btn btn-sm btn-fc w-100">'.$pub_lang['type_image'].'</a>';
}
if(strpos("$_SESSION[checked_type_string]", "video") !== false) {
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=start&type=video" class="btn btn-sm btn-fc active w-100">'.$pub_lang['type_video'].'</a>';
} else {
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=start&type=video" class="btn btn-sm btn-fc w-100">'.$pub_lang['type_video'].'</a>';
}
if(strpos("$_SESSION[checked_type_string]", "link") !== false) {
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=start&type=link" class="btn btn-sm btn-fc active w-100">'.$pub_lang['type_link'].'</a>';
} else {
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=start&type=link" class="btn btn-sm btn-fc w-100">'.$pub_lang['type_link'].'</a>';
}
if(strpos("$_SESSION[checked_type_string]", "product") !== false) {
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=start&type=product" class="btn btn-sm btn-fc active w-100">'.$pub_lang['type_product'].'</a>';
} else {
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=start&type=product" class="btn btn-sm btn-fc w-100">'.$pub_lang['type_product'].'</a>';
}
echo '</div>';

echo '</fieldset>';

echo '<fieldset class="mt-4">';
echo '<legend>'.$icon['filter'].' '.$pub_lang['label_status'].'</legend>';

/* status filter */
echo '<div class="btn-group d-flex">';
if(strpos("$_SESSION[checked_status_string]", "draft") !== false) {
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=start&status=draft" class="btn btn-sm btn-fc active w-100">'.$pub_lang['status_draft'].'</a>';
} else {
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=start&status=draft" class="btn btn-sm btn-fc w-100">'.$pub_lang['status_draft'].'</a>';
}
if(strpos("$_SESSION[checked_status_string]", "published") !== false) {
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=start&status=published" class="btn btn-sm btn-fc active w-100">'.$pub_lang['status_public'].'</a>';
} else {
	echo '<a href="acp.php?tn=moduls&sub=publisher.mod&a=start&status=published" class="btn btn-sm btn-fc w-100">'.$pub_lang['status_public'].'</a>';
}
echo '</div>';


echo '</fieldset>';

echo '<fieldset class="mt-4">';
echo '<legend>'.$icon['filter'].' '.$pub_lang['label_categories'].'</legend>';

echo $cat_btn_group;

echo '</fieldset>';

echo '</div>';
echo '</div>';


?>