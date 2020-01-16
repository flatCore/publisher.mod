<?php

if(!defined('FC_INC_DIR')) {
	die("No access");
}

include '../modules/publisher.mod/lang/en/dict.php';
if(is_file("../modules/publisher.mod/lang/$languagePack/dict.php")) {
	include "../modules/publisher.mod/lang/$languagePack/dict.php";
}

echo '<h4>publisher.mod</h4>';

$addon_data = str_replace('&quot;', '"', $page_addon_string);
$addon_data = utf8_encode($addon_data);
$addon_data = json_decode($addon_data,true, 512, JSON_UNESCAPED_UNICODE);


echo '<div class="form-group">';
echo '<label>'.$pub_lang['label_entries_per_page'].'</label>';
echo '<input type="text" name="addon[entries_per_page]" value="'.$addon_data['entries_per_page'].'" class="form-control">';
echo '</div>';

echo '<fieldset class="mt-4">';
echo '<legend>'.$pub_lang['legend_filter_by_type'].'</legend>';

if($addon_data['type_filter_messages'] == 'message') {
	$check_messages = 'checked';
}

echo '<div class="form-check form-check-inline">';
echo '<input class="form-check-input" type="checkbox" id="typeMessages" value="message" name="addon[type_filter_messages]" '.$check_messages.'>';
echo '<label class="form-check-label" for="typeMessages">'.$pub_lang['type_message'].'</label>';
echo '</div>';


if($addon_data['type_filter_events'] == 'event') {
	$check_events = 'checked';
}

echo '<div class="form-check form-check-inline">';
echo '<input class="form-check-input" type="checkbox" id="typeEvents" value="event" name="addon[type_filter_events]" '.$check_events.'>';
echo '<label class="form-check-label" for="typeEvents">'.$pub_lang['type_event'].'</label>';
echo '</div>';


if($addon_data['type_filter_image'] == 'image') {
	$check_image = 'checked';
}

echo '<div class="form-check form-check-inline">';
echo '<input class="form-check-input" type="checkbox" id="typeImages" value="image" name="addon[type_filter_image]" '.$check_image.'>';
echo '<label class="form-check-label" for="typeImages">'.$pub_lang['type_image'].'</label>';
echo '</div>';

if($addon_data['type_filter_gallery'] == 'gallery') {
	$check_gallery = 'checked';
}

echo '<div class="form-check form-check-inline">';
echo '<input class="form-check-input" type="checkbox" id="typeGallery" value="gallery" name="addon[type_filter_gallery]" '.$check_gallery.'>';
echo '<label class="form-check-label" for="typeGallery">'.$pub_lang['type_gallery'].'</label>';
echo '</div>';

if($addon_data['type_filter_link'] == 'link') {
	$check_link = 'checked';
}

echo '<div class="form-check form-check-inline">';
echo '<input class="form-check-input" type="checkbox" id="typeLinks" value="link" name="addon[type_filter_link]" '.$check_link.'>';
echo '<label class="form-check-label" for="typeLinks">'.$pub_lang['type_link'].'</label>';
echo '</div>';


if($addon_data['type_filter_video'] == 'video') {
	$check_video = 'checked';
}

echo '<div class="form-check form-check-inline">';
echo '<input class="form-check-input" type="checkbox" id="typeVideos" value="video" name="addon[type_filter_video]" '.$check_video.'>';
echo '<label class="form-check-label" for="typeVideos">'.$pub_lang['type_video'].'</label>';
echo '</div>';


if($addon_data['type_filter_product'] == 'product') {
	$check_product = 'checked';
}

echo '<div class="form-check form-check-inline">';
echo '<input class="form-check-input" type="checkbox" id="typeProducts" value="product" name="addon[type_filter_product]" '.$check_product.'>';
echo '<label class="form-check-label" for="typeProducts">'.$pub_lang['type_product'].'</label>';
echo '</div>';

echo '</fieldset>';

/**
echo '<div class="form-group">';
echo '<label>'.$pub_lang['legend_filter_by_category'].'</label>';
echo '<input type="text" name="addon[filter_by_category]" value="'.$addon_data['filter_by_category'].'" class="form-control">';
echo '</div>';
**/

?>