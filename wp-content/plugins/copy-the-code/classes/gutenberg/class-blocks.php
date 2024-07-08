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

		add_filter( 'block_categories_all', [ $this, 'add_block_category' ], 10, 2 );
	}

	/**
	 * Add block category.
	 *
	 * @param array  $categories Block categories.
	 * @param object $post Post object.
	 * @return array
	 */
	public function add_block_category( $categories, $post ) {
		return array_merge(
			$categories,
			[
				[
					'slug'  => 'ctc-blocks',
					'title' => esc_html__( 'Copy Anything to Clipboard', 'copy-the-code' ),
				],
			]
		);
	}

}

new Blocks();
