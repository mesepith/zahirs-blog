<?php
namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;

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

$wpaicg_parameters = array(
    'type' => 'topic',
    'post_type' => 'post',
    'provider' => 'openai',
    'model' => 'gpt-3.5-turbo-16k',
    'azure_deployment' => '',
    'google_model' => 'gemini-pro',
    'temperature' => '0.7',
    'max_tokens' => 3000,
    'top_p' => '0.01',
    'best_of' => 1,
    'frequency_penalty' => '0.01',
    'presence_penalty' => '0.01',
    'prompt_title' => esc_html__('Suggest [count] title for an article about [topic]','gpt3-ai-content-generator'),
    'prompt_section' => esc_html__('Write [count] consecutive headings for an article about [title]','gpt3-ai-content-generator'),
    'prompt_content' => esc_html__('Write a comprehensive article about [title], covering the following subtopics [sections]. Each subtopic should have at least [count] paragraphs. Use a cohesive structure to ensure smooth transitions between ideas. Include relevant statistics, examples, and quotes to support your arguments and engage the reader.','gpt3-ai-content-generator'),
    'prompt_meta' => esc_html__('Write a meta description about [title]. Max: 155 characters.','gpt3-ai-content-generator'),
    'prompt_excerpt' => esc_html__('Generate an excerpt for [title]. Max: 55 words.','gpt3-ai-content-generator')
);
$wpaicg_all_templates = get_posts(array(
    'post_type' => 'wpaicg_mtemplate',
    'posts_per_page' => -1
));
$wpaicg_templates = array(array(
    'title' => 'Default',
    'content' => $wpaicg_parameters
));
// foreach ($wpaicg_all_templates as $wpaicg_all_template){
//     $wpaicg_template_content = is_serialized($wpaicg_all_template->post_content) ? unserialize($wpaicg_all_template->post_content) : array();
//     $wpaicg_template_content = wp_parse_args($wpaicg_template_content,$wpaicg_parameters);
//     $wpaicg_templates[$wpaicg_all_template->ID] = array(
//         'title' => $wpaicg_all_template->post_title,
//         'content' => $wpaicg_template_content
//     );
// }
foreach ($wpaicg_all_templates as $wpaicg_all_template) {
    $wpaicg_template_content = json_decode($wpaicg_all_template->post_content, true);

    if (!is_array($wpaicg_template_content)) {
        $wpaicg_template_content = array();
    }

    $wpaicg_template_content = wp_parse_args($wpaicg_template_content, $wpaicg_parameters);
    $wpaicg_templates[$wpaicg_all_template->ID] = array(
        'title' => $wpaicg_all_template->post_title,
        'content' => $wpaicg_template_content
    );
}

$default_name = '';
if(isset($selected_template) && !empty($selected_template)){
    $wpaicg_parameters = $wpaicg_templates[$selected_template]['content'];
    $default_name = $wpaicg_templates[$selected_template]['title'];
}
?>
<style>
    .wpaicg-notice-info {
    color: #31708f;
    padding: 5px;
    border: 1px solid #31708f;
    border-radius: 3px;
    margin-bottom: 10px;
    }
</style>
<h3><?php echo esc_html__('Settings', 'gpt3-ai-content-generator'); ?></h3>
<div class="wpaicg-custom-parameters-content">
    <div class="wpaicg-form-field">
        <label><strong><?php echo esc_html__('Template','gpt3-ai-content-generator')?>:</strong></label>
        <select class="wpaicg_custom_template_select regular-text">
            <?php
            foreach ($wpaicg_templates as $key=>$wpaicg_template){
                echo '<option'.(isset($selected_template) && $selected_template == $key ? ' selected':'').' class="wpaicg_custom_template_'.esc_html($key).'" data-parameters="'.esc_html(json_encode($wpaicg_template['content'], JSON_UNESCAPED_UNICODE)).'" value="'.esc_html($key).'">'.esc_html($wpaicg_template['title']).'</option>';
            }
            ?>
        </select>
    </div>
    <div class="wpaicg-form-field">
        <label><strong><?php echo esc_html__('Name','gpt3-ai-content-generator')?>:</strong></label>
        <input value="<?php echo esc_html($default_name)?>" type="text" class="regular-text wpaicg_custom_template_title" name="title" placeholder="<?php echo esc_attr__('Enter a Template Name', 'gpt3-ai-content-generator')?>">
        <?php
        if(isset($selected_template) && !empty($selected_template)){
        ?>
            <input class="wpaicg_custom_template_id" type="hidden" name="id" value="<?php echo esc_html($selected_template)?>">
        <?php
        }
        ?>
    </div>
    <!-- Getting custom post types dynamically. Added by Hung Le. -->
    <div class="wpaicg-form-field">
        <label><strong><?php echo esc_html__('Type','gpt3-ai-content-generator')?>:</strong></label>
        <select name="template[post_type]" class="regular-text wpaicg_custom_template_post_type">
            <?php 
            $args = array(
            'public'   => true,
            '_builtin' => false
            );
            $post_types = get_post_types($args);
            $post_types = array_merge($post_types, ['post', 'page']); // to include post and page
            foreach ($post_types as $post_type) {
                $selected = (isset($wpaicg_parameters['post_type']) && $wpaicg_parameters['post_type'] == $post_type) ? ' selected' : '';
                echo '<option value="'.esc_html($post_type).'"'.$selected.'>'.esc_html(ucfirst($post_type)).'</option>';
            }
            ?>
        </select>
    </div>
    <div class="wpaicg-form-field">
        <label><strong><?php echo esc_html__('Provider','gpt3-ai-content-generator')?>:</strong></label>
        <select name="template[provider]" class="wpaicg_custom_template_provider regular-text">
            <option value="openai" selected>OpenAI</option>
            <option value="azure">Azure</option>
            <option value="google">Google</option>
        </select>
    </div>

    <div class="wpaicg-form-field">
    <label><strong><?php echo esc_html__('Model','gpt3-ai-content-generator')?>:</strong></label>
            <!-- Display dropdown for OpenAI -->
            <select name="template[model]" class="regular-text wpaicg_custom_template_model">
                <?php // Function to display options
                function display_options($models, $selected_model){
                    foreach ($models as $model_key => $model_name): ?>
                        <option value="<?php echo esc_attr($model_key); ?>"<?php selected($model_key, $selected_model); ?>><?php echo esc_html($model_name); ?></option>
                    <?php endforeach;
                }
                ?>
                <optgroup label="GPT-4">
                    <?php display_options($gpt4_models, $wpaicg_parameters['model']); ?>
                </optgroup>
                <optgroup label="GPT-3.5">
                    <?php display_options($gpt35_models, $wpaicg_parameters['model']); ?>
                </optgroup>
                <optgroup label="Custom Models">
                    <?php display_options($custom_models, $wpaicg_parameters['model']); ?>
                </optgroup>
            </select>
    </div>
    <div class="wpaicg-form-field" style="display:none;" id="azure-deployment-field">
        <label><strong><?php echo esc_html__('Model','gpt3-ai-content-generator')?>:</strong></label>
        <input type="text" name="template[azure_deployment]" class="wpaicg_custom_template_azure_deployment regular-text" value="<?php echo esc_attr($wpaicg_parameters['azure_deployment'])?>">
    </div>
    <!-- Google Models Dropdown -->
    <div class="wpaicg-form-field" style="display:none;" id="google-models-field">
        <label><strong><?php echo esc_html__('Model','gpt3-ai-content-generator')?>:</strong></label>
        <select name="template[google_model]" class="wpaicg_custom_template_google_model regular-text">
            <option value="gemini-pro"><?php echo esc_html__('Gemini Pro', 'gpt3-ai-content-generator'); ?></option>
        </select>
    </div>
    <!-- Toggle hyperlink -->
    <div class="wpaicg-form-field">
        <label><strong><?php echo esc_html__('Details','gpt3-ai-content-generator')?>:</strong></label>
        <a href="#" id="toggle-parameters"><?php echo esc_html__('Show Parameters', 'gpt3-ai-content-generator'); ?></a> <span id="dash">-</span> <a href="#" id="show-api-info"><?php echo esc_html__('API Settings', 'gpt3-ai-content-generator'); ?></a>
    </div>
    <!-- OpenAI API Info Status -->
    <div class="wpaicg-form-field" style="display:none;" id="openai-api-info">
        <label><strong><?php echo esc_html__('OpenAI API Key:', 'gpt3-ai-content-generator'); ?></strong> </label>
        <span id="openai-api-key-icon"></span><span id="openai-api-key-msg"></span>
    </div>
    <!-- Azure API Info Status -->
    <div class="wpaicg-form-field" style="display:none;" id="azure-api-info">
        <label><strong><?php echo esc_html__('Azure API Key:', 'gpt3-ai-content-generator'); ?></strong> </label>
        <span id="azure-api-key-icon"></span><span id="azure-api-key-msg"></span>
    </div>
    <div class="wpaicg-form-field" style="display:none;" id="azure-endpoint-info">
        <label><strong><?php echo esc_html__('Azure Endpoint:', 'gpt3-ai-content-generator'); ?></strong> </label>
        <span id="azure-endpoint-icon"></span><span id="azure-endpoint-msg"></span>
    </div>
    <!-- Google API Info Status -->
    <div class="wpaicg-form-field" style="display:none;" id="google-api-info">
        <label><strong><?php echo esc_html__('Google API Key:', 'gpt3-ai-content-generator'); ?></strong> </label>
        <span id="google-api-key-icon"></span><span id="google-api-key-msg"></span>
    </div>
    <?php
        $labels = array(
            'temperature' => esc_html__('Temperature', 'gpt3-ai-content-generator'),
            'max_tokens' => esc_html__('Max Tokens', 'gpt3-ai-content-generator'),
            'top_p' => esc_html__('Top P', 'gpt3-ai-content-generator'),
            'frequency_penalty' => esc_html__('Frequency Penalty', 'gpt3-ai-content-generator'),
            'presence_penalty' => esc_html__('Presence Penalty', 'gpt3-ai-content-generator')
        );

        foreach($labels as $item => $label) {
            ?>
            <div class="wpaicg-form-field">
                <label><strong><?php echo $label; ?>:</strong></label>
                <input type="text" value="<?php echo esc_attr($wpaicg_parameters[$item]); ?>" class="wpaicg_custom_template_<?php echo esc_attr($item); ?>" name="template[<?php echo esc_attr($item); ?>]" style="width: 80px">
            </div>
            <?php
        }
    ?>
</div>
<h3><?php echo esc_html__('Prompts', 'gpt3-ai-content-generator'); ?></h3>
<div class="wpaicg-custom-parameters-content">
    <div class="wpaicg-mb-10">
        <label class="mb-5" style="display: block"><strong><?php echo esc_html__('Prompt for Title','gpt3-ai-content-generator')?>:</strong></label>
        <textarea class="wpaicg_custom_template_prompt_title" name="template[prompt_title]" rows="2"><?php echo esc_html($wpaicg_parameters['prompt_title'])?></textarea>
        <p style="margin-top: 0;font-size: 13px;font-style: italic;"><?php echo sprintf(esc_html__('Ensure %s and %s is included in your prompt.','gpt3-ai-content-generator'),'<code>[count]</code>','<code>[topic]</code>')?></code></p>
    </div>
    <div class="wpaicg-mb-10">
        <label class="mb-5" style="display: block"><strong><?php echo esc_html__('Prompt for Sections','gpt3-ai-content-generator')?>:</strong></label>
        <textarea class="wpaicg_custom_template_prompt_section" name="template[prompt_section]" rows="2"><?php echo esc_html($wpaicg_parameters['prompt_section'])?></textarea>
        <p style="margin-top: 0;font-size: 13px;font-style: italic;"><?php echo sprintf(esc_html__('Ensure %s and %s is included in your prompt.','gpt3-ai-content-generator'),'<code>[count]</code>','<code>[title]</code>')?></code></p>
    </div>
    <div class="wpaicg-mb-10">
        <label class="mb-5" style="display: block"><strong><?php echo esc_html__('Prompt for Content','gpt3-ai-content-generator')?>:</strong></label>
        <textarea class="wpaicg_custom_template_prompt_content" name="template[prompt_content]" rows="5"><?php echo esc_html($wpaicg_parameters['prompt_content'])?></textarea>
        <p style="margin-top: 0;font-size: 13px;font-style: italic;"><?php echo sprintf(esc_html__('Ensure %s, %s and %s is included in your prompt.','gpt3-ai-content-generator'),'<code>[title]</code>','<code>[sections]</code>','<code>[count]</code>')?></code></p>
    </div>
    <div class="wpaicg-mb-10">
        <label class="mb-5" style="display: block"><strong><?php echo esc_html__('Prompt for Excerpt','gpt3-ai-content-generator')?>:</strong></label>
        <textarea class="wpaicg_custom_template_prompt_excerpt" name="template[prompt_excerpt]" rows="2"><?php echo esc_html($wpaicg_parameters['prompt_excerpt'])?></textarea>
        <p style="margin-top: 0;font-size: 13px;font-style: italic;"><?php echo sprintf(esc_html__('Ensure %s is included in your prompt.','gpt3-ai-content-generator'),'<code>[title]</code>')?></code></p>
    </div>
    <div class="wpaicg-mb-10">
        <label class="mb-5" style="display: block"><strong><?php echo esc_html__('Prompt for Meta','gpt3-ai-content-generator')?>:</strong></label>
        <textarea class="wpaicg_custom_template_prompt_meta" name="template[prompt_meta]" rows="2"><?php echo esc_html($wpaicg_parameters['prompt_meta'])?></textarea>
        <p style="margin-top: 0;font-size: 13px;font-style: italic;"><?php echo sprintf(esc_html__('Ensure %s is included in your prompt.','gpt3-ai-content-generator'),'<code>[title]</code>')?></code></p>
    </div>
    <div style="display: flex;justify-content: space-between">
        <div>
            <button style="<?php echo isset($selected_template) ? '' : 'display:none'?>" type="button" class="button button-primary wpaicg_template_update"><?php echo esc_html__('Update','gpt3-ai-content-generator')?></button>
            <button type="button" class="button button-primary wpaicg_template_save"><?php echo esc_html__('Save Template','gpt3-ai-content-generator')?></button>
        </div>
        <button type="button" class="button button-link-delete wpaicg_template_delete" style="<?php echo isset($selected_template) ? '' : 'display:none'?>"><?php echo esc_html__('Delete','gpt3-ai-content-generator')?></button>
    </div>
</div>
<script>
    jQuery(document).ready(function ($) {
        const providerSelect = $('.wpaicg_custom_template_provider');
        const modelSelect = $('.wpaicg_custom_template_model');
        const modelSelectDiv = modelSelect.parent();
        const azureFieldDiv = $('#azure-deployment-field');

        let isApiInfoVisible = true;

        const apiInfoLink = $('#show-api-info');

        // Function to show/hide API info fields
        function toggleAPIInfo() {
            const azureApiKey = "<?php echo esc_attr(get_option('wpaicg_azure_api_key', '')); ?>";
            const azureEndpoint = "<?php echo esc_attr(get_option('wpaicg_azure_endpoint', '')); ?>";
            const googleApiKey = "<?php echo esc_attr(get_option('wpaicg_google_model_api_key', '')); ?>";
            const openaiApiInfoDiv = $('#openai-api-info');

            const azureApiInfoDiv = $('#azure-api-info');
            const azureEndpointInfoDiv = $('#azure-endpoint-info');
            const googleApiInfoDiv = $('#google-api-info');
            const selectedProvider = providerSelect.val();

            // URLs for settings pages of each provider
            const azureSettingsUrl = '<?php echo admin_url(); ?>' + 'admin.php?page=wpaicg';
            const googleSettingsUrl = '<?php echo admin_url(); ?>' + 'admin.php?page=wpaicg_single_content&action=playground';

            // Function to get the "Not Set" message with the appropriate link
            function getNotSetMessage(providerSettingsUrl) {
                return '<?php echo esc_js(__('Your configuration is not complete. Add your info', 'gpt3-ai-content-generator')); ?> <a href="' + providerSettingsUrl + '" target="_blank"><?php echo esc_js(__('here', 'gpt3-ai-content-generator')); ?></a>.';
            }

            // SVG icons
            const okIconSVG = '<svg xmlns="http://www.w3.org/2000/svg" height="16" width="16" viewBox="0 0 512 512"><path fill="#00bd16" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>'; 
            const notSetIconSVG = '<svg xmlns="http://www.w3.org/2000/svg" height="16" width="16" viewBox="0 0 512 512"><path fill="#eed202" d="M256 32c14.2 0 27.3 7.5 34.5 19.8l216 368c7.3 12.4 7.3 27.7 .2 40.1S486.3 480 472 480H40c-14.3 0-27.6-7.7-34.7-20.1s-7-27.8 .2-40.1l216-368C228.7 39.5 241.8 32 256 32zm0 128c-13.3 0-24 10.7-24 24V296c0 13.3 10.7 24 24 24s24-10.7 24-24V184c0-13.3-10.7-24-24-24zm32 224a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z"/></svg>';

            if (!isApiInfoVisible) {
                // Update the icon and message based on the API key and endpoint values
                const azureNotSetMessage = getNotSetMessage(azureSettingsUrl);
                const googleNotSetMessage = getNotSetMessage(googleSettingsUrl);

                $('#azure-api-key-icon').html(azureApiKey ? okIconSVG : notSetIconSVG);
                $('#azure-api-key-msg').html(azureApiKey ? '' : azureNotSetMessage);
                $('#azure-endpoint-icon').html(azureEndpoint ? okIconSVG : notSetIconSVG);
                $('#azure-endpoint-msg').html(azureEndpoint ? '' : azureNotSetMessage);
                $('#google-api-key-icon').html(googleApiKey ? okIconSVG : notSetIconSVG);
                $('#google-api-key-msg').html(googleApiKey ? '' : googleNotSetMessage);

                // Show the relevant API info based on the selected provider
                if (selectedProvider === 'azure') {
                    azureApiInfoDiv.css('display', 'flex');
                    azureEndpointInfoDiv.css('display', 'flex');
                } else if (selectedProvider === 'google') {
                    googleApiInfoDiv.css('display', 'flex');
                } else if (selectedProvider === 'openai') {
                    $('#openai-api-key-icon').html(okIconSVG);
                    openaiApiInfoDiv.css('display', 'flex');
                }
                apiInfoLink.text('<?php echo esc_js(__('Hide API Settings', 'gpt3-ai-content-generator')); ?>');
            } else {
                // Hide all API info
                azureApiInfoDiv.css('display', 'none');
                azureEndpointInfoDiv.css('display', 'none');
                googleApiInfoDiv.css('display', 'none');
                openaiApiInfoDiv.css('display', 'none');
                apiInfoLink.text('<?php echo esc_js(__('Show API Settings', 'gpt3-ai-content-generator')); ?>');
            }
            isApiInfoVisible = !isApiInfoVisible;
        }


        apiInfoLink.on('click', function (event) {
            event.preventDefault();
            toggleAPIInfo();
        });

        // Hide API Info fields initially
        toggleAPIInfo();

        // Define the specific classes for the parameters we want to toggle
        const parameterClasses = [
            'wpaicg_custom_template_temperature', 
            'wpaicg_custom_template_max_tokens', 
            'wpaicg_custom_template_top_p', 
            'wpaicg_custom_template_frequency_penalty', 
            'wpaicg_custom_template_presence_penalty'
        ];

        // Select parameter fields based on the specific classes
        const parameterFields = parameterClasses.map(cls => $('.' + cls).closest('.wpaicg-form-field')).filter(el => el.length > 0);
        const toggleLink = $('#toggle-parameters');
        let parametersVisible = true; // Set this to true initially

        // Function to toggle the visibility of parameter fields
        function toggleParameters() {
            const isGoogleSelected = providerSelect.val() === 'google';

            parameterFields.forEach(field => {
                // Check if the field contains a frequency or presence penalty input field
                const containsFrequencyPenalty = field.find('.wpaicg_custom_template_frequency_penalty').length > 0;
                const containsPresencePenalty = field.find('.wpaicg_custom_template_presence_penalty').length > 0;

                // Check if it's a frequency or presence penalty field and if Google is selected
                if (isGoogleSelected && (containsFrequencyPenalty || containsPresencePenalty)) {
                    field.css('display', 'none');
                } else {
                    field.css('display', parametersVisible ? 'none' : 'flex');
                }
            });

            toggleLink.text(parametersVisible ? '<?php echo esc_js(__('Show Parameters', 'gpt3-ai-content-generator')); ?>' : '<?php echo esc_js(__('Hide Parameters', 'gpt3-ai-content-generator')); ?>');
            parametersVisible = !parametersVisible;
        }

        // Hide the parameters initially
        toggleParameters();

        // Event listener for the toggle link
        toggleLink.on('click', function (event) {
            event.preventDefault();
            toggleParameters();
        });

        // Event listener for provider select change
        providerSelect.on('change', function () {
            // Check if the API info is currently visible
            if (isApiInfoVisible) {
                // If visible, toggle to hide it
                toggleAPIInfo();
            }

            if ($(this).val() === 'azure') {
                modelSelectDiv.css('display', 'none');
                azureFieldDiv.css('display', 'flex');
            } 
            else if ($(this).val() === 'google') {
                modelSelectDiv.css('display', 'none');
                azureFieldDiv.css('display', 'none');
            }
            else {
                modelSelectDiv.css('display', 'flex');
                azureFieldDiv.css('display', 'none');
            }
        });
    });
</script>
