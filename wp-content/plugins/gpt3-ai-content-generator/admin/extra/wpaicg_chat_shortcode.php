<?php
if ( ! defined( 'ABSPATH' ) ) exit;
wp_enqueue_script('wp-color-picker');
wp_enqueue_style('wp-color-picker');
global  $wpdb ;
$table = $wpdb->prefix . 'wpaicg';
$wpaicg_save_setting_success = false;
if(isset($_POST['wpaicg_chat_shortcode_options']) && is_array($_POST['wpaicg_chat_shortcode_options'])){
    check_admin_referer('wpaicg_chat_shortcode_save');
    $posted_options = stripslashes_deep($_POST['wpaicg_chat_shortcode_options']);

    // Extract and sanitize the 'limited_message' field separately
    $limited_message = isset($posted_options['limited_message']) ? wp_kses_post($posted_options['limited_message']) : '';

    // Remove 'limited_message' from the array for general sanitization
    unset($posted_options['limited_message']);

    // extract and sanitize the 'footer_text' field separately
    $footer_text = isset($posted_options['footer_text']) ? wp_kses_post($posted_options['footer_text']) : '';

    // Remove 'footer_text' from the array for general sanitization
    unset($posted_options['footer_text']);

    $wpaicg_chat_shortcode_options = \WPAICG\wpaicg_util_core()->sanitize_text_or_array_field($posted_options);
    
    // Merge the separately sanitized 'limited_message' field back into the options
    $wpaicg_chat_shortcode_options['limited_message'] = $limited_message;

    // Merge the separately sanitized 'footer_text' field back into the options
    $wpaicg_chat_shortcode_options['footer_text'] = $footer_text;

    update_option('wpaicg_chat_shortcode_options',$wpaicg_chat_shortcode_options);

    $stream_nav_option = isset($_POST['wpaicg_stream_nav_option']) ? '1' : '0';
    update_option('wpaicg_shortcode_stream', $stream_nav_option);

    // Retrieve and sanitize the 'vectordb' option
    $vectordb = isset($posted_options['vectordb']) ? sanitize_text_field($posted_options['vectordb']) : 'pinecone';
    $wpaicg_chat_shortcode_options['vectordb'] = $vectordb;
    
    $wpaicg_save_setting_success = 'Setting saved successfully';
}
if(isset($_POST['wpaicg_azure_deployment_input'])){
    $new_deployment_name = sanitize_text_field($_POST['wpaicg_azure_deployment_input']);
    update_option('wpaicg_azure_deployment', $new_deployment_name);
}
if(isset($_POST['wpaicg_shortcode_google_model'])){
    $wpaicg_shortcode_google_model = sanitize_text_field($_POST['wpaicg_shortcode_google_model']);
    update_option('wpaicg_shortcode_google_model', $wpaicg_shortcode_google_model);
}

$existingValue = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE name = %s", 'wpaicg_settings' ), ARRAY_A );
$wpaicg_chat_shortcode_options = get_option('wpaicg_chat_shortcode_options',[]);

$wpaicg_stream_nav_setting = get_option('wpaicg_shortcode_stream', '0'); // Default to '1' if not set


$default_setting = array(
    'language' => 'en',
    'tone' => 'friendly',
    'profession' => 'none',
    'model' => 'gpt-3.5-turbo',
    'temperature' => $existingValue['temperature'],
    'max_tokens' => $existingValue['max_tokens'],
    'top_p' => $existingValue['top_p'],
    'best_of' => $existingValue['best_of'],
    'frequency_penalty' => $existingValue['frequency_penalty'],
    'presence_penalty' => $existingValue['presence_penalty'],
    'ai_name' => 'AI',
    'you' => __('You','gpt3-ai-content-generator'),
    'ai_thinking' => __('Gathering thoughts...','gpt3-ai-content-generator'),
    'placeholder' => __('Type a message','gpt3-ai-content-generator'),
    'welcome' => __('Hello, I am an AI bot. Ask me anything!','gpt3-ai-content-generator'),
    'remember_conversation' => 'yes',
    'conversation_cut' => 10,
    'content_aware' => 'yes',
    'embedding' =>  false,
    'embedding_type' =>  false,
    'embedding_index' =>  '',
    'embedding_pdf' => false,
    'embedding_pdf_message' => "Congrats! Your PDF is uploaded now! You can ask questions about your document.\nExample Questions:[questions]",
    'embedding_top' =>  false,
    'no_answer' => '',
    'fontsize' => 14,
    'fontcolor' => '#fff',
    'user_bg_color' => '#444654',
    'ai_bg_color' => '#343541',
    'ai_icon_url' => '',
    'ai_icon' => 'default',
    'use_avatar' => false,
    'width' => '100%',
    'height' => '50%',
    'save_logs' => false,
    'log_notice' => false,
    'log_notice_message' => __('Please note that your conversations will be recorded.','gpt3-ai-content-generator'),
    'bgcolor' => '#141414',
    'bg_text_field' => '#fff',
    'send_color' => '#fff',
    'border_text_field' => '#ccc',
    'footer_text' => '',
    'chat_addition' => false,
    'chat_addition_option' => 1,
    'chat_addition_text' => '',
    'audio_enable' => false,
    'image_enable' => false,
    'mic_color' => '#222',
    'pdf_color' => '#222',
    'stop_color' => '#f00',
    'user_aware' => 'no',
    'user_limited' => false,
    'guest_limited' => false,
    'user_tokens' => 0,
    'guest_tokens' => 0,
    'reset_limit' => 0,
    'limited_message' => __('You have reached your token limit.','gpt3-ai-content-generator'),
    'moderation' => false,
    'moderation_model' => 'text-moderation-latest',
    'moderation_notice' => __('Your message has been flagged as potentially harmful or inappropriate. Please ensure that your messages are respectful and do not contain language or content that could be offensive or harmful to others. Thank you for your cooperation.','gpt3-ai-content-generator'),
    'role_limited' => false,
    'limited_roles' => [],
    'log_request' => false,
    'fullscreen' => false,
    'download_btn' => false,
    'clear_btn' => false,
    'bar_color' => '#fff',
    'thinking_color' => '#fff',
    'chat_to_speech' => false,
    'elevenlabs_voice' => '',
    'elevenlabs_model' => '',
    'text_height' => 40,
    'text_rounded' => 8,
    'chat_rounded' => 8,
    'pdf_pages' => 120,
    'voice_language' => 'en-US',
    'voice_name' => 'en-US-Studio-M',
    'voice_device' => '',
    'voice_speed' => 1,
    'voice_pitch' => 0,
    'voice_service' => '',
    'openai_model' => 'tts-1',
    'openai_voice' => 'alloy',
    'openai_output_format' => 'mp3',
    'openai_voice_speed' => '1.0',
    'vectordb' => 'pinecone',
    'qdrant_collection' => '',
);
$wpaicg_pinecone_api = get_option('wpaicg_pinecone_api','');
$wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment','');
$wpaicg_settings = shortcode_atts($default_setting, $wpaicg_chat_shortcode_options);
if (!isset($wpaicg_settings['vectordb'])) {
    $wpaicg_settings['vectordb'] = 'pinecone'; // Set default to Pinecone
}
$wpaicg_custom_models = get_option('wpaicg_custom_models',array());

$wpaicg_save_logs = isset($wpaicg_settings['save_logs']) && !empty($wpaicg_settings['save_logs']) ? $wpaicg_settings['save_logs'] : false;
$wpaicg_log_notice = isset($wpaicg_settings['log_notice']) && !empty($wpaicg_settings['log_notice']) ? $wpaicg_settings['log_notice'] : false;
$wpaicg_log_notice_message = isset($wpaicg_settings['log_notice_message']) && !empty($wpaicg_settings['log_notice_message']) ? $wpaicg_settings['log_notice_message'] : __('Please note that your conversations will be recorded.','gpt3-ai-content-generator');
$wpaicg_user_limited = isset($wpaicg_settings['user_limited']) ? $wpaicg_settings['user_limited'] : false;
$wpaicg_guest_limited = isset($wpaicg_settings['guest_limited']) ? $wpaicg_settings['guest_limited'] : false;
$wpaicg_user_tokens = isset($wpaicg_settings['user_tokens']) ? $wpaicg_settings['user_tokens'] : 0;
$wpaicg_guest_tokens = isset($wpaicg_settings['guest_tokens']) ? $wpaicg_settings['guest_tokens'] : 0;
$wpaicg_reset_limit = isset($wpaicg_settings['reset_limit']) ? $wpaicg_settings['reset_limit'] : 0;
$wpaicg_limited_message = isset($wpaicg_settings['limited_message']) && !empty($wpaicg_settings['limited_message']) ? $wpaicg_settings['limited_message'] : __('You have reached your token limit.','gpt3-ai-content-generator');
$wpaicg_chat_fullscreen = isset($wpaicg_settings['fullscreen']) && !empty($wpaicg_settings['fullscreen']) ? $wpaicg_settings['fullscreen'] : false;
$wpaicg_chat_download_btn = isset($wpaicg_settings['download_btn']) && !empty($wpaicg_settings['download_btn']) ? $wpaicg_settings['download_btn'] : false;
$wpaicg_chat_clear_btn = isset($wpaicg_settings['clear_btn']) && !empty($wpaicg_settings['clear_btn']) ? $wpaicg_settings['clear_btn'] : false;
$wpaicg_bar_color = isset($wpaicg_settings['bar_color']) && !empty($wpaicg_settings['bar_color']) ? $wpaicg_settings['bar_color'] : '#fff';
$wpaicg_thinking_color = isset($wpaicg_settings['thinking_color']) && !empty($wpaicg_settings['thinking_color']) ? $wpaicg_settings['thinking_color'] : '#fff';
$wpaicg_chat_to_speech = isset($wpaicg_settings['chat_to_speech']) ? $wpaicg_settings['chat_to_speech'] : false;
$wpaicg_elevenlabs_voice = isset($wpaicg_settings['elevenlabs_voice']) ? $wpaicg_settings['elevenlabs_voice'] : '';
$wpaicg_elevenlabs_model = isset($wpaicg_settings['elevenlabs_model']) ? $wpaicg_settings['elevenlabs_model'] : '';
$wpaicg_elevenlabs_api = get_option('wpaicg_elevenlabs_api', '');
$wpaicg_chat_voice_service = isset($wpaicg_settings['voice_service']) ? $wpaicg_settings['voice_service'] : '';
$wpaicg_google_voices = get_option('wpaicg_google_voices',[]);
$wpaicg_google_api_key = get_option('wpaicg_google_api_key', '');
$wpaicg_roles = wp_roles()->get_names();
$wpaicg_pinecone_indexes = get_option('wpaicg_pinecone_indexes','');
$wpaicg_pinecone_indexes = empty($wpaicg_pinecone_indexes) ? array() : json_decode($wpaicg_pinecone_indexes,true);
$wpaicg_qdrant_collections = get_option('wpaicg_qdrant_collections',[]);
$wpaicg_qdrant_collections = empty($wpaicg_qdrant_collections) ? array() : $wpaicg_qdrant_collections;

?>

<?php
if($wpaicg_save_setting_success):
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php echo esc_html($wpaicg_save_setting_success);?></p>
    </div>
<?php
endif;
?>
<?php
// Define the options outside of the HTML structure for better readability and maintainability
$tone_options = array(
    'friendly' => esc_html__('Friendly', 'gpt3-ai-content-generator'),
    'professional' => esc_html__('Professional', 'gpt3-ai-content-generator'),
    'sarcastic' => esc_html__('Sarcastic', 'gpt3-ai-content-generator'),
    'humorous' => esc_html__('Humorous', 'gpt3-ai-content-generator'),
    'cheerful' => esc_html__('Cheerful', 'gpt3-ai-content-generator'),
    'anecdotal' => esc_html__('Anecdotal', 'gpt3-ai-content-generator'),
);

// Use the selected tone from settings, default to null if not set
$selected_tone = isset($wpaicg_settings['tone']) ? $wpaicg_settings['tone'] : null;

$profession_options = array(
    'none' => esc_html__('None', 'gpt3-ai-content-generator'),
    'accountant' => esc_html__('Accountant', 'gpt3-ai-content-generator'),
    'advertisingspecialist' => esc_html__('Advertising Specialist', 'gpt3-ai-content-generator'),
    'architect' => esc_html__('Architect', 'gpt3-ai-content-generator'),
    'artist' => esc_html__('Artist', 'gpt3-ai-content-generator'),
    'blogger' => esc_html__('Blogger', 'gpt3-ai-content-generator'),
    'businessanalyst' => esc_html__('Business Analyst', 'gpt3-ai-content-generator'),
    'businessowner' => esc_html__('Business Owner', 'gpt3-ai-content-generator'),
    'carexpert' => esc_html__('Car Expert', 'gpt3-ai-content-generator'),
    'consultant' => esc_html__('Consultant', 'gpt3-ai-content-generator'),
    'counselor' => esc_html__('Counselor', 'gpt3-ai-content-generator'),
    'cryptocurrencytrader' => esc_html__('Cryptocurrency Trader', 'gpt3-ai-content-generator'),
    'cryptocurrencyexpert' => esc_html__('Cryptocurrency Expert', 'gpt3-ai-content-generator'),
    'customersupport' => esc_html__('Customer Support', 'gpt3-ai-content-generator'),
    'designer' => esc_html__('Designer', 'gpt3-ai-content-generator'),
    'digitalmarketinagency' => esc_html__('Digital Marketing Agency', 'gpt3-ai-content-generator'),
    'editor' => esc_html__('Editor', 'gpt3-ai-content-generator'),
    'engineer' => esc_html__('Engineer', 'gpt3-ai-content-generator'),
    'eventplanner' => esc_html__('Event Planner', 'gpt3-ai-content-generator'),
    'freelancer' => esc_html__('Freelancer', 'gpt3-ai-content-generator'),
    'insuranceagent' => esc_html__('Insurance Agent', 'gpt3-ai-content-generator'),
    'insurancebroker' => esc_html__('Insurance Broker', 'gpt3-ai-content-generator'),
    'interiordesigner' => esc_html__('Interior Designer', 'gpt3-ai-content-generator'),
    'journalist' => esc_html__('Journalist', 'gpt3-ai-content-generator'),
    'marketingagency' => esc_html__('Marketing Agency', 'gpt3-ai-content-generator'),
    'marketingexpert' => esc_html__('Marketing Expert', 'gpt3-ai-content-generator'),
    'marketingspecialist' => esc_html__('Marketing Specialist', 'gpt3-ai-content-generator'),
    'photographer' => esc_html__('Photographer', 'gpt3-ai-content-generator'),
    'programmer' => esc_html__('Programmer', 'gpt3-ai-content-generator'),
    'publicrelationsagency' => esc_html__('Public Relations Agency', 'gpt3-ai-content-generator'),
    'publisher' => esc_html__('Publisher', 'gpt3-ai-content-generator'),
    'realestateagent' => esc_html__('Real Estate Agent', 'gpt3-ai-content-generator'),
    'recruiter' => esc_html__('Recruiter', 'gpt3-ai-content-generator'),
    'reporter' => esc_html__('Reporter', 'gpt3-ai-content-generator'),
    'salesperson' => esc_html__('Sales Person', 'gpt3-ai-content-generator'),
    'salerep' => esc_html__('Sales Representative', 'gpt3-ai-content-generator'),
    'seoagency' => esc_html__('SEO Agency', 'gpt3-ai-content-generator'),
    'seoexpert' => esc_html__('SEO Expert', 'gpt3-ai-content-generator'),
    'socialmediaagency' => esc_html__('Social Media Agency', 'gpt3-ai-content-generator'),
    'student' => esc_html__('Student', 'gpt3-ai-content-generator'),
    'teacher' => esc_html__('Teacher', 'gpt3-ai-content-generator'),
    'technicalsupport' => esc_html__('Technical Support', 'gpt3-ai-content-generator'),
    'trainer' => esc_html__('Trainer', 'gpt3-ai-content-generator'),
    'travelagency' => esc_html__('Travel Agency', 'gpt3-ai-content-generator'),
    'videographer' => esc_html__('Videographer', 'gpt3-ai-content-generator'),
    'webdesignagency' => esc_html__('Web Design Agency', 'gpt3-ai-content-generator'),
    'webdesignexpert' => esc_html__('Web Design Expert', 'gpt3-ai-content-generator'),
    'webdevelopmentagency' => esc_html__('Web Development Agency', 'gpt3-ai-content-generator'),
    'webdevelopmentexpert' => esc_html__('Web Development Expert', 'gpt3-ai-content-generator'),
    'webdesigner' => esc_html__('Web Designer', 'gpt3-ai-content-generator'),
    'webdeveloper' => esc_html__('Web Developer', 'gpt3-ai-content-generator'),
    'writer' => esc_html__('Writer', 'gpt3-ai-content-generator'),
);

$selected_profession = isset($wpaicg_settings['profession']) ? $wpaicg_settings['profession'] : 'none';

$language_options = array(
    'en' => 'English',
    'af' => 'Afrikaans',
    'ar' => 'Arabic',
    'bg' => 'Bulgarian',
    'zh' => 'Chinese',
    'hr' => 'Croatian',
    'cs' => 'Czech',
    'da' => 'Danish',
    'nl' => 'Dutch',
    'et' => 'Estonian',
    'fil' => 'Filipino',
    'fi' => 'Finnish',
    'fr' => 'French',
    'de' => 'German',
    'el' => 'Greek',
    'he' => 'Hebrew',
    'hi' => 'Hindi',
    'hu' => 'Hungarian',
    'id' => 'Indonesian',
    'it' => 'Italian',
    'ja' => 'Japanese',
    'ko' => 'Korean',
    'lv' => 'Latvian',
    'lt' => 'Lithuanian',
    'ms' => 'Malay',
    'no' => 'Norwegian',
    'fa' => 'Persian',
    'pl' => 'Polish',
    'pt' => 'Portuguese',
    'ro' => 'Romanian',
    'ru' => 'Russian',
    'sr' => 'Serbian',
    'sk' => 'Slovak',
    'sl' => 'Slovenian',
    'sv' => 'Swedish',
    'es' => 'Spanish',
    'th' => 'Thai',
    'tr' => 'Turkish',
    'uk' => 'Ukrainian',
    'vi' => 'Vietnamese',
);

$selected_language = isset($wpaicg_settings['language']) ? $wpaicg_settings['language'] : 'en';

?>

<div class="demo-page">
  <div class="demo-page-navigation">
    <nav>
      <ul>
        <li>
          <a href="javascript:void(0);" data-tab="aisettings">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tool">
              <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
            </svg>
            Settings</a>
        </li>
        <li>
        <a href="javascript:void(0);" data-tab="knowledge">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-align-justify">
              <line x1="21" y1="10" x2="3" y2="10" />
              <line x1="21" y1="6" x2="3" y2="6" />
              <line x1="21" y1="14" x2="3" y2="14" />
              <line x1="21" y1="18" x2="3" y2="18" />
            </svg>
            Knowledge</a>
        </li>
        <li>
          <a href="javascript:void(0);" data-tab="customtext">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layers">
              <polygon points="12 2 2 7 12 12 22 7 12 2" />
              <polyline points="2 17 12 22 22 17" />
              <polyline points="2 12 12 17 22 12" />
            </svg>
            Interface</a>
        </li>
        <li>
        <a href="javascript:void(0);" data-tab="style">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather">
              <path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z" />
              <line x1="16" y1="8" x2="2" y2="22" />
              <line x1="17.5" y1="15" x2="9" y2="15" />
            </svg>
            Style</a>
        </li>
        <li>
        <a href="javascript:void(0);" data-tab="speech">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sliders">
              <line x1="4" y1="21" x2="4" y2="14" />
              <line x1="4" y1="10" x2="4" y2="3" />
              <line x1="12" y1="21" x2="12" y2="12" />
              <line x1="12" y1="8" x2="12" y2="3" />
              <line x1="20" y1="21" x2="20" y2="16" />
              <line x1="20" y1="12" x2="20" y2="3" />
              <line x1="1" y1="14" x2="7" y2="14" />
              <line x1="9" y1="8" x2="15" y2="8" />
              <line x1="17" y1="16" x2="23" y2="16" />
        </svg>
        Speech</a>
        </li>
        <li>
        <a href="javascript:void(0);" data-tab="quota">
            <svg xmlns="http://www.w3.org/2000/svg" style="transform: rotate(90deg)" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-columns">
              <path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7m0-18H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7m0-18v18" />
            </svg>
            Quotas</a>
        </li>

        <li>
        <a href="javascript:void(0);" data-tab="security">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-square">
              <polyline points="9 11 12 14 22 4" />
              <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
            </svg>
            Security</a>
        </li>
      </ul>
    </nav>
  </div>
  <div class="demo-page-main-content">
    <main class="demo-page-content">
        <form action="" method="post" id="form-chatbox-setting">
            <?php wp_nonce_field('wpaicg_chat_shortcode_save'); ?>
            <!-- AI SETTINGS -->
            <section id="aisettings" class="tab-content">
                <h1>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tool">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
                    </svg>
                    Settings
                </h1>
                <p>
                    <div class="toggle-shortcode">[wpaicg_chatgpt]</div>
                </p>
                <!-- Model -->
                <?php $wpaicg_provider = get_option('wpaicg_provider', 'OpenAI'); ?>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Model', 'gpt3-ai-content-generator'); ?></label>
                    <?php
                        if ($wpaicg_provider === 'OpenAI') {

                            $gpt4_models = [
                                'gpt-4' => 'GPT-4',
                                'gpt-4-32k' => 'GPT-4 32K',
                                'gpt-4-1106-preview' => 'GPT-4 Turbo',
                                'gpt-4-vision-preview' => 'GPT-4 Vision'
                            ];
                            $gpt35_models = [
                                'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
                                'gpt-3.5-turbo-16k' => 'GPT-3.5 Turbo 16K',
                                'gpt-3.5-turbo-instruct' => 'GPT-3.5 Turbo Instruct'
                            ];

                            $custom_models = get_option('wpaicg_custom_models', []);
                            ?>
                            <select id="wpaicg_chat_model" name="wpaicg_chat_shortcode_options[model]">
                                <?php
                                // Function to display options
                                function display_options($models, $wpaicg_settings){
                                    foreach ($models as $model_key => $model_name): ?>
                                        <option value="<?php echo esc_attr($model_key); ?>"<?php selected($model_key, $wpaicg_settings['model']); ?>><?php echo esc_html($model_name); ?></option>
                                    <?php endforeach;
                                }
                                function display_custom_model_options($custom_models, $wpaicg_settings){
                                    foreach ($custom_models as $model_identifier) {
                                        ?>
                                        <option value="<?php echo esc_attr($model_identifier); ?>"<?php selected($model_identifier, $wpaicg_settings['model']); ?>><?php echo esc_html($model_identifier); ?></option>
                                        <?php
                                    }
                                }
                                ?>
                                <optgroup label="GPT-4">
                                    <?php display_options($gpt4_models, $wpaicg_settings); ?>
                                </optgroup>
                                <optgroup label="GPT-3.5">
                                    <?php display_options($gpt35_models, $wpaicg_settings); ?>
                                </optgroup>
                                <optgroup label="Custom Models">
                                    <?php display_custom_model_options($custom_models, $wpaicg_settings); ?>
                                </optgroup>
                            </select>
                            
                            <?php
                            } elseif ($wpaicg_provider === 'Google') {
                                $google_models = [
                                    'gemini-pro' => 'Gemini Pro'
                                ];
                                ?>
                                <select id="wpaicg_shortcode_google_model" name="wpaicg_shortcode_google_model">
                                    <?php
                                    $wpaicg_shortcode_google_model = get_option('wpaicg_shortcode_google_model', 'gemini-pro');
                                    ?>
                                    <?php
                                    foreach ($google_models as $model_key => $model_name): ?>
                                        <option value="<?php echo esc_attr($model_key); ?>"<?php selected($model_key, $wpaicg_shortcode_google_model); ?>><?php echo esc_html($model_name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                        <?php
                        } else {
                            // If not OpenAI, display the text field
                            $deployment_name = get_option('wpaicg_azure_deployment', '');
                            ?>
                            <input type="text" name="wpaicg_azure_deployment_input" placeholder="<?php echo esc_html__('Enter Azure Deployment', 'gpt3-ai-content-generator'); ?>" value="<?php echo esc_attr($deployment_name); ?>">
                    <?php } ?>
                </div>
                <!-- Streaming and Image Upload Options -->
                <fieldset class="nice-form-group">
                <legend><?php echo esc_html__('Options', 'gpt3-ai-content-generator'); ?></legend>
                    <div class="nice-form-group">
                        <input <?php echo $wpaicg_stream_nav_setting === '1' ? ' checked' : ''; ?> value="1" type="checkbox" name="wpaicg_stream_nav_option" id="wpaicg_stream_nav_option">
                        <label for="wpaicg_stream_nav_option"><?php echo esc_html__('Streaming','gpt3-ai-content-generator')?></label>
                    </div>
                    <div class="nice-form-group">
                        <input <?php echo isset($wpaicg_settings['image_enable']) && $wpaicg_settings['image_enable'] ? ' checked': ''?> value="1" type="checkbox" class="wpaicg_image_enable" name="wpaicg_chat_shortcode_options[image_enable]" id="wpaicg_image_enable">
                        <label for="wpaicg_image_enable"><?php echo esc_html__('Image Upload','gpt3-ai-content-generator')?></label>
                    </div>
                    <?php 
                    $wpaicg_chat_addition = false;
                    if(!isset($wpaicg_settings['chat_addition_option']) || $wpaicg_settings['chat_addition']){
                        $wpaicg_chat_addition = true;
                    }
                    ?>  
                    <div class="nice-form-group">
                        <input <?php echo $wpaicg_chat_addition ? ' checked': ''?> name="wpaicg_chat_shortcode_options[chat_addition]" value="1" type="checkbox" id="wpaicg_chat_addition">
                        <label for="wpaicg_chat_addition"><?php echo esc_html__('Instructions','gpt3-ai-content-generator')?></label>
                        <input name="wpaicg_chat_shortcode_options[chat_addition_option]" value="<?php echo $wpaicg_chat_addition ? 0 : 1?>" type="hidden" id="wpaicg_chat_addition_option">
                    </div>
                </fieldset>
                <!-- Instructions -->   
                <?php 
                $wpaicg_additions_json = file_get_contents(WPAICG_PLUGIN_DIR.'admin/chat/context.json');
                $wpaicg_additions = json_decode($wpaicg_additions_json, true);
                $wpaicg_settings['chat_addition_text'] = str_replace("\\",'',$wpaicg_settings['chat_addition_text']);
                ?>    
                <div class="nice-form-group">
                <label><?php echo esc_html__('Instructions','gpt3-ai-content-generator')?></label>
                    <select <?php echo !$wpaicg_chat_addition ? ' disabled':'';?> class="wpaicg_chat_addition_template">
                        <option value=""><?php echo esc_html__('Select Template','gpt3-ai-content-generator')?></option>
                        <?php
                        foreach($wpaicg_additions as $key=>$wpaicg_addition){
                            echo '<option value="'.esc_html($wpaicg_addition).'">'.esc_html($key).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="nice-form-group">
                    <textarea <?php echo !$wpaicg_chat_addition ? ' disabled':''?> name="wpaicg_chat_shortcode_options[chat_addition_text]" id="wpaicg_chat_addition_text" class="regular-text wpaicg_chat_addition_text" rows="8"><?php echo !empty($wpaicg_settings['chat_addition_text']) ? esc_html($wpaicg_settings['chat_addition_text']) : esc_html__('You are a helpful AI Assistant. Please be friendly.','gpt3-ai-content-generator')?></textarea>
                </div>
                <p></p>
                <!-- Advanced Parameters -->   
                <a href="#" id="wpaicg-advanced-settings-link"><?php echo esc_html__('Show Advanced Parameters','gpt3-ai-content-generator'); ?></a>
                <div id="wpaicg-advanced-settings" style="display: none;">
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Temperature','gpt3-ai-content-generator')?></label>
                        <input type="text" id="label_temperature" name="wpaicg_chat_shortcode_options[temperature]" value="<?php echo esc_html( $wpaicg_settings['temperature'] ) ;?>">
                    </div>
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Max Tokens','gpt3-ai-content-generator')?></label>
                        <input type="text" id="label_max_tokens" name="wpaicg_chat_shortcode_options[max_tokens]" value="<?php echo esc_html( $wpaicg_settings['max_tokens'] ) ;?>" >
                    </div>
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Top P','gpt3-ai-content-generator')?></label>
                        <input type="text" id="label_top_p" name="wpaicg_chat_shortcode_options[top_p]" value="<?php echo esc_html( $wpaicg_settings['top_p'] ) ; ?>" >
                    </div>

                    <div class="nice-form-group">
                        <input type="hidden" id="label_best_of" name="wpaicg_chat_shortcode_options[best_of]" value="<?php echo esc_html( $wpaicg_settings['best_of'] ) ; ?>" >
                    </div>

                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Frequency Penalty','gpt3-ai-content-generator')?></label>
                        <input type="text" id="label_frequency_penalty" name="wpaicg_chat_shortcode_options[frequency_penalty]" value="<?php echo esc_html( $wpaicg_settings['frequency_penalty'] ) ; ?>" >
                    </div>

                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Presence Penalty','gpt3-ai-content-generator')?></label>
                        <input type="text" id="label_presence_penalty" name="wpaicg_chat_shortcode_options[presence_penalty]" value="<?php echo esc_html( $wpaicg_settings['presence_penalty'] ) ;?>" >
                    </div>

                    <div class="nice-form-group">
                        <a class="wpaicg_sync_finetune" href="javascript:void(0)"><?php echo esc_html__('Sync Models', 'gpt3-ai-content-generator'); ?></a>
                    </div>
                </div>
                <details>
                    <summary>
                        <button class="button button-primary wpaicg-w-25"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
                        <!-- Export Button -->
                        <button type="button" class="button button-primary wpaicg-w-25" id="exportButton"><?php echo esc_html__('Export', 'gpt3-ai-content-generator'); ?></button>
                        <!-- Import Button -->
                        <button type="button" class="button button-primary wpaicg-w-25" id="importButton"><?php echo esc_html__('Import', 'gpt3-ai-content-generator'); ?></button>
                        <!-- Hidden File Input for Import -->
                        <input type="file" id="importFileInput" style="display: none;" accept=".json">
                        <!-- Reset Button -->
                        <button type="button" class="button button-link-delete" id="resetButton"><?php echo esc_html__('Reset', 'gpt3-ai-content-generator'); ?></button>
                    </summary>
                </details>
            </section>
            <!-- INTERFACE -->
            <section id="customtext" class="tab-content" style="display: none;">
                <h1>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layers">
                    <polygon points="12 2 2 7 12 12 22 7 12 2" />
                    <polyline points="2 17 12 22 22 17" />
                    <polyline points="2 12 12 17 22 12" />
                    </svg>
                    Interface
                </h1>
                <div class="nice-form-group">
                    <input <?php echo $wpaicg_settings['use_avatar'] ? ' checked': ''?> class="wpaicg_chat_shortcode_use_avatar" id="wpaicg_chat_shortcode_use_avatar" value="1" type="checkbox" name="wpaicg_chat_shortcode_options[use_avatar]">
                    <label for="wpaicg_chat_shortcode_use_avatar"><?php echo esc_html__('Use Avatar (40x40)','gpt3-ai-content-generator')?></label>
                </div>
                <div class="nice-form-group">
                    <input value="<?php echo esc_html($wpaicg_settings['ai_icon_url'])?>" type="hidden" name="wpaicg_chat_shortcode_options[ai_icon_url]" class="wpaicg_chat_icon_url">
                    <div style="display: inline-flex; align-items: center">
                        <input <?php echo $wpaicg_settings['ai_icon'] == 'default' ? ' checked': ''?> class="wpaicg_chatbox_icon_default" type="radio" value="default" name="wpaicg_chat_shortcode_options[ai_icon]">
                        <div>
                            <img style="display: block;width: 40px; height: 40px;" src="<?php echo esc_html(WPAICG_PLUGIN_URL).'admin/images/chatbot.png'?>">
                            <label><?php echo esc_html__('Default','gpt3-ai-content-generator')?></label>
                        </div>
                        <input <?php echo $wpaicg_settings['ai_icon'] == 'custom' ? ' checked': ''?> type="radio" class="wpaicg_chatbox_icon_custom" value="custom" name="wpaicg_chat_shortcode_options[ai_icon]">
                        <div>
                            <div class="wpaicg_chatbox_icon">
                                <?php
                                if(!empty($wpaicg_settings['ai_icon_url']) && $wpaicg_settings['ai_icon'] == 'custom'): $wpaicg_chatbox_icon_url = wp_get_attachment_url($wpaicg_settings['ai_icon_url']);?>
                                    <img src="<?php echo esc_html($wpaicg_chatbox_icon_url)?>" width="40" height="40">
                                <?php else: ?>
                                    <svg width="40px" height="40px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M246.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 109.3V320c0 17.7 14.3 32 32 32s32-14.3 32-32V109.3l73.4 73.4c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-128-128zM64 352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64c0 53 43 96 96 96H352c53 0 96-43 96-96V352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V352z"/></svg><br>
                                <?php
                                endif;
                                ?>
                            </div>
                            <label><?php echo esc_html__('Custom','gpt3-ai-content-generator')?></label>
                        </div>
                    </div>
                </div>
                <fieldset class="nice-form-group">
                <legend><?php echo esc_html__('Top Bar Options', 'gpt3-ai-content-generator'); ?></legend>
                    <div class="nice-form-group">
                        <input <?php echo $wpaicg_chat_fullscreen ? ' checked':''?> value="1" type="checkbox" class="wpaicgchat_fullscreen" id="wpaicgchat_fullscreen" name="wpaicg_chat_shortcode_options[fullscreen]">
                        <label for="wpaicgchat_fullscreen"><?php echo esc_html__('Fullscreen','gpt3-ai-content-generator')?></label>
                    </div>
                    <div class="nice-form-group">
                        <input <?php echo $wpaicg_chat_download_btn ? ' checked':''?> value="1" type="checkbox" class="wpaicgchat_download_btn" id="wpaicgchat_download_btn" name="wpaicg_chat_shortcode_options[download_btn]">
                        <label for="wpaicgchat_download_btn"><?php echo esc_html__('Download','gpt3-ai-content-generator')?></label>
                    </div>
                    <div class="nice-form-group">
                        <input <?php echo $wpaicg_chat_clear_btn ? ' checked':''?> value="1" type="checkbox" class="wpaicgchat_clear_btn" id="wpaicgchat_clear_btn" name="wpaicg_chat_shortcode_options[clear_btn]">
                        <label for="wpaicgchat_clear_btn"><?php echo esc_html__('Clear','gpt3-ai-content-generator')?></label>
                    </div>
                </fieldset>
                <div class="nice-form-group">
                    <label for="wpaicgchat_bar_color"><?php echo esc_html__('Icon Color','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_bar_color)?>" type="text" class="wpaicgchat_color wpaicgchat_bar_color" id="wpaicgchat_bar_color" name="wpaicg_chat_shortcode_options[bar_color]">
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('AI Name','gpt3-ai-content-generator')?></label>
                    <input type="text" class="wpaicg_chat_shortcode_ai_name" name="wpaicg_chat_shortcode_options[ai_name]" value="<?php echo esc_html( $wpaicg_settings['ai_name'] ) ;?>" >
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('User Name','gpt3-ai-content-generator')?></label>
                    <input type="text" class="wpaicg_chat_shortcode_you" name="wpaicg_chat_shortcode_options[you]" value="<?php echo esc_html( $wpaicg_settings['you'] ) ; ?>" >
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Response Wait Message','gpt3-ai-content-generator')?></label>
                    <input type="text" class="wpaicg_chat_shortcode_ai_thinking" name="wpaicg_chat_shortcode_options[ai_thinking]" value="<?php echo esc_html( $wpaicg_settings['ai_thinking'] ) ;?>" >
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Placeholder','gpt3-ai-content-generator')?></label>
                    <input type="text" class="wpaicg_chat_shortcode_placeholder" name="wpaicg_chat_shortcode_options[placeholder]" value="<?php echo esc_html( $wpaicg_settings['placeholder'] ) ;?>" >
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Welcome Message','gpt3-ai-content-generator')?></label>
                    <input type="text" class="wpaicg_chat_shortcode_welcome" name="wpaicg_chat_shortcode_options[welcome]" value="<?php echo esc_html( $wpaicg_settings['welcome'] ) ;?>" >
                </div>
                <div class="nice-form-group">
                    <input type="hidden" value="<?php echo esc_html($wpaicg_settings['no_answer'])?>" name="wpaicg_chat_shortcode_options[no_answer]">
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Footer Note','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_settings['footer_text'])?>" type="text" name="wpaicg_chat_shortcode_options[footer_text]" placeholder="<?php echo esc_html__('Powered by ...','gpt3-ai-content-generator')?>">
                </div>
                <details>
                    <summary>
                        <button class="button button-primary wpaicg-w-25"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
                        <!-- Export Button -->
                        <button type="button" class="button button-primary wpaicg-w-25" id="exportButton"><?php echo esc_html__('Export', 'gpt3-ai-content-generator'); ?></button>
                        <!-- Import Button -->
                        <button type="button" class="button button-primary wpaicg-w-25" id="importButton"><?php echo esc_html__('Import', 'gpt3-ai-content-generator'); ?></button>
                        <!-- Hidden File Input for Import -->
                        <input type="file" id="importFileInput" style="display: none;" accept=".json">
                        <!-- Reset Button -->
                        <button type="button" class="button button-link-delete" id="resetButton"><?php echo esc_html__('Reset', 'gpt3-ai-content-generator'); ?></button>
                    </summary>
                </details>
            </section>
            <!-- Knowledge -->
            <section id="knowledge" class="tab-content" style="display: none;">
                <h1>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-align-justify">
                    <line x1="21" y1="10" x2="3" y2="10" />
                    <line x1="21" y1="6" x2="3" y2="6" />
                    <line x1="21" y1="14" x2="3" y2="14" />
                    <line x1="21" y1="18" x2="3" y2="18" />
                    </svg>
                Knowledge
                </h1>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Conversational Memory','gpt3-ai-content-generator')?></label>
                    <select name="wpaicg_chat_shortcode_options[remember_conversation]">
                        <option <?php echo $wpaicg_settings['remember_conversation'] == 'yes' ? ' selected': ''?> value="yes"><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                        <option <?php echo $wpaicg_settings['remember_conversation'] == 'no' ? ' selected': ''?> value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                    </select>
                </div>

                <div class="nice-form-group">
                    <label><?php echo esc_html__('Content Aware','gpt3-ai-content-generator')?></label>
                    <select name="wpaicg_chat_shortcode_options[content_aware]" id="wpaicg_chat_content_aware">
                        <option <?php echo $wpaicg_settings['content_aware'] == 'yes' ? ' selected': ''?> value="yes"><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                        <option <?php echo $wpaicg_settings['content_aware'] == 'no' ? ' selected': ''?> value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                    </select>
                </div>

                <fieldset class="nice-form-group">
                <legend><?php echo esc_html__('Data Source', 'gpt3-ai-content-generator'); ?></legend>
                    <div class="nice-form-group">
                        <input <?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? ' checked': ''?><?php echo $wpaicg_settings['content_aware'] == 'no' ? ' disabled':''?> type="checkbox" id="wpaicg_chat_excerpt" class="<?php echo $wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? 'asdisabled' : ''?>">
                        <label for="wpaicg_chat_excerpt"><?php echo esc_html__('Excerpt','gpt3-ai-content-generator')?></label>
                    </div>
                    <div class="nice-form-group">
                        <input <?php echo $wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? ' checked': ''?><?php echo $wpaicg_settings['content_aware'] == 'no' ? ' disabled':''?> type="checkbox" value="1" name="wpaicg_chat_shortcode_options[embedding]" id="wpaicg_chat_embedding" class="<?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? 'asdisabled' : ''?>">
                        <label for="wpaicg_chat_embedding"><?php echo esc_html__('Embeddings','gpt3-ai-content-generator')?></label>
                    </div>
                </fieldset>

                <div class="nice-form-group">
                    <label><?php echo esc_html__('Vector DB', 'gpt3-ai-content-generator'); ?></label>
                    <select name="wpaicg_chat_shortcode_options[vectordb]" id="wpaicg_db_provider" class="wpaicg-form-select-vectordb" <?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? ' disabled' : '';?>>
                        <option value="pinecone" <?php echo ($wpaicg_settings['vectordb'] === 'pinecone' || empty($wpaicg_settings['vectordb'])) ? 'selected' : ''; ?>><?php echo esc_html__('Pinecone', 'gpt3-ai-content-generator'); ?></option>
                        <option value="qdrant" <?php echo $wpaicg_settings['vectordb'] === 'qdrant' ? 'selected' : ''; ?>><?php echo esc_html__('Qdrant', 'gpt3-ai-content-generator'); ?></option>
                    </select>
                </div>

                <div class="nice-form-group">
                    <label><?php echo esc_html__('Pinecone Index','gpt3-ai-content-generator')?></label>
                    <select <?php echo empty($wpaicg_settings['embedding']) || $wpaicg_settings['content_aware'] == 'no' ? ' disabled':''?> name="wpaicg_chat_shortcode_options[embedding_index]" id="wpaicg_chat_embedding_index" class="<?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? 'asdisabled' : ''?>">
                        <option value=""><?php echo esc_html__('Default','gpt3-ai-content-generator')?></option>
                        <?php
                        foreach($wpaicg_pinecone_indexes as $wpaicg_pinecone_index){
                            echo '<option'.(isset($wpaicg_settings['embedding_index']) && $wpaicg_settings['embedding_index'] == $wpaicg_pinecone_index['url'] ? ' selected':'').' value="'.esc_html($wpaicg_pinecone_index['url']).'">'.esc_html($wpaicg_pinecone_index['name']).'</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="nice-form-group">
                    <label><?php echo esc_html__('Qdrant Collection', 'gpt3-ai-content-generator'); ?></label>
                    <select <?php echo empty($wpaicg_settings['embedding']) || $wpaicg_settings['content_aware'] == 'no' ? ' disabled' : '' ?> name="wpaicg_chat_shortcode_options[qdrant_collection]" id="wpaicg_chat_qdrant_collection" class="<?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? 'asdisabled' : '' ?>">
                        <?php
                        foreach ($wpaicg_qdrant_collections as $collection) {
                            $selected = (isset($wpaicg_settings['qdrant_collection']) && $wpaicg_settings['qdrant_collection'] == $collection) ? ' selected' : '';
                            echo '<option value="' . esc_attr($collection) . '"' . $selected . '>' . esc_html($collection) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="nice-form-group">
                    <label><?php echo esc_html__('Limit','gpt3-ai-content-generator')?></label>
                    <select <?php echo empty($wpaicg_settings['embedding']) || $wpaicg_settings['content_aware'] == 'no' ? ' disabled':''?> name="wpaicg_chat_shortcode_options[embedding_top]" id="wpaicg_chat_embedding_top" class="<?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? 'asdisabled' : ''?>">
                        <?php
                        for($i = 1; $i <=5;$i++){
                            echo '<option'.($wpaicg_settings['embedding_top'] == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()): ?>
                    <div class="nice-form-group">
                        <input <?php echo empty($wpaicg_settings['embedding']) || $wpaicg_settings['content_aware'] == 'no' ? ' disabled':''?><?php echo isset($wpaicg_settings['embedding_pdf']) && $wpaicg_settings['embedding_pdf'] ? ' checked':''?> type="checkbox" value="1" name="wpaicg_chat_shortcode_options[embedding_pdf]" class="<?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? 'asdisabled' : ''?>" id="wpaicg_chat_embedding_pdf">
                        <label for="wpaicg_chat_embedding_pdf"><?php echo esc_html__('Enable PDF Upload','gpt3-ai-content-generator')?></label>
                    </div>
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('PDF Page Limit','gpt3-ai-content-generator')?></label>
                        <select <?php echo empty($wpaicg_settings['embedding']) || $wpaicg_settings['content_aware'] == 'no' ? ' disabled':''?> name="wpaicg_chat_shortcode_options[pdf_pages]" id="wpaicg_chat_pdf_pages" class="<?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? 'asdisabled' : ''?>" style="width: 65px!important;">
                            <?php
                            $pdf_pages = isset($wpaicg_settings['pdf_pages']) && !empty($wpaicg_settings['pdf_pages']) ? $wpaicg_settings['pdf_pages'] : 120;
                            for($i=1;$i <= 120;$i++){
                                echo '<option'.($pdf_pages == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="nice-form-group">
                        <label><?php echo esc_html__('PDF Upload Confirmation Message','gpt3-ai-content-generator')?></label>
                        <textarea <?php echo empty($wpaicg_settings['embedding']) || $wpaicg_settings['content_aware'] == 'no' ? ' disabled':''?> rows="8" name="wpaicg_chat_shortcode_options[embedding_pdf_message]" class="<?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? 'asdisabled' : ''?>" id="wpaicg_chat_embedding_pdf_message"><?php echo isset($wpaicg_settings['embedding_pdf_message']) && $wpaicg_settings['embedding_pdf_message'] ? esc_html(str_replace("\\",'',$wpaicg_settings['embedding_pdf_message'])):''?></textarea>
                    </div>
                    <div class="nice-form-group">
                        <label for="wpaicg_pdf_color"><?php echo esc_html__('PDF Icon Color','gpt3-ai-content-generator')?></label>
                        <input value="<?php echo esc_html($wpaicg_settings['pdf_color'])?>" type="text" class="wpaicgchat_color wpaicg_pdf_color" id="wpaicg_pdf_color" name="wpaicg_chat_shortcode_options[pdf_color]">
                    </div>
                <?php else: ?>
                    <fieldset class="nice-form-group">
                    <legend><?php echo esc_html__('Enable PDF Upload', 'gpt3-ai-content-generator'); ?></legend>
                        <div class="nice-form-group">
                            <input type="checkbox" disabled id="embedding_pdf_disabled">
                            <label for="embedding_pdf_disabled"><?php echo esc_html__('Available in Pro','gpt3-ai-content-generator')?></label>
                        </div>
                    </fieldset>
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('PDF Page Limit','gpt3-ai-content-generator')?></label>
                        <select disabled>
                            <option><?php echo esc_html__('Available in Pro','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('PDF Upload Confirmation Message','gpt3-ai-content-generator')?></label>
                        <textarea disabled rows="8" placeholder="<?php echo esc_html__('Available in Pro','gpt3-ai-content-generator')?>"></textarea>
                    </div>
                    <div class="nice-form-group">
                        <label for="wpaicg_pdf_color"><?php echo esc_html__('PDF Icon Color','gpt3-ai-content-generator')?></label>
                        <input disabled type="text" placeholder="<?php echo esc_html__('Available in Pro','gpt3-ai-content-generator')?>">
                    </div>
                <?php endif; ?>

                <!-- Advanced Parameters --> 
                <p></p>
                <a href="#" id="wpaicg-advanced-content-settings-link"><?php echo esc_html__('Show Additional Options','gpt3-ai-content-generator'); ?></a>
                <div id="wpaicg-advanced-content-settings" style="display: none;">
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('User Aware','gpt3-ai-content-generator')?></label>
                        <select name="wpaicg_chat_shortcode_options[user_aware]">
                            <option <?php echo $wpaicg_settings['user_aware'] == 'no' ? ' selected': ''?> value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                            <option <?php echo $wpaicg_settings['user_aware'] == 'yes' ? ' selected': ''?> value="yes"><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Embedding Type','gpt3-ai-content-generator')?></label>
                        <select <?php echo empty($wpaicg_settings['embedding']) || $wpaicg_settings['content_aware'] == 'no' ? ' disabled':''?> name="wpaicg_chat_shortcode_options[embedding_type]" id="wpaicg_chat_embedding_type" class="<?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? 'asdisabled' : ''?>">
                            <option <?php echo $wpaicg_settings['embedding_type'] ? ' selected':'';?> value="openai"><?php echo esc_html__('Conversational','gpt3-ai-content-generator')?></option>
                            <option <?php echo empty($wpaicg_settings['embedding_type']) ? ' selected':''?> value=""><?php echo esc_html__('Non-Conversational','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Memory Limit','gpt3-ai-content-generator')?></label>
                        <select name="wpaicg_chat_shortcode_options[conversation_cut]">
                            <?php
                            for($i=3;$i<=50;$i++){
                                echo '<option'.($wpaicg_settings['conversation_cut'] == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label for="label_wpai_language"><?php echo esc_html__('Language','gpt3-ai-content-generator')?></label>
                        <select id="label_wpai_language" name="wpaicg_chat_shortcode_options[language]" >
                            <?php
                            foreach ($language_options as $value => $label) {
                                $selected = $wpaicg_settings['language'] == $value ? ' selected' : '';
                                echo "<option value=\"{$value}\"{$selected}>". esc_html($label) ."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label for="wpaicg_chat_tone_id"><?php echo esc_html__('Tone','gpt3-ai-content-generator')?></label>
                        <select name="wpaicg_chat_shortcode_options[tone]" id="wpaicg_chat_tone_id">
                            <?php
                            foreach ($tone_options as $value => $label) {
                                $selected = $wpaicg_settings['tone'] == $value ? ' selected' : '';
                                echo "<option value=\"{$value}\"{$selected}>". esc_html($label) ."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label for="wpaicg_chat_act_id"><?php echo esc_html__('Act As','gpt3-ai-content-generator')?></label>
                        <select name="wpaicg_chat_shortcode_options[profession]" id="wpaicg_chat_act_id" >
                            <?php
                            foreach ($profession_options as $value => $label) {
                                $selected = $wpaicg_settings['profession'] == $value ? ' selected' : '';
                                echo "<option value=\"{$value}\"{$selected}>". esc_html($label) ."</option>";
                            }
                            ?>
                        </select>
                    </div>

                    
                </div>
                <details>
                    <summary>
                        <button class="button button-primary wpaicg-w-25"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
                        <!-- Export Button -->
                        <button type="button" class="button button-primary wpaicg-w-25" id="exportButton"><?php echo esc_html__('Export', 'gpt3-ai-content-generator'); ?></button>
                        <!-- Import Button -->
                        <button type="button" class="button button-primary wpaicg-w-25" id="importButton"><?php echo esc_html__('Import', 'gpt3-ai-content-generator'); ?></button>
                        <!-- Hidden File Input for Import -->
                        <input type="file" id="importFileInput" style="display: none;" accept=".json">
                        <!-- Reset Button -->
                        <button type="button" class="button button-link-delete" id="resetButton"><?php echo esc_html__('Reset', 'gpt3-ai-content-generator'); ?></button>
                    </summary>
                </details>
            </section>
            <!-- SPEECH -->
            <section id="speech" class="tab-content" style="display: none;">
                <h1>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sliders">
                    <line x1="4" y1="21" x2="4" y2="14" />
                    <line x1="4" y1="10" x2="4" y2="3" />
                    <line x1="12" y1="21" x2="12" y2="12" />
                    <line x1="12" y1="8" x2="12" y2="3" />
                    <line x1="20" y1="21" x2="20" y2="16" />
                    <line x1="20" y1="12" x2="20" y2="3" />
                    <line x1="1" y1="14" x2="7" y2="14" />
                    <line x1="9" y1="8" x2="15" y2="8" />
                    <line x1="17" y1="16" x2="23" y2="16" />
                </svg>
                Speech
                </h1>
                <?php
                    $wpaicg_provider = get_option('wpaicg_provider', 'OpenAI');  // Fetching the provider
                    // If the provider isn't Azure or Google, display the fields
                    if ($wpaicg_provider !== 'Azure' && $wpaicg_provider !== 'Google') {
                    ?>
                <div class="nice-form-group">
                    <input <?php echo isset($wpaicg_settings['audio_enable']) && $wpaicg_settings['audio_enable'] ? ' checked': ''?> value="1" type="checkbox" class="wpaicg_audio_enable" name="wpaicg_chat_shortcode_options[audio_enable]" id="wpaicg_audio_enable">
                    <label><?php echo esc_html__('Speech to Text','gpt3-ai-content-generator')?></label>
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_mic_color"><?php echo esc_html__('Mic Color','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_settings['mic_color'])?>" type="text" class="wpaicgchat_color wpaicg_mic_color" name="wpaicg_chat_shortcode_options[mic_color]" id="wpaicg_mic_color">
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_stop_color"><?php echo esc_html__('Stop Button Color','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_settings['stop_color'])?>" type="text" class="wpaicgchat_color wpaicg_stop_color" name="wpaicg_chat_shortcode_options[stop_color]" id="wpaicg_stop_color">
                </div>
                <?php 
                    } else {  // If the provider is Azure, display the notice
                    ?>
                <div class="nice-form-group">
                    <input type="checkbox" disabled>
                    <label><?php echo esc_html__('Speech to Text','gpt3-ai-content-generator')?></label>
                </div>
                <p>
                    <small><?php echo esc_html__('Speech to Text is not available in Azure or Google. If you want to use it, change your provider to OpenAI under Settings - AI Engine.', 'gpt3-ai-content-generator'); ?></small>
                </p>
                <?php 
                    }
                    ?>
                
                <div class="nice-form-group">
                    <input <?php echo $wpaicg_chat_to_speech ? 'checked' : ''; ?> value="1" type="checkbox" name="wpaicg_chat_shortcode_options[chat_to_speech]" class="wpaicg_chat_to_speech" id="wpaicg_chat_to_speech">
                    <label for="wpaicg_chat_to_speech"><?php echo esc_html__('Text to Speech','gpt3-ai-content-generator')?></label>
                </div>
                <?php
                    $wpaicg_provider = get_option('wpaicg_provider', 'OpenAI'); // Fetching the provider
                    $disabled_voice_fields = !$wpaicg_chat_to_speech;
                    $google_disabled = !isset($wpaicg_google_api_key) || empty($wpaicg_google_api_key) ? 'disabled' : '';
                    $elevenlabs_disabled = !isset($wpaicg_elevenlabs_api) || empty($wpaicg_elevenlabs_api) ? 'disabled' : '';
                    //azure or google
                    $openai_disabled = $wpaicg_provider === 'Azure' || $wpaicg_provider === 'Google' ? 'disabled' : '';
                    ?>
                <div class="nice-form-group">
                    <label for="wpaicg_voice_service"><?php echo esc_html__('Provider','gpt3-ai-content-generator')?></label>
                    <select name="wpaicg_chat_shortcode_options[voice_service]" id="wpaicg_voice_service" class="wpaicg_voice_service" <?php echo $disabled_voice_fields ? 'disabled' : ''; ?>>
                        <option <?php echo $wpaicg_chat_voice_service == 'openai' ? 'selected' : ''; ?> value="openai" <?php echo $openai_disabled; ?>><?php echo esc_html__('OpenAI','gpt3-ai-content-generator')?></option>
                        <option <?php echo $wpaicg_chat_voice_service == 'google' ? ' selected':'';?> value="google" <?php echo $google_disabled; ?>><?php echo esc_html__('Google','gpt3-ai-content-generator')?></option>
                        <option <?php echo $wpaicg_chat_voice_service == 'elevenlabs' ? ' selected':'';?> value="elevenlabs" <?php echo $elevenlabs_disabled; ?>><?php echo esc_html__('ElevenLabs','gpt3-ai-content-generator')?></option> 
                    </select>
                </div>
                <!-- OpenAI -->
                <?php
                    $wpaicg_openai_model = isset($wpaicg_settings['openai_model']) && !empty($wpaicg_settings['openai_model']) ? $wpaicg_settings['openai_model'] : 'tts-1';
                    $wpaicg_openai_voice = isset($wpaicg_settings['openai_voice']) && !empty($wpaicg_settings['openai_voice']) ? $wpaicg_settings['openai_voice'] : 'alloy';
                    $wpaicg_openai_output_format = isset($wpaicg_settings['openai_output_format']) && !empty($wpaicg_settings['openai_output_format']) ? $wpaicg_settings['openai_output_format'] : 'mp3';
                    $wpaicg_openai_voice_speed = isset($wpaicg_settings['openai_voice_speed']) && !empty($wpaicg_settings['openai_voice_speed']) ? $wpaicg_settings['openai_voice_speed'] : '1.0';
                    ?>
                <div class="wpaicg_voice_service_openai" style="<?php echo $wpaicg_chat_voice_service == 'openai' ? '' : 'display:none'?>">
                    <div class="nice-form-group">
                        <label for="wpaicg_openai_model"><?php echo esc_html__('Model','gpt3-ai-content-generator')?></label>
                        <select name="wpaicg_chat_shortcode_options[openai_model]" id="wpaicg_openai_model" class="wpaicg_openai_model" <?php echo $disabled_voice_fields ? 'disabled' : ''; ?>>
                            <option value="tts-1" <?php echo $wpaicg_openai_model == 'tts-1' ? 'selected' : ''; ?>><?php echo esc_html__('tts-1','gpt3-ai-content-generator')?></option>
                            <option value="tts-1-hd" <?php echo $wpaicg_openai_model == 'tts-1-hd' ? 'selected' : ''; ?>><?php echo esc_html__('tts-1-hd','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label for="wpaicg_openai_voice"><?php echo esc_html__('Voice','gpt3-ai-content-generator')?></label>
                        <select name="wpaicg_chat_shortcode_options[openai_voice]" id="wpaicg_openai_voice" class="wpaicg_openai_voice" <?php echo $disabled_voice_fields ? 'disabled' : ''; ?>>
                            <option value="alloy" <?php echo $wpaicg_openai_voice == 'alloy' ? 'selected' : ''; ?>><?php echo esc_html__('alloy','gpt3-ai-content-generator')?></option>
                            <option value="echo" <?php echo $wpaicg_openai_voice == 'echo' ? 'selected' : ''; ?>><?php echo esc_html__('echo','gpt3-ai-content-generator')?></option>
                            <option value="fable" <?php echo $wpaicg_openai_voice == 'fable' ? 'selected' : ''; ?>><?php echo esc_html__('fable','gpt3-ai-content-generator')?></option>
                            <option value="nova" <?php echo $wpaicg_openai_voice == 'nova' ? 'selected' : ''; ?>><?php echo esc_html__('nova','gpt3-ai-content-generator')?></option>
                            <option value="onyx" <?php echo $wpaicg_openai_voice == 'onyx' ? 'selected' : ''; ?>><?php echo esc_html__('onyx','gpt3-ai-content-generator')?></option>
                            <option value="shimmer" <?php echo $wpaicg_openai_voice == 'shimmer' ? 'selected' : ''; ?>><?php echo esc_html__('shimmer','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label for="wpaicg_openai_output_format"><?php echo esc_html__('Format', 'gpt3-ai-content-generator'); ?></label>
                        <select name="wpaicg_chat_shortcode_options[openai_output_format]" id="wpaicg_openai_output_format" class="wpaicg_openai_output_format" <?php echo $disabled_voice_fields ? 'disabled' : ''; ?>>
                            <option value="mp3" <?php echo $wpaicg_openai_output_format == 'mp3' ? 'selected' : ''; ?>><?php echo esc_html__('mp3', 'gpt3-ai-content-generator'); ?></option>
                            <option value="opus" <?php echo $wpaicg_openai_output_format == 'opus' ? 'selected' : ''; ?>><?php echo esc_html__('opus', 'gpt3-ai-content-generator'); ?></option>
                            <option value="aac" <?php echo $wpaicg_openai_output_format == 'aac' ? 'selected' : ''; ?>><?php echo esc_html__('aac', 'gpt3-ai-content-generator'); ?></option>
                            <option value="flac" <?php echo $wpaicg_openai_output_format == 'flac' ? 'selected' : ''; ?>><?php echo esc_html__('flac', 'gpt3-ai-content-generator'); ?></option>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label for="wpaicg_openai_voice_speed"><?php echo esc_html__('Speed (0.25 to 4.0)', 'gpt3-ai-content-generator'); ?></label>
                        <input type="number" name="wpaicg_chat_shortcode_options[openai_voice_speed]" id="wpaicg_openai_voice_speed" class="wpaicg_openai_voice_speed" min="0.25" max="4.0" step="0.01" value="<?php echo esc_attr($wpaicg_openai_voice_speed); ?>" <?php echo $disabled_voice_fields ? 'disabled' : ''; ?>>
                    </div>
                </div>

                <!-- Google -->
                <?php
                    $wpaicg_voice_language = isset($wpaicg_settings['voice_language']) && !empty($wpaicg_settings['voice_language']) ? $wpaicg_settings['voice_language'] : 'en-US';
                    $wpaicg_voice_name = isset($wpaicg_settings['voice_name']) && !empty($wpaicg_settings['voice_name']) ? $wpaicg_settings['voice_name'] : 'en-US-Studio-M';
                    $wpaicg_voice_device = isset($wpaicg_settings['voice_device']) && !empty($wpaicg_settings['voice_device']) ? $wpaicg_settings['voice_device'] : '';
                    $wpaicg_voice_speed = isset($wpaicg_settings['voice_speed']) && !empty($wpaicg_settings['voice_speed']) ? $wpaicg_settings['voice_speed'] : 1;
                    $wpaicg_voice_pitch = isset($wpaicg_settings['voice_pitch']) && !empty($wpaicg_settings['voice_pitch']) ? $wpaicg_settings['voice_pitch'] : 0;
                    ?>
                <div class="wpaicg_voice_service_google" style="<?php echo $wpaicg_chat_voice_service == 'google' && (!empty($wpaicg_google_api_key) || !empty($wpaicg_elevenlabs_api)) ? '' : 'display:none'?>">
                    <div class="nice-form-group">
                        <label for="wpaicg_voice_language"><?php echo esc_html__('Language','gpt3-ai-content-generator')?></label>
                        <select <?php echo empty($wpaicg_google_api_key) || $disabled_voice_fields ? ' disabled':''?> name="wpaicg_chat_shortcode_options[voice_language]" class="wpaicg_voice_language" id="wpaicg_voice_language">
                            <?php
                            foreach(\WPAICG\WPAICG_Google_Speech::get_instance()->languages as $key=>$voice_language){
                                echo '<option'.($wpaicg_voice_language == $key ? ' selected':'').' value="'.esc_html($key).'">'.esc_html($voice_language).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label for="wpaicg_voice_name"><?php echo esc_html__('Voice','gpt3-ai-content-generator')?></label>
                        <select <?php echo empty($wpaicg_google_api_key) || $disabled_voice_fields ? ' disabled':''?> data-value="<?php echo esc_html($wpaicg_voice_name)?>" name="wpaicg_chat_shortcode_options[voice_name]" class="wpaicg_voice_name" id="wpaicg_voice_name"></select>
                    </div>
                    <div class="nice-form-group">
                        <label for="wpaicg_voice_device"><?php echo esc_html__('Audio Device Profile','gpt3-ai-content-generator')?></label>
                        <select <?php echo empty($wpaicg_google_api_key) || $disabled_voice_fields ? ' disabled':''?> name="wpaicg_chat_shortcode_options[voice_device]" class="wpaicg_voice_device" id="wpaicg_voice_device">
                            <?php
                            foreach(\WPAICG\WPAICG_Google_Speech::get_instance()->devices() as $key => $device){
                                echo '<option'.($wpaicg_voice_device == $key ? ' selected':'').' value="'.esc_html($key).'">'.esc_html($device).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label for="wpaicg_voice_speed"><?php echo esc_html__('Speed','gpt3-ai-content-generator')?></label>
                        <input <?php echo empty($wpaicg_google_api_key) || $disabled_voice_fields ? ' disabled':''?> type="text" class="wpaicg_voice_speed" value="<?php echo esc_html($wpaicg_voice_speed)?>" name="wpaicg_chat_shortcode_options[voice_speed]" id="wpaicg_voice_speed">
                    </div>
                    <div class="nice-form-group">
                        <label for="wpaicg_voice_pitch"><?php echo esc_html__('Pitch','gpt3-ai-content-generator')?></label>
                        <input <?php echo empty($wpaicg_google_api_key) ||$disabled_voice_fields ? ' disabled':''?> type="text" class="wpaicg_voice_pitch" value="<?php echo esc_html($wpaicg_voice_pitch)?>" name="wpaicg_chat_shortcode_options[voice_pitch]" id="wpaicg_voice_pitch">
                    </div>
                </div>

                <!-- ElevenLabs -->
                <div class="wpaicg_voice_service_elevenlabs" style="<?php echo $wpaicg_chat_voice_service == 'google' || (empty($wpaicg_google_api_key) && empty($wpaicg_elevenlabs_api)) ? 'display:none' : ''?>">
                    <div class="nice-form-group">
                        <label for="wpaicg_elevenlabs_voice"><?php echo esc_html__('Voice','gpt3-ai-content-generator')?></label>
                        <select <?php echo empty($wpaicg_elevenlabs_api) || $disabled_voice_fields ? ' disabled':''?> name="wpaicg_chat_shortcode_options[elevenlabs_voice]" class="wpaicg_elevenlabs_voice" id="wpaicg_elevenlabs_voice">
                            <?php
                            foreach(\WPAICG\WPAICG_ElevenLabs::get_instance()->voices as $key=>$voice){
                                echo '<option'.($wpaicg_elevenlabs_voice == $key ? ' selected':'').' value="'.esc_html($key).'">'.esc_html($voice).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label for="wpaicg_elevenlabs_model"><?php echo esc_html__('Model','gpt3-ai-content-generator')?></label>
                        <select <?php echo empty($wpaicg_elevenlabs_api) || $disabled_voice_fields ? ' disabled':''?> name="wpaicg_chat_shortcode_options[elevenlabs_model]" class="wpaicg_elevenlabs_model" id="wpaicg_elevenlabs_model">
                            <?php
                            foreach(\WPAICG\WPAICG_ElevenLabs::get_instance()->models as $key=>$model){
                                echo '<option'.($wpaicg_elevenlabs_model == $key ? ' selected':'').' value="'.esc_html($key).'">'.esc_html($model).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <details>
                    <summary>
                        <button class="button button-primary wpaicg-w-25"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
                        <!-- Export Button -->
                        <button type="button" class="button button-primary wpaicg-w-25" id="exportButton"><?php echo esc_html__('Export', 'gpt3-ai-content-generator'); ?></button>
                        <!-- Import Button -->
                        <button type="button" class="button button-primary wpaicg-w-25" id="importButton"><?php echo esc_html__('Import', 'gpt3-ai-content-generator'); ?></button>
                        <!-- Hidden File Input for Import -->
                        <input type="file" id="importFileInput" style="display: none;" accept=".json">
                        <!-- Reset Button -->
                        <button type="button" class="button button-link-delete" id="resetButton"><?php echo esc_html__('Reset', 'gpt3-ai-content-generator'); ?></button>
                    </summary>
                </details>
            </section>
            <!-- QUOTA -->
            <section id="quota" class="tab-content" style="display: none;">
                <h1>
                    <svg xmlns="http://www.w3.org/2000/svg" style="transform: rotate(90deg)" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-columns">
                    <path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7m0-18H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7m0-18v18" />
                    </svg>
                    Quotas
                </h1>
                <div class="nice-form-group">
                    <input <?php echo $wpaicg_user_limited ? ' checked': ''?> type="checkbox" value="1" class="wpaicg_user_token_limit" name="wpaicg_chat_shortcode_options[user_limited]" id="wpaicg_user_token_limit">
                    <label for="wpaicg_user_token_limit"><?php echo esc_html__('Limit Registered Users','gpt3-ai-content-generator')?></label>
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_user_token_limit_text"><?php echo esc_html__('Token Allocation','gpt3-ai-content-generator')?></label>
                    <input <?php echo $wpaicg_user_limited ? '' : ' disabled'?> class="wpaicg_user_token_limit_text" type="text" value="<?php echo esc_html($wpaicg_user_tokens)?>" name="wpaicg_chat_shortcode_options[user_tokens]" id="wpaicg_user_token_limit_text">
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_role_limited"><?php echo esc_html__('Token Allocation by Role','gpt3-ai-content-generator')?></label>
                    <?php
                    foreach($wpaicg_roles as $key=>$wpaicg_role){
                        echo '<input class="wpaicg_role_'.esc_html($key).'" value="'.(isset($wpaicg_settings['limited_roles'][$key]) && !empty($wpaicg_settings['limited_roles'][$key]) ? esc_html($wpaicg_settings['limited_roles'][$key]) : '').'" type="hidden" name="wpaicg_chat_shortcode_options[limited_roles]['.esc_html($key).']">';
                    }
                    ?>
                    <input <?php echo $wpaicg_user_limited ? '': ($wpaicg_settings['role_limited'] ? ' checked':'')?> type="checkbox" value="1" class="wpaicg_role_limited" name="wpaicg_chat_shortcode_options[role_limited]" id="wpaicg_role_limited">
                    <a href="javascript:void(0)" class="wpaicg_limit_set_role<?php echo $wpaicg_user_limited || !$wpaicg_settings['role_limited'] ? ' disabled': ''?>"><?php echo esc_html__('Configure Role Allocations','gpt3-ai-content-generator')?></a>
                </div>
                <div class="nice-form-group">
                    <input <?php echo $wpaicg_guest_limited ? ' checked': ''?> type="checkbox" class="wpaicg_guest_token_limit" value="1" name="wpaicg_chat_shortcode_options[guest_limited]" id="wpaicg_guest_token_limit">
                    <label for="wpaicg_guest_token_limit"><?php echo esc_html__('Limit Non-Registered Users','gpt3-ai-content-generator')?></label>
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_guest_token_limit_text"><?php echo esc_html__('Token Allocation','gpt3-ai-content-generator')?></label>
                    <input <?php echo $wpaicg_guest_limited ? '' : ' disabled'?> class="wpaicg_guest_token_limit_text" type="text" value="<?php echo esc_html($wpaicg_guest_tokens)?>" name="wpaicg_chat_shortcode_options[guest_tokens]" id="wpaicg_guest_token_limit_text">
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_reset_limit"><?php echo esc_html__('Reset Interval','gpt3-ai-content-generator')?></label>
                    <select name="wpaicg_chat_shortcode_options[reset_limit]" id="wpaicg_reset_limit">
                        <option <?php echo $wpaicg_reset_limit == 0 ? ' selected':''?> value="0"><?php echo esc_html__('Never','gpt3-ai-content-generator')?></option>
                        <option <?php echo $wpaicg_reset_limit == 1 ? ' selected':''?> value="1"><?php echo esc_html__('1 Day','gpt3-ai-content-generator')?></option>
                        <option <?php echo $wpaicg_reset_limit == 3 ? ' selected':''?> value="3"><?php echo esc_html__('3 Days','gpt3-ai-content-generator')?></option>
                        <option <?php echo $wpaicg_reset_limit == 7 ? ' selected':''?> value="7"><?php echo esc_html__('1 Week','gpt3-ai-content-generator')?></option>
                        <option <?php echo $wpaicg_reset_limit == 14 ? ' selected':''?> value="14"><?php echo esc_html__('2 Weeks','gpt3-ai-content-generator')?></option>
                        <option <?php echo $wpaicg_reset_limit == 30 ? ' selected':''?> value="30"><?php echo esc_html__('1 Month','gpt3-ai-content-generator')?></option>
                        <option <?php echo $wpaicg_reset_limit == 60 ? ' selected':''?> value="60"><?php echo esc_html__('2 Months','gpt3-ai-content-generator')?></option>
                        <option <?php echo $wpaicg_reset_limit == 90 ? ' selected':''?> value="90"><?php echo esc_html__('3 Months','gpt3-ai-content-generator')?></option>
                        <option <?php echo $wpaicg_reset_limit == 180 ? ' selected':''?> value="180"><?php echo esc_html__('6 Months','gpt3-ai-content-generator')?></option>
                    </select>
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_limited_message"><?php echo esc_html__('Notice','gpt3-ai-content-generator')?></label>
                    <input type="text" value="<?php echo esc_html($wpaicg_limited_message)?>" name="wpaicg_chat_shortcode_options[limited_message]" id="wpaicg_limited_message">
                </div>
                <details>
                    <summary>
                        <button class="button button-primary wpaicg-w-25"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
                        <!-- Export Button -->
                        <button type="button" class="button button-primary wpaicg-w-25" id="exportButton"><?php echo esc_html__('Export', 'gpt3-ai-content-generator'); ?></button>
                        <!-- Import Button -->
                        <button type="button" class="button button-primary wpaicg-w-25" id="importButton"><?php echo esc_html__('Import', 'gpt3-ai-content-generator'); ?></button>
                        <!-- Hidden File Input for Import -->
                        <input type="file" id="importFileInput" style="display: none;" accept=".json">
                        <!-- Reset Button -->
                        <button type="button" class="button button-link-delete" id="resetButton"><?php echo esc_html__('Reset', 'gpt3-ai-content-generator'); ?></button>
                    </summary>
                </details>
            </section>
            <!-- SECURITY -->
            <section id="security" class="tab-content" style="display: none;">
                <h1>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-square">
                    <polyline points="9 11 12 14 22 4" />
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                </svg>
                    Security
                </h1>
                <fieldset class="nice-form-group">
                <legend><?php echo esc_html__('Logs', 'gpt3-ai-content-generator'); ?></legend>
                    <div class="nice-form-group">
                        <input <?php echo $wpaicg_save_logs ? ' checked': ''?> value="1" type="checkbox" class="wpaicg_chatbot_save_logs" name="wpaicg_chat_shortcode_options[save_logs]" id="wpaicg_chatbot_save_logs">
                        <label for="wpaicg_chatbot_save_logs"><?php echo esc_html__('Save Chat Logs','gpt3-ai-content-generator')?></label>
                    </div>
                    <div class="nice-form-group">
                        <input <?php echo $wpaicg_save_logs ? '' : ' disabled'?><?php echo $wpaicg_save_logs && isset($wpaicg_settings['log_request']) && $wpaicg_settings['log_request'] ? ' checked' : ''?> class="wpaicg_chatbot_log_request" value="1" type="checkbox" name="wpaicg_chat_shortcode_options[log_request]" id="wpaicg_chatbot_log_request">
                        <label for="wpaicg_chatbot_log_request"><?php echo esc_html__('Save Prompt Details','gpt3-ai-content-generator')?></label>
                    </div>
                    <div class="nice-form-group">
                        <input <?php echo $wpaicg_save_logs ? '': ' disabled'?><?php echo $wpaicg_log_notice ? ' checked': ''?> value="1" class="wpaicg_chatbot_log_notice" type="checkbox" name="wpaicg_chat_shortcode_options[log_notice]" id="wpaicg_chatbot_log_notice">
                        <label for="wpaicg_chatbot_log_notice"><?php echo esc_html__('Display Notice','gpt3-ai-content-generator')?></label>
                    </div>
                </fieldset>
                <div class="nice-form-group">
                    <label for="wpaicg_chatbot_log_notice_message"><?php echo esc_html__('Notice Text','gpt3-ai-content-generator')?></label>
                    <input <?php echo $wpaicg_save_logs ? '': ' disabled'?> value="<?php echo esc_html($wpaicg_log_notice_message)?>" class="wpaicg_chatbot_log_notice_message" id="wpaicg_chatbot_log_notice_message" type="text" name="wpaicg_chat_shortcode_options[log_notice_message]">
                </div>
                <?php 
                $wpaicg_provider = get_option('wpaicg_provider', 'OpenAI');
                $is_pro = \WPAICG\wpaicg_util_core()->wpaicg_is_pro();
                ?>
                <div class="nice-form-group">
                    <input <?php echo isset($wpaicg_settings['moderation']) && $wpaicg_settings['moderation'] ? ' checked': ''?>  id="openai_moderation_tool" name="wpaicg_chat_shortcode_options[moderation]" value="1" type="checkbox" <?php if(!\WPAICG\wpaicg_util_core()->wpaicg_is_pro() || $wpaicg_provider === 'Azure' || $wpaicg_provider === 'Google') echo 'disabled'; ?>>
                    <label for="openai_moderation_tool"><?php echo !$is_pro ? esc_html__('Moderation (Available in Pro)','gpt3-ai-content-generator') : esc_html__('Moderation','gpt3-ai-content-generator'); ?></label>
                </div>
                <?php 
                // Disable if it is Azure or Google.
                if($wpaicg_provider === 'Azure' || $wpaicg_provider === 'Google'):
                    echo '<small>'. esc_html__('Moderation is not available in Azure or Google. If you want to use the moderation tool, please change your provider to OpenAI under Settings - AI Engine tab.', 'gpt3-ai-content-generator') .'</small>';
                endif;
                ?>
                <div class="nice-form-group">
                    <label for="openai_moderation_model"><?php echo esc_html__('Model','gpt3-ai-content-generator')?></label>
                    <select id="openai_moderation_model"  name="wpaicg_chat_shortcode_options[moderation_model]" <?php if(!\WPAICG\wpaicg_util_core()->wpaicg_is_pro() || $wpaicg_provider === 'Azure' || $wpaicg_provider === 'Google') echo 'disabled'; ?>>
                        <option <?php echo isset($wpaicg_settings['moderation_model']) && $wpaicg_settings['moderation_model'] == 'text-moderation-latest' ? ' selected':'';?> value="text-moderation-latest">text-moderation-latest</option>
                        <option <?php echo isset($wpaicg_settings['moderation_model']) && $wpaicg_settings['moderation_model'] == 'text-moderation-stable' ? ' selected':'';?> value="text-moderation-stable">text-moderation-stable</option>
                    </select>
                </div>
                <div class="nice-form-group">
                    <label for="openai_moderation_notice"><?php echo esc_html__('Notice','gpt3-ai-content-generator')?></label>
                    <textarea rows="8" id="openai_moderation_notice" name="wpaicg_chat_shortcode_options[moderation_notice]" <?php if(!\WPAICG\wpaicg_util_core()->wpaicg_is_pro() || $wpaicg_provider === 'Azure' || $wpaicg_provider === 'Google') echo 'disabled'; ?>><?php echo isset($wpaicg_settings['moderation_notice']) ? esc_html($wpaicg_settings['moderation_notice']) : ''?></textarea>
                </div>
                <details>
                    <summary>
                        <button class="button button-primary wpaicg-w-25"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
                        <!-- Export Button -->
                        <button type="button" class="button button-primary wpaicg-w-25" id="exportButton"><?php echo esc_html__('Export', 'gpt3-ai-content-generator'); ?></button>
                        <!-- Import Button -->
                        <button type="button" class="button button-primary wpaicg-w-25" id="importButton"><?php echo esc_html__('Import', 'gpt3-ai-content-generator'); ?></button>
                        <!-- Hidden File Input for Import -->
                        <input type="file" id="importFileInput" style="display: none;" accept=".json">
                        <!-- Reset Button -->
                        <button type="button" class="button button-link-delete" id="resetButton"><?php echo esc_html__('Reset', 'gpt3-ai-content-generator'); ?></button>
                    </summary>
                </details>

            </section>
            <!-- STYLE -->
            <section id="style" class="tab-content" style="display: none;">
                <h1>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-feather">
                    <path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z" />
                    <line x1="16" y1="8" x2="2" y2="22" />
                    <line x1="17.5" y1="15" x2="9" y2="15" />
                </svg>
                    Style
                </h1>
                <div class="nice-form-group">
                    <label for="wpaicg_chat_shortcode_width"><?php echo esc_html__('Width','gpt3-ai-content-generator')?></label>
                    <input min="300" type="text" class="wpaicg_chat_shortcode_width" id="wpaicg_chat_shortcode_width" name="wpaicg_chat_shortcode_options[width]" value="<?php echo esc_html( $wpaicg_settings['width'] ) ;?>" >
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_chat_shortcode_height"><?php echo esc_html__('Height','gpt3-ai-content-generator')?></label>
                    <input min="300" type="text" class="wpaicg_chat_shortcode_height" id="wpaicg_chat_shortcode_height" name="wpaicg_chat_shortcode_options[height]" value="<?php echo esc_html( $wpaicg_settings['height'] ) ;?>" >
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_chat_shortcode_font_size"><?php echo esc_html__('Font Size','gpt3-ai-content-generator')?></label>
                    <select name="wpaicg_chat_shortcode_options[fontsize]" class="wpaicg_chat_shortcode_font_size" id="wpaicg_chat_shortcode_font_size">
                        <?php
                        for($i = 10; $i <= 30; $i++){
                            echo '<option'.($wpaicg_settings['fontsize'] == $i ? ' selected': '').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_font_color"><?php echo esc_html__('Font Color','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_settings['fontcolor'])?>" type="text" id="wpaicg_font_color" class="wpaicgchat_color wpaicg_font_color" name="wpaicg_chat_shortcode_options[fontcolor]">
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_bgcolor"><?php echo esc_html__('Background','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_settings['bgcolor'])?>" type="text" class="wpaicgchat_color wpaicg_bgcolor" id="wpaicg_bgcolor" name="wpaicg_chat_shortcode_options[bgcolor]">
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_bg_text_field"><?php echo esc_html__('Text Field Background','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_settings['bg_text_field'])?>" type="text" class="wpaicgchat_color wpaicg_bg_text_field" id="wpaicg_bg_text_field" name="wpaicg_chat_shortcode_options[bg_text_field]">
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_border_text_field"><?php echo esc_html__('Text Field Border','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_settings['border_text_field'])?>" type="text" class="wpaicgchat_color wpaicg_border_text_field" id="wpaicg_border_text_field" name="wpaicg_chat_shortcode_options[border_text_field]">
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_send_color"><?php echo esc_html__('Send Button','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_settings['send_color'])?>" type="text" class="wpaicgchat_color wpaicg_send_color" id="wpaicg_send_color" name="wpaicg_chat_shortcode_options[send_color]">
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_user_bg_color"><?php echo esc_html__('User Background','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_settings['user_bg_color'])?>" type="text" class="wpaicgchat_color wpaicg_user_bg_color" id="wpaicg_user_bg_color" name="wpaicg_chat_shortcode_options[user_bg_color]">
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_ai_bg_color"><?php echo esc_html__('AI Background','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_settings['ai_bg_color'])?>" type="text" class="wpaicgchat_color wpaicg_ai_bg_color" id="wpaicg_ai_bg_color" name="wpaicg_chat_shortcode_options[ai_bg_color]">
                </div>
                <div class="nice-form-group">
                    <label for="wpaicgchat_thinking_color"><?php echo esc_html__('Response Wait Message','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_thinking_color)?>" type="text" class="wpaicgchat_color wpaicgchat_thinking_color" id="wpaicgchat_thinking_color" name="wpaicg_chat_shortcode_options[thinking_color]">
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_chat_rounded"><?php echo esc_html__('Border Radius - Window','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_settings['chat_rounded'])?>" type="number" min="0" class="wpaicg_chat_rounded" id="wpaicg_chat_rounded" name="wpaicg_chat_shortcode_options[chat_rounded]">px
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_text_rounded"><?php echo esc_html__('Border Radius - Text Field','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_settings['text_rounded'])?>" type="number" min="0" class="wpaicg_text_rounded" id="wpaicg_text_rounded" name="wpaicg_chat_shortcode_options[text_rounded]">px
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_text_height"><?php echo esc_html__('Text Field Height','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_settings['text_height'])?>" type="number" min="30" class="wpaicg_text_height" id="wpaicg_text_height" name="wpaicg_chat_shortcode_options[text_height]">px
                </div>
                <details>
                    <summary>
                        <button class="button button-primary wpaicg-w-25"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
                        <!-- Export Button -->
                        <button type="button" class="button button-primary wpaicg-w-25" id="exportButton"><?php echo esc_html__('Export', 'gpt3-ai-content-generator'); ?></button>
                        <!-- Import Button -->
                        <button type="button" class="button button-primary wpaicg-w-25" id="importButton"><?php echo esc_html__('Import', 'gpt3-ai-content-generator'); ?></button>
                        <!-- Hidden File Input for Import -->
                        <input type="file" id="importFileInput" style="display: none;" accept=".json">
                        <!-- Reset Button -->
                        <button type="button" class="button button-link-delete" id="resetButton"><?php echo esc_html__('Reset', 'gpt3-ai-content-generator'); ?></button>
                    </summary>
                </details>
            </section>
        </form>
    </main>
    <aside class="demo-page-fixed-content">
            <div class="wpaicg-chat-shortcode-preview">
                <?php echo do_shortcode('[wpaicg_chatgpt]'); ?>
            </div>
    </aside>
  </div>
</div>

<div id="exportMessage" style="display: none;" class="notice notice-success"></div>

<script>
    jQuery(document).ready(function ($){
        let wpaicg_google_voices = <?php echo json_encode($wpaicg_google_voices)?>;
        let wpaicg_elevenlab_api = '<?php echo esc_html($wpaicg_elevenlabs_api)?>';
        let wpaicg_google_api_key = '<?php  echo $wpaicg_google_api_key?>';

        // Function to set initial visibility of voice service sections
        function setInitialVoiceServiceVisibility() {
            let selectedVoiceService = $('.wpaicg_voice_service').val();
            toggleVoiceServiceSections(selectedVoiceService);
        }

        // Function to toggle voice service sections based on selected service
        function toggleVoiceServiceSections(service) {
            let parent = $('.wpaicg_voice_service').parent().parent();
            if(service === 'google'){
                parent.find('.wpaicg_voice_service_elevenlabs').hide();
                parent.find('.wpaicg_voice_service_google').show();
                parent.find('.wpaicg_voice_service_openai').hide();
            } else if(service === 'openai'){
                parent.find('.wpaicg_voice_service_elevenlabs').hide();
                parent.find('.wpaicg_voice_service_google').hide();
                parent.find('.wpaicg_voice_service_openai').show();
            } else {
                parent.find('.wpaicg_voice_service_elevenlabs').show();
                parent.find('.wpaicg_voice_service_google').hide();
                parent.find('.wpaicg_voice_service_openai').hide();
            }
        }

        // Set initial visibility on page load
        setInitialVoiceServiceVisibility();

        $(document).on('click','.wpaicg_chat_to_speech', function(e){
            let parent = $(e.currentTarget).parent().parent();
            let voice_service = parent.find('.wpaicg_voice_service');
            let is_openai_selected = voice_service.val() === 'openai';

            if($(e.currentTarget).prop('checked')){
                voice_service.removeAttr('disabled');
                if(is_openai_selected){
                    parent.find('.wpaicg_openai_model').removeAttr('disabled');
                    parent.find('.wpaicg_openai_voice').removeAttr('disabled');
                    parent.find('.wpaicg_openai_output_format').removeAttr('disabled');
                    parent.find('.wpaicg_openai_voice_speed').removeAttr('disabled');
                }
                if(wpaicg_elevenlab_api !== '' || wpaicg_google_api_key !== ''){
                    voice_service.removeAttr('disabled');
                }
                if(wpaicg_elevenlab_api !== ''){
                    parent.find('.wpaicg_elevenlabs_voice').removeAttr('disabled');
                    parent.find('.wpaicg_elevenlabs_model').removeAttr('disabled');
                }
                if(wpaicg_google_api_key !== ''){
                    parent.find('.wpaicg_voice_language').removeAttr('disabled');
                    parent.find('.wpaicg_voice_name').removeAttr('disabled');
                    parent.find('.wpaicg_voice_device').removeAttr('disabled');
                    parent.find('.wpaicg_voice_speed').removeAttr('disabled');
                    parent.find('.wpaicg_voice_pitch').removeAttr('disabled');
                }
            }
            else{
                voice_service.attr('disabled','disabled');
                parent.find('.wpaicg_elevenlabs_voice').attr('disabled','disabled');
                parent.find('.wpaicg_elevenlabs_model').attr('disabled','disabled');
                parent.find('.wpaicg_openai_model').attr('disabled','disabled');
                parent.find('.wpaicg_openai_voice').attr('disabled','disabled');
                parent.find('.wpaicg_openai_output_format').attr('disabled','disabled');
                parent.find('.wpaicg_openai_voice_speed').attr('disabled','disabled');
                parent.find('.wpaicg_voice_language').attr('disabled','disabled');
                parent.find('.wpaicg_voice_name').attr('disabled','disabled');
                parent.find('.wpaicg_voice_device').attr('disabled','disabled');
                parent.find('.wpaicg_voice_speed').attr('disabled','disabled');
                parent.find('.wpaicg_voice_pitch').attr('disabled','disabled');
            }
        });

        $(document).on('change','.wpaicg_voice_service',function(e){
            let parent = $(e.currentTarget).parent().parent();
            if($(e.currentTarget).val() === 'google'){
                parent.find('.wpaicg_voice_service_elevenlabs').hide();
                parent.find('.wpaicg_voice_service_google').show();
                parent.find('.wpaicg_voice_service_openai').hide();
            }
            else if($(e.currentTarget).val() === 'openai'){
                parent.find('.wpaicg_voice_service_elevenlabs').hide();
                parent.find('.wpaicg_voice_service_google').hide();
                parent.find('.wpaicg_voice_service_openai').show();
            }
            else{
                parent.find('.wpaicg_voice_service_elevenlabs').show();
                parent.find('.wpaicg_voice_service_google').hide();
                parent.find('.wpaicg_voice_service_openai').hide();
            }
        })
        $(document).on('keypress','.wpaicg_voice_speed,.wpaicg_voice_pitch', function (e){
            var charCode = (e.which) ? e.which : e.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
                return false;
            }
            return true;
        });
        function wpaicgsetVoices(element){
            let parent = element.parent().parent();
            let language = element.val();
            let voiceNameInput = parent.find('.wpaicg_voice_name');
            voiceNameInput.empty();
            let selected = voiceNameInput.attr('data-value');
            $.each(wpaicg_google_voices[language], function (idx, item){
                voiceNameInput.append('<option'+(selected === item.name ? ' selected':'')+' value="'+item.name+'">'+item.name+' - '+item.ssmlGender+'</option>');
            })
        }
        function wpaicgcollectVoices(element){
            if(!Object.keys(wpaicg_google_voices).length === 0){
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php')?>',
                    data: {action: 'wpaicg_sync_google_voices',nonce: '<?php echo wp_create_nonce('wpaicg_sync_google_voices')?>'},
                    dataType: 'json',
                    type: 'post',
                    success: function(res){
                        if(res.status === 'success'){
                            wpaicg_google_voices = res.voices;
                            wpaicgsetVoices(element);
                        }else{
                            alert(res.message);
                        }
                    }

                });
            }
            else{
                wpaicgsetVoices(element);
            }
        }
        $(document).on('change','.wpaicg_voice_language', function(e){
            wpaicgcollectVoices($(e.currentTarget));
        })
        if($('.wpaicg_voice_language').length){
            wpaicgcollectVoices($('.wpaicg_voice_language'));
        }

        function resetForm() {
            var resetSource = 'shortcode';

            if (confirm('<?php echo esc_html__('Are you sure you want to reset the settings?', 'gpt3-ai-content-generator'); ?>')) {
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: {
                        action: 'wpaicg_reset_settings',
                        nonce: '<?php echo wp_create_nonce('wpaicg_reset_settings'); ?>',
                        source: resetSource
                    },
                    dataType: 'json',
                    type: 'post',
                    success: function(response) {
                        // Check if the request was successful
                        if (response.success) {
                            alert(response.data); // Show success message
                            location.reload(); // Reload the page to reflect changes
                        } else {
                            alert(response.data); // Show error message
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle potential AJAX errors here
                        alert('An error occurred: ' + error);
                    }
                });
            }
        }

        $(document).on('click', '#resetButton', function() {
            resetForm();
        });

        $(document).on('click', '#importButton', function() {
            //prevent the default action of the button
            event.preventDefault();
            $('#importFileInput').click();
        });

        // Handle file selection
        $('#importFileInput').on('change', function() {
            var file = this.files[0]; // Get the file
            var source = 'shortcode'; // Adjust based on context

            var formData = new FormData();
            formData.append('action', 'wpaicg_import_settings');
            formData.append('nonce', '<?php echo wp_create_nonce('wpaicg_import_settings_nonce'); ?>');
            formData.append('source', source);
            formData.append('file', file);

            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                processData: false, // Important for FormData
                contentType: false, // Important for FormData
                dataType: 'json',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        alert('Import successful.');
                        location.reload(); // Reload to reflect changes
                    } else {
                        alert('Import failed: ' + response.data);
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred: ' + error);
                }
            });
        });

        // Function to handle export settings
        function exportSettings() {
            var exportSource = 'shortcode'; // Adjust this based on the current context (shortcode, widget, bot)

            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'wpaicg_export_settings',
                    nonce: '<?php echo wp_create_nonce('wpaicg_export_settings'); ?>',
                    source: exportSource
                },
                success: function(response) {
                    var messageDiv = $('#exportMessage');
                    if (response.success) {
                        // Assuming the response contains a URL to the exported file
                        var downloadLink = '<a href="' + response.data.url + '" download><?php echo esc_html__('Download Exported Settings', 'gpt3-ai-content-generator'); ?></a>';
                        messageDiv.html('<?php echo esc_html__('Export successful.', 'gpt3-ai-content-generator'); ?> ' + downloadLink);
                        $('html, body').animate({scrollTop: 0}, 'slow');
                    } else {
                        messageDiv.html('<?php echo esc_html__('Export failed:', 'gpt3-ai-content-generator'); ?>' + response.data);
                    }
                    messageDiv.show();
                },
                error: function(xhr, status, error) {
                    $('#exportMessage').html('<?php echo esc_html__('An error occurred:', 'gpt3-ai-content-generator'); ?>' + error).show();
                }
            });
        }

        $(document).on('click', '#exportButton', function() {
            exportSettings();
        });

        function updateImageUploadOption() {
            var model = $('#wpaicg_chat_model').val();

            var $imageUploadCheckbox = $('.wpaicg_image_enable'); // Update this selector as needed
            var $streamingCheckbox = $('input[name="wpaicg_stream_nav_option"]');

            if (model === 'gpt-4-vision-preview') {
                $imageUploadCheckbox.prop('disabled', false); // Enable the checkbox
            } else {
                $imageUploadCheckbox.prop('disabled', true); // Disable the checkbox
                $imageUploadCheckbox.prop('checked', false); // Uncheck the checkbox
                // enable the "Streaming" checkbox
                $streamingCheckbox.prop('disabled', false);
            }
        }

        // Check and update the option on page load
        updateImageUploadOption();

        // Re-check and update whenever the model selection changes
        $('#wpaicg_chat_model').change(function () {
            updateImageUploadOption();
        });

        // Listen for changes on the "Enable Image Upload" checkbox
        $('.wpaicg_image_enable').on('change', function() {
            var isImageUploadEnabled = $(this).is(':checked');
            // Find the "Streaming" checkbox by its name attribute
            var $streamingCheckbox = $('input[name="wpaicg_stream_nav_option"]');

            if (isImageUploadEnabled) {
                // If "Enable Image Upload" is checked, disable the "Streaming" checkbox and uncheck it
                $streamingCheckbox.prop('disabled', true).prop('checked', false);
            } else {
                $streamingCheckbox.prop('disabled', false);
            }
        });

        // Optionally, trigger the change event on page load in case the "Enable Image Upload" checkbox is already checked
        $('.wpaicg_image_enable').trigger('change');

        let wpaicg_provider = '<?php echo $wpaicg_provider?>';

        // Function to disable the streaming option if the provider is Google..
        function disableStreamingForGoogle() {
            if(wpaicg_provider === 'Google') {
                var $streamingCheckbox = $('input[name="wpaicg_stream_nav_option"]');
                $streamingCheckbox.prop('disabled', true).prop('checked', false);
            }
        }

        // Call the function to apply the logic on page load
        disableStreamingForGoogle();

        $('#form-chatbox-setting').on('submit', function (e){
            if($('.wpaicg_voice_speed').length) {
                let wpaicg_voice_speed = parseFloat($('.wpaicg_voice_speed').val());
                let wpaicg_voice_pitch = parseFloat($('.wpaicg_voice_pitch').val());
                let wpaicg_voice_name = parseFloat($('.wpaicg_voice_name').val());
                let has_error = false;
                if (wpaicg_voice_speed < 0.25 || wpaicg_voice_speed > 4) {
                    has_error = '<?php printf(
                        /* translators: 1: minimum speed, 2: maximum speed */
                        esc_html__('Please enter valid voice speed value between %1$s and %2$s', 'gpt3-ai-content-generator'), 0.25, 4)?>';
                } else if (wpaicg_voice_pitch < -20 || wpaicg_voice_pitch > 20) {
                    has_error = '<?php printf(
                        /* translators: 1: minimum pitch, 2: maximum pitch */
                        esc_html__('Please enter valid voice pitch value between %1$s and %2$s', 'gpt3-ai-content-generator'), -20, 20)?>';
                }
                else if(wpaicg_voice_name === ''){
                    has_error = '<?php echo esc_html__('Please select voice name', 'gpt3-ai-content-generator')?>';
                }
                if (has_error) {
                    e.preventDefault();
                    alert(has_error);
                    return false;
                }
            }
        })
        let wpaicg_roles = <?php echo wp_kses_post(json_encode($wpaicg_roles))?>;
        $('.wpaicg_modal_close_second').click(function (){
            $('.wpaicg_modal_close_second').closest('.wpaicg_modal_second').hide();
            $('.wpaicg-overlay-second').hide();
        });
        $(document).on('keypress','.wpaicg_user_token_limit_text,.wpaicg_update_role_limit,.wpaicg_guest_token_limit_text', function (e){
            var charCode = (e.which) ? e.which : e.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
                return false;
            }
            return true;
        });
        $(document).on('click', '.wpaicg_chatbot_save_logs', function(e){
            if($(e.currentTarget).prop('checked')){
                $('.wpaicg_chatbot_log_request').removeAttr('disabled');
                $('.wpaicg_chatbot_log_notice').removeAttr('disabled');
                $('.wpaicg_chatbot_log_notice_message').removeAttr('disabled');
            }
            else{
                $('.wpaicg_chatbot_log_request').attr('disabled','disabled');
                $('.wpaicg_chatbot_log_request').prop('checked',false);
                $('.wpaicg_chatbot_log_notice').attr('disabled','disabled');
                $('.wpaicg_chatbot_log_notice').prop('checked',false);
                $('.wpaicg_chatbot_log_notice_message').attr('disabled','disabled');
            }
        });
        $('.wpaicg_limit_set_role').click(function (){
            if(!$(this).hasClass('disabled')) {
                if ($('.wpaicg_role_limited').prop('checked')) {
                    let html = '';
                    $.each(wpaicg_roles, function (key, role) {
                        let valueRole = $('.wpaicg_role_'+key).val();
                        html += '<div style="padding: 5px;display: flex;justify-content: space-between;align-items: center;"><label><strong>'+role+'</strong></label><input class="wpaicg_update_role_limit" data-target="'+key+'" value="'+valueRole+'" placeholder="Empty for no-limit" type="text"></div>';
                    });
                    html += '<div style="padding: 5px"><button class="button button-primary wpaicg_save_role_limit" style="width: 100%;margin: 5px 0;"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button></div>';
                    $('.wpaicg_modal_title_second').html('<?php echo esc_html__('Role Limit','gpt3-ai-content-generator')?>');
                    $('.wpaicg_modal_content_second').html(html);
                    $('.wpaicg-overlay-second').css('display','flex');
                    $('.wpaicg_modal_second').show();

                } else {
                    $.each(wpaicg_roles, function (key, role) {
                        $('.wpaicg_role_' + key).val('');
                    })
                }
            }
        });
        $(document).on('click','.wpaicg_save_role_limit', function (e){
            $('.wpaicg_update_role_limit').each(function (idx, item){
                let input = $(item);
                let target = input.attr('data-target');
                $('.wpaicg_role_'+target).val(input.val());
            });
            $('.wpaicg_modal_close_second').closest('.wpaicg_modal_second').hide();
            $('.wpaicg-overlay-second').hide();
        });
        $('.wpaicg_guest_token_limit').click(function (){
            if($(this).prop('checked')){
                $('.wpaicg_guest_token_limit_text').removeAttr('disabled');
            }
            else{
                $('.wpaicg_guest_token_limit_text').val('');
                $('.wpaicg_guest_token_limit_text').attr('disabled','disabled');
            }
        });
        $('.wpaicg_role_limited').click(function (){
            if($(this).prop('checked')){
                $('.wpaicg_user_token_limit').prop('checked',false);
                $('.wpaicg_user_token_limit_text').attr('disabled','disabled');
                $('.wpaicg_limit_set_role').removeClass('disabled');
            }
            else{
                $('.wpaicg_limit_set_role').addClass('disabled');
            }
        });
        $('.wpaicg_user_token_limit').click(function (){
            if($(this).prop('checked')){
                $('.wpaicg_user_token_limit_text').removeAttr('disabled');
                $('.wpaicg_role_limited').prop('checked',false);
                $('.wpaicg_limit_set_role').addClass('disabled');
            }
            else{
                $('.wpaicg_user_token_limit_text').val('');
                $('.wpaicg_user_token_limit_text').attr('disabled','disabled');
            }
        });
        $('.wpaicg-collapse-title').click(function (){
            if(!$(this).hasClass('wpaicg-collapse-active')){
                $('.wpaicg-collapse').removeClass('wpaicg-collapse-active');
                $('.wpaicg-collapse-title span').html('+');
                $(this).find('span').html('-');
                $(this).parent().addClass('wpaicg-collapse-active');
            }
        });
        
        // Function to toggle the visibility and enabled state of related fields based on DB Provider selection
        function toggleRelatedFieldsVisibility() {
            var dbProvider = $('#wpaicg_db_provider').val();
            if(dbProvider === 'qdrant') {
                // Hide and disable Pinecone Index section
                $('#wpaicg_chat_embedding_index').closest('.nice-form-group').hide();
                $('#wpaicg_chat_embedding_index').prop('disabled', true);
                
                // Show and enable Qdrant Collection section
                $('#wpaicg_chat_qdrant_collection').closest('.nice-form-group').show();
                $('#wpaicg_chat_qdrant_collection').prop('disabled', false);
            } else {
                // Show and enable Pinecone Index section
                $('#wpaicg_chat_embedding_index').closest('.nice-form-group').show();
                $('#wpaicg_chat_embedding_index').prop('disabled', false);
                
                // Hide and disable Qdrant Collection section
                $('#wpaicg_chat_qdrant_collection').closest('.nice-form-group').hide();
                $('#wpaicg_chat_qdrant_collection').prop('disabled', true);
            }
        }

        // Event handler for DB Provider dropdown change
        $('#wpaicg_db_provider').on('change', function() {
            toggleRelatedFieldsVisibility();
            updateDbProviderState(); // Ensure other related field states are also updated accordingly
        });

        

        // Function to update the DB Provider field state
        function updateDbProviderState() {
            var useExcerpt = $('#wpaicg_chat_excerpt').prop('checked');
            var useEmbeddings = $('#wpaicg_chat_embedding').prop('checked');
            var isContentAwareYes = $('#wpaicg_chat_content_aware').val() === 'yes';
            
            // Disable DB Provider if useExcerpt is enabled or content aware is 'no'
            // Enable DB Provider if useEmbeddings is enabled and content aware is 'yes'
            if (useExcerpt || !isContentAwareYes) {
                $('#wpaicg_db_provider').prop('disabled', true).addClass('asdisabled');
                //disable wpaicg_chat_embedding_type
                $('#wpaicg_chat_embedding_type').prop('disabled', true).addClass('asdisabled');
                //disable wpaicg_chat_embedding_index
                $('#wpaicg_chat_embedding_index').prop('disabled', true).addClass('asdisabled');
            } else if (useEmbeddings && isContentAwareYes) {
                $('#wpaicg_db_provider').prop('disabled', false).removeClass('asdisabled');
                toggleRelatedFieldsVisibility();
            }
        }
        // Initial call to set the correct state on page load
        toggleRelatedFieldsVisibility();
        updateDbProviderState();

        $('#wpaicg_chat_excerpt').on('click', function (){
            if($(this).prop('checked')){
                $('#wpaicg_chat_excerpt').removeClass('asdisabled');
                $('#wpaicg_chat_embedding').prop('checked',false);
                $('#wpaicg_chat_embedding').addClass('asdisabled');
                $('#wpaicg_chat_embedding_type').val('openai');
                $('#wpaicg_chat_embedding_type').addClass('asdisabled');
                $('#wpaicg_chat_embedding_type').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_index').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_index').addClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_pdf').addClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf_message').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_pdf_message').addClass('asdisabled');
                $('#wpaicg_chat_pdf_pages').attr('disabled','disabled');
                $('#wpaicg_chat_pdf_pages').addClass('asdisabled');
                $('#wpaicg_chat_embedding_top').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_top').val(1);
                $('#wpaicg_chat_qdrant_collection').attr('disabled','disabled');
                $('#wpaicg_chat_qdrant_collection').addClass('asdisabled');
                // Add call to update the DB Provider field
                updateDbProviderState();
            }
            else{
                $(this).prop('checked',true);
            }
        });

        $('#wpaicg_chat_embedding').on('click', function (){
            if($(this).prop('checked')){
                $('#wpaicg_chat_excerpt').prop('checked',false);
                $('#wpaicg_chat_excerpt').addClass('asdisabled');
                $('#wpaicg_chat_embedding').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_type').val('openai');
                $('#wpaicg_chat_embedding_type').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_type').removeAttr('disabled');
                $('#wpaicg_chat_embedding_index').removeAttr('disabled');
                $('#wpaicg_chat_embedding_index').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf').removeAttr('disabled');
                $('#wpaicg_chat_embedding_pdf').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf_message').removeAttr('disabled');
                $('#wpaicg_chat_embedding_pdf_message').removeClass('asdisabled');
                $('#wpaicg_chat_pdf_pages').removeAttr('disabled');
                $('#wpaicg_chat_pdf_pages').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_top').val(1);
                $('#wpaicg_chat_embedding_top').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_top').removeAttr('disabled');
                // qdrant collection
                $('#wpaicg_chat_qdrant_collection').attr('disabled','disabled');
                $('#wpaicg_chat_qdrant_collection').addClass('asdisabled');
                // Add call to update the DB Provider field
                updateDbProviderState();
            }
            else{
                $(this).prop('checked',true);
            }
        });

        $('#wpaicg_chat_content_aware').on('change', function (){
            if($(this).val() === 'yes'){
                $('#wpaicg_chat_excerpt').removeAttr('disabled');
                $('#wpaicg_chat_excerpt').prop('checked',true);
                $('#wpaicg_chat_embedding').removeAttr('disabled');
                $('#wpaicg_chat_embedding_type').removeAttr('disabled');
                $('#wpaicg_chat_embedding_index').removeAttr('disabled');
                $('#wpaicg_chat_embedding_index').addClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf').removeAttr('disabled');
                $('#wpaicg_chat_embedding_pdf').addClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf_message').removeAttr('disabled');
                $('#wpaicg_chat_embedding_pdf_message').addClass('asdisabled');
                $('#wpaicg_chat_pdf_pages').removeAttr('disabled');
                $('#wpaicg_chat_pdf_pages').addClass('asdisabled');
                $('#wpaicg_chat_embedding').addClass('asdisabled');
                $('#wpaicg_chat_embedding_type').val('openai');
                $('#wpaicg_chat_embedding_type').addClass('asdisabled');
                $('#wpaicg_chat_embedding_top').val(1);
                $('#wpaicg_chat_embedding_top').addClass('asdisabled');
                // Add call to update the DB Provider field
                updateDbProviderState();
            }
            else{
                $('#wpaicg_chat_embedding_type').removeClass('asdisabled');
                $('#wpaicg_chat_excerpt').removeClass('asdisabled');
                $('#wpaicg_chat_embedding').removeClass('asdisabled');
                $('#wpaicg_chat_excerpt').prop('checked',false);
                $('#wpaicg_chat_embedding').prop('checked',false);
                $('#wpaicg_chat_excerpt').attr('disabled','disabled');
                $('#wpaicg_chat_embedding').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_type').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_index').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_index').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_pdf').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf_message').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_pdf_message').removeClass('asdisabled');
                $('#wpaicg_chat_pdf_pages').attr('disabled','disabled');
                $('#wpaicg_chat_pdf_pages').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_top').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_top').removeClass('asdisabled');
                $('#wpaicg_db_provider').prop('disabled', true).addClass('asdisabled');
                // qdtant collection
                $('#wpaicg_chat_qdrant_collection').attr('disabled','disabled');
                $('#wpaicg_chat_qdrant_collection').addClass('asdisabled');

            }
        })
        
        $('#wpaicg_chat_addition').on('click', function (){
            if($(this).prop('checked')){
                $('#wpaicg_chat_addition_text').removeAttr('disabled');
                $('.wpaicg_chat_addition_template').removeAttr('disabled');
            }
            else{
                $('#wpaicg_chat_addition_text').attr('disabled','disabled');
                $('.wpaicg_chat_addition_template').attr('disabled','disabled');
            }
        });
        $(document).on('change', '.wpaicg_chat_addition_template',function (e){
            var addition_text_template = $(e.currentTarget).val();
            if(addition_text_template !== ''){
                $('.wpaicg_chat_addition_text').val(addition_text_template);
            }
        });

        $('.wpaicg_font_color').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-user-message').css('color', color);
                $('.wpaicg-ai-message').css('color', color);
            },
            clear: function(event){
                $('.wpaicg-user-message').css('color', '');
                $('.wpaicg-ai-message').css('color', '');
            }
        });
        $('.wpaicg_pdf_color').wpColorPicker();
        $('.wpaicg_audio_enable').click(function (){
            if($(this).prop('checked')){
                $('.wpaicg-mic-icon').show();
            }
            else{
                $('.wpaicg-mic-icon').hide();
            }
        })
        $('.wpaicg_image_enable').click(function (){
            if($(this).prop('checked')){
                $('.wpaicg-img-icon').show();
            }
            else{
                $('.wpaicg-img-icon').hide();
            }
        })
        $('.wpaicg_mic_color').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-mic-icon').css('color', color);
            },
            clear: function(event){
                $('.wpaicg-mic-icon').css('color', '');
            }
        });
        $('.wpaicg_stop_color').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-mic-icon').css('color', color);
            },
            clear: function(event){
                $('.wpaicg-mic-icon').css('color', '');
            }
        });
        $('.wpaicgchat_thinking_color').wpColorPicker();
        $('.wpaicg_user_bg_color').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-user-message').css('background-color', color);
            },
            clear: function(event){
                $('.wpaicg-user-message').css('background-color', '');
            }
        });
        $('.wpaicg_bgcolor').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-chat-shortcode').css('background-color', color);
            },
            clear: function(event){
                $('.wpaicg-chat-shortcode').css('background-color', '#343540');
            }
        });
        $('.wpaicg_bg_text_field').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-chat-shortcode-typing').css('background-color', color);
            },
            clear: function(event){
                $('.wpaicg-chat-shortcode-typing').css('background-color', '#fff');
            }
        });
        $('.wpaicg_border_text_field').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-chat-shortcode-typing').css('border-color', color);
            },
            clear: function(event){
                $('.wpaicg-chat-shortcode-typing').css('border-color', '#ccc');
            }
        });
        $('.wpaicg_send_color').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-chat-shortcode-send').css('color', color);
            },
            clear: function(event){
                $('.wpaicg-chat-shortcode-send').css('color', '#fff');
            }
        });
        $('.wpaicgchat_bar_color').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-chatbox-action-bar').css('color', color);
            },
            clear: function(event){
                $('.wpaicg-chatbox-action-bar').css('color', '');
            }
        });
        $('.wpaicg_ai_bg_color').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-ai-message').css('background-color', color);
            },
            clear: function(event){
                $('.wpaicg-ai-message').css('background-color', '');
            }
        });
        $('.wpaicg_chat_shortcode_width').on('input', function (){
            var chatbox_width = $(this).val();
            var preview_width = $('.wpaicg-chat-shortcode-preview').width();
            if(chatbox_width.indexOf('%') > -1){
                chatbox_width = chatbox_width.replace('%','');
                chatbox_width = parseFloat(chatbox_width);
                chatbox_width = chatbox_width*preview_width/100;
            }
            else{
                chatbox_width = chatbox_width.replace('px','');
                chatbox_width = parseFloat(chatbox_width);
            }
            if(chatbox_width > preview_width){
                chatbox_width = preview_width;
            }
            $('.wpaicg-chat-shortcode').width(chatbox_width+'px');
            $('.wpaicg-chat-shortcode').attr('data-width',chatbox_width);
            wpaicgChatShortcodeSize();
        });
        $('.wpaicg_chat_rounded,.wpaicg_text_height,wpaicg_text_rounded').on('input', function (){
            $('.wpaicg-chat-shortcode').attr('data-chat_rounded',$('.wpaicg_chat_rounded').val());
            $('.wpaicg-chat-shortcode').attr('data-text_height',$('.wpaicg_text_height').val());
            $('.wpaicg-chat-shortcode').attr('data-text_rounded',$('.wpaicg_text_rounded').val());
            wpaicgChatShortcodeSize();
        })
        $('.wpaicg_chat_shortcode_height').on('input', function (){
            var chatbox_height = $(this).val();
            var preview_width = $(window).height();
            if(chatbox_height.indexOf('%') > -1){
                chatbox_height = chatbox_height.replace('%','');
                chatbox_height = parseFloat(chatbox_height);
                chatbox_height = chatbox_height*preview_width/100;
            }
            else{
                chatbox_height = chatbox_height.replace('px','');
                chatbox_height = parseFloat(chatbox_height);
            }
            if(chatbox_height > preview_width){
                chatbox_height = preview_width;
            }
            $('.wpaicg-chat-shortcode-content ul').height((chatbox_height - 44)+'px');
            $('.wpaicg-chat-shortcode').attr('data-height',chatbox_height);
        });
        $('.wpaicg_chatbox_icon').click(function (e){
            e.preventDefault();
            $('.wpaicg_chatbox_icon_default').prop('checked',false);
            $('.wpaicg_chatbox_icon_custom').prop('checked',true);
            var button = $(e.currentTarget),
                custom_uploader = wp.media({
                    title: '<?php echo esc_html__('Insert image','gpt3-ai-content-generator')?>',
                    library : {
                        type : 'image'
                    },
                    button: {
                        text: '<?php echo esc_html__('Use this image','gpt3-ai-content-generator')?>'
                    },
                    multiple: false
                }).on('select', function() {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    button.html('<img width="40" height="40" src="'+attachment.url+'">');
                    $('.wpaicg_chat_icon_url').val(attachment.id);
                }).open();
        });
        $('.wpaicg_chat_shortcode_font_size').on('change', function (){
            var font_size = $(this).val();
            $('.wpaicg-chat-shortcode-messages li').each(function (idx, item){
                $(item).css('font-size',font_size+'px');
            })
        });
        function wpaicgChangeAvatarRealtime(){
            var wpaicg_user_avatar_check = $('.wpaicg_chat_shortcode_you').val()+':';
            var wpaicg_ai_avatar_check = $('.wpaicg_chat_shortcode_ai_name').val()+':';
            if($('.wpaicg_chat_shortcode_use_avatar').prop('checked')){
                wpaicg_user_avatar_check = '<img src="<?php echo get_avatar_url(get_current_user_id())?>" height="40" width="40">';
                wpaicg_ai_avatar_check = '<?php echo esc_html(WPAICG_PLUGIN_URL) . 'admin/images/chatbot.png';?>';
                if($('.wpaicg_chatbox_icon_custom').prop('checked') && $('.wpaicg_chatbox_icon img').length){
                    wpaicg_ai_avatar_check = $('.wpaicg_chatbox_icon img').attr('src');
                }
                wpaicg_ai_avatar_check = '<img src="'+wpaicg_ai_avatar_check+'" height="40" width="40">';
            }

            $('.wpaicg-chat-shortcode-messages li.wpaicg-ai-message').each(function (idx, item){
                $(item).find('.wpaicg-chat-avatar').html(wpaicg_ai_avatar_check);
            });
            $('.wpaicg-chat-shortcode-messages li.wpaicg-user-message').each(function (idx, item){
                $(item).find('.wpaicg-chat-avatar').html(wpaicg_user_avatar_check);
            });
        }
        $('.wpaicg_chat_shortcode_ai_name,.wpaicg_chat_shortcode_you').on('input', function (){
            wpaicgChangeAvatarRealtime();
        })
        $('.wpaicg_chat_shortcode_use_avatar,.wpaicg_chatbox_icon_default,.wpaicg_chatbox_icon_custom').on('click', function (){
            wpaicgChangeAvatarRealtime();
        })
        // Function to manage speech options based on streaming status
        function manageSpeechOptionsBasedOnStreaming() {
            var isStreamingChecked = $('input[name="wpaicg_stream_nav_option"]').is(':checked');

            // "Speech to Text" checkbox and its container
            var $speechToTextCheckbox = $('.wpaicg_audio_enable');
            var $textToSpeechCheckbox = $('.wpaicg_chat_to_speech');

            if(isStreamingChecked) {
                // Disable and uncheck "Speech to Text" and "Text to Speech" if streaming is checked
                $speechToTextCheckbox.prop('disabled', true).prop('checked', false);
                $textToSpeechCheckbox.prop('disabled', true).prop('checked', false);
            } else {
                // Enable "Speech to Text" only if provider is not Azure
                <?php if($wpaicg_provider !== 'Azure'): ?>
                $speechToTextCheckbox.prop('disabled', false);
                <?php endif; ?>

                // Always enable "Text to Speech" - you might need to adjust based on additional conditions
                $textToSpeechCheckbox.prop('disabled', false);
            }
        }

        // Listen for changes on the "Streaming" checkbox
        $('input[name="wpaicg_stream_nav_option"]').on('change', function() {
            manageSpeechOptionsBasedOnStreaming();
        });

        // Call the function on page load to apply the correct initial state
        manageSpeechOptionsBasedOnStreaming();
    })
</script>
<script>
    document.getElementById('wpaicg-advanced-settings-link').addEventListener('click', function(e) {
        e.preventDefault();
        var wpaicgadvancedSettings = document.getElementById('wpaicg-advanced-settings');
        var wpaicgadvancedSettingsLink = document.getElementById('wpaicg-advanced-settings-link');
        if (wpaicgadvancedSettings.style.display === 'none') {
            wpaicgadvancedSettings.style.display = 'block';
            wpaicgadvancedSettingsLink.textContent = '<?php echo esc_js(esc_html__('Hide Advanced Parameters','gpt3-ai-content-generator')); ?>';
        } else {
            wpaicgadvancedSettings.style.display = 'none';
            wpaicgadvancedSettingsLink.textContent = '<?php echo esc_js(esc_html__('Show Advanced Parameters','gpt3-ai-content-generator')); ?>';
        }
    });
</script>
<script>
    document.getElementById('wpaicg-advanced-content-settings-link').addEventListener('click', function(e) {
        e.preventDefault();
        var wpaicgadvancedContentSettings = document.getElementById('wpaicg-advanced-content-settings');
        var wpaicgadvancedContentSettingsLink = document.getElementById('wpaicg-advanced-content-settings-link');
        if (wpaicgadvancedContentSettings.style.display === 'none') {
            wpaicgadvancedContentSettings.style.display = 'block';
            wpaicgadvancedContentSettingsLink.textContent = '<?php echo esc_js(esc_html__('Hide Additional Options','gpt3-ai-content-generator')); ?>';
        } else {
            wpaicgadvancedContentSettings.style.display = 'none';
            wpaicgadvancedContentSettingsLink.textContent = '<?php echo esc_js(esc_html__('Show Additional Options','gpt3-ai-content-generator')); ?>';
        }
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const tabs = document.querySelectorAll('.demo-page-navigation a[data-tab]');
  tabs.forEach(tab => {
    tab.addEventListener('click', function(e) {
      const targetId = this.getAttribute('data-tab');
      const sections = document.querySelectorAll('.tab-content');
      sections.forEach(section => {
        if (section.id === targetId) {
          section.style.display = '';
        } else {
          section.style.display = 'none';
        }
      });
      tabs.forEach(t => t.classList.remove('active'));
      this.classList.add('active');
    });
  });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var copyCode = document.querySelector('.toggle-shortcode');

  copyCode.addEventListener('click', function() {
    // Copy text
    navigator.clipboard.writeText(this.textContent).then(() => {
      // Temporarily change the text to indicate copy
      const originalText = this.textContent;
      this.textContent = 'Shortcode copied!';
      setTimeout(() => {
        this.textContent = originalText;
      }, 2000); // Reset text after 2 seconds
    }).catch(err => {
      console.error('Failed to copy: ', err);
    });
  });
});
</script>