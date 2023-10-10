<?php 
/*
Plugin Name: Twitterify
Plugin URI: http://wpassist.me/plugins/twitterify/
Description: Enables use of <strong>autolink</strong>, <strong>#hashtags</strong> and <strong>@author</strong> links on your posts. <strong>Links are not directed to twitter. They provide this functionality on your site.</strong>
Version: 1.3
Author: WP Assist
Author URI: http://wpassist.me/
*/

global $twitterify;

add_action( 'save_post', 'twitterify_check_post_tags' );
function twitterify_check_post_tags( $post_id ) {
	global $twitterify;
	
	if ( wp_is_post_revision( $post_id ) )
		return;

	$content = do_shortcode( get_post_field( 'post_content', $post_id ) );
	
	if( strlen( $content ) > 0 ) {
		$found = preg_match_all( "|([^&//])#([A-Za-z0-9_-]+)|is", $content, $matches );
		
		if( $found !== 0 || false !== $found ) {
			$twitterify_tags = join( ',', $matches[2] );
			update_post_meta( $post_id, 'twitterify_tags_cache', $twitterify_tags );
			if( $twitterify->get_plugin_setting('use_hashtag_tax') == 'on' ){
				$twitterify_tags = explode( ',', $twitterify_tags );
				wp_set_object_terms( $post_id, $twitterify_tags, 'hashtag', false );
			} else {
				wp_set_post_tags( $post_id, $twitterify_tags, true );
			}
		}
	}
}


if(!class_exists('stf_twitterify')){
class stf_twitterify {

	public function __construct(){

		$this->version = "1.2";
		$this->settings_key = "stf_twitterify";
		$this->options_page = "twitterify";
	
		// Get permalink structure
		if ( get_option('permalink_structure') != '' ) { 

			// Permalinks enabled
			$permalink = get_option( 'permalink_structure', '' );
			
			$prefix = '';
			if( strpos( $permalink, '/index.php/') !== false ){
				$prefix = '/index.php';
			}
			
			if( $this->get_plugin_setting('use_hashtag_tax') == 'on' ){
				$tag_base = 'hashtag';
				$this->tag_base = $prefix . "/" . $tag_base . "/";
			} else {
				$tag_base = get_option( 'tag_base', 'tag' );
				if($tag_base == ''){ $tag_base = 'tag'; }
				$this->tag_base = $prefix . "/" . $tag_base . "/";
			}
			
			$this->author_base = $prefix . "/author/";
			
		} else {

			// Permalinks not enabled
			$this->tag_base = "?tag=";

			if( $this->get_plugin_setting('use_hashtag_tax') == 'on' ){
				$this->tag_base = "?hashtag=";
			} else {
				$this->tag_base = "?tag=";
			}

			$this->author_base = "?author=";
			
		}
		
		// Register filters
		add_filter( 'the_content', array( &$this, 'twitterify_content' ), 99, 1 );
		add_filter( 'the_excerpt', array( &$this, 'twitterify_content' ), 99, 1 );
		
		// Include options
		require_once("twitterify-options.php");
		$this->options = $twitterify_options;
		$this->settings = $this->get_plugin_settings();
		
		add_action('admin_menu', array( &$this, 'admin_menu') );
		
	}

	function stf_twitterify(){
		self::__construct();
	}

	function get_plugin_settings(){
		$settings = get_option( $this->settings_key );		
		
		if(FALSE === $settings && isset($this->options)){ // Options doesn't exist, install standard settings
			// Create settings array
			$settings = array();
			// Set default values
			foreach($this->options as $option){
				if( array_key_exists( 'id', $option ) )
					$settings[ $option['id'] ] = $option['std'];
			}
			
			$settings['version'] = $this->version;
			// Save the settings
			update_option( $this->settings_key, $settings );
		} else { // Options exist, update if necessary
			
			if( !empty( $settings['version'] ) ){ $ver = $settings['version']; } 
			else { $ver = ''; }
			
			if($ver != $this->version && isset($this->options)){ // Update needed
			
				// Add missing keys
				foreach($this->options as $option){
					if( array_key_exists ( 'id' , $option ) && !array_key_exists ( $option['id'] ,$settings ) ){
						$settings[ $option['id'] ] = $option['std'];
					}
				}
				
				update_option( $this->settings_key, $settings );
				
				return $settings; 
			} else { 
			
				// Everythings gonna be alright. Return.
				return $settings;
			} 
		}		
	}

	function update_plugin_setting( $key, $value ){
		$settings = $this->get_plugin_settings();
		$settings[$key] = $value;
		update_option( $this->settings_key, $settings );
	}

	function get_plugin_setting( $key, $default = '' ) {
		$settings = $this->get_plugin_settings();
		if( is_array( $settings ) && array_key_exists($key, $settings) ){
			return $settings[$key];
		} else {
			return $default;
		}
		
		return FALSE;
	}

	function admin_menu(){

		if ( array_key_exists( 'page', $_GET ) && $_GET['page'] == $this->options_page ) {		
			
			if ( array_key_exists( 'action', $_REQUEST ) && 'save' == $_REQUEST['action'] ) {
			
				// Save settings
				// Get settings array
				$settings = $this->get_plugin_settings();
				
				// Set updated values
				foreach($this->options as $option){					
					if( $option['type'] == 'checkbox' && empty( $_REQUEST[ $option['id'] ] ) ) {
						$settings[ $option['id'] ] = 'off';
					} else {
						$settings[ $option['id'] ] = $_REQUEST[ $option['id'] ]; 
					}
				}

				// Save the settings
				update_option( $this->settings_key, $settings );
				header("Location: admin.php?page=" . $this->options_page . "&saved=true&message=1");
				die;
			} else if( array_key_exists( 'action', $_REQUEST ) && 'reset' == $_REQUEST['action'] ) {
				// Remove settings key
				delete_option( $this->settings_key );
				header("Location: admin.php?page=" . $this->options_page . "&reset=true&message=2");
				die;
			}
			
			// Enqueue scripts & styles
			wp_enqueue_style( "twitterify-admin", plugins_url( '/twitterify.css' , __FILE__ ), false, "1.0", "all");	
			
		}

		$page = add_options_page( __('Twitterify Options', 'twitterify') , __('Twitterify', 'twitterify'), 'edit_themes', $this->options_page, array( &$this, 'options_page') );
	}

	function options_page(){
		global $options, $current;

		$title = "Twitterify Options";
		
		$options = $this->options;	
		$current = $this->get_plugin_settings();
		
		$messages = array( 
			"1" => __("Twitterify settings saved.", "twitterify"),
			"2" => __("Twitterify settings reset.", "twitterify")
		);
		
		$navigation = '<div id="stf_nav"><a href="https://wpassist.me/plugins/twitterify/" target="_blank">Plugin page</a> | <a href="https://wpassist.me/twitterify-usage/" target="_blank">Usage</a> | <a href="https://wpassist.me/donate/" target="_blank">Donate</a> | <a href="https://wpassist.me/get-more/" target="_blank">Get more..</a></div>';
		
		include_once( dirname(__FILE__) . "/inc/stf-page-options.php" );

	}

	function twitterify_content ( $content ){ 
		$content = preg_replace_callback ( "#(.*?)(\<([a-z]+)[^\>]*\>([^\<]*?)\<\/(\\3)[^\>]*\>)(.*?)#is", array( &$this, 'twitterify_filter_codes' ), $content );
		return $content;
	}

	function twitterify_filter_codes( $matches = array() ){ 
	
		if( $matches[3] !== 'code' && $matches[3] !== 'pre' ){
			return $this->twitterify_text( $matches[0] );
		} else {
			return $matches[0];
		}
		
	}

	function twitterify_text( $text ){
		global $twitterify;

		$ret = ' ' . $text;

		if( $this->get_plugin_setting('use_autolink') == 'on' ){
			$ret = preg_replace_callback( "#(^|[\n> ])([\w]+?://[\w]+[^ \"\n\r\t<]*)#is", array( &$this, 'twitterify_url_callback' ), $ret );
		
			// links starting with www and ftp
			$ret = preg_replace_callback( "#(^|[\n> ])((www|ftp)\.[^ \"\t\n\r<]*)#is", array( &$this, 'twitterify_wwwftp_callback' ), $ret);
		
			// Remove http://
			$ret = preg_replace( '/(>http[s]?:\/\/)(.*?)<\/a>/i', ">$2</a>", $ret ); 
		
		 	// Remove www.
			$ret = preg_replace( '/(>www.)(.*?)<\/a>/i', ">$2</a>", $ret );
		}
		
		// Author links
		$author_pattern = "/([\n> ])@([A-Za-z0-9_]+)/is";
		$ret = preg_replace_callback ( $author_pattern, array( &$this, 'twitterify_author_callback' ), $ret );

		// Hashtags
		$hashtag_pattern = "{([^&//])#([A-Za-z0-9_-]+)}is";
		$ret = preg_replace_callback ( $hashtag_pattern, array( &$this, 'twitterify_tag_callback' ), $ret );
		
		// Return post content
		return '<!-- Twitterify Start [ -->' . substr( $ret, 1 ) . '<!-- ] Twitterify End -->';
	}
	
	function twitterify_wwwftp_callback( $matches ){
		
		if( substr( $matches[2], 0, 3 ) === "ftp" ){
			return $matches[1] . '<a target="_blank" rel="nofollow" href="ftp://' . $matches[2] . '" >' . $matches[2] . '</a>';
		} else {
			return $matches[1] . '<a target="_blank" rel="nofollow" href="http://' . $matches[2] . '" >' . $matches[2] . '</a>';
		}
	}

	function twitterify_url_callback( $matches ){
		
		return $matches[1] . '<a target="_blank" rel="nofollow" href="' . $matches[2] . '" >' . $matches[2] . '</a>';
		
	}

	// Check if author exists
	function twitterify_author_callback( $matches ){
		global $author_base;
		
		if ( username_exists( $matches[2] ) ){
			return $matches[1] . "<a href='" . home_url( $this->author_base ) . $matches[2] . "'>@" . $matches[2] . "</a>";
		} else {
			return $matches[1] . "<a href='http://twitter.com/" . $matches[2] . "'>@" . $matches[2] . "</a>";
		}
	}

	// Check tags for color codes
	function twitterify_tag_callback( $matches ){
		global $tag_base;
		
		// Check fox hex color codes 
		if( strlen( $matches[2] ) === 3 || strlen( $matches[2] ) === 6 ){
			// Check for chars
			if( strlen( preg_replace("/[^0-9A-Fa-f]/", '', $matches[2])) === 6 || strlen( preg_replace("/[^0-9A-Fa-f]/", '', $matches[2])) === 3 ){
				// Surely, hexadecimal value
				return $matches[1] . "#" . $matches[2];
			}
		}
		
		$hash = '#';
		if( 'on' == $this->get_plugin_setting('hide_hash') )
			$hash = '';
			
		$hashtags_link_to = $this->get_plugin_setting( 'hashtags_link_to' );
		
		if( 'twitter' == $hashtags_link_to ){
			$hash_base = "http://twitter.com/search?q=%23";
		} elseif ( 'search' == $hashtags_link_to ){
			$hash_base = home_url( '?s=' );
		} else {
			$hash_base = home_url( $this->tag_base );
		}
		
		return $matches[1] . " <a href='" . $hash_base . "". $matches[2] ."/'>" . $hash . $matches[2] . "</a>";
	}

} } // stf_twitterify

$twitterify = new stf_twitterify();

if( $twitterify->get_plugin_setting('use_hashtag_tax') == 'on' ){
	add_action( 'init', 'twitterify_create_hashtag_tax' );
	function twitterify_create_hashtag_tax() {
		global $wp_rewrite, $twitterify;
	
		if( taxonomy_exists('hashtag') )
			return;
		register_taxonomy(
			'hashtag',
			'post',
			array(
				'label' => __( 'Hashtag' ),
				'rewrite' => array( 'slug' => 'hashtag' ),
				'hierarchical' => false,
			)
		);
	
		$wp_rewrite->flush_rules();
	}
}
