<?php
/**
 * CUSTOM FILTERS AND HOOKS
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
 * ALLOWED FORM HTML TAGS ARRAY
 *
 * @param array $allowed - array of allowed tags.
 * @return $allowed
 */
function allowed_form_f( $allowed = array() ) {
	$allowed = array(
		'p'      => array(),
		'label'  => array(
			'for' => array(),
		),
		'select' => array(
			'name' => array(),
		),
		'option' => array(
			'selected' => array(),
			'value'    => array(),
		),
	);

	return $allowed;
}
add_filter( 'mmtt_allowed_form_tags', 'allowed_form_f' );
