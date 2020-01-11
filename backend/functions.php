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
	
	
?>