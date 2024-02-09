<?php

namespace AIIFY;

use Orhanerday\OpenAi\OpenAi;

require AIIFY_VENDOR . '/autoload.php';

class Settings extends \WP_Settings_Kit {

	protected $settings_name = 'aiify';
	public function admin_menu() {
		add_menu_page( 'Aiify', __( 'Aiify Blocks', 'aiify' ), 'manage_options', 'aiify', array( $this, 'plugin_page' ), 'data:image/svg+xml;base64,' . base64_encode( file_get_contents( AIIFY_ASSETS_DIR . 'img/icon.svg' ) ) );
	}

	public function plugin_page() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['welcome-message'] ) && $_GET['welcome-message'] == 'true' ) {
			echo '<div class="notice notice-success is-dismissible"><p>' .
			wp_kses_post( sprintf( __( 'Welcome to Aiify Blocks, feel to <a href="%s" >contact us</a> if you have any question.', 'aiify' ), esc_url( aii_fs()->contact_url() ) ) ) .
			'</p></div>';
		}
		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'Aiify Blocks Settings', 'aiify' ) . '</h1>';
		$this->show_navigation();
		$this->show_forms();
		echo '</div>';
	}

	public function default_sanitization_error_message( $field_config ) {
		return sprintf( __( 'Please insert a valid %s', 'aiify' ), $field_config['type'] );
	}
}

function get_languages() {
	// get language list form WordPress available translations, that's an exhaustive list
	require_once ABSPATH . 'wp-admin/includes/translation-install.php';
	$translations = wp_get_available_translations();
	$languages    = array( 'en_US' => 'English (United States)' );
	$languages   += array_map(
		function ( $t ) {
			return $t['native_name'];
		},
		$translations
	);
	$return       = array();
	foreach ( $languages as $locale => $language ) {
		$return[ $language ] = $language;
	}
	return $return;
}

function get_ai_engine( $engine = null ) {
	switch ( $engine ? $engine : AIIFY_AI_ENGINE ) {
		case 'openai':
			return new OpenAi( AIIFY_OPEN_AI_KEY );
		case 'openrouter':
			return new OpenRouter( AIIFY_OPENROUTER_KEY );
		case 'ollama':
			return new Ollama( AIIFY_OLLAMA_URL );
	}
	return null;
}

function get_ai_model( $engine ) {
	if ( $engine instanceof Ollama ) {
		return AIIFY_OLLAMA_MODEL;
	} elseif ( $engine instanceof OpenRouter ) {
		return AIIFY_OPENROUTER_MODEL;
	} elseif ( $engine instanceof OpenAi ) {
		return AIIFY_OPEN_AI_MODEL;
	}
}

function settings() {
	$update_models = wp_nonce_url( admin_url( 'admin-ajax.php?action=aiify_update_models' ), 'aiify_update_models', 'aiify_settings_nonce' );

	$models = get_option( 'AIIFY_MODELS', array() );

	$engine_settings = array(
		'name'   => 'AIIFY',
		'title'  => __( 'AI Engine Settings', 'aiify' ),
		'fields' => array(
			array(
				'id'          => 'AI_ENGINE',
				'type'        => 'select',
				'title'       => __( 'Your AI Engine', 'aiify' ),
				'default'     => 'openai',
				'options'     => AIIFY_AI_ENGINES,
				'description' => esc_html__( 'Choose you default ai engine here', 'aiify' ),

			),
			array(
				'id'          => 'TEMPERATURE',
				'type'        => 'range',
				'title'       => __( 'Temparature', 'aiify' ),
				'default'     => 0.2,
				'attributes'  => array(
					'min'  => 0,
					'max'  => 1,
					'step' => 0.1,
				),
				'description' => __( 'Lower temperature means model generates repetitive text like training data. Higher temperature leads to more varied and creative text.', 'aiify' ),

			),
			array(
				'id'          => 'FREQUENCY_PENALTY',
				'type'        => 'range',
				'title'       => __( 'Frequency penalty', 'aiify' ),
				'default'     => 0,
				'attributes'  => array(
					'min'  => -2,
					'max'  => 2,
					'step' => 0.1,
				),
				'description' => __( 'A frequency penalty of 0.5 reduces the likelihood of the model using frequently seen words or phrases by 50%. A penalty of 1 will eliminate them completely.', 'aiify' ),
			),
			array(
				'id'          => 'PRESENCE_PENALTY',
				'type'        => 'range',
				'title'       => __( 'Presence penalty', 'aiify' ),
				'default'     => 0,
				'attributes'  => array(
					'min'  => -2,
					'max'  => 2,
					'step' => 0.1,
				),
				'description' => __( 'With a presence penalty of 0.5, the model reduces the chance of generating words or phrases not in the training data by 50%. A penalty of 1 avoids all new words or phrases.', 'aiify' ),

			),
		),
	);

	$openai_settings = array(
		'name'   => 'AIIFY_OPEN_AI',
		'title'  => __( 'Open AI Settings', 'aiify' ),
		'fields' => array(
			array(
				'id'          => 'KEY',
				'type'        => 'text',
				'title'       => __( 'Your Open AI Key', 'aiify' ),
				'default'     => '',
				'placeholder' => 'sk-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
				'description' => sprintf( esc_html__( 'You can get your key here : %s', 'aiify' ), '<a href="https://platform.openai.com/" target="_blank"> https://platform.openai.com/</a>' ),

			),
			array(
				'id'          => 'MODEL',
				'type'        => 'select',
				// get value from old setting
				'default'     => defined( 'AIFFY_CHAT_MODEL' ) ? AIFFY_CHAT_MODEL : 'gpt-3.5-turbo',
				'title'       => __( 'Open AI Model', 'aiify' ),
				'options'     => isset( $models['openai'] ) ? $models['openai'] : AIIFY_CHAT_MODELS,
				'description' => sprintf( __( "GPT-4 is currently in a limited beta and only accessible to those who have been granted access. Please join the <a target='_blank' href='%s'>waitlist</a> to get access when capacity is available.", 'aiify' ), 'https://openai.com/waitlist/gpt-4-api' ) . '<br/>' . sprintf(
					__( "This list is retrieved from the API, click <a href='%s'>here</a> to update it now", 'aiify' ),
					esc_url(
						add_query_arg(
							array( 'engine' => 'openai' ),
							$update_models
						)
					)
				),
			),

		),
	);

	$ollama_settings = array(
		'name'   => 'AIIFY_OLLAMA',
		'title'  => __( 'Ollama Settings', 'aiify' ),
		'fields' => array(
			array(
				'id'          => 'URL',
				'type'        => 'text',
				'title'       => __( 'Your Ollama Service url', 'aiify' ),
				'default'     => '',
				'placeholder' => 'http://127.0.0.1:11434',
				'description' => esc_html__( 'If your Ollama service is in the same host as this website, it would be hosted at http://127.0.0.1:11434', 'aiify' ),

			),
			array(
				'id'          => 'MODEL',
				'type'        => 'select',
				// get value from old setting
				'default'     => '',
				'title'       => __( 'Ollama Model', 'aiify' ),
				'options'     => isset( $models['ollama'] ) ? $models['ollama'] : array(),
				'description' => sprintf(
					__( "This list is retrieved from the API, click <a href='%s'>here</a> to update it now", 'aiify' ),
					esc_url(
						add_query_arg(
							array( 'engine' => 'ollama' ),
							$update_models
						)
					)
				),
			),

		),
	);

	$openrouter_settings = array(
		'name'   => 'AIIFY_OPENROUTER',
		'title'  => __( 'OpenRouter Settings (beta)', 'aiify' ),
		'fields' => array(
			array(
				'id'          => 'KEY',
				'type'        => 'text',
				'title'       => __( 'Your Open Router Key', 'aiify' ),
				'default'     => '',
				'placeholder' => 'sk-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
				'description' => sprintf( esc_html__( 'You can get your key here : %s', 'aiify' ), '<a href="https://openrouter.ai/keys" target="_blank"> https://openrouter.ai/keys</a>' ),

			),
			array(
				'id'          => 'MODEL',
				'type'        => 'select',
				// get value from old setting
				'default'     => 'openrouter/auto',
				'title'       => __( 'OpenRouter Models', 'aiify' ),
				'options'     => isset( $models['openrouter'] ) ? $models['openrouter'] : array(),
				'description' => sprintf(
					__( "This list is retrieved from the API, click <a href='%s'>here</a> to update it now", 'aiify' ),
					esc_url(
						add_query_arg(
							array( 'engine' => 'openrouter' ),
							$update_models
						)
					)
				),
			),

		),
	);

	$writing_settings = array(
		'name'   => 'AIIFY_WRITING',
		'title'  => __( 'Writing Settings', 'aiify' ),
		'fields' => array(
			array(
				'id'      => 'LANGUAGE',
				'type'    => 'select',
				'default' => AIIFY_WRITING_DEFAULT_LANGUAGE,
				'title'   => __( 'Output Language', 'aiify' ),
				'options' => get_languages(),
			),
			array(
				'id'      => 'STYLE',
				'type'    => 'select',
				'default' => 'Journalistic',
				'title'   => __( 'Writing Style', 'aiify' ),
				'options' => AIIFY_STYLES,
			),

			array(
				'id'      => 'TONE',
				'type'    => 'select',
				'title'   => __( 'Writing Tone', 'aiify' ),
				'default' => 'Professional',
				'options' => AIIFY_TONES,
			),
			array(
				'id'         => 'MAX_WORDS',
				'type'       => 'range',
				'title'      => __( 'Maximum words', 'aiify' ),
				'default'    => 1000,
				'attributes' => array(
					'min'  => 300,
					'max'  => 2000,
					'step' => 10,
				),
			),

		),
	);

	$prompts_settings = array(
		'name'   => 'AIIFY_SYSTEM',
		'title'  => __( 'System Prompts and templates', 'aiify' ),
		'fields' => array(
			array(
				'id'          => 'PROMPT',
				'type'        => 'textarea',
				'default'     => AIIFY_SYSTEM_PROMPT_DEFAULT,
				'placeholder' => AIIFY_SYSTEM_PROMPT_DEFAULT,
				'title'       => __( 'Main system prompt, sets up the "ai personnality" for content creation', 'aiify' ),
			),
			array(
				'id'          => 'EDIT_PROMPT',
				'type'        => 'textarea',
				'default'     => AIIFY_SYSTEM_EDIT_PROMPT_DEFAULT,
				'placeholder' => AIIFY_SYSTEM_EDIT_PROMPT_DEFAULT,
				'title'       => __( 'Main system prompt, sets up the "ai personnality" for content edition', 'aiify' ),
			),
			array(
				'id'          => 'PROMPT_FORMATING',
				'type'        => 'textarea',
				'default'     => AIIFY_SYSTEM_PROMPT_FORMATING_DEFAULT,
				'placeholder' => AIIFY_SYSTEM_PROMPT_FORMATING_DEFAULT,
				'title'       => __( 'Formatting for content creation (be very careful while changing)', 'aiify' ),
			),
			array(
				'id'          => 'EDIT_PROMPT_FORMATING',
				'type'        => 'textarea',
				'default'     => AIIFY_SYSTEM_EDIT_PROMPT_FORMATING_DEFAULT,
				'placeholder' => AIIFY_SYSTEM_EDIT_PROMPT_FORMATING_DEFAULT,
				'title'       => __( 'Formatting for content edition (be very careful while changing)', 'aiify' ),
			),

			array(
				'id'          => 'INSTRUCTION_HEADER',
				'type'        => 'textarea',
				'default'     => AIIFY_SYSTEM_INSTRUCTION_HEADER_DEFAULT,
				'placeholder' => AIIFY_SYSTEM_INSTRUCTION_HEADER_DEFAULT,
				'title'       => __( 'Instructions about style, tone, language, words..', 'aiify' ),
				'description' => sprintf( __( 'You can use those variables: %s', 'aiify' ), '{style}, {tone}, {maxWords}, {language}' ),
			),
			array(
				'id'          => 'PROMPT_STRUCTURE',
				'type'        => 'textarea',
				'default'     => AIIFY_SYSTEM_PROMPT_STRUCTURE_DEFAULT,
				'placeholder' => AIIFY_SYSTEM_PROMPT_STRUCTURE_DEFAULT,
				'title'       => __( 'Create a new content prompt structure', 'aiify' ),
				'description' => sprintf( __( 'You can use those variables: %s', 'aiify' ), '{header}, {context}, {keywords}, {prompt}, {maxWords}' ),
			),
			array(
				'id'          => 'EDIT_STRUCTURE',
				'type'        => 'textarea',
				'default'     => AIIFY_SYSTEM_EDIT_STRUCTURE_DEFAULT,
				'placeholder' => AIIFY_SYSTEM_EDIT_STRUCTURE_DEFAULT,
				'title'       => __( 'Edit a content prompt structure', 'aiify' ),
				'description' => sprintf( __( 'You can use those variables: %s', 'aiify' ), '{style}, {command}, {language}, {header}, {maxWords}' ),
			),
		),
	);
	// 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

		return array(
			'AIIFY'            => $engine_settings,
			'AIIFY_OPEN_AI'    => $openai_settings,
			'AIIFY_OLLAMA'     => $ollama_settings,
			'AIIFY_OPENROUTER' => $openrouter_settings,
			'AIIFY_WRITING'    => $writing_settings,
			'AIIFY_SYSTEM'     => $prompts_settings,
		);
}

add_action(
	'wsa_form_bottom_AIIFY_SYSTEM',
	function () {
		$revertAction = wp_nonce_url( admin_url( 'admin-ajax.php?action=aiify_reset_prompt' ), 'aiify_reset_prompt', 'aiify_settings_nonce' );
		?>
	<p class="notice-error notice" style="padding:1em"><?php esc_html_e( 'Danger Zone : ', 'aiify' ); ?><a class="button" href="<?php echo esc_url( $revertAction ); ?>"><?php esc_html_e( 'Revert to default prompts', 'aiify' ); ?></a></p>
		<?php
	}
);

add_action(
	'wp_ajax_aiify_reset_prompt',
	function () {
		check_ajax_referer( 'aiify_reset_prompt', 'aiify_settings_nonce' );
		update_option( 'AIIFY_SYSTEM', array() );
		wp_safe_redirect( admin_url( 'admin.php?page=aiify' ) );
		exit;
	}
);

add_action(
	'wp_ajax_aiify_update_models',
	function () {
		check_ajax_referer( 'aiify_update_models', 'aiify_settings_nonce' );
		$models = get_option(
			'AIIFY_MODELS',
			array(
				'openai'     => null,
				'ollama'     => null,
				'openrouter' => null,
			)
		);
		if ( isset( $_GET['engine'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- no need to unslash or sanitiza, it's just a key, get_ai_engine validates
			$engine = get_ai_engine( $_GET['engine'] );

			if ( $engine instanceof Ollama ) {
				$models['ollama'] = $engine->listModels(
					function ( $model ) {
						return $model['name'] . ' - ' . $model['details']['parameter_size'];
					}
				);
			} elseif ( $engine instanceof OpenRouter ) {
				$response          = json_decode( $engine->listModels(), true );
				$openrouter_models = array();
				foreach ( $response['data'] as $model ) {
					$is_free                           = $model['pricing']['prompt'] === '0' && $model['pricing']['completion'] === '0';
					$openrouter_models[ $model['id'] ] = array(
						'label'       => $model['name'] . ( $is_free ? ' (free)' : '' ),
						'description' => $model['description'],
					);
				}
				$models['openrouter'] = $openrouter_models;
			} elseif ( $engine instanceof OpenAi ) {
				$response      = json_decode( $engine->listModels(), true );
				$openai_models = array();
				foreach ( $response['data'] as $model ) {
					if ( 'openai' === $model['owned_by'] || 'organization-owner' === $model['owned_by'] || 'user' === substr( $model['owned_by'], 0, 4 ) ) {
						$openai_models[ $model['id'] ] = $model['id'] . ' - ' . $model['owned_by'];

					}
				}
				$models['openai'] = $openai_models;
			} else {
				wp_die( 'Umknown engine' );
			}
		} else {
			wp_die( 'Wrong request' );
		}

		update_option( 'AIIFY_MODELS', $models, false );
		wp_safe_redirect( admin_url( 'admin.php?page=aiify' ) );
		exit;
	}
);


function install_or_upgrade( $current_version ) {
	if ( version_compare( $current_version, '0.1.6' ) <= 0 ) {
		// before moving keys fron AIIFY to AIIFY_OPEN_AI
		$aiify = get_option( 'AIIFY', array() );
		if ( isset( $aiify['OPEN_AI_KEY'] ) ) {
			update_option(
				'AIIFY_OPEN_AI',
				array(
					'KEY'   => $aiify['OPEN_AI_KEY'],
					'MODEL' => $aiify['CHAT_MODEL'],
				),
				false
			);
			unset( $aiify['OPEN_AI_KEY'] );
			unset( $aiify['CHAT_MODEL'] );
			// Save cleaned array
			update_option( 'AIIFY', $aiify, false );
		}
	}

}

/**
 * Checks databse version and updates it if necessary
 */
function db_check() {
	$current_version = get_option( 'aiify_version', '0' );
	if ( $current_version != AIIFY_VERSION ) {
		install_or_upgrade( $current_version );
		update_option( 'aiify_version', AIIFY_VERSION, false );
	}
}

add_action(
	'plugins_loaded',
	function () {
		load_plugin_textdomain( 'aiify', false, dirname( plugin_basename( AIIFY_PLUGIN_FILE ) ) . '/languages' );
		// init consts, need translation to be loaded
		require AIIFY_INCLUDES . 'constants.php';
		// Before loading the settings, we run our upgrade if necessary
		db_check();

		new Settings( apply_filters( 'aiify_settings', settings() ) );
	}
);
