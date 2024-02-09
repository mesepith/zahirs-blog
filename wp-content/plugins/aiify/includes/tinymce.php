<?php

namespace AIIFY;

function add_tinymce_plugin( $plugin_array ) {

	$plugin_array['aiify'] = AIIFY_URL . 'aiify-block/build/tinymce.js';
	wp_localize_script(
		'wp-tinymce',
		'aiify',
		js_config()
	);

	wp_enqueue_style( 'wp-edit-blocks' );
	wp_enqueue_style( 'wp-block-library-theme' );

	return $plugin_array;
}

function add_tinymce_toolbar_button( $buttons ) {
	array_push( $buttons, '|', 'aiify_action' );
	return $buttons;
}


add_action(
	'admin_init',
	function () {
		// Check if the logged in WordPress User can edit Posts or Pages
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		// Check if the logged in WordPress User has the Visual Editor enabled
		if ( get_user_option( 'rich_editing' ) !== 'true' ) {
			return;
		}

		// Setup some filters
		add_filter( 'mce_external_plugins', __NAMESPACE__ . '\add_tinymce_plugin' );
		add_filter( 'mce_buttons', __NAMESPACE__ . '\add_tinymce_toolbar_button' );

	}
);
