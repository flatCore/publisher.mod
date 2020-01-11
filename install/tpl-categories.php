<?php

/**
 * publisher Database-Scheme
 * install/update the table for categories
 * 
 */

$database = "publisher";
$table_name = "categories";

$cols = array(
	"id"  => 'INTEGER NOT NULL PRIMARY KEY',
	"name"  => 'VARCHAR',
	"name_safe"  => 'VARCHAR',
	"hash"  => 'VARCHAR',
	"description"  => 'VARCHAR',
	"thumbnail"  => 'VARCHAR',
	"sort"  => 'VARCHAR',
	"counter" => 'VARCHAR'
  );
 
?>
