<?php 

 /* Twitterify options */

$hashtags_link_to_options = array(
	'tags' => 'Hashtags / Tags',
	'search' => 'Search Page',
	'twitter' => 'Twitter'
);


$twitterify_options = array(

	array( "name" => __( "General Settings", 'twitterify' ),
		"type" => "section"),

	array( "type" => "open"),

		array(
			"type" => "checkbox",
			"name" => __( 'Use custom taxonomy', 'twitterify' ),
			"id" => "use_hashtag_tax",
			"desc" => __( "Use hashtag taxonomy to link posts <a href='https://wpassist.me/twitterify-options/use-custom-taxonomy/' class='helplink' target='_blank'>(?)</a> </span>", 'twitterify' ),
			"std" => "on"
		),

		array(
			"type" => "checkbox",
			"name" => __( 'Hide hash symbols', 'twitterify' ),
			"id" => "hide_hash",
			"desc" => __( "Hide # symbols in front of the tags <a href='https://wpassist.me/twitterify-options/hide-hash-symbols/' class='helplink' target='_blank'>(?)</a> </span>", 'twitterify' ),
			"std" => "off"
		),

		array(
			"type" => "select",
			"name" => __( 'Hashtags link to', 'twitterify' ),
			"id" => "hashtags_link_to",
			"desc" => __( "Choose where to link your hashtags <a href='https://wpassist.me/twitterify-options/hashtags-link-to/' class='helplink' target='_blank'>(?)</a> </span>", 'twitterify' ),
			"std" => "",
			"options" => $hashtags_link_to_options
		),

		array(
			"type" => "checkbox",
			"name" => __( 'Autolink', 'twitterify' ),
			"id" => "use_autolink",
			"desc" => __( "Convert urls to links <a href='https://wpassist.me/twitterify-options/use-auto-link/' class='helplink' target='_blank'>(?)</a> </span>", 'twitterify' ),
			"std" => "off"
		),

		array(
			"type" => "checkbox",
			"name" => __( 'Use mentions', 'twitterify' ),
			"id" => "use_mentions",
			"desc" => __( "Convert @ mentions to author links <a href='https://wpassist.me/twitterify-options/use-mentions/' class='helplink' target='_blank'>(?)</a> </span>", 'twitterify' ),
			"std" => "off"
		),

	array( "type" => "close")

);