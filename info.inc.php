<?php

/**
 * publisher | flatCore Modul
 * Configuration File
 */

if(FC_SOURCE == 'backend') {
	$mod_root = '../modules/';
} else {
	$mod_root = 'modules/';
}

include $mod_root.'publisher.mod/lang/en/dict.php';

if(is_file($mod_root.'publisher.mod/lang/'.$languagePack.'/dict.php')) {
	include $mod_root.'publisher.mod/lang/'.$languagePack.'/dict.php';
}

$mod = array(
	"name" => "publisher",
	"version" => "0.0.7",
	"author" => "KONSTAND · Das Designbüro",
	"description" => "Publish News, Events, Products, Images, Galleries, Videos or Links",
	"database" => "content/SQLite/publisher.sqlite3"
);


/* acp */
$modnav[] = array('link' => $pub_lang['nav_new_edit'], 'title' => $pub_lang['nav_new_edit_title'], 'file' => "edit");
$modnav[] = array('link' => $pub_lang['nav_categories'], 'title' => $pub_lang['nav_categories_title'], 'file' => "categories");
$modnav[] = array('link' => $pub_lang['nav_preferences'], 'title' => $pub_lang['nav_preferences_title'], 'file' => "prefs");

?>