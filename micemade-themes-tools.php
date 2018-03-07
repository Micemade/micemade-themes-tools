<?php
/**
 * Plugin Name: Micemade themes tools
 * Plugin URI: http://micemade.com
 * Description: Used for Micemade themes. Extension plugin for theme setup wizard and few bonus functionalities.
 * Version: 0.1.7
 * Author: Micemade themes
 * Author URI: http://micemade.com
 * Text Domain: micemade-themes-tools

 * Copyright: © 2017 Micemade.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Micemade Themes Tools
 */
class Micemade_Themes_Tools {

	/**
	 * Instance
	 *
	 * @var [type]
	 */
	private static $instance = null;
	/**
	 * Micemade theme active check
	 *
	 * @var boolean
	 */
	public $micemade_theme_active = false;

	/**
	 * Get instance
	 *
	 * @return self::$instance
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function init() {

		add_action( 'init', array( self::$instance, 'Load_plugin' ) );

	}

	/**
	 * Load plugin
	 * 
	 * @return void
	 * 
	 * fired upon 'init' hook - to check if Micemade theme is active
	 * delay plugin method after theme initializes
	 */
	public function Load_plugin() {

		if( self::$instance->activation_check() ) {

			// Translations.
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain') );

			// Social button bellow the main content.
			add_filter( 'the_content', array( self:: $instance , 'social_buttons' ) );

			// User additional data fields.
			add_filter( 'user_contactmethods', array( self:: $instance, 'add_to_author_profile' ), 10, 1 );

			// Github updater.
			self::$instance->updater();

			$this->micemade_theme_active = true;

		} else {

			add_action( 'admin_notices', array( self::$instance , 'admin_notice' ) );

			$this->micemade_theme_active = false;
		}

	}
	/**
	 * Activation check
	 *
	 * @return bool $micemade_theme_active - if current theme is made by Micemade.
	 */
	public function activation_check() {

		$current_is_micemade_theme = false;

		// Array of supported Micemade Themes -to deprecate.
		$micemade_themes	= array( 'natura', 'beautify', 'ayame', 'lillabelle', 'inspace' );
		if ( is_child_theme() ) {
			$parent_theme = wp_get_theme();
			$active_theme = $parent_theme->get( 'Template' );
		} else {
			$active_theme = get_option( 'template' );
		}
		$active_theme_supported = in_array( $active_theme, $micemade_themes );
		// end deprecate.

		$current_theme_supported = current_theme_supports( 'micemade-themes-tools' );

		// Deprecate $active_theme_supported and leave only $current_theme_supported.
		if ( $current_theme_supported || $active_theme_supported ) {
			$current_is_micemade_theme = true;
		}

		return $current_is_micemade_theme;

	}

	/**
	 * Admin notice
	 * 
	 * @return void
	 * 
	 * notice if this plugin is active without Micemade theme
	 */
	public function admin_notice() {

		$class = "error updated settings-error notice is-dismissible";
		$message = __( '"Micemade Themes Tools" is active without active Micemade theme. Please, either activate Micemade theme, or deactivate "Micemade Themes Tools" plugin.', 'micemade-themes-tools' );
		echo '<div class="' . esc_attr( $class ) . '"><p>' . esc_html( $message ) . '</p></div>'; 

	}

	/**
	 * Load textdomain
	 *
	 * @return void
	 * 
	 * load plugin translations
	 */
	public function load_textdomain() {

		$lang_dir = apply_filters( 'micemade_themes_tools_lang_dir', trailingslashit(  plugin_dir_path( __FILE__ ) . 'languages') );

		// Traditional WordPress plugin locale filter
		$locale = apply_filters( 'plugin_locale', get_locale(), 'micemade-themes-tools' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'micemade-themes-tools', $locale );

		// Setup paths to current locale file
		$mofile_local = $lang_dir . $mofile;

		if ( file_exists( $mofile_local ) ) {
			// Look in the /wp-content/plugins/micemade-themes-tools/languages/ folder.
			load_textdomain( 'micemade-themes-tools', $mofile_local );
		} else {
			// Load the default language files.
			load_plugin_textdomain( 'micemade-themes-tools', false, $lang_dir );
		}

		return false;
	}

	/**
	 * Social buttons
	 *
	 * @param html $content - output html with social icons
	 * @return html $content
	 */
	public function social_buttons( $content ) {

		global $post;
		$permalink = get_permalink($post->ID);
		$thumb     = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
		$title     = get_the_title();
		$title     = str_replace(" ","&nbsp;",$title);
		$share_txt = esc_html__( "Share this on ","micemade-themes-tools" );

		if( in_the_loop() && !is_feed() && !is_home() && !is_page() && !is_archive() ) {

			$content = $content . '<div class="share-post"><p>' . esc_html__( 'Share this','micemade-themes-tools' ) . '</p><div class="social">
			<a class="icon-twitter tip-top share-link" href="http://twitter.com/share?text='. esc_attr( $title ) . '&amp;url='. esc_url ( $permalink ) . '"
				onclick="window.open(this.href, \'twitter-share\', \'width=550,height=235\');return false;" title="' . esc_attr( $share_txt ) . 'Twitter">
				<i class="fa fa-twitter" aria-hidden="true"></i>
			</a>

			<a class="icon-fb tip-top share-link" href="https://www.facebook.com/sharer/sharer.php?u=' . esc_url( $permalink ) . '"
				 onclick="window.open(this.href, \'facebook-share\',\'width=580,height=296\');return false;" title="' . esc_attr( $share_txt ) . 'Facebook">
				<i class="fa fa-facebook" aria-hidden="true"></i>
			</a>

			<a class="icon-gplus tip-top share-link" href="https://plus.google.com/share?url=' . esc_url( $permalink ) . '"
			   onclick="window.open(this.href, \'google-plus-share\', \'width=490,height=530\');return false;" title="' . esc_attr( $share_txt ) . 'Google Plus">
				<i class="fa fa-google-plus" aria-hidden="true"></i>
			</a>

			<a class="icon-pinterest tip-top share-link" href="https://pinterest.com/pin/create/button/?url=' . esc_url( $permalink ) . '&amp;media=' . esc_url( $thumb[0] ) . '&amp;description=' . esc_attr( $title ) . '" onclick="window.open(this.href, \'pinterest-share\', \'width=490,height=530\');return false;" title="' . esc_attr( $share_txt ) . 'Pinterest">
				<i class="fa fa-pinterest" aria-hidden="true"></i>
			</a>

		</div></div>';
		}
		return $content;
	}

	/**
	 * Add to author profile
	 *
	 * @param array $contactmethods - additional input fields for author profile.
	 * @return $contactmethods
	 *
	 */
	public function add_to_author_profile( $contactmethods = array() ) {

		// Show additional contact fields only on Micemade themes.
		$current_theme_supported = current_theme_supports( 'micemade-themes-tools' );
		if ( $current_theme_supported ) {
			$contactmethods['rss_url']          = 'RSS URL';
			$contactmethods['google_profile']   = esc_html__( 'Google Profile URL', 'micemade-themes-tools' );
			$contactmethods['twitter_profile']  = esc_html__( 'Twitter Profile URL', 'micemade-themes-tools' );
			$contactmethods['facebook_profile'] = esc_html__( 'Facebook Profile URL', 'micemade-themes-tools' );
			$contactmethods['linkedin_profile'] = esc_html__( 'Linkedin Profile URL', 'micemade-themes-tools' );
			$contactmethods['skype']            = esc_html__( 'Skype', 'micemade-themes-tools' );
		}

		return $contactmethods;
	}

	/**
	 * GitHub Updater
	 *
	 * @return void
	 *
	 * include Github based plugin updater class
	 */
	private function updater() {

		require_once( plugin_dir_path( __FILE__ ) . 'github_updater.php' );
		if ( is_admin() ) {
			new Micemade_GitHub_Plugin_Updater( __FILE__, 'Micemade', "micemade-themes-tools" );
		}

	}

}
// Initialize the plugin.
Micemade_Themes_Tools::get_instance()->init();
/**
 * Import WC attributes helper
 *
 * @param [string] $attribute_name
 * @return void
 *
 * import product attributes helper :
 * register atts taxonomies to import attribute terms
 * used by Micemade Theme Setup Wizard
 */
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
