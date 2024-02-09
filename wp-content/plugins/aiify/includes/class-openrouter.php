<?php

namespace AIIFY;

use Orhanerday\OpenAi\OpenAi;

/**
 * OpenRouter uses almost the same API as OpenAI, so, just a quick extend to setup the URL and Application Name
 */
class OpenRouter extends OpenAi {
	const OPENROUTER_API_URL = 'https://openrouter.ai/api/';
	public function __construct( $OPENROUTER_API_KEY ) {
		parent::__construct( $OPENROUTER_API_KEY );
		$this->setCustomURL( self::OPENROUTER_API_URL );
		$this->setHeader( array( 'X-Title' => 'WP-Aiify' ) );

	}
}
