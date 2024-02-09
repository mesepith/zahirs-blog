<?php

namespace AIIFY;

use Orhanerday\OpenAi\OpenAi;
use Youniwemi\StringTemplate\Engine;


function render( string $template, $values = array() ) {
	static $tpl;
	if ( $tpl === null ) {
		$tpl = new Engine();
	}
	return $tpl->render( $template, $values );
}

function get_system_prompt( $is_edit = false ) {
	if ( $is_edit ) {
		return AIIFY_SYSTEM_EDIT_PROMPT . "\n\n" . AIIFY_SYSTEM_EDIT_PROMPT_FORMATING;
	} else {
		return AIIFY_SYSTEM_PROMPT . "\n\n" . AIIFY_SYSTEM_PROMPT_FORMATING;
	}
}

/**
 * Prepares the options for OpenAi call
 *
 * @param      string $prompt  The prompt
 */
function prepare_options_openai( string $model, string $prompt, $is_stream = true, $is_edit = false ) {
	
	$options = array(
		'model'             => $model,
		'temperature'       => (float) AIIFY_TEMPERATURE,
		'frequency_penalty' => (float) AIIFY_FREQUENCY_PENALTY,
		'presence_penalty'  => (float) AIIFY_PRESENCE_PENALTY,
		'messages'          => array(
			// array(
			// 'role'    => 'system',
			// 'content' => AIIFY_SYSTEM_PROMPT,
			// ),
			array(
				'role'    => 'system',
				'content' => get_system_prompt( $is_edit ),
			),
			array(
				'role'    => 'user',
				'content' => $prompt,
			),
		)
	);
	if ($is_stream){
		$options['stream'] = true;
	}
	return $options;
}

/**
 * Prepares the options for Ollama call
 *
 * @param      string $prompt  The prompt
 */
function prepare_options_ollama( string $model, string $prompt, $is_stream = true, $is_edit = false ) {
	$options = array(
		'model'   => $model,
		'options' => array(
			'temperature' => (float) AIIFY_TEMPERATURE,
		),
	);

	if ( true ) {
		$options['messages'] = array(
			// array(
			// 'role'    => 'system',
			// 'content' => AIIFY_SYSTEM_PROMPT,
			// ),
			// array(
			// 'role'    => 'system',
			// 'content' => AIIFY_SYSTEM_PROMPT_FORMATING,
			// ),
			array(
				'role'    => 'system',
				'content' => get_system_prompt( $is_edit ),
			),
			array(
				'role'    => 'user',
				'content' => $prompt,
			),
		);
	} else {
		$options['prompt'] = $prompt;
		$options['system'] = get_system_prompt( $is_edit );
	}

	if ($is_stream){
		$options['stream'] = true;
	}

	return $options;
}

/**
 * Formats message as OpenAi content dela
 *
 * @param      string  $message  The message
 *
 * @return     string OpenAi compatible json string
 */
function format_delta($message){
	return json_encode(
		array(
			'choices' => array(
				array(
					'delta' => array(
						'content' => $message,
					),

				),
			),
		)
	);
}

/**
 * Adapt Ollama response format to OpenAi
 *
 * @param      <type>  $ollama  The ollama
 *
 * @return     string  OpenAi compatible string output
 */
function ollama_to_openai( $ollama ) {
	if ( $ollama->done ) {
		return '[DONE]';
	}

	return format_delta( isset( $ollama->message->content ) ? $ollama->message->content : $ollama->response);

}

/**
 * Detects and returs any error in the buffer
 *
 * @param      string  $buffer  The buffer
 *
 * @return     mixed  The stream error object or void.
 */
function get_stream_error( string $buffer ) {
	// Avoid parsing...
	if ( false === strpos( $buffer, 'error' ) ) {
		return;
	}

	if ( 0 === strpos( $buffer, 'data: ' ) ) {
		$maybe_json = substr( $buffer, 6 );
	} else {
		$maybe_json = $buffer;
	}
	// if it is a valid json
	$obj = json_decode( $maybe_json );
	if ( JSON_ERROR_NONE === json_last_error() ) {
		if ( isset( $obj->error ) && $obj->error->message != '' ) {
			return $obj->error;
		}
	}
}

/**
 * Main AI query action
 */
add_action(
	'wp_ajax_open_ai',
	function () {
		check_ajax_referer( 'secure-nonce', 'openai_nonce' );

		$engine = get_ai_engine();
		$model  = get_ai_model( $engine );
		if ( $model === null ) {
			wp_send_json_error( 'Please setup your ai engine and model' );
		}

		// Make sur style and tone are in our list of styles and tones
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		$style = isset( $_GET['style'] ) && isset( AIIFY_STYLES[ $_GET['style'] ] ) ? $_GET['style'] : AIIFY_WRITING_STYLE;
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		$tone = isset( $_GET['tone'] ) && isset( AIIFY_TONES[ $_GET['tone'] ] ) ? $_GET['tone'] : AIIFY_WRITING_TONE;
		// keep words for compatibility
		$maxWords = $words = isset( $_GET['maxWords'] ) ? intval( $_GET['maxWords'] ) : AIIFY_WRITING_MAX_WORDS;

		$tpl = new Engine();

		// Prepare context for style, tone, formating and length
		$languages = get_languages();
    	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		$language = isset( $_GET['language'] ) && isset( $languages[ $_GET['language'] ] ) ? $_GET['language'] : AIIFY_WRITING_LANGUAGE;

		// don't need slashes, sani
		$prompt = isset( $_GET['prompt'] ) ? wp_kses_post( wp_unslash( $_GET['prompt'] ) ) : null;

		$is_edit = isset( $_GET['edit'] );
		if ( $is_edit ) {
			$edit    = trim( wp_kses_post( wp_unslash( $_GET['edit'] ) ), "\n" );
			$commads = array_merge( AIIFY_EDIT_PROMPTS, AIIFY_GENERATE_AFTER_PROMPTS, AIIFY_GENERATE_BEFORE_PROMPTS );

			$command = isset( $commads[ $prompt ] ) ? $commads[ $prompt ] : $prompt;
			// if ( isset( AIIFY_EDIT_PROMPTS[ $prompt ] ) ) {
			// Do not change structure
			// $command .= 'Please avoid adding any additional headings if the provided text has a paragraph structure.';
			// }
			$header = '';
			$prompt = render( AIIFY_SYSTEM_EDIT_STRUCTURE, compact( 'header', 'command', 'edit', 'language', 'maxWords', 'words' ) );
		} else {

			$header   = render( AIIFY_SYSTEM_INSTRUCTION_HEADER, compact( 'style', 'tone', 'words', 'maxWords', 'language' ) );
			$context  = isset( $_GET['context'] ) ? wp_kses_post( wp_unslash( $_GET['context'] ) ) : null;
			$keywords = isset( $_GET['keywords'] ) ? wp_kses_post( wp_unslash( $_GET['keywords'] ) ) : null;
			$prompt   = rtrim( $prompt, '.' );

			$prompt = render( AIIFY_SYSTEM_PROMPT_STRUCTURE, compact( 'header', 'language', 'prompt', 'context', 'keywords', 'maxWords', 'words' ) );

		}

		$is_stream = ! isset( $_GET['nostream'] ) ;

		// Both OpenRouter extends OpenAi
		if ( $engine instanceof OpenAi ) {
			$opts = prepare_options_openai( $model, $prompt, $is_stream, $is_edit );
		} elseif ( $engine instanceof Ollama ) {
			$opts = prepare_options_ollama( $model, $prompt, $is_stream, $is_edit );
		}

		if ( $is_stream ) {
			header( 'Content-type: text/event-stream' );
			header( 'Cache-Control: no-cache' );
			header( 'X-Accel-Buffering: no' ); // Turn off bufferinf in Nginx.
		}

		$complete = $engine->chat(
			$opts,
			$is_stream ? function ( $curl_info, $data ) use ( $engine, $opts ) {
				static $sentDebug;
				static $buffer = ''; // Initialize a static buffer to store partial JSON.
				$length = strlen( $data );
				// Open Router processing request, we skip
				if ( $engine instanceof OpenRouter && strpos( $data, ": OPENROUTER PROCESSING") === 0 ){
					return $length;
				}
				
				// Append the new data to the buffer.
				$buffer .= $data;

				// Do we have an error here?
				$error = get_stream_error( $buffer );

				// Send debug/error as data, EventSource does not allow to extract the error.
				if ( $sentDebug === null ) {
					if ( $error ) {
						$opts['error'] = $error;
					}
					$debug = json_encode( $opts ) ;
					echo 'data: ' . wp_kses_post( $debug ). PHP_EOL . PHP_EOL;
					$sentDebug = true;
					ob_end_flush();

				} else {
					if ( $error ) {
						echo 'data: ' . wp_kses_post( json_encode( array( 'error' => $obj ) ) ) . PHP_EOL . PHP_EOL;
						ob_end_flush();

					}
				}

				// Now the buffer can be multiple lines, so we split and analyse them.
				$parts = explode( "\n", $buffer );
				// If the last part is not complete (does not end with \n), we keep it as our buffer, else, fresh buffer.
				$buffer = ( $buffer[-1] !== "\n" ) ? array_pop( $parts ) : '';
				$finish = '';
				foreach ( $parts as $part ) {
					if ( '' === $part ) {
						continue;
					}

					if ( 0 === strpos( $part, 'data: ' ) ) {
						$maybe_json = substr( $part, 6 );
					} else {
						$maybe_json = $part;
					}
					$obj = json_decode( $maybe_json );
					if ( json_last_error() === JSON_ERROR_NONE ) {
						if ( $engine instanceof OpenAi ) {
							if ( isset( $obj->choices[0]->finish_reason ) ) {
								$finish = $obj->choices[0]->finish_reason;
							}
						}

						$response = ( $engine instanceof OpenAi ) ? $maybe_json : ollama_to_openai( $obj );
						echo 'data: ' . wp_kses_post( $response ) . PHP_EOL . PHP_EOL;
						ob_end_flush();

					} else {
						$buffer .= $part;
					}
				}

				// Open AI : We are done here.
				if ( $buffer === 'data: [DONE]' || $finish === 'length' ) {
					echo 'data: [DONE]' . PHP_EOL . PHP_EOL;
					ob_end_flush();
				}

				
				flush();
				return $length;
			} : null
		);

		// Almost dead code, need client side handle correctly either a string (markdown) to format to blocks, or multiples lines
		if (!$is_stream){
			$json = json_decode($complete);
			// Sorry not sorry. but for now, we'll send it as a stream ( of lines )
			if ( json_last_error() === JSON_ERROR_NONE ) {
				if ( isset( $json->error ) && $json->error->message != '' ) {
					 $opts['error'] =   $json->error;
				}
				header( 'Content-type: text/event-stream' );
				header( 'Cache-Control: no-cache' );
				header( 'X-Accel-Buffering: no' ); // Turn off bufferinf in Nginx.
				// Send debug
				echo 'data: ' . wp_kses_post( json_encode( $opts )). PHP_EOL . PHP_EOL;
				flush();
				if ( isset($json->choices[0]->message->content)){
					$lines = explode("\n", $json->choices[0]->message->content ) ;
					foreach($lines as $line){
						if ($line==""){
							$line="\n";
						} else{
							$line.="\n";
						}
						echo "data: ".wp_kses_post(format_delta($line)). PHP_EOL . PHP_EOL;
						flush();
					}

					echo 'data: [DONE]' . PHP_EOL . PHP_EOL;
					flush();
					exit();
				}
			}
		}

	}
);

