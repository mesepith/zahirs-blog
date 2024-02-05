<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$success = false;
if(isset($_POST['wpaicg_chat_save'])){
    // Check the nonce
    if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'wpaicg_chat_nonce' ) ) {
        wp_die( __('Nonce verification failed.', 'gpt3-ai-content-generator') );
    }
    if (isset($_POST['wpaicg_chat_enable_sale']) && !empty($_POST['wpaicg_chat_enable_sale'])) {
        update_option('wpaicg_chat_enable_sale', sanitize_text_field($_POST['wpaicg_chat_enable_sale']));
    } else {
        delete_option('wpaicg_chat_enable_sale');
    }
    if (isset($_POST['wpaicg_elevenlabs_hide_error']) && !empty($_POST['wpaicg_elevenlabs_hide_error'])) {
        update_option('wpaicg_elevenlabs_hide_error', sanitize_text_field($_POST['wpaicg_elevenlabs_hide_error']));
    } else {
        delete_option('wpaicg_elevenlabs_hide_error');
    }
    if (isset($_POST['wpaicg_google_api_key']) && !empty($_POST['wpaicg_google_api_key'])) {
        update_option('wpaicg_google_api_key', sanitize_text_field($_POST['wpaicg_google_api_key']));
    } else {
        delete_option('wpaicg_google_api_key');
    }
    if (isset($_POST['wpaicg_elevenlabs_api']) && !empty($_POST['wpaicg_elevenlabs_api'])) {
        update_option('wpaicg_elevenlabs_api', sanitize_text_field($_POST['wpaicg_elevenlabs_api']));
    } else {
        delete_option('wpaicg_elevenlabs_api');
        delete_option('wpaicg_chat_to_speech');
    }
    if (isset($_POST['wpaicg_banned_ips']) && !empty($_POST['wpaicg_banned_ips'])) {
        update_option('wpaicg_banned_ips', sanitize_text_field($_POST['wpaicg_banned_ips']));
    } else {
        delete_option('wpaicg_banned_ips');
    }
    // Handling the form submission to save banned words
    if (isset($_POST['wpaicg_banned_words']) && !empty($_POST['wpaicg_banned_words'])) {
        update_option('wpaicg_banned_words', sanitize_text_field($_POST['wpaicg_banned_words']));
    } else {
        delete_option('wpaicg_banned_words');
    }
    // Handling the new "Enable Assistants" option
    $wpaicg_assistant_feature = isset($_POST['wpaicg_assistant_feature']) ? 1 : 0;
    update_option('wpaicg_assistant_feature', $wpaicg_assistant_feature);
    $success = true;
}
$wpaicg_chat_enable_sale = get_option('wpaicg_chat_enable_sale', false);
$wpaicg_elevenlabs_hide_error = get_option('wpaicg_elevenlabs_hide_error', false);
$wpaicg_elevenlabs_api = get_option('wpaicg_elevenlabs_api', '');
$wpaicg_google_api_key = get_option('wpaicg_google_api_key', '');
if($success){
    echo '<div class="notice notice-success is-dismissible"><p>'.esc_html__('Settings saved successfully!','gpt3-ai-content-generator').'</p></div>';
    
}
?>
<div id="wpaicg_message" style="display: none;"></div>
<form action="" method="post">
    <?php wp_nonce_field('wpaicg_chat_nonce'); ?>
    <h3 class="title"><?php echo esc_html__('Text to Speech','gpt3-ai-content-generator')?></h3>
    <p>
    <?php 
    printf(
        /* translators: 1: Start link tag, 2: End link tag */
        esc_html__('Read tutorial %1$shere%2$s', 'gpt3-ai-content-generator'), 
        '<a href="https://docs.aipower.org/docs/ChatGPT/advanced-setup/voice-chat#voice-output" target="_blank">', 
        '</a>'
    ); 
    ?>
    </p>
    <table class="form-table">
        <tr>
            <th><?php echo esc_html__('ElevenLabs API Key','gpt3-ai-content-generator')?></th>
            <td>
                <input type="text" class="regular-text wpaicg_elevenlabs_api" value="<?php echo esc_html($wpaicg_elevenlabs_api)?>" name="wpaicg_elevenlabs_api">
                <a href="https://beta.elevenlabs.io/speech-synthesis" target="_blank"><?php echo esc_html__('Get API Key','gpt3-ai-content-generator')?></a>
            </td>
        </tr>
        <tr style="<?php echo empty($wpaicg_elevenlabs_api) ? 'display:none' : ''?>" class="wpaicg_elevenlabs_service">
            <th><?php echo esc_html__('ElevenLabs Voices','gpt3-ai-content-generator')?></th>
            <td><button class="button button-primary wpaicg_sync_voices" type="button"><?php echo esc_html__('Sync','gpt3-ai-content-generator')?></button></td>
        </tr>
        <tr style="<?php echo empty($wpaicg_elevenlabs_api) ? 'display:none' : ''?>" class="wpaicg_elevenlabs_service">
            <th><?php echo esc_html__('ElevenLabs Models','gpt3-ai-content-generator')?></th>
            <td><button class="button button-primary wpaicg_sync_models" type="button"><?php echo esc_html__('Sync','gpt3-ai-content-generator')?></button></td>
        </tr>

        <tr>
            <th><?php echo esc_html__('Google API Key','gpt3-ai-content-generator')?></th>
            <td>
                <input type="text" class="regular-text wpaicg_google_api_key" value="<?php echo esc_html($wpaicg_google_api_key)?>" name="wpaicg_google_api_key">
                <a href="https://cloud.google.com/text-to-speech" target="_blank"><?php echo esc_html__('Get API Key','gpt3-ai-content-generator')?></a>
            </td>
        </tr>
        <tr style="<?php echo empty($wpaicg_google_api_key) ? 'display:none' : ''?>" class="wpaicg_google_service">
            <th><?php echo esc_html__('Google Voices','gpt3-ai-content-generator')?></th>
            <td><button class="button button-primary wpaicg_sync_google_voices" type="button"><?php echo esc_html__('Sync','gpt3-ai-content-generator')?></button></td>
        </tr>
        <tr>
            <th><?php echo esc_html__('Hide errors in chat','gpt3-ai-content-generator')?></th>
            <td><input<?php echo $wpaicg_elevenlabs_hide_error ? ' checked':''?> type="checkbox" class="wpaicg_elevenlabs_hide_error" value="1" name="wpaicg_elevenlabs_hide_error"></td>
        </tr>
    </table>
    <h3 class="title"><?php echo esc_html__('Token Management','gpt3-ai-content-generator')?></h3>
    <table class="form-table">
        <tr>
            <th><?php echo esc_html__('Enable Token Purchase?','gpt3-ai-content-generator')?></th>
            <td><input<?php echo $wpaicg_chat_enable_sale ? ' checked':''?> type="checkbox" class="wpaicg_chat_enable_sale" value="1" name="wpaicg_chat_enable_sale"></td>
        </tr>
    </table>
    <h3 class="title"><?php echo esc_html__('Security','gpt3-ai-content-generator')?></h3>
        <table class="form-table">
            <tr>
                <th><?php echo esc_html__('Banned IP Addresses','gpt3-ai-content-generator')?></th>
                <td>
                    <input type="text" class="regular-text" value="<?php echo esc_attr(get_option('wpaicg_banned_ips', ''))?>" name="wpaicg_banned_ips" placeholder="e.g., 123.456.789.0, 987.654.321.0">
                    <p class="description"><?php echo esc_html__('Enter IP addresses separated by commas.','gpt3-ai-content-generator')?></p>
                </td>
            </tr>
            <tr>
                <th><?php echo esc_html__('Banned Words','gpt3-ai-content-generator')?></th>
                <td>
                    <input type="text" class="regular-text" value="<?php echo esc_attr(get_option('wpaicg_banned_words', ''))?>" name="wpaicg_banned_words" placeholder="e.g., badword1, badword2">
                    <p class="description"><?php echo esc_html__('Enter words separated by commas.','gpt3-ai-content-generator')?></p>
                </td>
            </tr>
        </table>
        <h3 class="title"><?php echo esc_html__('Assistants','gpt3-ai-content-generator')?></h3>
        <table class="form-table">
            <tr>
                <th><?php echo esc_html__('Enable Assistants', 'gpt3-ai-content-generator'); ?></th>
                <td>
                    <?php $wpaicg_assistant_feature = get_option('wpaicg_assistant_feature', 0); ?>
                    <input type="checkbox" name="wpaicg_assistant_feature" value="1" <?php checked(1, $wpaicg_assistant_feature, true); ?>>
                </td>
            </tr>
        </table>
    <p class="submit"><button class="button button-primary" name="wpaicg_chat_save"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button></p>
</form>
<script>
    jQuery(document).ready(function($){

        function showMessageSuccess(message) {
            $("#wpaicg_message").css({
                'color': 'green',
            }).text(message).fadeIn().delay(10000).fadeOut();
        }

        function showMessageError(message) {
            $("#wpaicg_message").css({
                'color': 'red',
            }).text(message).fadeIn().delay(10000).fadeOut();
        }

        $('.wpaicg_google_api_key').on('input', function (){
            if($(this).val() === ''){
                $('.wpaicg_google_service').hide();
            }
            else{
                $('.wpaicg_google_service').show();
            }
        });
        $('.wpaicg_elevenlabs_api').on('input', function (){
            if($(this).val() === ''){
                $('.wpaicg_elevenlabs_service').hide();
            }
            else{
                $('.wpaicg_elevenlabs_service').show();
            }
        });
        $('.wpaicg_sync_voices').click(function(){
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php')?>',
                data: {action: 'wpaicg_sync_voices',nonce: '<?php echo wp_create_nonce('wpaicg_sync_voices')?>'},
                dataType: 'json',
                type: 'post',
                beforeSend: function(){
                    $('.wpaicg_sync_voices').attr('disabled','disabled');
                    $('.wpaicg_sync_voices').text('<?php echo esc_html__('Syncing voices...Please wait...','gpt3-ai-content-generator')?>');
                },
                success: function(res){
                $('.wpaicg_sync_voices').removeAttr('disabled');
                $('.wpaicg_sync_voices').text('<?php echo esc_html__('Sync','gpt3-ai-content-generator')?>');
                if(res.status === 'success') {
                    showMessageSuccess('<?php echo esc_html__('Voices synced successfully!','gpt3-ai-content-generator')?>');
                } else {
                    showMessageError(res.message);
                }
            }


            })
        })
        $('.wpaicg_sync_google_voices').click(function(){
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php')?>',
                data: {action: 'wpaicg_sync_google_voices',nonce: '<?php echo wp_create_nonce('wpaicg_sync_google_voices')?>'},
                dataType: 'json',
                type: 'post',
                beforeSend: function(){
                    $('.wpaicg_sync_google_voices').attr('disabled','disabled');
                    $('.wpaicg_sync_google_voices').text('<?php echo esc_html__('Syncing voices...Please wait...','gpt3-ai-content-generator')?>');
                },
                success: function(res){
                    $('.wpaicg_sync_google_voices').removeAttr('disabled');
                    $('.wpaicg_sync_google_voices').text('<?php echo esc_html__('Sync','gpt3-ai-content-generator')?>');
                    if(res.status === 'success'){
                        showMessageSuccess('<?php echo esc_html__('Voices synced successfully!','gpt3-ai-content-generator')?>');
                    }else{
                        showMessageError(res.msg);
                    }
                }


            });
        })
        $('.wpaicg_sync_models').click(function(){
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php')?>',
                data: {action: 'wpaicg_sync_models',nonce: '<?php echo wp_create_nonce('wpaicg_sync_models')?>'},
                dataType: 'json',
                type: 'post',
                beforeSend: function(){
                    $('.wpaicg_sync_models').attr('disabled','disabled');
                    $('.wpaicg_sync_models').text('<?php echo esc_html__('Syncing models...Please wait...','gpt3-ai-content-generator')?>');
                },
                success: function(res){
                $('.wpaicg_sync_models').removeAttr('disabled');
                $('.wpaicg_sync_models').text('<?php echo esc_html__('Sync','gpt3-ai-content-generator')?>');
                if(res.status === 'success') {
                    showMessageSuccess('<?php echo esc_html__('Models synced successfully!','gpt3-ai-content-generator')?>');
                } else {
                    showMessageError(res.message);
                }
            }

            });
        });

    })
</script>
