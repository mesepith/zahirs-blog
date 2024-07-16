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
    }
}
add_action( 'wp_head', 'amp_copy_code_button_scripts' );

// Add copy button to code blocks
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

        $content .= '
        <amp-script script="copy-button-script">
            <script type="text/plain" target="amp-script" id="copy-button-script">
                window.addEventListener("message", (event) => {
                    if (event.data === "copy_button_clicked") {
                        const copyEvent = new CustomEvent("amp-script-response", {
                            detail: {
                                name: "copy_button_click"
                            }
                        });
                        window.dispatchEvent(copyEvent);
                        
                        // Explicitly trigger analytics event
                        const analytics = document.createElement("amp-analytics");
                        analytics.setAttribute("type", "gtag");
                        analytics.setAttribute("data-credentials", "include");
                        analytics.innerHTML = `
                            <script type="application/json">
                            {
                                "requests": {
                                    "copyClick": {
                                        "baseUrl": "https://www.google-analytics.com/collect",
                                        "params": {
                                            "v": "1",
                                            "t": "event",
                                            "tid": "G-27WDDW3TCL",
                                            "cid": "CLIENT_ID(AMP_ECID_GOOGLE)",
                                            "ec": "User Interaction",
                                            "ea": "copy_button_click",
                                            "el": "Copy Button"
                                        }
                                    }
                                },
                                "triggers": {
                                    "trackCopyClick": {
                                        "on": "visible",
                                        "request": "copyClick"
                                    }
                                }
                            }
                            </script>
                        `;
                        document.body.appendChild(analytics);
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
