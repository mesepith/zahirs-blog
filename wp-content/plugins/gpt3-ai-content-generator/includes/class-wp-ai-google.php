<?php
namespace WPAICG;

if (!defined('ABSPATH')) exit;

if (!class_exists('\\WPAICG\\WPAICG_Google')) {
    class WPAICG_Google {
        private static $instance = null;
        private $apiKey;
        private $timeout = 300; // Timeout for Google API requests

        public static function get_instance() {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function __construct() {
            $this->apiKey = get_option('wpaicg_google_model_api_key', '');
        }

        public function send_google_request($title, $model, $temperature, $top_p, $max_tokens) {
            if (empty($this->apiKey)) {
                return 'Error: Google API key is not set';
            }

            $args = array(
                'headers' => array('Content-Type' => 'application/json'),
                'method' => 'POST',
                'timeout' => $this->timeout
            );

            // Construct the request URL and body based on the model
            switch ($model) {
                case 'text-bison-001':
                    $url = "https://generativelanguage.googleapis.com/v1beta3/models/text-bison-001:generateText?key={$this->apiKey}";
                    $args['body'] = json_encode(array("prompt" => array("text" => $title)));
                    break;
                case 'chat-bison-001':
                    $url = "https://generativelanguage.googleapis.com/v1beta2/models/chat-bison-001:generateMessage?key={$this->apiKey}";
                    $args['body'] = json_encode(array("prompt" => array("messages" => array(array("content" => $title)))));
                    break;
                case 'gemini-pro':
                    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$this->apiKey}";
                    $args['body'] = json_encode(array(
                        "contents" => [
                            ["role" => "user", "parts" => [["text" => $title]]]
                        ],
                        "generationConfig" => [
                            "temperature" => $temperature, 
                            "topK" => 1, 
                            "topP" => $top_p, 
                            "maxOutputTokens" => $max_tokens, 
                            "stopSequences" => []
                        ],
                        "safetySettings" => [
                            ["category" => "HARM_CATEGORY_HARASSMENT", "threshold" => "BLOCK_MEDIUM_AND_ABOVE"]
                        ]
                    ));
                    break;
                default:
                    return 'Error: Invalid model selected';
            }
            $response = wp_remote_post($url, $args);
            return $this->handle_response($response);
        }

        private function handle_response($response) {
            if (is_wp_error($response)) {
                return 'HTTP request error: ' . $response->get_error_message();
            }
        
            $body = wp_remote_retrieve_body($response);
            $decodedResponse = json_decode($body, true);
    
            if (isset($decodedResponse['error'])) {
                // Extracting the detailed error message if available
                $errorMsg = isset($decodedResponse['error']['message']) ? $decodedResponse['error']['message'] : 'Unknown error from Google API';
                error_log('Parsed error message: ' . $errorMsg);
                return array('error' => 'Error: ' . $errorMsg);
            } elseif (empty($decodedResponse)) {
                // Handle cases where the response is empty or not as expected
                error_log('No data found in the response');
                return array('error' => 'No valid content found in the response');
            }
        
            return $decodedResponse;
        }
        
    }
}
