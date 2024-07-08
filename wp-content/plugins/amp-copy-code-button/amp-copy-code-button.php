<?php
/*
Plugin Name: AMP Copy Code Button
Description: Adds a "copy" button to code blocks in AMP pages or posts.
Version: 1.0
Author: Zahir Alam
*/

// Enqueue AMP iframe script
// Enqueue AMP iframe script
function amp_copy_code_button_scripts() {
    if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
        echo '<script async custom-element="amp-iframe" src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js"></script>';
    }
}
add_action( 'wp_head', 'amp_copy_code_button_scripts' );

// Add copy button to code blocks
function add_copy_button_to_code_blocks( $content ) {
    if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
        $plugin_url = plugin_dir_url( __FILE__ );
        $pattern = '/<pre class="wp-block-code"><code>(.*?)<\/code><\/pre>/is';
        $replacement = '<div class="code-block-container">
                            <pre class="wp-block-code"><code>$1</code></pre>
                            <amp-iframe sandbox="allow-scripts allow-same-origin" width="64" height="42" frameborder="0" src="' . $plugin_url . 'amp-copy-code.html#$1">
                                <button class="copy-button" placeholder>Copy</button>
                            </amp-iframe>
                        </div>';
        $content = preg_replace( $pattern, $replacement, $content );
    }
    return $content;
}
add_filter( 'the_content', 'add_copy_button_to_code_blocks' );


// Enqueue the stylesheet
function enqueue_amp_copy_code_stylesheet() {
    if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
        wp_enqueue_style( 'amp-copy-code', plugin_dir_url( __FILE__ ) . 'css/amp-copy-code.css', array(), '1.0' );
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_amp_copy_code_stylesheet' );

