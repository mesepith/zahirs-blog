<?php
/**
 * Gutenberg Blocks
 *
 * @package Copy the Code
 * @since 3.1.0
 */

namespace CopyTheCode\Gutenberg;

use CopyTheCode\Gutenberg\Blocks\Icon;

/**
 * Blocks
 *
 * @since 3.1.0
 */
class Blocks {

	/**
	 * Constructor
	 */
	public function __construct() {
		require_once COPY_THE_CODE_DIR . 'classes/gutenberg/blocks/icon/class-block.php';

		new Icon();
	}

}

new Blocks();