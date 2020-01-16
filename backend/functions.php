<?php
	


/* list template folders */
function pub_list_template_folders() {
	$tpl_folders = array();
	
	$directory = "../modules/publisher.mod/styles";
	
	if(is_dir($directory)) {
	
		$all_folders = glob("$directory/*");
		
		foreach($all_folders as $v) {
			if((is_dir("$v") && $v != "$directory/default")) {
				$tpl_folders[] = basename($v);
			}
		}
	
	 }
	 
	 return $tpl_folders;
}


function pub_list_gallery_thumbs($gid) {
	
	global $mod_db;
	global $icon;
	$gid = (int) $gid;
	
	$dbh = new PDO("sqlite:$mod_db");
	$sql = 'SELECT date from posts WHERE id = :id';
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':id', $gid, PDO::PARAM_INT);
	
	$sth->execute();
	$date = $sth->fetch(PDO::FETCH_ASSOC);
	$dbh = null;
	
	$filepath = '../content/publisher/galleries/'.date('Y',$date['date']).'/gallery'.$gid.'/*_tmb.jpg';
	
	$thumbs_array = glob("$filepath");
	arsort($thumbs_array);
	
	$thumbs = '';
	foreach($thumbs_array as $tmb) {
		$thumbs .= '<div class="tmb">';
		$thumbs .= '<div class="tmb-preview"><img src="'.$tmb.'" class="img-fluid"></div>';
		$thumbs .= '<div class="tmb-actions d-flex btn-group">';
		$thumbs .= '<button type="submit" name="sort_tmb" value="'.$tmb.'" class="btn btn-sm btn-fc w-100">'.$icon['angle_up'].'</button>';
		$thumbs .= '<button type="submit" name="del_tmb" value="'.$tmb.'" class="btn btn-sm btn-danger w-50">'.$icon['trash_alt'].'</button>';
		$thumbs .= '</div>';
		$thumbs .= '</div>';
	}
	
	
	$str = '';
	$str .= $thumbs;
	
	return $str;
		
}

function pub_rename_image($thumb) {
	
	$timestring = microtime(true);
	
	$path_parts = pathinfo($thumb);
	$dir = $path_parts['dirname'].'/';
	$tmb = $dir.$path_parts['basename'];
	$img = str_replace("_tmb", "_img", $tmb);
	
	$new_tmb = $dir.$timestring.'_tmb.jpg';
	$new_img = $dir.$timestring.'_img.jpg';

	
	rename("$tmb", "$new_tmb");
	rename("$img", "$new_img");
	
}


function pub_remove_gallery($id,$dir) {

	$fp = '../content/publisher/galleries/'.$dir.'/gallery'.$id.'/';
	$files = glob("$fp*jpg");

	foreach($files as $file) {
		unlink($file);
	}
	
	rmdir($fp);
	
	
}
	
	
?>
