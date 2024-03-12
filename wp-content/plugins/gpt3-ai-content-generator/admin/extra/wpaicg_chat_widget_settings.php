<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;
$errors = false;
$message = false;
if ( isset( $_POST['wpaicg_submit'] ) ) {
    check_admin_referer('wpaicg_chat_widget_save');

    $stream_nav_option = isset($_POST['wpaicg_stream_nav_option']) ? '1' : '0';
    update_option('wpaicg_widget_stream', $stream_nav_option);

    $wpaicg_chat_vectordb = isset($_POST['wpaicg_chat_vectordb']) ? sanitize_text_field($_POST['wpaicg_chat_vectordb']) : 'pinecone';
    update_option('wpaicg_chat_vectordb', $wpaicg_chat_vectordb);

    if (isset($_POST['wpaicg_widget_qdrant_collection'])) {
        $selectedCollection = sanitize_text_field($_POST['wpaicg_widget_qdrant_collection']);
        update_option('wpaicg_widget_qdrant_collection', $selectedCollection);
    }

    if ( isset($_POST['wpaicg_chat_temperature']) && (!is_numeric( $_POST['wpaicg_chat_temperature'] ) || floatval( $_POST['wpaicg_chat_temperature'] ) < 0 || floatval( $_POST['wpaicg_chat_temperature'] ) > 1 )) {
        $errors = sprintf(
            /* translators: 1: minimum temperature, 2: maximum temperature */
            esc_html__('Please enter a valid temperature value between %1$d and %2$d.','gpt3-ai-content-generator'),0,1);
    }
    if (isset($_POST['wpaicg_chat_max_tokens']) && ( !is_numeric( $_POST['wpaicg_chat_max_tokens'] ) || floatval( $_POST['wpaicg_chat_max_tokens'] ) < 64 || floatval( $_POST['wpaicg_chat_max_tokens'] ) > 8000 )) {
        $errors = sprintf(
            /* translators: 1: minimum max_tokens, 2: maximum max_tokens */
            esc_html__('Please enter a valid max token value between %1$d and %2$d.','gpt3-ai-content-generator'),64,8000);
    }
    if (isset($_POST['wpaicg_chat_top_p']) && (!is_numeric( $_POST['wpaicg_chat_top_p'] ) || floatval( $_POST['wpaicg_chat_top_p'] ) < 0 || floatval( $_POST['wpaicg_chat_top_p'] ) > 1 )){
        $errors = sprintf(
            /* translators: 1: minimum top_p, 2: maximum top_p */
            esc_html__('For the widget, please enter a valid top p value between %1$d and %2$d.','gpt3-ai-content-generator'),0,1);
    }
    if (isset($_POST['wpaicg_chat_best_of']) && ( !is_numeric( $_POST['wpaicg_chat_best_of'] ) || floatval( $_POST['wpaicg_chat_best_of'] ) < 1 || floatval( $_POST['wpaicg_chat_best_of'] ) > 20 )) {
        $errors = sprintf(
            /* translators: 1: minimum best of value, 2: maximum best of value */
            esc_html__('Please enter a valid best of value between %1$d and %2$d.','gpt3-ai-content-generator'),1,20);
    }
    if (isset($_POST['wpaicg_chat_frequency_penalty']) && ( !is_numeric( $_POST['wpaicg_chat_frequency_penalty'] ) || floatval( $_POST['wpaicg_chat_frequency_penalty'] ) < 0 || floatval( $_POST['wpaicg_chat_frequency_penalty'] ) > 2 )) {
        $errors = sprintf(
            /* translators: 1: minimum frequency_penalty, 2: maximum frequency_penalty */
            esc_html__('For the widget, please enter a valid frequency penalty value between %1$d and %2$d.','gpt3-ai-content-generator'),0,2);
    }
    if (isset($_POST['wpaicg_chat_presence_penalty']) && ( !is_numeric( $_POST['wpaicg_chat_presence_penalty'] ) || floatval( $_POST['wpaicg_chat_presence_penalty'] ) < 0 || floatval( $_POST['wpaicg_chat_presence_penalty'] ) > 2 ) ){
        $errors = sprintf(
            /* translators: 1: minimum presence_penalty, 2: maximum presence_penalty */
            esc_html__('For the widget, please enter a valid presence penalty value between %1$d and %2$d.','gpt3-ai-content-generator'),0,2);
    }
    if(!$errors){
        $wpaicg_keys = array(
            '_wpaicg_chatbox_you',
            '_wpaicg_ai_thinking',
            '_wpaicg_typing_placeholder',
            '_wpaicg_chatbox_welcome_message',
            '_wpaicg_chatbox_ai_name',
            'wpaicg_chat_model',
            'wpaicg_chat_temperature',
            'wpaicg_chat_max_tokens',
            'wpaicg_chat_top_p',
            'wpaicg_chat_best_of',
            'wpaicg_chat_frequency_penalty',
            'wpaicg_chat_presence_penalty',
            'wpaicg_chat_widget',
            'wpaicg_chat_language',
            'wpaicg_conversation_cut',
            'wpaicg_chat_embedding',
            'wpaicg_chat_addition',
            'wpaicg_chat_addition_text',
            'wpaicg_chat_no_answer',
            'wpaicg_chat_embedding_type',
            'wpaicg_chat_embedding_top'
        );
        foreach($wpaicg_keys as $wpaicg_key){
            if(isset($_POST[$wpaicg_key]) && !empty($_POST[$wpaicg_key])){
                // Strip slashes from the POST data
                $posted_value = stripslashes_deep($_POST[$wpaicg_key]);
                        // Check if the current key is 'wpaicg_chat_widget' and handle it separately
                if ($wpaicg_key === 'wpaicg_chat_widget' && is_array($posted_value)) {
                    // Extract the 'footer_text' field and handle it separately if needed
                    $footer_text = isset($posted_value['footer_text']) ? wp_kses_post($posted_value['footer_text']) : '';

                    // Remove the 'footer_text' field from the array temporarily to sanitize the rest
                    unset($posted_value['footer_text']);

                    // Sanitize the rest of the array
                    $sanitized_value = \WPAICG\wpaicg_util_core()->sanitize_text_or_array_field($posted_value);

                    // Add 'footer_text' back into the array or handle it as necessary
                    $sanitized_value['footer_text'] = $footer_text;

                    // Update the option with the sanitized array including 'footer_text'
                    update_option($wpaicg_key, $sanitized_value);
                } else {
                    // For all other keys, sanitize normally
                    update_option($wpaicg_key, \WPAICG\wpaicg_util_core()->sanitize_text_or_array_field($posted_value));
                }
            }
            else{
                delete_option($wpaicg_key);
            }
        }
        if(isset($_POST['wpaicg_azure_model'])){
            $new_deployment_name = sanitize_text_field($_POST['wpaicg_azure_model']);
            update_option('wpaicg_azure_deployment', $new_deployment_name);
        }
        if(isset($_POST['wpaicg_widget_google_model'])){
            $wpaicg_widget_google_model = sanitize_text_field($_POST['wpaicg_widget_google_model']);
            update_option('wpaicg_widget_google_model', $wpaicg_widget_google_model);
        }
        $message = esc_html__('Setting saved successfully','gpt3-ai-content-generator');
    }
}
wp_enqueue_script('wp-color-picker');
wp_enqueue_style('wp-color-picker');
$wpaicg_custom_models = get_option('wpaicg_custom_models',array());
$table = $wpdb->prefix . 'wpaicg';
$existingValue = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE name = %s", 'wpaicg_settings' ), ARRAY_A );
$wpaicg_chat_temperature = get_option('wpaicg_chat_temperature',$existingValue['temperature']);
$wpaicg_chat_max_tokens = get_option('wpaicg_chat_max_tokens',$existingValue['max_tokens']);
$wpaicg_chat_top_p = get_option('wpaicg_chat_top_p',$existingValue['top_p']);
$wpaicg_chat_best_of = get_option('wpaicg_chat_best_of',$existingValue['best_of']);
$wpaicg_chat_frequency_penalty = get_option('wpaicg_chat_frequency_penalty',$existingValue['frequency_penalty']);
$wpaicg_chat_presence_penalty = get_option('wpaicg_chat_presence_penalty',$existingValue['presence_penalty']);
$wpaicg_chat_widget = get_option('wpaicg_chat_widget',[]);
$wpaicg_chat_icon = isset($wpaicg_chat_widget['icon']) && !empty($wpaicg_chat_widget['icon']) ? $wpaicg_chat_widget['icon'] : 'default';
$wpaicg_chat_status = isset($wpaicg_chat_widget['status']) && !empty($wpaicg_chat_widget['status']) ? $wpaicg_chat_widget['status'] : '';
$wpaicg_chat_fontsize = isset($wpaicg_chat_widget['fontsize']) && !empty($wpaicg_chat_widget['fontsize']) ? $wpaicg_chat_widget['fontsize'] : '13';
$wpaicg_chat_fontcolor = isset($wpaicg_chat_widget['fontcolor']) && !empty($wpaicg_chat_widget['fontcolor']) ? $wpaicg_chat_widget['fontcolor'] : '#fff';
$wpaicg_chat_bgcolor = isset($wpaicg_chat_widget['bgcolor']) && !empty($wpaicg_chat_widget['bgcolor']) ? $wpaicg_chat_widget['bgcolor'] : '#343540';
$wpaicg_bg_text_field = isset($wpaicg_chat_widget['bg_text_field']) && !empty($wpaicg_chat_widget['bg_text_field']) ? $wpaicg_chat_widget['bg_text_field'] : '#fff';
$wpaicg_send_color = isset($wpaicg_chat_widget['send_color']) && !empty($wpaicg_chat_widget['send_color']) ? $wpaicg_chat_widget['send_color'] : '#fff';
$wpaicg_border_text_field = isset($wpaicg_chat_widget['border_text_field']) && !empty($wpaicg_chat_widget['border_text_field']) ? $wpaicg_chat_widget['border_text_field'] : '#ccc';
$wpaicg_footer_text = isset($wpaicg_chat_widget['footer_text']) && !empty($wpaicg_chat_widget['footer_text']) ? $wpaicg_chat_widget['footer_text'] : '';
$wpaicg_user_bg_color = isset($wpaicg_chat_widget['user_bg_color']) && !empty($wpaicg_chat_widget['user_bg_color']) ? $wpaicg_chat_widget['user_bg_color'] : '#444654';
$wpaicg_ai_bg_color = isset($wpaicg_chat_widget['ai_bg_color']) && !empty($wpaicg_chat_widget['ai_bg_color']) ? $wpaicg_chat_widget['ai_bg_color'] : '#343541';
$wpaicg_bar_color = isset($wpaicg_chat_widget['bar_color']) && !empty($wpaicg_chat_widget['bar_color']) ? $wpaicg_chat_widget['bar_color'] : '#fff';
$wpaicg_use_avatar = isset($wpaicg_chat_widget['use_avatar']) && !empty($wpaicg_chat_widget['use_avatar']) ? $wpaicg_chat_widget['use_avatar'] : false;
$wpaicg_ai_avatar = isset($wpaicg_chat_widget['ai_avatar']) && !empty($wpaicg_chat_widget['ai_avatar']) ? $wpaicg_chat_widget['ai_avatar'] : 'default';
$wpaicg_ai_avatar_id = isset($wpaicg_chat_widget['ai_avatar_id']) && !empty($wpaicg_chat_widget['ai_avatar_id']) ? $wpaicg_chat_widget['ai_avatar_id'] : '';
$wpaicg_chat_width = isset($wpaicg_chat_widget['width']) && !empty($wpaicg_chat_widget['width']) ? $wpaicg_chat_widget['width'] : '40%';
$wpaicg_chat_height = isset($wpaicg_chat_widget['height']) && !empty($wpaicg_chat_widget['height']) ? $wpaicg_chat_widget['height'] : '50%';
$wpaicg_chat_position = isset($wpaicg_chat_widget['position']) && !empty($wpaicg_chat_widget['position']) ? $wpaicg_chat_widget['position'] : 'left';
$wpaicg_chat_tone = isset($wpaicg_chat_widget['tone']) && !empty($wpaicg_chat_widget['tone']) ? $wpaicg_chat_widget['tone'] : 'friendly';
$wpaicg_user_aware = isset($wpaicg_chat_widget['user_aware']) && !empty($wpaicg_chat_widget['user_aware']) ? $wpaicg_chat_widget['user_aware'] : 'no';
$wpaicg_chat_proffesion = isset($wpaicg_chat_widget['proffesion']) && !empty($wpaicg_chat_widget['proffesion']) ? $wpaicg_chat_widget['proffesion'] : 'none';
$wpaicg_chat_remember_conversation = isset($wpaicg_chat_widget['remember_conversation']) && !empty($wpaicg_chat_widget['remember_conversation']) ? $wpaicg_chat_widget['remember_conversation'] : 'yes';
$wpaicg_chat_content_aware = isset($wpaicg_chat_widget['content_aware']) && !empty($wpaicg_chat_widget['content_aware']) ? $wpaicg_chat_widget['content_aware'] : 'yes';
$wpaicg_pinecone_api = get_option('wpaicg_pinecone_api','');
$wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment','');
$wpaicg_save_logs = isset($wpaicg_chat_widget['save_logs']) && !empty($wpaicg_chat_widget['save_logs']) ? $wpaicg_chat_widget['save_logs'] : false;
$wpaicg_log_notice = isset($wpaicg_chat_widget['log_notice']) && !empty($wpaicg_chat_widget['log_notice']) ? $wpaicg_chat_widget['log_notice'] : false;
$wpaicg_log_notice_message = isset($wpaicg_chat_widget['log_notice_message']) && !empty($wpaicg_chat_widget['log_notice_message']) ? $wpaicg_chat_widget['log_notice_message'] : esc_html__('Please note that your conversations will be recorded.','gpt3-ai-content-generator');
$wpaicg_conversation_cut = get_option('wpaicg_conversation_cut',10);
$wpaicg_chat_embedding = get_option('wpaicg_chat_embedding',false);
$wpaicg_chat_addition = get_option('wpaicg_chat_addition',false);
$wpaicg_chat_addition_text = get_option('wpaicg_chat_addition_text',false);
$wpaicg_chat_addition_text = str_replace("\\",'',$wpaicg_chat_addition_text);
$wpaicg_chat_embedding_type = get_option('wpaicg_chat_embedding_type',false);
$wpaicg_chat_embedding_top = get_option('wpaicg_chat_embedding_top',false);
$wpaicg_audio_enable = isset($wpaicg_chat_widget['audio_enable']) ? $wpaicg_chat_widget['audio_enable'] : false;
$wpaicg_image_enable = isset($wpaicg_chat_widget['image_enable']) ? $wpaicg_chat_widget['image_enable'] : false;
$wpaicg_mic_color = isset($wpaicg_chat_widget['mic_color']) ? $wpaicg_chat_widget['mic_color'] : '#222';
$wpaicg_stop_color = isset($wpaicg_chat_widget['stop_color']) ? $wpaicg_chat_widget['stop_color'] : '#f00';
$wpaicg_user_limited = isset($wpaicg_chat_widget['user_limited']) ? $wpaicg_chat_widget['user_limited'] : false;
$wpaicg_guest_limited = isset($wpaicg_chat_widget['guest_limited']) ? $wpaicg_chat_widget['guest_limited'] : false;
$wpaicg_user_tokens = isset($wpaicg_chat_widget['user_tokens']) ? $wpaicg_chat_widget['user_tokens'] : 0;
$wpaicg_guest_tokens = isset($wpaicg_chat_widget['guest_tokens']) ? $wpaicg_chat_widget['guest_tokens'] : 0;
$wpaicg_reset_limit = isset($wpaicg_chat_widget['reset_limit']) ? $wpaicg_chat_widget['reset_limit'] : 0;
$wpaicg_limited_message = isset($wpaicg_chat_widget['limited_message']) && !empty($wpaicg_chat_widget['limited_message']) ? $wpaicg_chat_widget['limited_message'] : esc_html__('You have reached your token limit.','gpt3-ai-content-generator');
$wpaicg_include_footer = (isset($wpaicg_chat_widget['footer_text']) && !empty($wpaicg_chat_widget['footer_text'])) ? 5 : 0;
$wpaicg_chat_widget['role_limited'] = isset($wpaicg_chat_widget['role_limited']) && !empty($wpaicg_chat_widget['role_limited']) ? $wpaicg_chat_widget['role_limited'] : false;
$wpaicg_chat_widget['limited_roles'] = isset($wpaicg_chat_widget['limited_roles']) && !empty($wpaicg_chat_widget['limited_roles']) ? $wpaicg_chat_widget['limited_roles'] : array();
$wpaicg_chat_fullscreen = isset($wpaicg_chat_widget['fullscreen']) && !empty($wpaicg_chat_widget['fullscreen']) ? $wpaicg_chat_widget['fullscreen'] : false;
$wpaicg_chat_close_btn = isset($wpaicg_chat_widget['close_btn']) && !empty($wpaicg_chat_widget['close_btn']) ? $wpaicg_chat_widget['close_btn'] : false;
$wpaicg_chat_download_btn = isset($wpaicg_chat_widget['download_btn']) && !empty($wpaicg_chat_widget['download_btn']) ? $wpaicg_chat_widget['download_btn'] : false;
$wpaicg_chat_clear_btn = isset($wpaicg_chat_widget['clear_btn']) && !empty($wpaicg_chat_widget['clear_btn']) ? $wpaicg_chat_widget['clear_btn'] : false;
$wpaicg_thinking_color = isset($wpaicg_chat_widget['thinking_color']) && !empty($wpaicg_chat_widget['thinking_color']) ? $wpaicg_chat_widget['thinking_color'] : '#fff';
$wpaicg_delay_time = isset($wpaicg_chat_widget['delay_time']) && !empty($wpaicg_chat_widget['delay_time']) ? $wpaicg_chat_widget['delay_time'] : '';
$wpaicg_chat_to_speech = isset($wpaicg_chat_widget['chat_to_speech']) ? $wpaicg_chat_widget['chat_to_speech'] : false;
$wpaicg_elevenlabs_voice = isset($wpaicg_chat_widget['elevenlabs_voice']) ? $wpaicg_chat_widget['elevenlabs_voice'] : '';
$wpaicg_elevenlabs_model = isset($wpaicg_chat_widget['elevenlabs_model']) ? $wpaicg_chat_widget['elevenlabs_model'] : '';

$wpaicg_openai_model = isset($wpaicg_chat_widget['openai_model']) && !empty($wpaicg_chat_widget['openai_model']) ? $wpaicg_chat_widget['openai_model'] : 'tts-1';
$wpaicg_openai_voice = isset($wpaicg_chat_widget['openai_voice']) && !empty($wpaicg_chat_widget['openai_voice']) ? $wpaicg_chat_widget['openai_voice'] : 'alloy';
$wpaicg_openai_output_format = isset($wpaicg_chat_widget['openai_output_format']) && !empty($wpaicg_chat_widget['openai_output_format']) ? $wpaicg_chat_widget['openai_output_format'] : 'mp3';
$wpaicg_openai_voice_speed = isset($wpaicg_chat_widget['openai_voice_speed']) && !empty($wpaicg_chat_widget['openai_voice_speed']) ? $wpaicg_chat_widget['openai_voice_speed'] : '1.0';

$wpaicg_text_height = isset($wpaicg_chat_widget['text_height']) && !empty($wpaicg_chat_widget['text_height']) ? $wpaicg_chat_widget['text_height'] : 40;
$wpaicg_text_rounded = isset($wpaicg_chat_widget['text_rounded']) && !empty($wpaicg_chat_widget['text_height']) ? $wpaicg_chat_widget['text_rounded'] : 8;
$wpaicg_chat_rounded = isset($wpaicg_chat_widget['chat_rounded']) && !empty($wpaicg_chat_widget['text_height']) ? $wpaicg_chat_widget['chat_rounded'] : 8;
$wpaicg_elevenlabs_api = get_option('wpaicg_elevenlabs_api', '');
$wpaicg_chat_voice_service = isset($wpaicg_chat_widget['voice_service']) ? $wpaicg_chat_widget['voice_service'] : '';
$wpaicg_google_voices = get_option('wpaicg_google_voices',[]);
$wpaicg_roles = wp_roles()->get_names();
$wpaicg_google_api_key = get_option('wpaicg_google_api_key', '');
$wpaicg_pinecone_indexes = get_option('wpaicg_pinecone_indexes','');
$wpaicg_stream_nav_setting = get_option('wpaicg_widget_stream', true);
$wpaicg_pinecone_indexes = empty($wpaicg_pinecone_indexes) ? array() : json_decode($wpaicg_pinecone_indexes,true);
$wpaicg_chat_vectordb = get_option('wpaicg_chat_vectordb','pinecone');
$wpaicg_qdrant_collections = get_option('wpaicg_qdrant_collections',[]);
$wpaicg_qdrant_collections = empty($wpaicg_qdrant_collections) ? array() : $wpaicg_qdrant_collections;
// Retrieve the currently selected Qdrant collection
$wpaicg_widget_qdrant_collection = get_option('wpaicg_widget_qdrant_collection', '');
?>
<style>
    .asdisabled{
        background: #ebebeb!important;
    }
    .wpaicg_chatbox_avatar{
        cursor: pointer;
    }
    .wp-picker-holder{
        position: absolute;
    }
    .wpaicg_chatbox_icon{
        cursor: pointer;
    }
    .wpaicg_chatbox_icon svg{
    }
    .wpaicg_chat_widget_content .wpaicg-chatbox{
        height: 100%;
        background-color: <?php echo esc_html($wpaicg_chat_bgcolor)?>;
        border-radius: 5px;
    }
    .wpaicg_widget_open .wpaicg_chat_widget_content{
        height: <?php echo esc_html($wpaicg_chat_height)?>px;
    }
    .wpaicg_chat_widget_content{
        position: absolute;
        bottom: calc(100% + 15px);
        width: <?php echo esc_html($wpaicg_chat_width)?>px;

    }
    .wpaicg-collapse-content textarea{
        display: inline-block!important;
        width: 48%!important;
    }
    .wpaicg_widget_open .wpaicg_chat_widget_content .wpaicg-chatbox{
        top: 0;
    }
    .wpaicg_chat_widget_content .wpaicg-chatbox{
        position: absolute;
        top: 100%;
        left: 0;
        width: <?php echo esc_html($wpaicg_chat_width)?>px;
        height: <?php echo esc_html($wpaicg_chat_height)?>px;
        transition: top 300ms cubic-bezier(0.17, 0.04, 0.03, 0.94);
    }
    .wpaicg_chat_widget_content .wpaicg-chatbox-content ul{
        box-sizing: border-box;
        background: <?php echo esc_html($wpaicg_chat_bgcolor)?>;
    }
    .wpaicg_chat_widget_content .wpaicg-chatbox-content ul li{
        color: <?php echo esc_html($wpaicg_chat_fontcolor)?>;
        font-size: <?php echo esc_html($wpaicg_chat_fontsize)?>px;
    }
    .wpaicg_chat_widget_content .wpaicg-bot-thinking{
        color: <?php echo esc_html($wpaicg_chat_fontcolor)?>;
    }
    .wpaicg_chat_widget_content .wpaicg-chatbox-type{
    <?php
    if($wpaicg_include_footer):
    ?>
        padding: 5px 5px 0 5px;
    <?php
    endif;
    ?>
        border-top: 0;
        background: rgb(0 0 0 / 19%);
    }
    .wpaicg_chat_widget_content .wpaicg-chat-message{
        color: <?php echo esc_html($wpaicg_chat_fontcolor)?>;
    }
    .wpaicg_chat_widget_content textarea.wpaicg-chatbox-typing{
        background-color: <?php echo esc_html($wpaicg_bg_text_field)?>;
        border-color: <?php echo esc_html($wpaicg_border_text_field)?>;
    }
    .wpaicg_chat_widget_content .wpaicg-chatbox-send{
        color: <?php echo esc_html($wpaicg_send_color)?>;
    }
    .wpaicg-chatbox-footer{
        height: 18px;
        font-size: 11px;
        padding: 0 5px;
        color: <?php echo esc_html($wpaicg_send_color)?>;
        background: rgb(0 0 0 / 19%);
        margin-top:2px;
        margin-bottom: 2px;
    }
    /* inherit for hyperlink */
    .wpaicg-chatbox-footer a{
        color: inherit;
    }
    .wpaicg_chat_widget_content textarea.wpaicg-chatbox-typing:focus{
        outline: none;
    }
    .wpaicg_chat_widget .wpaicg_toggle{
        cursor: pointer;
    }
    .wpaicg_chat_widget .wpaicg_toggle img{
        width: 75px;
        height: 75px;
    }
    .wpaicg-chat-shortcode-type,.wpaicg-chatbox-type{
        position: relative;
    }
    .wpaicg-mic-icon{
        cursor: pointer;
    }
    .wp-picker-input-wrap input[type=text]{
        width: 4rem!important;
    }
    .wpaicg-mic-icon svg{
        width: 16px;
        height: 16px;
        fill: currentColor;
    }
    .wpaicg-img-icon{
        cursor: pointer;
    }
    .wpaicg-img-icon svg{
        width: 16px;
        height: 16px;
        fill: currentColor;
    }
    .wpaicg-chatbox-preview{
        position: relative;
    }
    /*.wpaicg_chat_widget_content{*/
    /*    position: relative;*/
    /*    overflow: unset;*/
    /*}*/
    /*.wpaicg_chat_widget_content .wpaicg-chatbox{*/
    /*    position: relative;*/
    /*}*/
    .wpaicg_toggle{}
    .wpaicg_chat_widget{
        position: absolute;
        bottom: 0;
    }
    .wpaicg_chat_widget_content .wpaicg-chat-ai-message *, .wpaicg_chat_widget_content .wpaicg-chat-user-message *, .wpaicg_chat_widget_content .wpaicg-chat-user-message .wpaicg-chat-message, .wpaicg_chat_widget_content .wpaicg-chat-ai-message .wpaicg-chat-message, .wpaicg_chat_widget_content .wpaicg-chat-ai-message a, .wpaicg_chat_widget_content .wpaicg-chat-user-message a{
        color: inherit!important;
        font-size: inherit!important;
    }
    .wpaicg_chat_widget_content{
        overflow: hidden;
    }
    .wpaicg_widget_open .wpaicg_chat_widget_content{
        overflow: unset;
    }
    .wpaicg-notice {
    color: #d9534f;
    padding: 5px;
    border: 1px solid #d9534f;
    border-radius: 3px;
}
</style>
<div id="exportMessage" style="display: none;" class="notice notice-success"></div>
<?php
$wpaicg_chat_model = get_option('wpaicg_chat_model','');
$wpaicg_provider = get_option('wpaicg_provider', 'OpenAI');
$wpaicg_chat_language = get_option('wpaicg_chat_language','');
if ( !empty($errors)) {
    echo  "<h4 id='setting_message' style='color: red;'>" . esc_html( $errors ) . "</h4>" ;
} elseif(!empty($message)) {
    echo  "<h4 id='setting_message' style='color: green;'>" . esc_html( $message ) . "</h4>" ;
}
?>
<?php
$language_options = [
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
];
$tone_options = [
    'friendly' => esc_html__('Friendly', 'gpt3-ai-content-generator'),
    'professional' => esc_html__('Professional', 'gpt3-ai-content-generator'),
    'sarcastic' => esc_html__('Sarcastic', 'gpt3-ai-content-generator'),
    'humorous' => esc_html__('Humorous', 'gpt3-ai-content-generator'),
    'cheerful' => esc_html__('Cheerful', 'gpt3-ai-content-generator'),
    'anecdotal' => esc_html__('Anecdotal', 'gpt3-ai-content-generator'),
];
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
            <?php wp_nonce_field('wpaicg_chat_widget_save'); ?>
            <!-- AI SETTINGS -->
            <section id="aisettings" class="tab-content">
                <h1>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tool">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
                    </svg>
                    Settings
                </h1>
                <div class="nice-form-group">
                    <label for="wpaicg_chat_widget_activate"><?php echo esc_html__('Enable Widget','gpt3-ai-content-generator')?></label>
                    <select name="wpaicg_chat_widget[status]" id="wpaicg_chat_widget_activate">
                        <option value="">No</option>
                        <option <?php echo $wpaicg_chat_status == 'active' ? ' selected': ''?> value="active">Yes</option>
                    </select>
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_chat_model"><?php echo esc_html__('Model', 'gpt3-ai-content-generator'); ?></label>
                    <?php if ($wpaicg_provider === 'Azure'): ?>
                        <?php $azure_model = get_option('wpaicg_azure_deployment', ''); ?>
                        <input type="text" id="wpaicg_azure_model" name="wpaicg_azure_model" value="<?php echo esc_attr($azure_model); ?>" placeholder="<?php echo esc_attr__('Enter Azure Deployment Name', 'gpt3-ai-content-generator'); ?>">
                        <!-- else if google -->
                        <?php elseif ($wpaicg_provider === 'Google'): ?>
                            <?php $google_models = ['gemini-pro' => 'Gemini Pro']; ?>
                                <select id="wpaicg_widget_google_model" name="wpaicg_widget_google_model">
                                    <?php
                                    $wpaicg_widget_google_model = get_option('wpaicg_widget_google_model', 'gemini-pro');
                                    ?>
                                    <?php
                                    foreach ($google_models as $model_key => $model_name): ?>
                                        <option value="<?php echo esc_attr($model_key); ?>"<?php selected($model_key, $wpaicg_widget_google_model); ?>><?php echo esc_html($model_name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                    <?php else: ?>
                        <?php
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
                    <select id="wpaicg_chat_model" name="wpaicg_chat_model">
                        <?php // Function to display options
                        function display_options($models, $selected_model){
                            foreach ($models as $model_key => $model_name): ?>
                                <option value="<?php echo esc_attr($model_key); ?>" <?php selected($model_key, $selected_model); ?>><?php echo esc_html($model_name); ?></option>
                            <?php endforeach;
                        }
                        function display_custom_model_options($custom_models, $selected_model){
                            foreach ($custom_models as $model_identifier) {
                                ?>
                                <option value="<?php echo esc_attr($model_identifier); ?>" <?php selected($model_identifier, $selected_model); ?>><?php echo esc_html($model_identifier); ?></option>
                                <?php
                            }
                        }
                        ?>
                        <optgroup label="GPT-4">
                            <?php display_options($gpt4_models, $wpaicg_chat_model); ?>
                        </optgroup>
                        <optgroup label="GPT-3.5">
                            <?php display_options($gpt35_models, $wpaicg_chat_model); ?>
                        </optgroup>
                        <optgroup label="Custom Models">
                            <?php display_custom_model_options($custom_models, $wpaicg_chat_model); ?>
                        </optgroup>
                    </select>
                    <?php endif; ?>
                </div>
                <!-- Streaming and Image Upload Options -->
                <fieldset class="nice-form-group">
                <legend><?php echo esc_html__('Options', 'gpt3-ai-content-generator'); ?></legend>
                    <div class="nice-form-group">
                        <input <?php echo $wpaicg_stream_nav_setting === '1' ? ' checked' : ''; ?> value="1" type="checkbox" name="wpaicg_stream_nav_option" id="wpaicg_stream_nav_option">
                        <label for="wpaicg_stream_nav_option"><?php echo esc_html__('Streaming','gpt3-ai-content-generator')?></label>
                    </div>
                    <div class="nice-form-group">
                        <input <?php echo $wpaicg_image_enable ? ' checked' : '' ?> value="1" class="wpaicg_chat_widget_image" type="checkbox" name="wpaicg_chat_widget[image_enable]" id="wpaicg_chat_widget_image">
                        <label for="wpaicg_chat_widget_image"><?php echo esc_html__('Image Upload', 'gpt3-ai-content-generator'); ?></label>
                    </div>
                    <?php
                    if(!isset($wpaicg_chat_widget['chat_addition_option']) || $wpaicg_chat_addition){
                        $wpaicg_chat_addition = true;
                    }
                    ?>
                    <div class="nice-form-group">
                        <input <?php echo $wpaicg_chat_addition == '1' ? ' checked': ''?> name="wpaicg_chat_addition" value="1" type="checkbox" id="wpaicg_chat_addition">
                        <label for="wpaicg_chat_addition"><?php echo esc_html__('Instructions','gpt3-ai-content-generator')?></label>
                        <input name="wpaicg_chat_widget[chat_addition_option]" value="<?php echo $wpaicg_chat_addition ? 0 : 1?>" type="hidden" id="wpaicg_chat_addition_option">
                    </div>
                </fieldset>
                <!-- Instructions -->
                <?php
                    $wpaicg_additions_json = file_get_contents(WPAICG_PLUGIN_DIR.'admin/chat/context.json');
                    $wpaicg_additions = json_decode($wpaicg_additions_json, true);
                    ?>
                <div class="nice-form-group">
                    <label for="wpaicg_chat_addition_template"><?php echo esc_html__('Instructions','gpt3-ai-content-generator')?></label>
                    <select <?php echo !$wpaicg_chat_addition ? ' disabled':'';?> class="wpaicg_chat_addition_template" id="wpaicg_chat_addition_template">
                        <option value=""><?php echo esc_html__('Select Template','gpt3-ai-content-generator')?></option>
                        <?php
                        foreach($wpaicg_additions as $key=>$wpaicg_addition){
                            echo '<option value="'.esc_html($wpaicg_addition).'">'.esc_html($key).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="nice-form-group">
                    <textarea <?php echo !$wpaicg_chat_addition ? ' disabled':''?> class="wpaicg_chat_addition_text" rows="8" id="wpaicg_chat_addition_text" name="wpaicg_chat_addition_text"><?php echo !empty($wpaicg_chat_addition_text) ? esc_html($wpaicg_chat_addition_text) : esc_html__('You are a helpful AI Assistant. Please be friendly.','gpt3-ai-content-generator')?></textarea>
                </div>
                <p></p>
                <!-- Advanced Parameters -->
                <a href="#" id="wpaicg-advanced-settings-link"><?php echo esc_html__('Show Advanced Parameters','gpt3-ai-content-generator'); ?></a>
                <div id="wpaicg-advanced-settings" style="display: none;">
                    <div class="nice-form-group">
                        <label for="label_temperature"><?php echo esc_html__('Temperature','gpt3-ai-content-generator')?></label>
                        <input type="text" id="label_temperature" name="wpaicg_chat_temperature" value="<?php echo esc_html( $wpaicg_chat_temperature ) ; ?>">
                    </div>
                    <div class="nice-form-group">
                        <label for="label_max_tokens"><?php echo esc_html__('Max Tokens','gpt3-ai-content-generator')?></label>
                        <input type="text" id="label_max_tokens" name="wpaicg_chat_max_tokens" value="<?php echo esc_html( $wpaicg_chat_max_tokens ) ; ?>" >
                    </div>
                    <div class="nice-form-group">
                        <label for="label_top_p"><?php echo esc_html__('Top P','gpt3-ai-content-generator')?></label>
                        <input type="text" id="label_top_p" name="wpaicg_chat_top_p" value="<?php echo esc_html( $wpaicg_chat_top_p ) ; ?>" >
                    </div>
                    <div class="nice-form-group">
                        <input type="hidden" id="label_best_of" name="wpaicg_chat_best_of" value="<?php echo esc_html( $wpaicg_chat_best_of ) ; ?>" >
                    </div>
                    <div class="nice-form-group">
                        <label for="label_frequency_penalty"><?php echo esc_html__('Frequency Penalty','gpt3-ai-content-generator')?></label>
                        <input type="text" id="label_frequency_penalty" name="wpaicg_chat_frequency_penalty" value="<?php echo esc_html( $wpaicg_chat_frequency_penalty ) ; ?>" >
                    </div>
                    <div class="nice-form-group">
                        <label for="label_presence_penalty"><?php echo esc_html__('Presence Penalty','gpt3-ai-content-generator')?></label>
                        <input type="text" id="label_presence_penalty" name="wpaicg_chat_presence_penalty" value="<?php echo esc_html( $wpaicg_chat_presence_penalty ) ; ?>" >
                    </div>
                    <div class="nice-form-group">
                        <a class="wpaicg_sync_finetune" href="javascript:void(0)"><?php echo esc_html__('Sync Models', 'gpt3-ai-content-generator'); ?></a>
                    </div>
                </div>
                <details>
                    <summary>
                        <button class="button button-primary wpaicg-w-25" name="wpaicg_submit"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
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
                    <label><?php echo esc_html__('Widget Icon (75x75)','gpt3-ai-content-generator')?></label>
                    <div style="display: inline-flex; align-items: center">
                        <input <?php echo $wpaicg_chat_icon == 'default' ? ' checked': ''?> class="wpaicg_chatbox_icon_default" type="radio" value="default" name="wpaicg_chat_widget[icon]">
                        <div>
                            <img style="display: block" width="40" height="40" src="<?php echo esc_html(WPAICG_PLUGIN_URL).'admin/images/chatbot.png'?>">
                            <label><?php echo esc_html__('Default','gpt3-ai-content-generator')?></label>
                        </div>
                        <input <?php echo $wpaicg_chat_icon == 'custom' ? ' checked': ''?> type="radio" class="wpaicg_chatbox_icon_custom" value="custom" name="wpaicg_chat_widget[icon]">
                        <div>
                            <div class="wpaicg_chatbox_icon">
                                <?php
                                $wpaicg_chat_icon_url = isset($wpaicg_chat_widget['icon_url']) && !empty($wpaicg_chat_widget['icon_url']) ? $wpaicg_chat_widget['icon_url'] : '';
                                if(!empty($wpaicg_chat_icon_url) && $wpaicg_chat_icon == 'custom'):
                                    $wpaicg_chatbox_icon_url = wp_get_attachment_url($wpaicg_chat_icon_url);
                                    ?>
                                    <img src="<?php echo esc_html($wpaicg_chatbox_icon_url)?>" width="40" height="40">
                                <?php else: ?>
                                    <svg width="40px" height="40px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M246.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 109.3V320c0 17.7 14.3 32 32 32s32-14.3 32-32V109.3l73.4 73.4c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-128-128zM64 352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64c0 53 43 96 96 96H352c53 0 96-43 96-96V352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V352z"/></svg><br>
                                <?php endif; ?>
                            </div>
                            <label><?php echo esc_html__('Custom','gpt3-ai-content-generator')?></label>
                        </div>
                    </div>
                </div>
                <fieldset class="nice-form-group">
                <legend><?php echo esc_html__('Widget Position', 'gpt3-ai-content-generator'); ?></legend>
                    <div class="nice-form-group">
                        <input <?php echo $wpaicg_chat_position == 'left' ? ' checked': ''?> type="radio" value="left" name="wpaicg_chat_widget[position]">
                        <label><?php echo esc_html__('Left','gpt3-ai-content-generator')?></label>
                    </div>
                    <div class="nice-form-group">
                        <input <?php echo $wpaicg_chat_position == 'right' ? ' checked': ''?> type="radio" value="right" name="wpaicg_chat_widget[position]">
                        <label><?php echo esc_html__('Right','gpt3-ai-content-generator')?></label>
                    </div>
                </fieldset>

                <div class="nice-form-group">
                    <label><?php echo esc_html__('Display After','gpt3-ai-content-generator')?></label>
                    <input placeholder="<?php echo esc_html__('in seconds. eg. 5','gpt3-ai-content-generator')?>" value="<?php echo esc_html($wpaicg_delay_time)?>" type="text" class="wpaicgchat_delay_time" name="wpaicg_chat_widget[delay_time]">
                </div>
                <div class="nice-form-group">
                    <input <?php echo $wpaicg_use_avatar ? ' checked':''?> value="1" type="checkbox" class="wpaicgchat_use_avatar" name="wpaicg_chat_widget[use_avatar]">
                    <label><?php echo esc_html__('Use Avatar (40x40)','gpt3-ai-content-generator')?></label>
                </div>
                <div class="nice-form-group">
                    <input value="<?php echo esc_html($wpaicg_chat_icon_url)?>" type="hidden" name="wpaicg_chat_widget[icon_url]" class="wpaicg_chat_icon_url">
                    <input value="<?php echo esc_html($wpaicg_ai_avatar_id)?>" type="hidden" name="wpaicg_chat_widget[ai_avatar_id]" class="wpaicg_ai_avatar_id">
                    <div style="display: inline-flex; align-items: center">
                        <input <?php echo $wpaicg_ai_avatar == 'default' ? ' checked': ''?> class="wpaicg_chatbox_avatar_default" type="radio" value="default" name="wpaicg_chat_widget[ai_avatar]">
                        <div>
                            <img style="display: block;width: 40px; height: 40px" src="<?php echo esc_html(WPAICG_PLUGIN_URL).'admin/images/chatbot.png'?>">
                            <label><?php echo esc_html__('Default','gpt3-ai-content-generator')?></label>
                        </div>
                        <input <?php echo $wpaicg_ai_avatar == 'custom' ? ' checked': ''?> type="radio" class="wpaicg_chatbox_avatar_custom" value="custom" name="wpaicg_chat_widget[ai_avatar]">
                        <div>
                            <div class="wpaicg_chatbox_avatar">
                                <?php
                                if(!empty($wpaicg_ai_avatar_id) && $wpaicg_ai_avatar == 'custom'):
                                    $wpaicg_ai_avatar_url = wp_get_attachment_url($wpaicg_ai_avatar_id);
                                    ?>
                                    <img src="<?php echo esc_html($wpaicg_ai_avatar_url)?>" width="40" height="40">
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
                        <input <?php echo $wpaicg_chat_fullscreen ? ' checked':''?> value="1" type="checkbox" class="wpaicgchat_fullscreen" name="wpaicg_chat_widget[fullscreen]">
                        <label><?php echo esc_html__('Fullscreen','gpt3-ai-content-generator')?></label>
                    </div>
                    <div class="nice-form-group">
                        <input <?php echo $wpaicg_chat_download_btn ? ' checked':''?> value="1" type="checkbox" class="wpaicgchat_download_btn" name="wpaicg_chat_widget[download_btn]">
                        <label><?php echo esc_html__('Download','gpt3-ai-content-generator')?></label>
                    </div>
                    <div class="nice-form-group">
                        <input <?php echo $wpaicg_chat_close_btn ? ' checked':''?> value="1" type="checkbox" class="wpaicgchat_close_btn" name="wpaicg_chat_widget[close_btn]">
                        <label><?php echo esc_html__('Close','gpt3-ai-content-generator')?></label>
                    </div>
                    <div class="nice-form-group">
                        <input <?php echo $wpaicg_chat_clear_btn ? ' checked':''?> value="1" type="checkbox" class="wpaicgchat_clear_btn" name="wpaicg_chat_widget[clear_btn]">
                        <label><?php echo esc_html__('Clear','gpt3-ai-content-generator')?></label>
                    </div>
                </fieldset>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Icon Color','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_bar_color)?>" type="text" class="wpaicgchat_color wpaicgchat_bar_color" name="wpaicg_chat_widget[bar_color]">
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('AI Name','gpt3-ai-content-generator')?></label>
                    <input type="text" name="_wpaicg_chatbox_ai_name" value="<?php echo esc_html( get_option( '_wpaicg_chatbox_ai_name', 'AI' ) ) ; ?>" >
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('User Name','gpt3-ai-content-generator')?></label>
                    <input type="text" name="_wpaicg_chatbox_you" value="<?php echo esc_html( get_option( '_wpaicg_chatbox_you', __('You','gpt3-ai-content-generator') ) ) ;?>" >
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Response Wait Message','gpt3-ai-content-generator')?></label>
                    <input type="text" name="_wpaicg_ai_thinking" value="<?php echo esc_html( get_option( '_wpaicg_ai_thinking', __('AI thinking','gpt3-ai-content-generator') ) ) ;?>" >
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Placeholder','gpt3-ai-content-generator')?></label>
                    <input type="text" name="_wpaicg_typing_placeholder" value="<?php echo esc_html( get_option( '_wpaicg_typing_placeholder', __('Type a message','gpt3-ai-content-generator') ) ) ;?>" >
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Welcome Message','gpt3-ai-content-generator')?></label>
                    <input type="text" name="_wpaicg_chatbox_welcome_message" value="<?php echo esc_html( get_option( '_wpaicg_chatbox_welcome_message', __('Hello, I am an AI bot. Ask me anything!','gpt3-ai-content-generator') ) ) ;?>" >
                </div>
                <div class="nice-form-group">
                    <?php $wpaicg_chat_no_answer = get_option('wpaicg_chat_no_answer','')?>
                    <input type="hidden" value="<?php echo esc_html($wpaicg_chat_no_answer)?>" name="wpaicg_chat_no_answer">
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Footer Note','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo wp_kses_post($wpaicg_footer_text)?>" class="wpaicg-footer-note" type="text" name="wpaicg_chat_widget[footer_text]" placeholder="<?php echo esc_html__('Powered by ...','gpt3-ai-content-generator')?>">
                </div>
                <details>
                    <summary>
                        <button class="button button-primary wpaicg-w-25" name="wpaicg_submit"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
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
                    <label for="remember_conversation_label"><?php echo esc_html__('Conversational Memory','gpt3-ai-content-generator')?></label>
                    <select name="wpaicg_chat_widget[remember_conversation]" id="remember_conversation_label">
                        <option <?php echo $wpaicg_chat_remember_conversation == 'yes' ? ' selected': ''?> value="yes"><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                        <option <?php echo $wpaicg_chat_remember_conversation == 'no' ? ' selected': ''?> value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                    </select>
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_chat_content_aware"><?php echo esc_html__('Content Aware','gpt3-ai-content-generator')?></label>
                    <select name="wpaicg_chat_widget[content_aware]" id="wpaicg_chat_content_aware">
                        <option <?php echo $wpaicg_chat_content_aware == 'yes' ? ' selected': ''?> value="yes"><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                        <option <?php echo $wpaicg_chat_content_aware == 'no' ? ' selected': ''?> value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                    </select>
                </div>
                <fieldset class="nice-form-group">
                <legend><?php echo esc_html__('Data Source', 'gpt3-ai-content-generator'); ?></legend>
                    <div class="nice-form-group">
                        <input <?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? ' checked': ''?><?php echo $wpaicg_chat_content_aware == 'no' ? ' disabled':''?> type="checkbox" id="wpaicg_chat_excerpt" class="<?php echo $wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : ''?>">
                        <label for="wpaicg_chat_excerpt"><?php echo esc_html__('Excerpt','gpt3-ai-content-generator')?></label>
                    </div>
                    <div class="nice-form-group">
                        <input <?php echo $wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? ' checked': ''?><?php echo $wpaicg_chat_content_aware == 'no' ? ' disabled':''?> type="checkbox" value="1" name="wpaicg_chat_embedding" id="wpaicg_chat_embedding" class="<?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : ''?>">
                        <label for="wpaicg_chat_embedding"><?php echo esc_html__('Embeddings','gpt3-ai-content-generator')?></label>
                    </div>
                </fieldset>
                <div class="nice-form-group">
                    <label for="wpaicg_chat_vectordb"><?php echo esc_html__('Vector DB','gpt3-ai-content-generator')?></label>
                    <select <?php echo empty($wpaicg_chat_embedding) || $wpaicg_chat_content_aware == 'no' ? ' disabled':''?> name="wpaicg_chat_vectordb" id="wpaicg_chat_vectordb" class="<?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : ''?>">
                        <option value="pinecone" <?php selected($wpaicg_chat_vectordb, 'pinecone'); ?>><?php echo esc_html__('Pinecone','gpt3-ai-content-generator')?></option>
                        <option value="qdrant" <?php selected($wpaicg_chat_vectordb, 'qdrant'); ?>><?php echo esc_html__('Qdrant','gpt3-ai-content-generator')?></option>
                    </select>
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_chat_embedding_index"><?php echo esc_html__('Pinecone Index','gpt3-ai-content-generator')?></label>
                    <select <?php echo empty($wpaicg_chat_embedding) || $wpaicg_chat_content_aware == 'no' ? ' disabled':''?> name="wpaicg_chat_widget[embedding_index]" id="wpaicg_chat_embedding_index" class="<?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : ''?>">
                        <option value=""><?php echo esc_html__('Default','gpt3-ai-content-generator')?></option>
                        <?php
                        foreach($wpaicg_pinecone_indexes as $wpaicg_pinecone_index){
                            echo '<option'.(isset($wpaicg_chat_widget['embedding_index']) && $wpaicg_chat_widget['embedding_index'] == $wpaicg_pinecone_index['url'] ? ' selected':'').' value="'.esc_html($wpaicg_pinecone_index['url']).'">'.esc_html($wpaicg_pinecone_index['name']).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_chat_qdrant_collection"><?php echo esc_html__('Qdrant Collection', 'gpt3-ai-content-generator'); ?></label>
                    <select <?php echo empty($wpaicg_chat_embedding) || $wpaicg_chat_content_aware == 'no' ? ' disabled' : '' ?> name="wpaicg_widget_qdrant_collection" id="wpaicg_chat_qdrant_collection" class="<?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : '' ?>">
                        <?php foreach ($wpaicg_qdrant_collections as $collection) {
                            $selected = ($wpaicg_widget_qdrant_collection === $collection) ? ' selected' : '';
                            echo '<option value="' . esc_attr($collection) . '"' . $selected . '>' . esc_html($collection) . '</option>';
                        } ?>
                    </select>
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_chat_embedding_top"><?php echo esc_html__('Limit','gpt3-ai-content-generator')?>:</label>
                        <select <?php echo empty($wpaicg_chat_embedding) || $wpaicg_chat_content_aware == 'no' ? ' disabled':''?> name="wpaicg_chat_embedding_top" id="wpaicg_chat_embedding_top" class="<?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : ''?>">
                            <?php
                            for($i = 1; $i <=5;$i++){
                                echo '<option'.($wpaicg_chat_embedding_top == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                            }
                            ?>
                        </select>
                </div>
                <?php if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()): ?>
                <div class="nice-form-group">
                    <input <?php echo empty($wpaicg_chat_embedding) || $wpaicg_chat_content_aware == 'no' ? ' disabled':''?><?php echo isset($wpaicg_chat_widget['embedding_pdf']) && $wpaicg_chat_widget['embedding_pdf'] ? ' checked':''?> type="checkbox" value="1" name="wpaicg_chat_widget[embedding_pdf]" class="<?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : ''?>" id="wpaicg_chat_embedding_pdf">
                    <label for="wpaicg_chat_embedding_pdf"><?php echo esc_html__('Enable PDF Upload','gpt3-ai-content-generator')?></label>
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_chat_pdf_pages"><?php echo esc_html__('PDF Page Limit','gpt3-ai-content-generator')?></label>
                    <select <?php echo empty($wpaicg_chat_embedding) || $wpaicg_chat_content_aware == 'no' ? ' disabled':''?> name="wpaicg_chat_widget[pdf_pages]" id="wpaicg_chat_pdf_pages" class="<?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : ''?>" style="width: 65px!important;">
                        <?php
                        $pdf_pages = isset($wpaicg_chat_widget['pdf_pages']) && !empty($wpaicg_chat_widget['pdf_pages']) ? $wpaicg_chat_widget['pdf_pages'] : 120;
                        for($i=1;$i <= 120;$i++){
                            echo '<option'.($pdf_pages == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_chat_embedding_pdf_message" style="vertical-align:top"><?php echo esc_html__('PDF Upload Confirmation Message','gpt3-ai-content-generator')?></label>
                    <textarea <?php echo empty($wpaicg_chat_embedding) || $wpaicg_chat_content_aware == 'no' ? ' disabled':''?> rows="8" name="wpaicg_chat_widget[embedding_pdf_message]" class="<?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : ''?>" id="wpaicg_chat_embedding_pdf_message"><?php echo isset($wpaicg_chat_widget['embedding_pdf_message']) && !empty($wpaicg_chat_widget['embedding_pdf_message']) ? esc_html(str_replace("\\",'',$wpaicg_chat_widget['embedding_pdf_message'])):"Congrats! Your PDF is uploaded now! You can ask questions about your document.\nExample Questions:[questions]"?></textarea>
                </div>
                <div class="nice-form-group">
                    <label for="wpaicg_pdf_color"><?php echo esc_html__('PDF Icon Color','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo isset($wpaicg_chat_widget['pdf_color']) ? esc_html($wpaicg_chat_widget['pdf_color']): '#222'?>" type="text" class="wpaicgchat_color wpaicg_pdf_color" name="wpaicg_chat_widget[pdf_color]" id="wpaicg_pdf_color">
                </div>
                <?php else: ?>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Enable PDF Upload','gpt3-ai-content-generator')?></label>
                    <input type="checkbox" disabled> <?php echo esc_html__('Available in Pro','gpt3-ai-content-generator')?>
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('PDF Page Limit','gpt3-ai-content-generator')?></label>
                    <select disabled>
                        <option><?php echo esc_html__('Available in Pro','gpt3-ai-content-generator')?></option>
                    </select>
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('PDF Upload Confirmation Message','gpt3-ai-content-generator')?></label>
                    <textarea disabled rows="8" ><?php echo esc_html__('Available in Pro','gpt3-ai-content-generator')?></textarea>
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('PDF Icon Color','gpt3-ai-content-generator')?></label>
                    <input disabled type="text" placeholder="<?php echo esc_html__('Available in Pro','gpt3-ai-content-generator')?>">
                </div>
                <?php endif; ?>
                <!-- Advanced Parameters --> 
                <p></p>
                <a href="#" id="wpaicg-advanced-content-settings-link"><?php echo esc_html__('Show Additional Options','gpt3-ai-content-generator'); ?></a>
                <div id="wpaicg-advanced-content-settings" style="display: none;">
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('User Aware','gpt3-ai-content-generator')?></label>
                        <select name="wpaicg_chat_widget[user_aware]">
                            <option <?php echo $wpaicg_user_aware == 'no' ? ' selected': ''?> value="no"><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                            <option <?php echo $wpaicg_user_aware == 'yes' ? ' selected': ''?> value="yes"><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Embedding Type','gpt3-ai-content-generator')?></label>
                        <select <?php echo empty($wpaicg_chat_embedding) || $wpaicg_chat_content_aware == 'no' ? ' disabled':''?> name="wpaicg_chat_embedding_type" id="wpaicg_chat_embedding_type" class="<?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : ''?>">
                            <option <?php echo $wpaicg_chat_embedding_type ? ' selected':'';?> value="openai"><?php echo esc_html__('Conversational','gpt3-ai-content-generator')?></option>
                            <option <?php echo empty($wpaicg_chat_embedding_type) ? ' selected':''?> value=""><?php echo esc_html__('Non-Conversational','gpt3-ai-content-generator')?></option>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Memory Limit','gpt3-ai-content-generator')?></label>
                        <select name="wpaicg_conversation_cut">
                            <?php
                            for($i=3;$i<=50;$i++){
                                echo '<option'.($wpaicg_conversation_cut == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Language','gpt3-ai-content-generator')?></label>
                        <select class="wpaicg-input" id="label_wpai_language" name="wpaicg_chat_language">
                            <?php
                                foreach ($language_options as $code => $name) {
                                    $selected = esc_html($wpaicg_chat_language) == $code ? 'selected' : '';
                                    echo "<option value=\"$code\" $selected>$name</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Tone','gpt3-ai-content-generator')?></label>
                        <select name="wpaicg_chat_widget[tone]">
                            <?php
                                foreach ($tone_options as $code => $name) {
                                    $selected = esc_html($wpaicg_chat_tone) == $code ? 'selected' : '';
                                    echo "<option value=\"$code\" $selected>$name</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Act As','gpt3-ai-content-generator')?></label>
                        <select name="wpaicg_chat_widget[proffesion]">
                            <?php
                                foreach ($profession_options as $code => $name) {
                                    $selected = esc_html($wpaicg_chat_proffesion) == $code ? 'selected' : '';
                                    echo "<option value=\"$code\" $selected>$name</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <details>
                    <summary>
                        <button class="button button-primary wpaicg-w-25" name="wpaicg_submit"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
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
                if ($wpaicg_provider !== 'Azure' && $wpaicg_provider !== 'Google') {
                    $wpaicg_audio_enable = isset($wpaicg_chat_widget['audio_enable']) && $wpaicg_chat_widget['audio_enable'];
                    $wpaicg_mic_color = isset($wpaicg_chat_widget['mic_color']) ? esc_html($wpaicg_chat_widget['mic_color']) : '';
                    $wpaicg_stop_color = isset($wpaicg_chat_widget['stop_color']) ? esc_html($wpaicg_chat_widget['stop_color']) : '';
                ?>
                <div class="nice-form-group">
                    <input <?php echo $wpaicg_audio_enable ? ' checked' : '' ?> value="1" class="wpaicg_chat_widget_audio" type="checkbox" name="wpaicg_chat_widget[audio_enable]">
                    <label><?php echo esc_html__('Speech to Text', 'gpt3-ai-content-generator'); ?></label>
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Mic Color', 'gpt3-ai-content-generator'); ?></label>
                    <input value="<?php echo $wpaicg_mic_color; ?>" type="text" class="wpaicgchat_color wpaicg_chat_widget_mic_color" name="wpaicg_chat_widget[mic_color]">
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Stop Button Color', 'gpt3-ai-content-generator'); ?></label>
                    <input value="<?php echo $wpaicg_stop_color; ?>" type="text" name="wpaicg_chat_widget[stop_color]" class="wpaicgchat_color">
                </div>
                <?php
                    } else {
                        // Notice for Azure/Google
                    ?>
                        <div class="nice-form-group">
                            <input type="checkbox" disabled>
                            <label><?php echo esc_html__('Speech to Text', 'gpt3-ai-content-generator'); ?></label>
                        </div>
                        <p>
                            <small><?php echo esc_html__('Speech to Text is not available in Azure or Google. If you want to use it, change your provider to OpenAI under Settings - AI Engine.', 'gpt3-ai-content-generator'); ?></small>
                        </p>
                    <?php
                    }
                    ?>
                <div class="nice-form-group">
                    <input <?php echo $wpaicg_chat_to_speech ? 'checked' : ''; ?> value="1" type="checkbox" name="wpaicg_chat_widget[chat_to_speech]" class="wpaicg_chat_to_speech">
                    <label><?php echo esc_html__('Text to Speech','gpt3-ai-content-generator')?></label>
                </div>
                <?php
                    $wpaicg_provider = get_option('wpaicg_provider', 'OpenAI'); // Fetching the provider
                    $disabled_voice_fields = !$wpaicg_chat_to_speech;
                    $google_disabled = empty($wpaicg_google_api_key) ? 'disabled' : '';
                    $elevenlabs_disabled = empty($wpaicg_elevenlabs_api) ? 'disabled' : '';
                    $openai_disabled = $wpaicg_provider === 'Azure'  || $wpaicg_provider === 'Google' ? 'disabled' : '';
                    ?>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Provider','gpt3-ai-content-generator')?></label>
                    <select name="wpaicg_chat_widget[voice_service]" class="wpaicg_voice_service" <?php echo $disabled_voice_fields ? 'disabled' : ''; ?>>
                        <option <?php echo $wpaicg_chat_voice_service == 'openai' ? 'selected' : ''; ?> value="openai" <?php echo $openai_disabled; ?>><?php echo esc_html__('OpenAI', 'gpt3-ai-content-generator'); ?></option>
                        <option <?php echo $wpaicg_chat_voice_service == 'google' ? 'selected' : ''; ?> value="google" <?php echo $google_disabled; ?>><?php echo esc_html__('Google', 'gpt3-ai-content-generator'); ?></option>
                        <option <?php echo $wpaicg_chat_voice_service == 'elevenlabs' ? 'selected' : ''; ?> value="elevenlabs" <?php echo $elevenlabs_disabled; ?>><?php echo esc_html__('ElevenLabs', 'gpt3-ai-content-generator'); ?></option>
                    </select>
                </div>
                <!-- OpenAI -->
                <div class="wpaicg_voice_service_openai" style="<?php echo $wpaicg_chat_voice_service == 'openai' ? '' : 'display:none'?>">
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Model', 'gpt3-ai-content-generator'); ?></label>
                        <select name="wpaicg_chat_widget[openai_model]" class="wpaicg_openai_model" <?php echo $disabled_voice_fields ? 'disabled' : ''; ?>>
                            <option value="tts-1" <?php echo $wpaicg_openai_model == 'tts-1' ? 'selected' : ''; ?>><?php echo esc_html__('tts-1', 'gpt3-ai-content-generator'); ?></option>
                            <option value="tts-1-hd" <?php echo $wpaicg_openai_model == 'tts-1-hd' ? 'selected' : ''; ?>><?php echo esc_html__('tts-1-hd', 'gpt3-ai-content-generator'); ?></option>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Voice', 'gpt3-ai-content-generator'); ?></label>
                        <select name="wpaicg_chat_widget[openai_voice]" class="wpaicg_openai_voice" <?php echo $disabled_voice_fields ? 'disabled' : ''; ?>>
                            <option value="alloy" <?php echo $wpaicg_openai_voice == 'alloy' ? 'selected' : ''; ?>><?php echo esc_html__('alloy', 'gpt3-ai-content-generator'); ?></option>
                            <option value="echo" <?php echo $wpaicg_openai_voice == 'echo' ? 'selected' : ''; ?>><?php echo esc_html__('echo', 'gpt3-ai-content-generator'); ?></option>
                            <option value="fable" <?php echo $wpaicg_openai_voice == 'fable' ? 'selected' : ''; ?>><?php echo esc_html__('fable', 'gpt3-ai-content-generator'); ?></option>
                            <option value="nova" <?php echo $wpaicg_openai_voice == 'nova' ? 'selected' : ''; ?>><?php echo esc_html__('nova', 'gpt3-ai-content-generator'); ?></option>
                            <option value="onyx" <?php echo $wpaicg_openai_voice == 'onyx' ? 'selected' : ''; ?>><?php echo esc_html__('onyx', 'gpt3-ai-content-generator'); ?></option>
                            <option value="shimmer" <?php echo $wpaicg_openai_voice == 'shimmer' ? 'selected' : ''; ?>><?php echo esc_html__('shimmer', 'gpt3-ai-content-generator'); ?></option>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Format', 'gpt3-ai-content-generator'); ?></label>
                        <select name="wpaicg_chat_widget[openai_output_format]" class="wpaicg_openai_output_format" <?php echo $disabled_voice_fields ? 'disabled' : ''; ?>>
                            <option value="mp3" <?php echo $wpaicg_openai_output_format == 'mp3' ? 'selected' : ''; ?>><?php echo esc_html__('mp3', 'gpt3-ai-content-generator'); ?></option>
                            <option value="opus" <?php echo $wpaicg_openai_output_format == 'opus' ? 'selected' : ''; ?>><?php echo esc_html__('opus', 'gpt3-ai-content-generator'); ?></option>
                            <option value="aac" <?php echo $wpaicg_openai_output_format == 'aac' ? 'selected' : ''; ?>><?php echo esc_html__('aac', 'gpt3-ai-content-generator'); ?></option>
                            <option value="flac" <?php echo $wpaicg_openai_output_format == 'flac' ? 'selected' : ''; ?>><?php echo esc_html__('flac', 'gpt3-ai-content-generator'); ?></option>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Speed (0.25 to 4.0)', 'gpt3-ai-content-generator'); ?></label>
                        <input type="number" name="wpaicg_chat_widget[openai_voice_speed]" class="wpaicg_openai_voice_speed" min="0.25" max="4.0" step="0.01" value="<?php echo esc_attr($wpaicg_openai_voice_speed); ?>" <?php echo $disabled_voice_fields ? 'disabled' : ''; ?>>
                    </div>
                </div>
                <!-- Google -->
                <div class="wpaicg_voice_service_google" style="<?php echo $wpaicg_chat_voice_service == 'google' && !empty($wpaicg_google_api_key) ? '' : 'display:none'?>">
                    <?php
                        $wpaicg_voice_language = isset($wpaicg_chat_widget['voice_language']) && !empty($wpaicg_chat_widget['voice_language']) ? $wpaicg_chat_widget['voice_language'] : 'en-US';
                        $wpaicg_voice_name = isset($wpaicg_chat_widget['voice_name']) && !empty($wpaicg_chat_widget['voice_name']) ? $wpaicg_chat_widget['voice_name'] : 'en-US-Studio-M';
                        $wpaicg_voice_device = isset($wpaicg_chat_widget['voice_device']) && !empty($wpaicg_chat_widget['voice_device']) ? $wpaicg_chat_widget['voice_device'] : '';
                        $wpaicg_voice_speed = isset($wpaicg_chat_widget['voice_speed']) && !empty($wpaicg_chat_widget['voice_speed']) ? $wpaicg_chat_widget['voice_speed'] : 1;
                        $wpaicg_voice_pitch = isset($wpaicg_chat_widget['voice_pitch']) && !empty($wpaicg_chat_widget['voice_pitch']) ? $wpaicg_chat_widget['voice_pitch'] : 0;
                    ?>
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Language','gpt3-ai-content-generator')?></label>
                        <select <?php echo empty($wpaicg_google_api_key) || $disabled_voice_fields ? ' disabled':''?> name="wpaicg_chat_widget[voice_language]" class="wpaicg_voice_language">
                            <?php
                            foreach(\WPAICG\WPAICG_Google_Speech::get_instance()->languages as $key=>$voice_language){
                                echo '<option'.($wpaicg_voice_language == $key ? ' selected':'').' value="'.esc_html($key).'">'.esc_html($voice_language).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Voice','gpt3-ai-content-generator')?></label>
                        <select <?php echo empty($wpaicg_google_api_key) || $disabled_voice_fields ? ' disabled':''?> data-value="<?php echo esc_html($wpaicg_voice_name)?>" name="wpaicg_chat_widget[voice_name]" class="wpaicg_voice_name"></select>
                    </div>
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Audio Device Profile','gpt3-ai-content-generator')?></label>
                        <select <?php echo empty($wpaicg_google_api_key) ? ' disabled':''?> name="wpaicg_chat_widget[voice_device]" class="wpaicg_voice_device">
                            <?php
                            foreach(\WPAICG\WPAICG_Google_Speech::get_instance()->devices() as $key => $device){
                                echo '<option'.($wpaicg_voice_device == $key ? ' selected':'').' value="'.esc_html($key).'">'.esc_html($device).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Speed','gpt3-ai-content-generator')?></label>
                        <input <?php echo empty($wpaicg_google_api_key) || $disabled_voice_fields ? ' disabled':''?> type="text" class="wpaicg_voice_speed" value="<?php echo esc_html($wpaicg_voice_speed)?>" name="wpaicg_chat_widget[voice_speed]">
                    </div>
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Pitch','gpt3-ai-content-generator')?></label>
                        <input <?php echo empty($wpaicg_google_api_key) || $disabled_voice_fields ? ' disabled':''?> type="text" class="wpaicg_voice_pitch" value="<?php echo esc_html($wpaicg_voice_pitch)?>" name="wpaicg_chat_widget[voice_pitch]">
                    </div>
                </div>
                <!-- ElevenLabs -->
                <div class="wpaicg_voice_service_elevenlabs" style="<?php echo $wpaicg_chat_voice_service == 'elevenlabs' && !empty($wpaicg_elevenlabs_api) ? '' : 'display:none'?>">
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Voice','gpt3-ai-content-generator')?></label>
                        <select <?php echo empty($wpaicg_elevenlabs_api) || $disabled_voice_fields ? ' disabled':''?> name="wpaicg_chat_widget[elevenlabs_voice]" class="wpaicg_elevenlabs_voice">
                            <?php
                            foreach(\WPAICG\WPAICG_ElevenLabs::get_instance()->voices as $key=>$voice){
                                echo '<option'.($wpaicg_elevenlabs_voice == $key ? ' selected':'').' value="'.esc_html($key).'">'.esc_html($voice).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="nice-form-group">
                        <label><?php echo esc_html__('Model', 'gpt3-ai-content-generator')?></label>
                        <select <?php echo empty($wpaicg_elevenlabs_api) || $disabled_voice_fields ? ' disabled':''?> name="wpaicg_chat_widget[elevenlabs_model]" class="wpaicg_elevenlabs_model">
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
                        <button class="button button-primary wpaicg-w-25" name="wpaicg_submit"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
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
                    <input <?php echo $wpaicg_user_limited ? ' checked': ''?> class="wpaicg_user_token_limit" type="checkbox" value="1" name="wpaicg_chat_widget[user_limited]">
                    <label><?php echo esc_html__('Limit Registered Users','gpt3-ai-content-generator')?></label>
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Token Allocation','gpt3-ai-content-generator')?></label>
                    <input <?php echo $wpaicg_user_limited ? '' : ' disabled'?> class="wpaicg_user_token_limit_text" type="text" value="<?php echo esc_html($wpaicg_user_tokens)?>" name="wpaicg_chat_widget[user_tokens]">
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Token Allocation by Role','gpt3-ai-content-generator')?></label>
                    <?php
                    foreach($wpaicg_roles as $key=>$wpaicg_role){
                        echo '<input class="wpaicg_role_'.esc_html($key).'" value="'.(isset($wpaicg_chat_widget['limited_roles'][$key]) && !empty($wpaicg_chat_widget['limited_roles'][$key]) ? esc_html($wpaicg_chat_widget['limited_roles'][$key]) : '').'" type="hidden" name="wpaicg_chat_widget[limited_roles]['.esc_html($key).']">';
                    }
                    ?>
                    <input <?php echo $wpaicg_user_limited ? '': (isset($wpaicg_chat_widget['role_limited']) && $wpaicg_chat_widget['role_limited'] ? ' checked':'')?> type="checkbox" value="1" class="wpaicg_role_limited" name="wpaicg_chat_widget[role_limited]">
                    <a href="javascript:void(0)" class="wpaicg_limit_set_role<?php echo $wpaicg_user_limited || !isset($wpaicg_chat_widget['role_limited']) || !$wpaicg_chat_widget['role_limited'] ? ' disabled': ''?>"><?php echo esc_html__('Configure Role Allocations','gpt3-ai-content-generator')?></a>
                </div>
                <div class="nice-form-group">
                    <input <?php echo $wpaicg_guest_limited ? ' checked': ''?> class="wpaicg_guest_token_limit" type="checkbox" value="1" name="wpaicg_chat_widget[guest_limited]">
                    <label><?php echo esc_html__('Limit Non-Registered Users','gpt3-ai-content-generator')?></label>
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Token Allocation','gpt3-ai-content-generator')?></label>
                    <input <?php echo $wpaicg_guest_limited ? '' : ' disabled'?> class="wpaicg_guest_token_limit_text" type="text" value="<?php echo esc_html($wpaicg_guest_tokens)?>" name="wpaicg_chat_widget[guest_tokens]">
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Reset Interval','gpt3-ai-content-generator')?></label>
                    <select name="wpaicg_chat_widget[reset_limit]">
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
                    <label><?php echo esc_html__('Notice','gpt3-ai-content-generator')?></label>
                    <input type="text" value="<?php echo esc_html($wpaicg_limited_message)?>" name="wpaicg_chat_widget[limited_message]">
                </div>
                <details>
                    <summary>
                        <button class="button button-primary wpaicg-w-25" name="wpaicg_submit"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
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
                        <input <?php echo $wpaicg_save_logs ? ' checked':''?> class="wpaicg_chatbot_save_logs" value="1" type="checkbox" name="wpaicg_chat_widget[save_logs]">
                        <label><?php echo esc_html__('Save Chat Logs','gpt3-ai-content-generator')?></label>
                    </div>
                    <div class="nice-form-group">
                        <input <?php echo $wpaicg_save_logs ? '': ' disabled'?><?php echo $wpaicg_save_logs && isset($wpaicg_chat_widget['log_request']) && $wpaicg_chat_widget['log_request'] ? ' checked' : ''?> class="wpaicg_chatbot_log_request" value="1" type="checkbox" name="wpaicg_chat_widget[log_request]">
                        <label><?php echo esc_html__('Save Prompt Details','gpt3-ai-content-generator')?></label>
                    </div>
                    <div class="nice-form-group">
                        <input <?php echo $wpaicg_save_logs ? '': ' disabled'?><?php echo $wpaicg_log_notice ? ' checked':''?> class="wpaicg_chatbot_log_notice" value="1" type="checkbox" name="wpaicg_chat_widget[log_notice]">
                        <label><?php echo esc_html__('Display Notice','gpt3-ai-content-generator')?></label>
                    </div>
                </fieldset>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Notice Text','gpt3-ai-content-generator')?></label>
                    <input <?php echo $wpaicg_save_logs ? '': ' disabled'?> class="regular-text wpaicg_chatbot_log_notice_message" value="<?php echo esc_html($wpaicg_log_notice_message)?>" type="text" name="wpaicg_chat_widget[log_notice_message]">
                </div>
                <div class="nice-form-group">
                    <input disabled type="checkbox">
                    <label><?php echo esc_html__('Moderation (Available in Pro)','gpt3-ai-content-generator')?></label>
                </div>
                <?php
                if(!\WPAICG\wpaicg_util_core()->wpaicg_is_pro() || $wpaicg_provider === 'Azure' || $wpaicg_provider === 'Google'):
                    if($wpaicg_provider === 'Azure' || $wpaicg_provider === 'Google'):
                        echo '<small>'. esc_html__('Moderation is not available in Azure or Google. If you want to use the moderation tool, please change your model to OpenAI under Settings - AI Engine tab.', 'gpt3-ai-content-generator') .'</small>';
                    endif;
                ?>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Model','gpt3-ai-content-generator')?></label>
                    <select disabled>
                        <option value="text-moderation-latest">text-moderation-latest</option>
                        <option value="text-moderation-stable">text-moderation-stable</option>
                    </select>
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Notice','gpt3-ai-content-generator')?></label>
                    <textarea rows="8" disabled><?php echo esc_html__('Your message has been flagged as potentially harmful or inappropriate. Please ensure that your messages are respectful and do not contain language or content that could be offensive or harmful to others. Thank you for your cooperation.','gpt3-ai-content-generator')?></textarea>
                </div>
                <?php else: ?>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Enable','gpt3-ai-content-generator')?></label>
                    <input <?php echo isset($wpaicg_chat_widget['moderation']) && $wpaicg_chat_widget['moderation'] ? ' checked': ''?>  name="wpaicg_chat_widget[moderation]" value="1" type="checkbox">
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Model','gpt3-ai-content-generator')?></label>
                    <select name="wpaicg_chat_widget[moderation_model]">
                        <option <?php echo isset($wpaicg_chat_widget['moderation_model']) && $wpaicg_chat_widget['moderation_model'] == 'text-moderation-latest' ? ' selected':'';?> value="text-moderation-latest">text-moderation-latest</option>
                        <option <?php echo isset($wpaicg_chat_widget['moderation_model']) && $wpaicg_chat_widget['moderation_model'] == 'text-moderation-stable' ? ' selected':'';?> value="text-moderation-stable">text-moderation-stable</option>
                    </select>
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Notice','gpt3-ai-content-generator')?></label>
                    <textarea name="wpaicg_chat_widget[moderation_notice]"><?php echo isset($wpaicg_chat_widget['moderation_notice']) ? esc_html($wpaicg_chat_widget['moderation_notice']) : esc_html__('Your message has been flagged as potentially harmful or inappropriate. Please ensure that your messages are respectful and do not contain language or content that could be offensive or harmful to others. Thank you for your cooperation.','gpt3-ai-content-generator')?></textarea>
                </div>
                <?php endif; ?>
                <details>
                    <summary>
                        <button class="button button-primary wpaicg-w-25" name="wpaicg_submit"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
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
                    <label><?php echo esc_html__('Width','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_chat_width)?>" class="wpaicg_chat_widget_width" min="100" type="text" name="wpaicg_chat_widget[width]">
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Height','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_chat_height)?>" class="wpaicg_chat_widget_height" min="100" type="text" name="wpaicg_chat_widget[height]">
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Font Size','gpt3-ai-content-generator')?></label>
                    <select name="wpaicg_chat_widget[fontsize]" class="wpaicg_chat_widget_font_size">
                        <?php
                        for($i = 10; $i <= 30; $i++){
                            echo '<option'.($wpaicg_chat_fontsize == $i ? ' selected': '').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Font Color','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_chat_fontcolor)?>" type="text" class="wpaicgchat_color wpaicgchat_font_color" name="wpaicg_chat_widget[fontcolor]">
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Background','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_chat_bgcolor)?>" type="text" class="wpaicgchat_color wpaicgchat_bg_color" name="wpaicg_chat_widget[bgcolor]">
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Text Field Background','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_bg_text_field)?>" type="text" class="wpaicgchat_color wpaicgchat_input_color" name="wpaicg_chat_widget[bg_text_field]">
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Text Field Border','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_border_text_field)?>" type="text" class="wpaicgchat_color wpaicgchat_input_border" name="wpaicg_chat_widget[border_text_field]">
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Send Button','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_send_color)?>" type="text" class="wpaicgchat_color wpaicgchat_send_color" name="wpaicg_chat_widget[send_color]">
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('User Background','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_user_bg_color)?>" type="text" class="wpaicgchat_color wpaicgchat_user_color" name="wpaicg_chat_widget[user_bg_color]">
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('AI Background','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_ai_bg_color)?>" type="text" class="wpaicgchat_color wpaicgchat_ai_color" name="wpaicg_chat_widget[ai_bg_color]">
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Response Wait Message','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_thinking_color)?>" type="text" class="wpaicgchat_color wpaicgchat_thinking_color" name="wpaicg_chat_widget[thinking_color]">
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Border Radius - Window','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_chat_rounded)?>" type="number" min="0" class="wpaicg_chat_rounded" name="wpaicg_chat_widget[chat_rounded]">px
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Border Radius - Text Field','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_text_rounded)?>" type="number" min="0" class="wpaicg_text_rounded" name="wpaicg_chat_widget[text_rounded]">px
                </div>
                <div class="nice-form-group">
                    <label><?php echo esc_html__('Text Field Height','gpt3-ai-content-generator')?></label>
                    <input value="<?php echo esc_html($wpaicg_text_height)?>" type="number" min="30" class="wpaicg_text_height" name="wpaicg_chat_widget[text_height]">px
                </div>
                <details>
                    <summary>
                        <button class="button button-primary wpaicg-w-25" name="wpaicg_submit"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
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
        <div class="wpaicg-chatbox-preview">
            <div class="wpaicg-chatbox-preview-box" style="position: relative;">
                <?php
                // Check if $wpaicg_chat_status is set to 'active'
                if($wpaicg_chat_status == 'active') {
                    include __DIR__.'/wpaicg_chat_widget.php';
                } else {
                    // Display this text if $wpaicg_chat_status is not 'active'
                    echo esc_html__('The widget is not active. Once enabled and saved, the preview will be shown here.','gpt3-ai-content-generator');
                }
                ?>
            </div>
        </div>
    </aside>
  </div>
</div>

<script>
    jQuery(document).ready(function ($){
        let wpaicg_google_voices = <?php echo json_encode($wpaicg_google_voices)?>;
        let wpaicg_elevenlab_api = '<?php echo esc_html($wpaicg_elevenlabs_api)?>';
        let wpaicg_google_api_key = '<?php  echo $wpaicg_google_api_key?>';

        function resetForm() {
            var resetSource = 'widget';

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

        // trigger when resetButton is clicked
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
            var source = 'widget'; // Adjust based on context

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
            var exportSource = 'widget'; // Adjust this based on the current context (shortcode, widget, bot)

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

        // Attach the exportSettings function to the exportButton's click event
        $(document).on('click', '#exportButton', function() {
            exportSettings();
        });

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
        
        $('#form-chatbox-setting').on('submit', function (e){
            if($('.wpaicg_voice_speed').length) {
                let wpaicg_voice_speed = parseFloat($('.wpaicg_voice_speed').val());
                let wpaicg_voice_pitch = parseFloat($('.wpaicg_voice_pitch').val());
                let wpaicg_voice_name = parseFloat($('.wpaicg_voice_name').val());
                let has_error = false;
                if (wpaicg_voice_speed < 0.25 || wpaicg_voice_speed > 4) {
                    has_error = '<?php echo sprintf(
                        /* translators: 1: minimum speed, 2: maximum speed */
                        esc_html__('Please enter valid voice speed value between %1$s and %2$s', 'gpt3-ai-content-generator'), 0.25, 4)?>';
                } else if (wpaicg_voice_pitch < -20 || wpaicg_voice_pitch > 20) {
                    has_error = '<?php echo sprintf(
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
        $(document).on('keypress','.wpaicg_user_token_limit_text,.wpaicg_update_role_limit,.wpaicg_guest_token_limit_text', function (e){
            var charCode = (e.which) ? e.which : e.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
                return false;
            }
            return true;
        });
        $('.wpaicg_limit_set_role').click(function (){
            if(!$(this).hasClass('disabled')) {
                if ($('.wpaicg_role_limited').prop('checked')) {
                    let html = '';
                    $.each(wpaicg_roles, function (key, role) {
                        let valueRole = $('.wpaicg_role_'+key).val();
                        html += '<div style="padding: 5px;display: flex;justify-content: space-between;align-items: center;"><label><strong>'+role+'</strong></label><input class="wpaicg_update_role_limit" data-target="'+key+'" value="'+valueRole+'" placeholder="<?php echo esc_html__('Empty for no-limit','gpt3-ai-content-generator')?>" type="text"></div>';
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
        $('.wpaicg-chatbox-preview-box > .wpaicg_chat_widget').addClass('wpaicg_widget_open');
        $('.wpaicg-chatbox-preview-box .wpaicg_toggle').addClass('wpaicg_widget_open');
        function wpaicgChangeAvatarRealtime(){
            var wpaicg_user_avatar_check = $('input[name=_wpaicg_chatbox_you]').val()+':';
            var wpaicg_ai_avatar_check = $('input[name=_wpaicg_chatbox_ai_name]').val()+':';
            if($('.wpaicgchat_use_avatar').prop('checked')){
                wpaicg_user_avatar_check = '<img src="<?php echo get_avatar_url(get_current_user_id())?>" height="40" width="40">';
                wpaicg_ai_avatar_check = '<?php echo esc_html(WPAICG_PLUGIN_URL) . 'admin/images/chatbot.png';?>';
                if($('.wpaicg_chatbox_avatar_custom').prop('checked') && $('.wpaicg_chatbox_avatar img').length){
                    wpaicg_ai_avatar_check = $('.wpaicg_chatbox_avatar img').attr('src');
                }
                wpaicg_ai_avatar_check = '<img src="'+wpaicg_ai_avatar_check+'" height="40" width="40">';
            }

            $('.wpaicg-chat-ai-message').each(function (idx, item){
                $(item).find('strong').html(wpaicg_ai_avatar_check);
            });
            $('.wpaicg-chat-user-message').each(function (idx, item){
                $(item).find('strong').html(wpaicg_user_avatar_check);
            });
        }
        $('input[name=_wpaicg_chatbox_you],input[name=_wpaicg_chatbox_ai_name]').on('input', function (){
            wpaicgChangeAvatarRealtime();
        })
        $('.wpaicgchat_use_avatar,.wpaicg_chatbox_avatar_default,.wpaicg_chatbox_avatar_custom').on('click', function (){
            wpaicgChangeAvatarRealtime();
        })
        $('.wpaicg_chat_rounded,.wpaicg_text_rounded,.wpaicg_text_height').on('input', function(){
            wpaicgUpdateRealtime();
        })
        function wpaicgUpdateRealtime(){
            var wpaicgWindowWidth = window.innerWidth;
            var wpaicgWindowHeight = window.innerHeight;
            let fontsize = $('.wpaicg_chat_widget_font_size').val();
            let fontcolor = $('.wpaicgchat_font_color').iris('color');
            let bgcolor = $('.wpaicgchat_bg_color').iris('color');
            let inputbg = $('.wpaicgchat_input_color').iris('color');
            let inputborder = $('.wpaicgchat_input_border').iris('color');
            let buttoncolor = $('.wpaicgchat_send_color').iris('color');
            let chatbarcolor = $('.wpaicgchat_bar_color').iris('color');
            let userbg = $('.wpaicgchat_user_color').iris('color');
            let aibg = $('.wpaicgchat_ai_color').iris('color');
            let useavatar = $('.wpaicgchat_use_avatar').val();
            let width = $('.wpaicg_chat_widget_width').val();
            let height = $('.wpaicg_chat_widget_height').val();
            let mic_color = $('.wpaicg_chat_widget_mic_color').iris('color');
            let wpaicg_chat_rounded = $('.wpaicg_chat_rounded').val();
            let wpaicg_text_rounded = $('.wpaicg_text_rounded').val();
            let wpaicg_text_height = $('.wpaicg_text_height').val();
            $('.wpaicg-mic-icon').css('color', mic_color);
            $('.wpaicg-img-icon').css('color', mic_color);
            $('.wpaicg-chatbox').attr('data-height',height);
            $('.wpaicg-chatbox').attr('data-width',width);
            $('.wpaicg-chatbox').attr('data-chat_rounded',wpaicg_chat_rounded);
            $('.wpaicg-chatbox').attr('data-text_rounded',wpaicg_text_rounded);
            $('.wpaicg-chatbox').attr('data-text_height',wpaicg_text_height);
            $('.wpaicg-chatbox-action-bar').css({
                'color':chatbarcolor,
                'background-color':bgcolor
            });
            let footernote = $('.wpaicg-footer-note').val();
            let footerheight = 0;

            if(footernote === ''){
                footerheight = 18;
                $('.wpaicg-chatbox-footer').hide();
                $('.wpaicg-chatbox-type').css('padding','5px');
            }
            else{
                $('.wpaicg-chatbox-type').css('padding','5px 5px 0 5px');
                $('.wpaicg-chatbox-footer').show();
                $('.wpaicg-chatbox-footer').html(footernote);
            }
            if($('.wpaicg_chat_widget_audio').prop('checked')){
                $('.wpaicg-mic-icon').show();
            }
            else{
                $('.wpaicg-mic-icon').hide();
            }
            if($('.wpaicg_chat_widget_image').prop('checked')){
                $('.wpaicg-img-icon').show();
            }
            else{
                $('.wpaicg-img-icon').hide();
            }
            $('.wpaicg-chatbox-messages li.wpaicg-chat-ai-message').css({
                'font-size': fontsize+'px',
                'color': fontcolor,
                'background-color': aibg
            })
            $('.wpaicg-chatbox-messages li.wpaicg-chat-user-message').css({
                'font-size': fontsize+'px',
                'color': fontcolor,
                'background-color': userbg
            });
            $('.wpaicg_chat_widget_content .wpaicg-chatbox-content ul,.wpaicg_chat_widget_content .wpaicg-chatbox').css({
                'background-color': bgcolor
            });
            $('.wpaicg-chatbox-typing').css({
                'border-color': inputborder,
                'background-color': inputbg
            });
            var previewboxWidth = $('.wpaicg-chatbox-preview-box').width();
            $('.wpaicg-chatbox-send').css('color',buttoncolor);
            if(width.indexOf('%') < 0){
                if(width.indexOf('px') < 0){
                    width = parseFloat(width);
                }
                else{
                    width = parseFloat(width.replace(/px/g,''));
                }
            }
            else{
                width = parseFloat(width.replace(/%/g,''));
                width = width*previewboxWidth/100;
            }
            if(height.indexOf('%') < 0){
                if(height.indexOf('px') < 0){
                    height = parseFloat(height);
                }
                else{
                    height = parseFloat(height.replace(/px/g,''));
                }
            }
            else{
                height = parseFloat(height.replace(/%/g,''));
                height = height*wpaicgWindowHeight/100;
            }
            $('.wpaicg-chatbox-preview-box').height((parseInt(height)+125)+'px');
            if(width > previewboxWidth){
                width = previewboxWidth;
            }
            $('.wpaicg_chat_widget_content .wpaicg-chatbox,.wpaicg_widget_open .wpaicg_chat_widget_content').css({
                'height': height+'px',
                'width': width+'px',
            });
            $('.wpaicg_chat_widget_content .wpaicg-chatbox-content').css({
                'height': (height - 58 + footerheight)+'px'
            });
            $('.wpaicg_chat_widget_content .wpaicg-chatbox-content ul').css({
                'height': (height - 82 + footerheight)+'px'
            });
            wpaicgChatBoxSize();
        }
        $('.wpaicg_chat_widget_font_size,.wpaicg_chat_widget_width,.wpaicg_chat_widget_height').on('input', function(){
            wpaicgUpdateRealtime();
        });
        $('.wpaicg_chat_widget_audio,.wpaicg_chat_widget_image,.wpaicgchat_use_avatar,.wpaicgchat_fullscreen,.wpaicgchat_close_btn,.wpaicgchat_download_btn,.wpaicgchat_clear_btn').click(function(){
            wpaicgUpdateRealtime();
        })
        $('.wpaicgchat_color').wpColorPicker({
            change: function (event, ui){
                wpaicgUpdateRealtime();
            },
            clear: function(event){
                wpaicgUpdateRealtime();
            }
        });
        $('.wpaicg-footer-note').on('input', function(){
            wpaicgUpdateRealtime();
        })
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
                    button.html('<img width="75" height="75" src="'+attachment.url+'">');
                    $('.wpaicg_chat_icon_url').val(attachment.id);
                }).open();
        });
        $('.wpaicg_chatbox_avatar').click(function (e){
            e.preventDefault();
            $('.wpaicg_chatbox_avatar_default').prop('checked',false);
            $('.wpaicg_chatbox_avatar_custom').prop('checked',true);
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
                    $('.wpaicg_ai_avatar_id').val(attachment.id);
                }).open();
        });
        $('.wpaicg-collapse-title').click(function (){
            if(!$(this).hasClass('wpaicg-collapse-active')){
                $('.wpaicg-collapse').removeClass('wpaicg-collapse-active');
                $('.wpaicg-collapse-title span').html('+');
                $(this).find('span').html('-');
                $(this).parent().addClass('wpaicg-collapse-active');
            }
        });

        // Function to toggle visibility based on Vector DB selection
        function toggleDBRelatedFields() {
            var selectedDB = $('#wpaicg_chat_vectordb').val();
            if (selectedDB === 'qdrant') {
                // Hide Pinecone Index div and Show Qdrant Collection div if Qdrant is selected
                $('#wpaicg_chat_embedding_index').closest('.nice-form-group').hide();
                $('#wpaicg_chat_qdrant_collection').closest('.nice-form-group').show();
            } else {
                // Show Pinecone Index div and Hide Qdrant Collection div if Pinecone or any other option is selected
                $('#wpaicg_chat_embedding_index').closest('.nice-form-group').show();
                $('#wpaicg_chat_qdrant_collection').closest('.nice-form-group').hide();
            }
        }

        // Initial check on page load
        toggleDBRelatedFields();

        // Set up event listener for changes on the Vector DB dropdown
        $('#wpaicg_chat_vectordb').change(function() {
            toggleDBRelatedFields();
        });


        $('#wpaicg_chat_excerpt').on('click', function (){
            if($(this).prop('checked')){
                $('#wpaicg_chat_excerpt').removeClass('asdisabled');
                $('#wpaicg_chat_embedding').prop('checked',false);
                $('#wpaicg_chat_embedding').addClass('asdisabled');
                $('#wpaicg_chat_embedding_type').val('openai');
                $('#wpaicg_chat_embedding_type').addClass('asdisabled');
                $('#wpaicg_chat_embedding_type').attr('disabled','disabled');
                $('#wpaicg_chat_vectordb').val('pinecone');
                $('#wpaicg_chat_vectordb').attr('disabled','disabled');
                $('#wpaicg_chat_vectordb').addClass('asdisabled');
                $('#wpaicg_chat_embedding_top').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_top').val(1);
                $('#wpaicg_chat_embedding_index').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_index').addClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_pdf').addClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf_message').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_pdf_message').addClass('asdisabled');
                $('#wpaicg_chat_pdf_pages').attr('disabled','disabled');
                $('#wpaicg_chat_pdf_pages').addClass('asdisabled');
                $('#wpaicg_chat_qdrant_collection').attr('disabled','disabled');
                $('#wpaicg_chat_qdrant_collection').addClass('asdisabled');
            }
            else{
                $(this).prop('checked',true);
            }
        });
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
        $('#wpaicg_chat_embedding').on('click', function (){
            if($(this).prop('checked')){
                $('#wpaicg_chat_excerpt').prop('checked',false);
                $('#wpaicg_chat_excerpt').addClass('asdisabled');
                $('#wpaicg_chat_embedding').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_type').val('openai');
                $('#wpaicg_chat_embedding_type').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_type').removeAttr('disabled');
                $('#wpaicg_chat_vectordb').val('pinecone');
                $('#wpaicg_chat_vectordb').removeAttr('disabled');
                $('#wpaicg_chat_vectordb').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_top').val(1);
                $('#wpaicg_chat_embedding_top').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_top').removeAttr('disabled');
                $('#wpaicg_chat_embedding_index').removeAttr('disabled');
                $('#wpaicg_chat_embedding_index').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf').removeAttr('disabled');
                $('#wpaicg_chat_embedding_pdf').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf_message').removeAttr('disabled');
                $('#wpaicg_chat_embedding_pdf_message').removeClass('asdisabled');
                $('#wpaicg_chat_pdf_pages').removeAttr('disabled');
                $('#wpaicg_chat_pdf_pages').removeClass('asdisabled');
                $('#wpaicg_chat_qdrant_collection').removeAttr('disabled');
                $('#wpaicg_chat_qdrant_collection').removeClass('asdisabled');
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
                $('#wpaicg_chat_embedding').addClass('asdisabled');
                $('#wpaicg_chat_embedding_type').val('openai');
                $('#wpaicg_chat_embedding_type').addClass('asdisabled');
                $('#wpaicg_chat_vectordb').val('pinecone');
                $('#wpaicg_chat_vectordb').addClass('asdisabled');
                $('#wpaicg_chat_vectordb').removeAttr('disabled');
                $('#wpaicg_chat_embedding_top').val(1);
                $('#wpaicg_chat_embedding_top').addClass('asdisabled');
                $('#wpaicg_chat_embedding_index').removeAttr('disabled');
                $('#wpaicg_chat_embedding_index').addClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf').removeAttr('disabled');
                $('#wpaicg_chat_embedding_pdf').addClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf_message').removeAttr('disabled');
                $('#wpaicg_chat_embedding_pdf_message').addClass('asdisabled');
                $('#wpaicg_chat_pdf_pages').removeAttr('disabled');
                $('#wpaicg_chat_pdf_pages').addClass('asdisabled');
                $('#wpaicg_chat_qdrant_collection').removeAttr('disabled');
                $('#wpaicg_chat_qdrant_collection').addClass('asdisabled');
            }
            else{
                $('#wpaicg_chat_embedding_type').removeClass('asdisabled');
                $('#wpaicg_chat_vectordb').removeClass('asdisabled');
                $('#wpaicg_chat_excerpt').removeClass('asdisabled');
                $('#wpaicg_chat_embedding').removeClass('asdisabled');
                $('#wpaicg_chat_excerpt').prop('checked',false);
                $('#wpaicg_chat_embedding').prop('checked',false);
                $('#wpaicg_chat_excerpt').attr('disabled','disabled');
                $('#wpaicg_chat_embedding').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_type').attr('disabled','disabled');
                $('#wpaicg_chat_vectordb').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_top').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_top').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_index').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_index').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_pdf').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_pdf_message').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_pdf_message').removeClass('asdisabled');
                $('#wpaicg_chat_pdf_pages').attr('disabled','disabled');
                $('#wpaicg_chat_pdf_pages').removeClass('asdisabled');
                $('#wpaicg_chat_qdrant_collection').attr('disabled','disabled');
                $('#wpaicg_chat_qdrant_collection').removeClass('asdisabled');
            }
        })

        function updateImageUploadOption() {
            var model = $('#wpaicg_chat_model').val();

            var $imageUploadCheckbox = $('.wpaicg_chat_widget_image'); // Update this selector as needed
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
        $('.wpaicg_chat_widget_image').on('change', function() {
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
        $('.wpaicg_chat_widget_image').trigger('change');

        // Function to manage speech options based on streaming status
        function manageSpeechOptionsBasedOnStreaming() {
            var isStreamingChecked = $('input[name="wpaicg_stream_nav_option"]').is(':checked');

            // "Speech to Text" checkbox and its container
            var $speechToTextCheckbox = $('.wpaicg_chat_widget_audio');
            var $textToSpeechCheckbox = $('.wpaicg_chat_to_speech');

            if(isStreamingChecked) {
                // Disable and uncheck "Speech to Text" and "Text to Speech" if streaming is checked
                $speechToTextCheckbox.prop('disabled', true).prop('checked', false);
                $textToSpeechCheckbox.prop('disabled', true).prop('checked', false);
            } else {
                // Enable "Speech to Text" only if provider is OpenAI
                <?php if($wpaicg_provider === 'OpenAI'): ?>
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

        // disable streaming if wpaicg_provider is Google
        <?php if($wpaicg_provider === 'Google'): ?>
        $('input[name="wpaicg_stream_nav_option"]').prop('disabled', true);
        <?php endif; ?>

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