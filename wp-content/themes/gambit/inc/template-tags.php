<?php
/**
 * Template Tags
 *
 * This file contains several template functions which are used to print out specific HTML markup
 * in the theme. You can override these template functions within your child theme.
 *
 * @package Gambit
 */

if ( ! function_exists( 'gambit_site_logo' ) ) :
	/**
	 * Displays the site logo in the header area
	 */
	function gambit_site_logo() {

		if ( function_exists( 'the_custom_logo' ) ) {

			the_custom_logo();

		}
	}
endif;


if ( ! function_exists( 'gambit_site_title' ) ) :
	/**
	 * Displays the site title in the header area
	 */
	function gambit_site_title() {

		// Get theme options from database.
		$theme_options = gambit_theme_options();

		if ( ( is_home() and '' === $theme_options['blog_title'] ) or is_page_template( 'template-magazine.php' ) ) : ?>

			<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>

		<?php else : ?>

			<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>

			<?php
		endif;
	}
endif;


if ( ! function_exists( 'gambit_site_description' ) ) :
	/**
	 * Displays the site description in the header area
	 */
	function gambit_site_description() {

		$description = get_bloginfo( 'description', 'display' ); /* WPCS: xss ok. */

		if ( $description || is_customize_preview() ) :
			?>

			<p class="site-description"><?php echo $description; ?></p>

			<?php
		endif;
	}
endif;


if ( ! function_exists( 'gambit_header_image' ) ) :
	/**
	 * Displays the custom header image below the navigation menu
	 */
	function gambit_header_image() {

		// Get theme options from database.
		$theme_options = gambit_theme_options();

		// Display featured image as header image on static pages.
		if ( is_page() && has_post_thumbnail() ) :
			?>

			<div id="headimg" class="header-image featured-image-header">
				<?php the_post_thumbnail( 'gambit-header-image' ); ?>
			</div>

			<?php
			// Display default header image set on Appearance > Header.
		elseif ( get_header_image() ) :
			?>

			<div id="headimg" class="header-image">

				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<img src="<?php header_image(); ?>" srcset="<?php echo esc_attr( wp_get_attachment_image_srcset( get_custom_header()->attachment_id, 'full' ) ); ?>" width="<?php echo esc_attr( get_custom_header()->width ); ?>" height="<?php echo esc_attr( get_custom_header()->height ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
				</a>

			</div>

			<?php
		endif;
	}
endif;


if ( ! function_exists( 'gambit_post_content' ) ) :
	/**
	 * Displays the post content on archive pages
	 */
	function gambit_post_content() {

		// Get theme options from database.
		$theme_options = gambit_theme_options();

		// Return early if no featured image should be displayed.
		if ( 'excerpt' === $theme_options['post_content'] ) {

			the_excerpt();
			gambit_more_link();

		} else {

			the_content( esc_html__( 'Read more', 'gambit' ) );

		}
	}
endif;


if ( ! function_exists( 'gambit_post_image' ) ) :
	/**
	 * Displays the featured image on archive posts.
	 *
	 * @param string $size Post thumbnail size.
	 * @param array  $attr Post thumbnail attributes.
	 */
	function gambit_post_image( $size = 'post-thumbnail', $attr = array() ) {

		// Display Post Thumbnail.
		if ( has_post_thumbnail() ) :
			?>

			<a href="<?php the_permalink(); ?>" rel="bookmark">
				<?php the_post_thumbnail( $size, $attr ); ?>
			</a>

			<?php
		endif;
	}
endif;


if ( ! function_exists( 'gambit_post_image_single' ) ) :
	/**
	 * Displays the featured image on single posts
	 */
	function gambit_post_image_single() {

		// Get theme options from database.
		$theme_options = gambit_theme_options();

		// Display Post Thumbnail if activated.
		if ( true === $theme_options['post_image'] ) :

			the_post_thumbnail();

		endif;
	}
endif;


if ( ! function_exists( 'gambit_entry_meta' ) ) :
	/**
	 * Displays the date, author and categories of a post
	 */
	function gambit_entry_meta() {

		$postmeta  = gambit_meta_date();
		$postmeta .= gambit_meta_author();
		$postmeta .= gambit_meta_category();

		echo '<div class="entry-meta">' . $postmeta . '</div>';
	}
endif;


if ( ! function_exists( 'gambit_meta_date' ) ) :
	/**
	 * Displays the post date
	 */
	function gambit_meta_date() {

		$time_string = sprintf(
			'<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date published updated" datetime="%3$s">%4$s</time></a>',
			esc_url( get_permalink() ),
			esc_attr( get_the_time() ),
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() )
		);

		$posted_on = gambit_get_svg( 'standard' ) . $time_string;

		return '<span class="meta-date">' . $posted_on . '</span>';
	}
endif;


if ( ! function_exists( 'gambit_meta_author' ) ) :
	/**
	 * Displays the post author
	 */
	function gambit_meta_author() {

		$author_string = sprintf(
			'<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( esc_html__( 'View all posts by %s', 'gambit' ), get_the_author() ) ),
			esc_html( get_the_author() )
		);

		$posted_by = gambit_get_svg( 'user' ) . $author_string;

		return '<span class="meta-author"> ' . $posted_by . '</span>';
	}
endif;


if ( ! function_exists( 'gambit_meta_category' ) ) :
	/**
	 * Displays the category of posts
	 */
	function gambit_meta_category() {

		// Return early if post has no category.
		if ( ! has_category() ) {
			return;
		}

		$posted_in = gambit_get_svg( 'category' ) . get_the_category_list( ', ' );

		return '<span class="meta-category"> ' . $posted_in . '</span>';

	}
endif;


if ( ! function_exists( 'gambit_entry_tags' ) ) :
	/**
	 * Displays the post tags on single post view
	 */
	function gambit_entry_tags() {

		// Get tags.
		$tag_list = get_the_tag_list( '', '' );

		// Display tags.
		if ( $tag_list ) :
			?>

			<div class="entry-tags clearfix">
				<span class="meta-tags">
					<?php echo $tag_list; ?>
				</span>
			</div><!-- .entry-tags -->

			<?php
		endif;
	}
endif;


if ( ! function_exists( 'gambit_more_link' ) ) :
	/**
	 * Displays the more link on posts
	 */
	function gambit_more_link() {
		?>

		<a href="<?php echo esc_url( get_permalink() ); ?>" class="more-link"><?php esc_html_e( 'Read more', 'gambit' ); ?></a>

		<?php
	}
endif;


if ( ! function_exists( 'gambit_post_navigation' ) ) :
	/**
	 * Displays Single Post Navigation
	 */
	function gambit_post_navigation() {

		// Get theme options from database.
		$theme_options = gambit_theme_options();

		if ( true === $theme_options['post_navigation'] || is_customize_preview() ) {

			the_post_navigation(
				array(
					'prev_text' => '<span class="screen-reader-text">' . esc_html_x( 'Previous Post:', 'post navigation', 'gambit' ) . '</span>%title',
					'next_text' => '<span class="screen-reader-text">' . esc_html_x( 'Next Post:', 'post navigation', 'gambit' ) . '</span>%title',
				)
			);

		}
	}
endif;


if ( ! function_exists( 'gambit_breadcrumbs' ) ) :
	/**
	 * Displays ThemeZee Breadcrumbs plugin
	 */
	function gambit_breadcrumbs() {

		if ( function_exists( 'themezee_breadcrumbs' ) ) {

			themezee_breadcrumbs(
				array(
					'before' => '<div class="breadcrumbs-container container clearfix">',
					'after'  => '</div>',
				)
			);

		}
	}
endif;


if ( ! function_exists( 'gambit_related_posts' ) ) :
	/**
	 * Displays ThemeZee Related Posts plugin
	 */
	function gambit_related_posts() {

		if ( function_exists( 'themezee_related_posts' ) ) {

			themezee_related_posts(
				array(
					'class'        => 'related-posts type-page clearfix',
					'before_title' => '<header class="page-header"><h2 class="archive-title related-posts-title">',
					'after_title'  => '</h2></header>',
				)
			);

		}
	}
endif;


if ( ! function_exists( 'gambit_pagination' ) ) :
	/**
	 * Displays pagination on archive pages
	 */
	function gambit_pagination() {

		the_posts_pagination(
			array(
				'mid_size'  => 2,
				'prev_text' => '&laquo;<span class="screen-reader-text">' . esc_html_x( 'Previous Posts', 'pagination', 'gambit' ) . '</span>',
				'next_text' => '<span class="screen-reader-text">' . esc_html_x( 'Next Posts', 'pagination', 'gambit' ) . '</span>&raquo;',
			)
		);

	}
endif;


/**
 * Displays credit link on footer line
 */
function gambit_footer_text() {
	?>

	<span class="credit-link">
		<?php
		// translators: Theme Name and Link to ThemeZee.
		printf(
			esc_html__( 'WordPress Theme: %1$s by %2$s.', 'gambit' ),
			esc_html__( 'Gambit', 'gambit' ),
			'ThemeZee'
		);
		?>
	</span>

	<?php
}
add_action( 'gambit_footer_text', 'gambit_footer_text' );
