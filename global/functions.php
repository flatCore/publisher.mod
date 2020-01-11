<?php
	
/**
 * global publisher functions for backend and frontend
 * please prefix publisher functions 'pub_'
 */



/**
 * get list of entries
 * $start (int)
 * $limit (int)
 * $filter (array) - filter by type, language ...
 * 
 */
 
function pub_get_entries($start=0,$limit=10,$filter) {
	
	global $mod_db;
	global $mod;
	global $time_string_start;
	global $time_string_end;
	global $time_string_now;
		
	if(FC_SOURCE == 'frontend') {
		$mod_db = $mod['database'];
	}
	
	$limit_str = 'LIMIT '. (int) $start;
	
	if($limit == 'all') {
		$limit_str = '';
	} else {
		$limit_str .= ', '. (int) $limit;
	}
	
	
	/**
	 * order and direction
	 * we ignore $order and $direction
	 */

	$order = 'fixed DESC, sortdate DESC, priority DESC, id DESC';
	
	if($direction == 'ASC') {
		$direction = 'ASC';
	} else {
		$direction = 'DESC';
	}
	
	
	
	/* set filters */
	$sql_filter_start = 'WHERE id IS NOT NULL ';
	
	/* language filter */
	$sql_lang_filter = "lang ISNULL OR ";
	$lang = explode('-', $filter['languages']);
	foreach($lang as $l) {
		if($l != '') {
			$sql_lang_filter .= '(lang LIKE "%'.$l.'%") OR ';
		}		
	}
	$sql_lang_filter = substr("$sql_lang_filter", 0, -3); // cut the last ' OR'
	
	
	/* type filter */
	$sql_types_filter = "type ISNULL OR ";
	$types = explode('-', $filter['types']);
	foreach($types as $t) {
		if($t != '') {
			$sql_types_filter .= '(type LIKE "%'.$t.'%") OR ';
		}		
	}
	$sql_types_filter = substr("$sql_types_filter", 0, -3); // cut the last ' OR'

	/* status filter */
	$sql_status_filter = "status ISNULL OR ";
	$status = explode('-', $filter['status']);
	foreach($status as $s) {
		if($s != '') {
			$sql_status_filter .= '(status LIKE "%'.$s.'%") OR ';
		}		
	}
	$sql_status_filter = substr("$sql_status_filter", 0, -3); // cut the last ' OR'
	
	if(FC_SOURCE == 'frontend') {
		$sql_status_filter = "status LIKE 'published' ";
	}
	
	/* category filter */
	if($filter['categories'] == 'all') {
		$sql_cat_filter = '';
	} else {
		$sql_cat_filter = "categories LIKE '%".$filter['categories']."%'";
	}


	$sql_filter = $sql_filter_start;
	
	if($sql_lang_filter != "") {
		$sql_filter .= " AND ($sql_lang_filter) ";
	}
	if($sql_types_filter != "") {
		$sql_filter .= " AND ($sql_types_filter) ";
	}
	if($sql_status_filter != "") {
		$sql_filter .= " AND ($sql_status_filter) ";
	}
	if($sql_cat_filter != "") {
		$sql_filter .= " AND ($sql_cat_filter) ";
	}
	
	if($time_string_start != '') {
		$sql_filter .= "AND releasedate >= '$time_string_start' AND releasedate <= '$time_string_end' AND releasedate < '$time_string_now' ";
	}
	
	$dbh = new PDO("sqlite:$mod_db");
	
	$sql = "SELECT *, strftime('%Y-%m-%d',datetime(releasedate, 'unixepoch')) as 'sortdate' FROM posts $sql_filter ORDER BY $order $limit_str";
	//echo $sql;
	$sth = $dbh->prepare($sql);
	$sth->execute();
	$entries = $sth->fetchAll(PDO::FETCH_ASSOC);
	
	$sql_cnt = "SELECT count(*) AS 'A', (SELECT count(*) FROM posts $sql_filter) AS 'F'";
	$stat = $dbh->query("$sql_cnt")->fetch(PDO::FETCH_ASSOC);

	
	$dbh = null;
	
	/* number of posts that match the filter */
	$entries[0]['cnt_posts'] = $stat['F'];
	
	
	return $entries;
	
}






/**
 * count all entries
 */
 
function pub_cnt_entries() {
	
	global $mod_db;
	global $mod;
	
	if(FC_SOURCE == 'frontend') {
		$mod_db = $mod['database'];
	}
	
	$dbh = new PDO("sqlite:$mod_db");
	$sql = "SELECT count(*) AS 'All',
		(SELECT count(*) FROM posts WHERE status LIKE '%published%' ) AS 'Public',
		(SELECT count(*) FROM posts WHERE status LIKE '%draft%' ) AS 'Draft',
		(SELECT count(*) FROM posts WHERE type LIKE '%message%' ) AS 'Message',
		(SELECT count(*) FROM posts WHERE type LIKE '%link%' ) AS 'Link',
		(SELECT count(*) FROM posts WHERE type LIKE '%video%' ) AS 'Video',
		(SELECT count(*) FROM posts WHERE type LIKE '%image%' ) AS 'Image',
		(SELECT count(*) FROM posts WHERE type LIKE '%event%' ) AS 'Event'
	FROM posts
	";
	$stats = $dbh->query("$sql")->fetch(PDO::FETCH_ASSOC);
	return $stats;
}





/**
 * get data of a post
 * $post (int) or (string)
 * if $post is numeric get data by 'id'
 * if $post is a string, get data by 'slug'
 * 
 */
function pub_get_post_data($post) {
	
	global $mod_db;
	global $mod;
	
	if(FC_SOURCE == 'frontend') {
		$mod_db = $mod['database'];
	}
	
	$dbh = new PDO("sqlite:$mod_db");
	
	if(is_numeric($post)) {
		$id = (int) $post;
		$sql = "SELECT * FROM posts WHERE id = :id";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':id', $id, PDO::PARAM_STR);
	} else {
		$sql = "SELECT * FROM posts WHERE slug = :slug ";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':slug', $post, PDO::PARAM_STR);
	}
	$sth->execute();
	$item = $sth->fetch(PDO::FETCH_ASSOC);
	$dbh = null;
	return $item;	
}


/**
 * get preferences
 *
 */

function pub_get_preferences() {
	
	global $mod_db;
	global $mod;
	
	if(FC_SOURCE == 'frontend') {
		$mod_db = $mod['database'];
	}
	
	$dbh = new PDO("sqlite:$mod_db");
	$sql = "SELECT * FROM preferences WHERE status LIKE '%active%' ";
	$prefs = $dbh->query($sql);
	$prefs = $prefs->fetch(PDO::FETCH_ASSOC);
	$dbh = null;
	
	
	
	return $prefs;
	
}



/**
 * get categories
 *
 */

function pub_get_categories() {
	
	global $mod_db;
	global $mod;
	
	if(FC_SOURCE == 'frontend') {
		$mod_db = $mod['database'];
	}
	
	$dbh = new PDO("sqlite:$mod_db");
	$sql = "SELECT * FROM categories ORDER BY sort ASC";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	$entries = $sth->fetchAll(PDO::FETCH_ASSOC);
	$dbh = null;
	return $entries;

}



?>