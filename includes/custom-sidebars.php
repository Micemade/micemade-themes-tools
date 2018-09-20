<?php
/**
 * CUSTOM WIDGET AREAS (CUSTOM SIDEBARS)
 *
 * @since 0.1.8
 * @package WordPress
 * @subpackage Micemade Themes Tools
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REGISTER SIDEBARS
 *
 * @return void
 */
function mmtt_register_sidebars() {

	$default_custom_sidebar = array(
		array(
			'sidebar_name' => 'Home widgets',
		),
	);
	// Theme customizer.
	$custom_sidebars = get_theme_mod( 'custom_sidebars', $default_custom_sidebar );

	if ( isset( $custom_sidebars ) && count( $custom_sidebars ) > 0 ) {
		foreach ( $custom_sidebars as $sidebar ) {
			register_sidebar(
				array(
					'name'          => $sidebar['sidebar_name'],
					'id'            => sanitize_title( $sidebar['sidebar_name'] ),
					'description'   => 'custom widget area created in theme customizer',
					'before_widget' => '<section class="widget %2$s custom-widget" id="%1$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h4 class="widget-title"><span>',
					'after_title'   => '</span></h4>',
				)
			);
		}
	}

}
add_action( 'init', 'mmtt_register_sidebars', 1 );

// Define the custom box.
add_action( 'add_meta_boxes', 'mmtt_add_sidebar_metabox' );
add_action( 'save_post', 'mmtt_save_sidebar_postdata' );

// Adds a box to the side column on the Post and Page edit screens.
/**
 * ADD SIDEBAR METABOX
 *
 * @return void
 */
function mmtt_add_sidebar_metabox() {
	add_meta_box(
		'custom_sidebar',
		esc_html__( 'Custom Sidebar', 'micemade-themes-tools' ),
		'mmtt_custom_sidebar_callback',
		'post',
		'side'
	);
	add_meta_box(
		'custom_sidebar',
		esc_html__( 'Custom Sidebar', 'micemade-themes-tools' ),
		'mmtt_custom_sidebar_callback',
		'page',
		'side'
	);
}

/**
 * CUSTOM SIDEBAR CALLBACK
 * prints the box content.
 *
 * @param object $post - post object
 * @return $output
 */
function mmtt_custom_sidebar_callback( $post ) {
	global $wp_registered_sidebars;

	$custom = get_post_custom( $post->ID );

	if ( isset( $custom['custom_sidebar'] ) ) {
		$val = $custom['custom_sidebar'][0];
	} else {
		$val = 'default';
	}

	// Use nonce for verification.
	wp_nonce_field( wp_get_theme() . '_custom_sidebar', 'custom_sidebar_nonce' );

	// The actual fields for data entry.
	$output  = '<p><label for="custom_sidebar">' . esc_html__( 'Choose a sidebar (widget area) to display', 'micemade-themes-tools' ) . '</label></p>';
	$output .= '<select name="custom_sidebar">';

	// Add a default option.
	$output .= '<option';
	if ( 'default' === $val ) {
		$output .= ' selected="selected"';
	}
	$output .= ' value="default">' . esc_html__( 'Default', 'micemade-themes-tools' ) . '</option>';

	// Fill the select element with all registered sidebars.
	foreach ( $wp_registered_sidebars as $sidebar_id => $sidebar ) {
		$output .= '<option';
		if ( $sidebar_id === $val ) {
			$output .= " selected='selected'";
		}
		$output .= ' value="' . $sidebar_id . '">' . $sidebar['name'] . '</option>';
	}

	$output .= '</select>';

	echo wp_kses( $output, apply_filters( 'mmtt_allowed_form_tags', '' ) );
}

/**
 * SAVE SIDEBAR POSTDATA
 *
 * @param int $post_id
 * @return void
 */
function mmtt_save_sidebar_postdata( $post_id ) {

	if ( 'nav_menu_item' === get_post_type() ) {
		return;
	}

	// Verify if this is an auto save routine.
	// If it is our form has not been submitted, so we dont want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Verify this came from our screen and with proper authorization,
	// because save_post can be triggered at other times.
	$nonce_set = isset( $_POST['custom_sidebar_nonce'] ) ? true : false;

	if ( $nonce_set && ! wp_verify_nonce( $_POST['custom_sidebar_nonce'], wp_get_theme() . '_custom_sidebar' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['custom_sidebar'] ) ) {
		$data = $_POST['custom_sidebar'];
		update_post_meta( $post_id, 'custom_sidebar', $data );
	}

}
