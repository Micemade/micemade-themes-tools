<?php
/*
Plugin Name: Micemade themes tools
Plugin URI: http://micemade.com
Description: Used for Micemade themes. Extension plugin for theme setup wizard and few bonus functionalities.
Version: 0.1.1
Author: Micemade themes
Author URI: http://micemade.com
Text Domain: micemade-themes-tools

Copyright: © 2017 Micemade.
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

function micemade_themes_tools_textdomain() {
					
	$lang_dir = apply_filters('micemade_themes_tools_lang_dir', trailingslashit( MICEMADE_ELEMENTS_DIR . 'languages') );

	// Traditional WordPress plugin locale filter
	$locale = apply_filters('plugin_locale', get_locale(), 'micemade-themes-tools');
	$mofile = sprintf('%1$s-%2$s.mo', 'micemade-themes-tools', $locale);

	// Setup paths to current locale file
	$mofile_local = $lang_dir . $mofile;

	if ( file_exists( $mofile_local ) ) {
		// Look in the /wp-content/plugins/micemade-themes-tools/languages/ folder
		load_textdomain('micemade-themes-tools', $mofile_local);
	} else {
		// Load the default language files
		load_plugin_textdomain('micemade-themes-tools', false, $lang_dir);
	}

	return false;
}


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
		$contactmethods['google_profile']	= esc_html__("Google Profile URL", $current_theme, 'micemade-themes-tools' );
		$contactmethods['twitter_profile']	= esc_html__("Twitter Profile URL", $current_theme, 'micemade-themes-tools' );
		$contactmethods['facebook_profile'] = esc_html__("Facebook Profile URL", $current_theme, 'micemade-themes-tools' );
		$contactmethods['linkedin_profile']	= esc_html__("Linkedin Profile URL", $current_theme, 'micemade-themes-tools' );
		$contactmethods['skype']			= esc_html__("Skype","micemade-themes-tools");
		
		return $contactmethods;
	}
	add_filter( 'user_contactmethods', 'micemade_micemade_add_to_author_profile', 10, 1);
	
	// SOCIAL SHARING BUTTONS
	function micemade_social_buttons( $content ) {
		global $post;
		$permalink	= get_permalink($post->ID);
		$thumb		= wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
		$title		= get_the_title();
		$title		= str_replace(" ","&nbsp;",$title);
		$share_txt	= esc_html__( "Share this on ","micemade-themes-tools" );
	   
	   if( !is_feed() && !is_home() && !is_page() && !is_archive() ) {
			
			$content = $content . '<div class="share-post"><p>'. esc_html__('Share this','micemade-themes-tools'). '</p><div class="social">
			<a class="icon-twitter tip-top share-link" href="http://twitter.com/share?text='. esc_attr( $title ) .'&amp;url='. esc_url ($permalink ) .'"
				onclick="window.open(this.href, \'twitter-share\', \'width=550,height=235\');return false;" title="'. esc_attr( $share_txt ) .'Twitter">
				<i class="fa fa-twitter" aria-hidden="true"></i>
			</a>   
				  
			<a class="icon-fb tip-top share-link" href="https://www.facebook.com/sharer/sharer.php?u='. esc_url( $permalink ) .'"
				 onclick="window.open(this.href, \'facebook-share\',\'width=580,height=296\');return false;" title="'. esc_attr( $share_txt ) .'Facebook">
				<i class="fa fa-facebook" aria-hidden="true"></i>
			</a>
			  
			<a class="icon-gplus tip-top share-link" href="https://plus.google.com/share?url='. esc_url( $permalink ) .'"
			   onclick="window.open(this.href, \'google-plus-share\', \'width=490,height=530\');return false;" title="'. esc_attr( $share_txt ).'Google Plus">
				<i class="fa fa-google-plus" aria-hidden="true"></i>
			</a>
			  
			<a class="icon-pinterest tip-top share-link" href="https://pinterest.com/pin/create/button/?url='. esc_url( $permalink ) .'&amp;media='. esc_url( $thumb[0] ).'&amp;description='. esc_attr( $title ).'" onclick="window.open(this.href, \'pinterest-share\', \'width=490,height=530\');return false;" title="'. esc_attr( $share_txt ) .'Pinterest">
				<i class="fa fa-pinterest" aria-hidden="true"></i>
			</a>
			
		</div></div>';
		}
		return $content;
	}
	add_filter('the_content', 'micemade_social_buttons');
	
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
// Load plugin textdomain
micemade_themes_tools_textdomain();