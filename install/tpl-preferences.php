<?php

/**
 * flatNews Database-Scheme
 * install/update the table for preferences
 * 
 */

$database = "publisher";
$table_name = "preferences";

$cols = array(
	"id"  => 'INTEGER NOT NULL PRIMARY KEY',
	"status"  => 'VARCHAR',
	"url" => 'VARCHAR',
	"url_pattern" => 'VARCHAR',
	"url_separator_categories" => 'VARCHAR',
	"url_separator_pages" => 'VARCHAR',
	"images_prefix" => 'VARCHAR',
	"default_banner" => 'VARCHAR',
	"default_template" => 'VARCHAR',
	"ignore_inline_css" => 'VARCHAR',
	"default_page_template_entries" => 'VARCHAR',
	"intro_snippet" => 'VARCHAR',
	"entries_per_page" => 'INTEGER',
	"products_default_tax" => 'INTEGER',
	"products_default_currency" => 'VARCHAR',
	"version" => 'VARCHAR',
	"event_time_offset" => 'VARCHAR'
  );
  
  
 
?>
