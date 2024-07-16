<?php
/*
Plugin Name: AMP Copy Code Button
Description: Adds a "copy" button to code blocks in AMP pages or posts.
Version: 1.0
Author: Zahir Alam
*/

// Enqueue AMP scripts
function amp_copy_code_button_scripts() {
    if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
        echo '<script async custom-element="amp-iframe" src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js"></script>';
        echo '<script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>';
        echo '<script async custom-element="amp-script" src="https://cdn.ampproject.org/v0/amp-script-0.1.js"></script>';

        echo '<meta name="amp-script-src" content="sha384-w-Q5Hf2fy8mMQU5TTe6V283imDH_JYVjIY6ft2D8kczHtq6NSb1TRTRbtJRekZHh">';
    }
}
add_action( 'wp_head', 'amp_copy_code_button_scripts' );

function add_copy_button_to_code_blocks( $content ) {
    if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
        $pattern = '/<pre class="wp-block-code"><code>(.*?)<\/code><\/pre>/is';
        $replacement = function($matches) {
            static $id = 0;
            $id++;
            $code = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $plugin_url = plugin_dir_url( __FILE__ );
            return '<div class="code-block-container">
                        <pre class="wp-block-code"><code id="code-' . $id . '">' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '</code></pre>
                        <amp-iframe sandbox="allow-scripts" width="94" height="72" frameborder="0" 
                                    src="' . $plugin_url . 'copier.html#' . rawurlencode($code) . '">
                            <button class="copy-button" data-label="' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '"  placeholder disabled>Copy</button>
                        </amp-iframe>
                    </div>';
        };
        $content = preg_replace_callback( $pattern, $replacement, $content );

        // Add AMP Analytics
        $content .= '
        <amp-analytics type="gtag" data-credentials="include">
            <script type="application/json">
            {
              "vars" : {
                "gtag_id": "G-27WDDW3TCL",
                "config" : {
                  "G-27WDDW3TCL": { "groups": "default" }
                }
              },
              "triggers": {
                "copyButtonClick": {
                  "on": "amp-script-response",
                  "request": "event",
                  "vars": {
                    "event_name": "copy_button_click",
                    "event_category": "User Interaction",
                    "event_label": "Copy Button"
                  }
                }
              }
            }
            </script>
        </amp-analytics>';

        // Add AMP Script for handling copy button clicks
        $content .= '
        <amp-script script="copy-button-script" data-ampdevmode>
            <script type="text/plain" target="amp-script" id="copy-button-script">
                window.addEventListener("message", (event) => {
                    if (event.data === "copy_button_clicked") {
                        const copyEvent = new CustomEvent("amp-script-response", {
                            detail: {
                                name: "copy_button_click"
                            }
                        });
                        window.dispatchEvent(copyEvent);
                    }
                });
            </script>
        </amp-script>';
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
