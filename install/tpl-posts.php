<?php

/**
 * flatNews Database-Scheme
 * install/update the table for news
 * 
 */

$database = "publisher";
$table_name = "posts";

$cols = array(
	"id"  => 'INTEGER NOT NULL PRIMARY KEY',
	"type"  => 'VARCHAR', /* message | images | event | video | link */
	"date"  => 'VARCHAR', /* timestring entry time */
	"releasedate"  => 'VARCHAR', /* timestring release time */
	"lastedit"  => 'VARCHAR', /* timestring last edit time */
	"lastedit_from"  => 'VARCHAR', /* User */
	"title" => 'VARCHAR',
	"teaser" => 'VARCHAR',
	"text" => 'VARCHAR',
	"images" => 'VARCHAR',
	"tags" => 'VARCHAR',
	"link" => 'VARCHAR',
	"video_url" => 'VARCHAR',
	"categories" => 'VARCHAR',
	"comments" => 'LONGTEXT',
	"author" => 'VARCHAR',
	"source" => 'VARCHAR',
	"status" => 'VARCHAR',
	"user" => 'VARCHAR',
	"usergroups" => 'VARCHAR',
	"webcode" => 'VARCHAR',
	"rss" => 'VARCHAR',
	"rss_url" => 'VARCHAR',
	"lang" => 'VARCHAR',
	"slug" => 'VARCHAR',
	"hidden" => 'VARCHAR',
	"priority" => 'INTEGER',
	"fixed" => 'VARCHAR',
	"rating" => 'NUMERIC',
	"rating_cnt" => 'INTEGER',
	"related" => 'VARCHAR',
	"hits" => 'INTEGER',
	"group_id" => 'INTEGER',
	"label" => 'VARCHAR',
	"attachments" => 'VARCHAR',
	/* events */
	"startdate"  => 'VARCHAR',
	"enddate" => 'VARCHAR',
	"event_zip" => 'VARCHAR',
	"event_city" => 'VARCHAR',
	"event_street" => 'VARCHAR',
	"event_street_nbr" => 'VARCHAR',
	"event_hotline" => 'VARCHAR',
	"event_price_cat1" => 'VARCHAR',
	"event_price_cat1_description" => 'VARCHAR',
	"event_price_cat2" => 'VARCHAR',
	"event_price_cat2_description" => 'VARCHAR',
	"event_price_cat3" => 'VARCHAR',
	"event_price_cat3_description" => 'VARCHAR',
	"event_price_cat4" => 'VARCHAR',
	"event_price_cat4_description" => 'VARCHAR',
	"event_price_cat5" => 'VARCHAR',
	"event_price_cat5_description" => 'VARCHAR',
	"event_price_cat6" => 'VARCHAR',
	"event_price_cat6_description" => 'VARCHAR',
	"event_price_note" => 'VARCHAR',
	/* products */
	"product_number" => 'VARCHAR',
	"product_manufacturer" => 'VARCHAR',
	"product_supplier" => 'VARCHAR',
	"product_tax" => 'INTEGER',
	"product_price_net" => 'VARCHAR',
	"product_price_label" => 'VARCHAR',
	"product_quantity_price_net_1" => 'VARCHAR',
	"product_quantity_price_net_1_amount" => 'VARCHAR',
	"product_quantity_price_net_2" => 'VARCHAR',
	"product_quantity_price_net_2_amount" => 'VARCHAR',
	"product_quantity_price_net_3" => 'VARCHAR',
	"product_quantity_price_net_3_amount" => 'VARCHAR',
	"product_quantity_price_net_4" => 'VARCHAR',
	"product_quantity_price_net_4_amount" => 'VARCHAR',
	"product_quantity_price_net_5" => 'VARCHAR',
	"product_quantity_price_net_5_amount" => 'VARCHAR',
	"product_quantity_price_net_6" => 'VARCHAR',
	"product_quantity_price_net_6_amount" => 'VARCHAR',
	"product_quantity_price_net_7" => 'VARCHAR',
	"product_quantity_price_net_7_amount" => 'VARCHAR',
	"product_quantity_price_net_8" => 'VARCHAR',
	"product_quantity_price_net_8_amount" => 'VARCHAR',
	"product_quantity_price_net_9" => 'VARCHAR',
	"product_quantity_price_net_9_amount" => 'VARCHAR',
	"product_quantity_price_net_10" => 'VARCHAR',
	"product_quantity_price_net_10_amount" => 'VARCHAR',
	"product_textlib_price" => 'VARCHAR',
	"product_textlib_content" => 'VARCHAR',
	"product_currency" => 'VARCHAR',
	"product_unit" => 'VARCHAR',
	"product_amount" => 'VARCHAR',
	"product_width" => 'VARCHAR',
	"product_height" => 'VARCHAR',
	"product_depth" => 'VARCHAR',
);
 
?>