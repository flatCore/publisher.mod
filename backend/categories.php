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

echo '<h3>'.$mod_name.' '.$mod_version.' <small>| '.$pub_lang['nav_categories'].'</small></h3>';



if($_POST['delete_cat']) {

	$id = (int) $_POST['id'];
	$dbh = new PDO("sqlite:$mod_db");
	$delete_sql = "DELETE FROM fc_newscats WHERE id = $id";
	$cnt_changes = $dbh->exec($delete_sql);

	if($cnt_changes > 0){
		$sys_message = '{OKAY} ' . $lang['db_changed'];
	} else {
		$sys_message = '{ERROR} ' . $lang['db_not_changed'];
	}
	$dbh = null;
}


/* Save new Category */
if($_POST['save_cat']) {

	$new_sql = "INSERT INTO categories	(
					id, name, name_safe, description, sort, thumbnail
					) VALUES (
					NULL, :name, :name_safe, :description, :sort, :thumbnail
					) ";
	
	$name_safe = clean_filename($_POST['name']);
	
	$dbh = new PDO("sqlite:$mod_db");
	$sth = $dbh->prepare($new_sql);
	$sth->bindParam(':name_safe', $name_safe, PDO::PARAM_STR);
	$sth->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
	$sth->bindParam(':description', $_POST['description'], PDO::PARAM_STR);
	$sth->bindParam(':sort', $_POST['sort'], PDO::PARAM_STR);
	$sth->bindParam(':thumbnail', $_POST['thumbnail'], PDO::PARAM_STR);

	$cnt_changes = $sth->execute();
	$dbh = null;

	if($cnt_changes == TRUE) {
		$sys_message = '{OKAY} ' . $lang['db_changed'];
	} else {
		$sys_message = '{ERROR} ' . $lang['db_not_changed'];
	}

}


/* Update Category */
if($_POST['update_cat']) {

	$editcat = (int) $_POST['id'];
	$name_safe = clean_filename($_POST['name']);
	
	$update_sql = "UPDATE categories
									SET name = :name,
										name_safe = :name_safe,
										description = :description,
										sort = :sort,
										thumbnail = :thumbnail
									WHERE id = $editcat ";
									
	$dbh = new PDO("sqlite:$mod_db");							
	$sth = $dbh->prepare($update_sql);
	$sth->bindParam(':name_safe', $name_safe, PDO::PARAM_STR);
	$sth->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
	$sth->bindParam(':description', $_POST['description'], PDO::PARAM_STR);
	$sth->bindParam(':sort', $_POST['sort'], PDO::PARAM_STR);
	$sth->bindParam(':thumbnail', $_POST['thumbnail'], PDO::PARAM_STR);

	$cnt_changes = $sth->execute();
	$dbh = null;

	if($cnt_changes == TRUE) {
		$sys_message = '{OKAY} ' . $lang['db_changed'];
	} else {
		$sys_message = '{ERROR} ' . $lang['db_not_changed'];
	}

	$_REQUEST['editcat'] = $editcat;

}


$submit_button = "<input type='submit' class='btn btn-save' name='save_cat' value='$lang[save]'>";
$delete_button = "";



if($_REQUEST['editcat'] != "") {
	
	$editcat = (int) $_REQUEST['editcat'];
	
	$submit_button = "<input type='submit' class='btn btn-save' name='update_cat' value='$lang[update]'>";
	$delete_button = "<input type='submit' class='btn btn-fc text-danger' name='delete_cat' value='$lang[delete]' onclick=\"return confirm('$lang[confirm_delete_data]')\">";
	$hidden_field = "<input type='hidden' name='id' value='$editcat'>";
	
	$dbh = new PDO("sqlite:$mod_db");
	$editsql = "SELECT * FROM categories WHERE id = $editcat";
	
	$edit_cat = $dbh->query($editsql);
	$edit_cat = $edit_cat->fetch(PDO::FETCH_ASSOC);
	
	$name = stripslashes($edit_cat['name']);
	$description = stripslashes($edit_cat['description']);
	$sort = stripslashes($edit_cat['sort']);
	$thumbnail = stripslashes($edit_cat['thumbnail']);
	$name_safe = $result['name_safe'];
	$dbh = null;
	
}




/* MESSAGES */

if($sys_message != ""){
	print_sysmsg("$sys_message");
}












$pub_cat = pub_get_categories();
$cnt_pub_cat = count($pub_cat);

echo '<div class="row">';
echo '<div class="col-md-6">';

echo "<form action='?tn=moduls&sub=publisher.mod&a=$a' class='' method='POST'>";


echo '<div class="form-group">';
echo '<label>'.$pub_lang['label_cat_name'].'</label>';
echo '<input type="text" class="form-control" name="name" value="'.$name.'">';
if($name_safe != '') {
	echo '<p>Modul-Query: <code>cat='.$name_safe.'</code></p>';
}
echo '</div>';

echo '<div class="form-group">';
echo '<label>'.$pub_lang['label_cat_sort'].'</label>';
echo '<input type="text" class="form-control" name="sort" value="'.$sort.'">';
echo '</div>';


$images = fc_scandir_rec('../'.FC_CONTENT_DIR.'/images');

/* avatar */
$choose_tmb = '<select class="form-control choose-thumb custom-select" name="thumbnail">';
$choose_tmb .= '<option value="">Kein Bild ...</option>';
foreach($images as $img) {
	$selected = '';
	if($thumbnail == $img) {$selected = 'selected';}
	$img = str_replace('../content/', '/content/', $img);
	$choose_tmb .= '<option '.$selected.' value='.$img.'>'.$img.'</option>';
}
$choose_tmb .= '</select>';

if($edit_cat['thumbnail'] == '') {
	$thumb_saved = '../modules/publisher.mod/backend/poster.jpg';
} else {
	$thumb_saved = $edit_cat['thumbnail'];
}

echo '<div class="form-group">';
echo '<label>'.$pub_lang['label_cat_thumbnail'].'</label>';
echo '<div class="row">';
echo '<div class="col-md-2">';
echo '<img src="'.$thumb_saved.'" class="rounded img-fluid thumb-preview">';
echo '</div>';
echo '<div class="col-md-10">';
echo $choose_tmb;
echo '</div>';
echo '</div>';
echo '</div>';


echo '<div class="form-group">';
echo '<label>'.$pub_lang['label_cat_description'].'</label>';
echo "<textarea class='form-control' rows='8' name='description'>$description</textarea>";
echo '</div>';



echo"<div class='formfooter'>";
echo"$hidden_field $delete_button $submit_button";
echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo"</div>";

echo"</form>";




echo '</div>';
echo '<div class="col-md-6">';









echo '<div class="scroll-container">';

for($i=0;$i<$cnt_pub_cat;$i++) {
	
	if($pub_cat[$i]['thumbnail'] == '') {
		$thumb_saved = '../modules/publisher.mod/backend/poster.jpg';
	} else {
		$thumb_saved = $pub_cat[$i]['thumbnail'];
	}
	
	echo '<a class="btn-categories" href="acp.php?tn=moduls&sub=publisher.mod&a=categories&editcat='.$pub_cat[$i]['id'].'">';
	echo '<div class="row">';
	echo '<div class="col-sm-2">';
	echo '<img src="'.$thumb_saved.'" class="img-fluid">';
	echo '</div>';
	echo '<div class="col-sm-10">';
	echo '<p><code>'.$pub_cat[$i]['sort'].'</code> <strong>'.$pub_cat[$i]['name'].'</strong><br />'.$pub_cat[$i]['description'].'</p>';
	echo '<p>Modul-Query: <code>cat='.$pub_cat[$i]['name_safe'].'</code></p>';
	echo '</div>';
	echo '</div>';
	echo '</a>';
}
echo '</div>';


echo '</div>';
echo '</div>';




?>