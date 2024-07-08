<?php
/**
 * Settings Page
 *
 * @package Copy the Code
 * @since 1.2.0
 */

use CopyTheCode\Helpers;

if ( ! class_exists( 'Copy_The_Code_Page' ) ) :

	/**
	 * Copy_The_Code_Page
	 *
	 * @since 1.2.0
	 */
	class Copy_The_Code_Page {

		/**
		 * Instance
		 *
		 * @since 1.2.0
		 *
		 * @access private
		 * @var object Class object.
		 */
		private static $instance;

		/**
		 * Current selector
		 *
		 * @since 3.0.0
		 *
		 * @access private
		 * @var string Current CSS selector.
		 */
		private $selector;

		/**
		 * Initiator
		 *
		 * @since 1.2.0
		 *
		 * @return object initialized object of class.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 1.2.0
		 */
		public function __construct() {
			add_filter( 'admin_url', [ $this, 'admin_url' ], 10, 3 );
			add_action( 'plugin_action_links_' . COPY_THE_CODE_BASE, [ $this, 'action_links' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
			add_action( 'init', [ $this, 'register_post_type' ] );
			add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
			add_action( 'manage_copy-to-clipboard_posts_custom_column', [ $this, 'column_markup' ], 10, 2 );
			add_action( 'manage_copy-to-clipboard_posts_columns', [ $this, 'add_column' ], 10 );
		}

		/**
		 * Add meta box
		 *
		 * @since 3.0.0
		 */
		public function add_meta_box() {
			add_meta_box(
				'copy-the-code-meta-box',
				esc_html__( 'Settings', 'copy-the-code' ),
				[ $this, 'meta_box_markup' ],
				'copy-to-clipboard',
				'normal',
				'high'
			);
		}

		/**
		 * Meta box markup
		 *
		 * @param object $post Post object.
		 * @since 3.0.0
		 */
		public function meta_box_markup( $post ) {
			$selector         = get_post_meta( $post->ID, 'selector', true );
			$style            = get_post_meta( $post->ID, 'style', true );
			$button_text      = get_post_meta( $post->ID, 'button-text', true );
			$button_title     = get_post_meta( $post->ID, 'button-title', true );
			$button_copy_text = get_post_meta( $post->ID, 'button-copy-text', true );
			$button_position  = get_post_meta( $post->ID, 'button-position', true );
			$copy_format      = get_post_meta( $post->ID, 'copy-format', true );

			?>
			<table class="form-table">
				<tr>
					<td><?php esc_html_e( 'CSS Selector', 'copy-the-code' ); ?></td>
					<td><?php echo esc_html( $selector ); ?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Style', 'copy-the-code' ); ?></td>
					<td><?php echo esc_html( $style ); ?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Button Text', 'copy-the-code' ); ?></td>
					<td><?php echo esc_html( $button_text ); ?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Button Title', 'copy-the-code' ); ?></td>
					<td><?php echo esc_html( $button_title ); ?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Button Copy Text', 'copy-the-code' ); ?></td>
					<td><?php echo esc_html( $button_copy_text ); ?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Button Position', 'copy-the-code' ); ?></td>
					<td><?php echo esc_html( $button_position ); ?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Copy Format', 'copy-the-code' ); ?></td>
					<td><?php echo esc_html( $copy_format ); ?></td>
				</tr>
			</table>
			
			<p>
				<a href="<?php echo esc_url( admin_url( 'options-general.php?page=copy-the-code&id=' . $post->ID ) ); ?>" class="button button-primary"><?php esc_html_e( 'Edit', 'copy-the-code' ); ?></a>
			</p>
			<?php

		}

		/**
		 * Add custom column
		 *
		 * @param array $columns Columns.
		 * @since 2.0.0
		 */
		function add_column( $columns = [] ) {

			if ( isset( $columns['author'] ) ) {
				unset( $columns['author'] );
			}

			if ( isset( $columns['date'] ) ) {
				unset( $columns['date'] );
			}

			$new_columns = [
				'style'    => __( 'Style', 'copy-the-code' ),
				'settings' => __( 'Settings', 'copy-the-code' ),
				'author'   => 'Author',
				'date'     => 'Date',
			];

			return wp_parse_args( $new_columns, $columns );
		}

		/**
		 * Column markup
		 *
		 * @since 2.0.0
		 *
		 * @param  string  $column_name     Column slug.
		 * @param  integer $post_id         Post ID.
		 * @return void
		 */
		function column_markup( $column_name = '', $post_id = 0 ) {

			if ( 'style' === $column_name ) {
				$style = get_post_meta( $post_id, 'style', true );
				switch ( $style ) {
					case 'cover':
								echo 'Cover';
						break;
					case 'svg-icon':
								echo 'SVG Icon';
						break;
					case 'button':
								echo 'Button';
						break;
				}
			}
			if ( 'settings' === $column_name ) {
				$button_text = get_post_meta( $post_id, 'button-text', true );
				if ( ! empty( $button_text ) ) {
					echo '<i>Button Text: </i><b>' . $button_text . '</b><br/>';
				}
				$button_title = get_post_meta( $post_id, 'button-title', true );
				if ( ! empty( $button_title ) ) {
					echo '<i>Button Title: </i><b>' . $button_title . '</b><br/>';
				}
				$button_copy_text = get_post_meta( $post_id, 'button-copy-text', true );
				if ( ! empty( $button_copy_text ) ) {
					echo '<i>Button Copy Text: </i><b>' . $button_copy_text . '</b><br/>';
				}
				$button_position = get_post_meta( $post_id, 'button-position', true );
				if ( ! empty( $button_position ) ) {
					echo '<i>Button Position: </i><b>' . $button_position . '</b><br/>';
				}
				$format = get_post_meta( $post_id, 'copy-format', true );
				if ( ! empty( $format ) ) {
					echo '<i>Copy Format: </i><b>' . $format . '</b><br/>';
				}
			}

		}

		/**
		 * Filters the admin area URL.
		 *
		 * @since 1.0.2
		 *
		 * @param string   $url     The complete admin area URL including scheme and path.
		 * @param string   $path    Path relative to the admin area URL. Blank string if no path is specified.
		 * @param int|null $blog_id Site ID, or null for the current site.
		 */
		public function admin_url( $url, $path, $blog_id ) {

			if ( 'post-new.php?post_type=copy-to-clipboard' !== $path ) {
				return $url;
			}

			$url  = get_site_url( $blog_id, 'wp-admin/', 'admin' );
			$path = 'options-general.php?page=copy-the-code';

			if ( $path && is_string( $path ) ) {
				$url .= ltrim( $path, '/' );
			}

			return $url;
		}

		/**
		 * Add new page
		 *
		 * @since 2.0.0
		 */
		public function add_new_page() {
			$data = $this->get_page_settings();
			require_once COPY_THE_CODE_DIR . 'includes/add-new-form.php';
		}

		/**
		 * Registers a new post type
		 *
		 * @since 2.0.0
		 */
		function register_post_type() {

			$labels = [
				'name'               => __( 'Copy to Clipboard', 'copy-the-code' ),
				'singular_name'      => __( 'Copy to Clipboard', 'copy-the-code' ),
				'add_new'            => _x( 'Add New', 'copy-the-code', 'copy-the-code' ),
				'add_new_item'       => __( 'Add New', 'copy-the-code' ),
				'edit_item'          => __( 'Edit Copy to Clipboard', 'copy-the-code' ),
				'new_item'           => __( 'New Copy to Clipboard', 'copy-the-code' ),
				'view_item'          => __( 'View Copy to Clipboard', 'copy-the-code' ),
				'search_items'       => __( 'Search Copy to Clipboard', 'copy-the-code' ),
				'not_found'          => __( 'No Copy to Clipboard found', 'copy-the-code' ),
				'not_found_in_trash' => __( 'No Copy to Clipboard found in Trash', 'copy-the-code' ),
				'parent_item_colon'  => __( 'Parent Copy to Clipboard:', 'copy-the-code' ),
				'menu_name'          => __( 'Copy to Clipboard', 'copy-the-code' ),
			];

			$args = [
				'labels'              => $labels,
				'hierarchical'        => false,
				'description'         => 'description',
				'taxonomies'          => [],
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => 'options-general.php',
				'show_in_admin_bar'   => false,
				'menu_position'       => null,
				'menu_icon'           => 'dashicons-clipboard',
				'show_in_nav_menus'   => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => false,
				'has_archive'         => false,
				'query_var'           => false,
				'can_export'          => true,
				'rewrite'             => false,
				'capability_type'     => 'post',
				'supports'            => [
					'title',
				],
			];

			register_post_type( 'copy-to-clipboard', $args );
		}

		/**
		 * Enqueue Assets.
		 *
		 * @version 1.0.0
		 *
		 * @return void
		 */
		public function enqueue_assets() {
			$vars = $this->get_localize_vars();
			if ( ! $vars['selectors'] ) {
				return;
			}

			$conditions = [];
			foreach ( $vars['selectors'] as $selector ) {
				if ( ! $selector['conditions'] ) {
					continue;
				}

				$conditions = array_merge( $conditions, $selector['conditions'] );
			}
			if ( ! $this->meet_conditions( $conditions ) ) {
				return;
			}

			wp_enqueue_style( 'copy-the-code', COPY_THE_CODE_URI . 'assets/css/copy-the-code.css', null, COPY_THE_CODE_VER, 'all' );
			wp_enqueue_script( 'copy-the-code', COPY_THE_CODE_URI . 'assets/js/copy-the-code.js', [ 'jquery' ], COPY_THE_CODE_VER, true );
			wp_localize_script(
				'copy-the-code',
				'copyTheCode',
				$vars
			);
		}

		/**
		 * Meet Conditions
		 *
		 * @param  array $conditions Conditions.
		 * @return boolean
		 */
		public function meet_conditions( $conditions = [] ) {
			if ( ! $conditions ) {
				return true;
			}

			$meet = true;
			foreach ( $conditions as $condition ) {
				$meet = $this->meet_condition( $condition );
				if ( ! $meet ) {
					return false;
				}
			}

			return $meet;
		}

		/**
		 * Meet Condition
		 *
		 * @param  array $condition Condition.
		 * @return boolean
		 */
		public function meet_condition( $condition = [] ) {
			if ( ! $condition ) {
				return true;
			}

			$meet = false;
			switch ( $condition['type'] ) {
				case 'post_type':
					$meet = $this->meet_post_type_condition( $condition );
					break;
				case 'user_role':
					$meet = $this->meet_user_role_condition( $condition );
					break;
				case 'taxonomy':
					$meet = $this->meet_taxonomy_condition( $condition );
					break;
			}

			return $meet;
		}

		/**
		 * Meet Post Type Condition
		 *
		 * @param  array $condition Condition.
		 * @return boolean
		 */
		public function meet_post_type_condition( $condition = [] ) {
			if ( ! $condition ) {
				return true;
			}

			$post_type = get_post_type();
			if ( ! $post_type ) {
				return false;
			}

			$meet = false;
			switch ( $condition['operator'] ) {
				case '=':
					$meet = $post_type === $condition['value'];
					break;
				case '!=':
					$meet = $post_type !== $condition['value'];
					break;
			}

			return $meet;
		}

		/**
		 * Meet User Role Condition
		 *
		 * @param  array $condition Condition.
		 * @return boolean
		 */
		public function meet_user_role_condition( $condition = [] ) {
			if ( ! $condition ) {
				return true;
			}

			$user = wp_get_current_user();
			if ( ! $user ) {
				return false;
			}

			$meet = false;
			switch ( $condition['operator'] ) {
				case '=':
					$meet = in_array( $condition['value'], $user->roles, true );
					break;
				case '!=':
					$meet = ! in_array( $condition['value'], $user->roles, true );
					break;
			}

			return $meet;
		}

		/**
		 * Meet Taxonomy Condition
		 *
		 * @param  array $condition Condition.
		 * @return boolean
		 */
		public function meet_taxonomy_condition( $condition = [] ) {
			if ( ! $condition ) {
				return true;
			}

			$taxonomy = get_current_screen()->taxonomy;
			if ( ! $taxonomy ) {
				return false;
			}

			$meet = false;
			switch ( $condition['operator'] ) {
				case '=':
					$meet = $taxonomy === $condition['value'];
					break;
				case '!=':
					$meet = $taxonomy !== $condition['value'];
					break;
			}

			return $meet;
		}

		/**
		 * Localize Vars
		 *
		 * @return array
		 */
		function get_localize_vars() {

			$query_args = [
				'post_type'      => 'copy-to-clipboard',

				// Query performance optimization.
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'posts_per_page' => -1,
			];

			$query     = new WP_Query( $query_args );
			$selectors = [];
			if ( $query->posts ) {
				foreach ( $query->posts as $key => $post_id ) {
					$selectors[] = [
						'selector'         => get_post_meta( $post_id, 'selector', true ),
						'style'            => get_post_meta( $post_id, 'style', true ),
						// '/ $copy_as' => get_post_meta( $post_id, 'copy-as', true ),
						'button_text'      => get_post_meta( $post_id, 'button-text', true ),
						'button_title'     => get_post_meta( $post_id, 'button-title', true ),
						'button_copy_text' => get_post_meta( $post_id, 'button-copy-text', true ),
						'button_position'  => get_post_meta( $post_id, 'button-position', true ),
						'copy_format'      => get_post_meta( $post_id, 'copy-format', true ),
						'conditions'       => get_post_meta( $post_id, 'conditions', true ),
					];
				}
			}

			return apply_filters(
				'copy_the_code_localize_vars',
				[
					'trim_lines'      => false,
					'remove_spaces'   => true,
					'copy_content_as' => '',
					'previewMarkup'   => '&lt;h2&gt;Hello World&lt;/h2&gt;',
					'buttonMarkup'    => '<button class="copy-the-code-button" title=""></button>',
					'buttonSvg'       => Helpers::get_svg_copy_icon(),
					'selectors'       => $selectors,
					'selector'        => 'pre', // Selector in which have the actual `<code>`.
					'settings'        => $this->get_page_settings(),
					'string'          => [
						'title'  => $this->get_page_setting( 'button-title', __( 'Copy to Clipboard', 'copy-the-code' ) ),
						'copy'   => $this->get_page_setting( 'button-text', __( 'Copy to Clipboard', 'copy-the-code' ) ),
						'copied' => $this->get_page_setting( 'button-copy-text', __( 'Copied!', 'copy-the-code' ) ),
					],
					'image-url'       => COPY_THE_CODE_URI . '/assets/images/copy-1.svg',
					'redirect_url'    => '',
				]
			);
		}

		/**
		 * Get Setting
		 *
		 * @param  string $key           Setting key.
		 * @param  string $default_value Setting default value.
		 * @return mixed Single Setting.
		 */
		function get_page_setting( $key = '', $default_value = '' ) {
			$settings = $this->get_page_settings();

			if ( array_key_exists( $key, $settings ) ) {
				return $settings[ $key ];
			}

			return $default_value;
		}

		/**
		 * Settings
		 *
		 * @return array Settings.
		 */
		public function get_page_settings() {
			$defaults = apply_filters(
				'copy_the_code_default_page_settings',
				[
					'selector'         => 'pre',
					// 'copy-as'          => 'text',
					'button-text'      => __( 'Copy to Clipboard', 'copy-the-code' ),
					'button-title'     => __( 'Copy to Clipboard', 'copy-the-code' ),
					'button-copy-text' => __( 'Copied!', 'copy-the-code' ),
					'button-position'  => 'inside',
					'copy-format'      => 'default',
				]
			);

			$stored = get_option( 'copy-the-code-settings', $defaults );

			return apply_filters( 'copy_the_code_page_settings', wp_parse_args( $stored, $defaults ) );
		}

		/**
		 * Show action links on the plugin screen.
		 *
		 * @param   mixed $links Plugin Action links.
		 * @return  array
		 */
		function action_links( $links ) {
			$action_links = [
				'add-new' => '<a href="' . admin_url( 'options-general.php?page=copy-the-code' ) . '" aria-label="' . esc_attr__( 'Add new', 'copy-the-code' ) . '">' . esc_html__( 'Add new', 'copy-the-code' ) . '</a>',
			];

			return array_merge( $action_links, $links );
		}

	}

	/**
	 * Initialize class object with 'get_instance()' method
	 */
	Copy_The_Code_Page::get_instance();

endif;
