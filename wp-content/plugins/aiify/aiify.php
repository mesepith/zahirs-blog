<?php

/**
 * Plugin Name: Aiify - ChatGPT, Ollama and OpenRouter AI Copywriting, Content Writing and Editing
 * Plugin URI: https://www.wpaiify.com
 * Version: 0.1.7
 * Author: Instareza
 * Author URI: https://www.instareza.com
 * Description: Create and edit content using Chatgpt or Ollama AI or any OpenRouter model. Improve your content's quality and optimize it for search engines.
 * License: GPL
 * Text Domain: aiify
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Stable tag: 0.1.7
 *
 * @package AIIFY
 */

namespace AIIFY;

define( 'AIIFY_VERSION', '0.1.7' );
define( 'AIIFY_VENDOR', __DIR__ . '/vendor/' );
define( 'AIIFY_ASSETS_DIR', __DIR__ . '/assets/' );
define( 'AIIFY_INCLUDES', __DIR__ . '/includes/' );
define( 'AIIFY_BLOCK', __DIR__ . '/aiify-block/' );
define( 'AIIFY_URL', plugin_dir_url( __FILE__ ) );
define( 'AIIFY_ASSET_URL', AIIFY_URL . '/assets/' );
define( 'AIIFY_PLUGIN_FILE', __FILE__ );
define( 'AIIFY_LANGUAGE_PATH', plugin_dir_path( __FILE__ ) . '/languages' );

// Init freemius integration.
require AIIFY_INCLUDES . 'init_freemius.php';

function array_to_options( $array ) {
	$options = array();
	foreach ( $array as $key => $value ) {
		$options[] = array(
			'label' => $value,
			'value' => $key,
		);
	}
	return $options;
}

function js_config() {
	return array(
		'prompts'         => AIIFY_WRITER_PROMPTS,
		'edits'           => array_keys( AIIFY_EDIT_PROMPTS ),
		'after'           => array_keys( AIIFY_GENERATE_AFTER_PROMPTS ),
		'before'          => array_keys( AIIFY_GENERATE_BEFORE_PROMPTS ),
		'styles'          => array_to_options( AIIFY_STYLES ),
		'style'           => AIIFY_WRITING_STYLE,
		'tones'           => array_to_options( AIIFY_TONES ),
		'languages'       => array_to_options( get_languages() ),
		'language'        => AIIFY_WRITING_LANGUAGE,
		'tone'            => AIIFY_WRITING_TONE,
		'maxWords'        => AIIFY_WRITING_MAX_WORDS,
		'nonce'           => wp_create_nonce( 'secure-nonce' ),
		'currentPlan'     => aii_fs()->get_plan_name(),
		'paragraphPrompt' => AIIFY_PARAGRAPH_BLOCK_PROMPT,
		'latestPrompt'    => '',

	);
}

function register_aiify_block() {
	if ( ( ! defined( 'AIIFY_OPEN_AI_KEY' ) || AIIFY_OPEN_AI_KEY == '' ) &&
	( ! defined( 'AIIFY_OLLAMA_URL' ) || AIIFY_OLLAMA_URL === '' ) &&
	( ! defined( 'AIIFY_OPENROUTER_KEY' ) || AIIFY_OPENROUTER_KEY === '' )
	) {
		// just bail if no key is there and notify.
		add_action(
			'admin_notices',
			function () {
            	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( isset( $_GET['page'] ) && $_GET['page'] === 'aiify' ) {
					return;
				}

				?>
			<div class="notice notice-info is-dismissible" style="display:flex; align-items: center;column-gap:1em">
				<div>
					<img width="100" src="<?php echo esc_url( AIIFY_ASSET_URL . '/img/icon.svg' ); ?>" />
				</div>
				<div>
					<h3><?php echo esc_html__( 'Aiify For WordPress is installed, almost ready to go', 'aiify' ); ?></h3>
					<p><?php echo esc_html__( 'To start using AIIFY, please setup your Ai Engine, OpenAi or OpenRouter Key or Ollama URL', 'aiify' ); ?></p>
					<p>
					<a class="button-primary button" href="<?php echo esc_url( admin_url( 'admin.php?page=aiify&welcome-message=true' ) ); ?>"  ><?php echo esc_html__( 'Go to AIIFY settings', 'aiify' ); ?></a>
					</p>
				</div>
			</div>
				<?php
			}
		);
		 return;
	}

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		require AIIFY_INCLUDES . 'ai.php';
	} else {
		// No need to register block in ajax mode
		register_block_type( AIIFY_BLOCK );

		wp_set_script_translations( 'aiify-aiify-editor-script', 'aiify', AIIFY_LANGUAGE_PATH );
		wp_localize_script(
			'aiify-aiify-editor-script',
			'aiify',
			js_config()
		);

		add_filter(
			'write_your_story',
			function () {
				return AIIFY_PARAGRAPH_BLOCK_PROMPT;
			},
			100,
			0
		);
	}
}

if ( defined( 'WP_ADMIN' ) && WP_ADMIN ) {
	require_once AIIFY_VENDOR . '/autoload.php';
	require AIIFY_INCLUDES . 'class-ollama.php';
	require AIIFY_INCLUDES . 'class-openrouter.php';
	require AIIFY_INCLUDES . 'settings.php';
	require AIIFY_INCLUDES . 'tinymce.php';
	// allow only on admin ( or ajax )
	add_action( 'admin_init', __NAMESPACE__ . '\register_aiify_block' );
}
