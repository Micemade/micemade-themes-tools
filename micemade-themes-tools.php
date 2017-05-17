<?php
/*
Plugin Name: Micemade themes tools
Plugin URI: http://micemade.com
Description: Used for Micemade themes. Extension plugin for theme setup wizard and few bonus functionalities.
Version: 0.1.1
Author: Micemade themes
Author URI: http://micemade.com


Copyright: © 2017 Micemade.
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Get info about currently active theme
$current_theme		= get_option( 'template' );
$micemade_themes	= array( "natura", "adorn", "cloth" );

// Check if active theme is a Micemade theme
if( in_array( $current_theme , $micemade_themes ) ) {

	// Theme setup wizard - import product attributes helper - register atts taxonomies to import attribute terms
	function micemade_import_wc_attibutes_helper( $attribute_name ) {
		
		register_taxonomy(
			'pa_' . $attribute_name,
			apply_filters( 'woocommerce_taxonomy_objects_' . $attribute_name, array( 'product' ) ),
			apply_filters( 'woocommerce_taxonomy_args_' . $attribute_name, array(
				'hierarchical' => true,
				'show_ui'      => false,
				'query_var'    => true,
				'rewrite'      => false,
			) )
		);
		register_taxonomy_for_object_type( 'pa_' . $attribute_name, 'product' );
	}
	
	// Adds inputs for author social links in WP admin - Users - Your profile
	function micemade_micemade_add_to_author_profile( $contactmethods ) {
	
		$current_theme		= get_option( 'template' );
		
		$contactmethods['rss_url']			= 'RSS URL';
		$contactmethods['google_profile']	= esc_html__("Google Profile URL", $current_theme );
		$contactmethods['twitter_profile']	= esc_html__("Twitter Profile URL", $current_theme );
		$contactmethods['facebook_profile'] = esc_html__("Facebook Profile URL", $current_theme );
		$contactmethods['linkedin_profile']	= esc_html__("Linkedin Profile URL", $current_theme );
		$contactmethods['skype']			= esc_html__("Skype","natura");
		
		return $contactmethods;
	}
	add_filter( 'user_contactmethods', 'micemade_micemade_add_to_author_profile', 10, 1);
}
// Plugin Github updater function
if( ! function_exists( 'micemade_themes_tools_updater' ) ) {
	
	function micemade_themes_tools_updater() {
				
		require_once( plugin_dir_path( __FILE__ ) . 'github_updater.php' );
		if ( is_admin() ) {
			new Micemade_GitHub_Plugin_Updater( __FILE__, 'Micemade', "micemade-themes-tools" );
		}
		
	}
}
// run the updater
micemade_themes_tools_updater();
?>