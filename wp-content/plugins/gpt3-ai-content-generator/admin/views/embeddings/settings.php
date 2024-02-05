<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;

$wpaicg_embeddings_settings_updated = false;

// Retrieving the saved Embeddings Model option
$wpaicg_openai_embeddings = get_option('wpaicg_openai_embeddings', 'text-embedding-ada-002');
// Retrieving the provider option
$wpaicg_provider = get_option('wpaicg_provider', 'OpenAI');

if(isset($_POST['wpaicg_save_builder_settings'])){
    check_admin_referer('wpaicg_embeddings_settings');
    if(isset($_POST['wpaicg_pinecone_api']) && !empty($_POST['wpaicg_pinecone_api'])) {
        update_option('wpaicg_pinecone_api', sanitize_text_field($_POST['wpaicg_pinecone_api']));
    }
    else{
        delete_option('wpaicg_pinecone_api');
    }
    if(isset($_POST['wpaicg_pinecone_environment']) && !empty($_POST['wpaicg_pinecone_environment'])) {
        update_option('wpaicg_pinecone_environment', sanitize_text_field($_POST['wpaicg_pinecone_environment']));
    }
    else{
        delete_option('wpaicg_pinecone_environment');
    }

    if(isset($_POST['wpaicg_builder_enable']) && !empty($_POST['wpaicg_builder_enable'])){
        update_option('wpaicg_builder_enable','yes');
    }
    else{
        delete_option('wpaicg_builder_enable');
    }
    if(isset($_POST['wpaicg_builder_types']) && is_array($_POST['wpaicg_builder_types']) && count($_POST['wpaicg_builder_types'])){
        update_option('wpaicg_builder_types',\WPAICG\wpaicg_util_core()->sanitize_text_or_array_field($_POST['wpaicg_builder_types']));
    }
    else{
        delete_option('wpaicg_builder_types');
    }
    if(isset($_POST['wpaicg_instant_embedding']) && !empty($_POST['wpaicg_instant_embedding'])){
        update_option('wpaicg_instant_embedding',\WPAICG\wpaicg_util_core()->sanitize_text_or_array_field($_POST['wpaicg_instant_embedding']));
    }
    else{
        update_option('wpaicg_instant_embedding','no');
    }
    if(isset($_POST['wpaicg_vector_db_provider'])) {
        update_option('wpaicg_vector_db_provider', sanitize_text_field($_POST['wpaicg_vector_db_provider']));
    }
    if(isset($_POST['wpaicg_qdrant_api_key'])) {
        update_option('wpaicg_qdrant_api_key', sanitize_text_field($_POST['wpaicg_qdrant_api_key']));
    }
    if(isset($_POST['wpaicg_qdrant_endpoint'])) {

        $qdrantEndpoint = sanitize_text_field($_POST['wpaicg_qdrant_endpoint']);
        // Check if ':6333' is already part of the endpoint
        if(strpos($qdrantEndpoint, ':6333') === false) {
            // Append ':6333' if not present
            $qdrantEndpoint .= ':6333';
        }
        update_option('wpaicg_qdrant_endpoint', $qdrantEndpoint);
        
    }

    // Save the currently selected Qdrant collection as the default
    $selected_qdrant_collection = isset($_POST['wpaicg_qdrant_collections']) ? sanitize_text_field($_POST['wpaicg_qdrant_collections']) : '';
    update_option('wpaicg_qdrant_default_collection', $selected_qdrant_collection);
    
    // Saving the Embeddings Model option
    $wpaicg_openai_embeddings_value = isset($_POST['wpaicg_openai_embeddings']) ? sanitize_text_field($_POST['wpaicg_openai_embeddings']) : 'text-embedding-ada-002';
    update_option('wpaicg_openai_embeddings', $wpaicg_openai_embeddings_value);
    // Update the variable to reflect the new setting immediately
    $wpaicg_openai_embeddings = get_option('wpaicg_openai_embeddings', 'text-embedding-ada-002');
    
    $wpaicg_embeddings_settings_updated = true;
}
$default_qdrant_collection = get_option('wpaicg_qdrant_default_collection', '');
// Retrieve the current default vector DB setting
$wpaicg_vector_db_provider = get_option('wpaicg_vector_db_provider', 'pinecone');
$wpaicg_qdrant_collections = get_option('wpaicg_qdrant_collections', []);
$wpaicg_pinecone_api = get_option('wpaicg_pinecone_api','');
$wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment','');
$wpaicg_builder_types = get_option('wpaicg_builder_types',[]);
$wpaicg_builder_enable = get_option('wpaicg_builder_enable','');
$wpaicg_instant_embedding = get_option('wpaicg_instant_embedding','yes');
$wpaicg_pinecone_indexes = get_option('wpaicg_pinecone_indexes','');
$wpaicg_pinecone_indexes = empty($wpaicg_pinecone_indexes) ? array() : json_decode($wpaicg_pinecone_indexes,true);
$wpaicg_pinecone_environments = array(
    'asia-northeast1-gcp' => 'GCP Asia-Northeast-1 (Tokyo)',
    'asia-northeast1-gcp-free' => 'GCP Asia-Northeast-1 Free (Tokyo)',
    'asia-northeast2-gcp' => 'GCP Asia-Northeast-2 (Osaka)',
    'asia-northeast2-gcp-free' => 'GCP Asia-Northeast-2 Free (Osaka)',
    'asia-northeast3-gcp' => 'GCP Asia-Northeast-3 (Seoul)',
    'asia-northeast3-gcp-free' => 'GCP Asia-Northeast-3 Free (Seoul)',
    'asia-southeast1-gcp' => 'GCP Asia-Southeast-1 (Singapore)',
    'asia-southeast1-gcp-free' => 'GCP Asia-Southeast-1 Free',
    'eu-west1-gcp' => 'GCP EU-West-1 (Ireland)',
    'eu-west1-gcp-free' => 'GCP EU-West-1 Free (Ireland)',
    'eu-west2-gcp' => 'GCP EU-West-2 (London)',
    'eu-west2-gcp-free' => 'GCP EU-West-2 Free (London)',
    'eu-west3-gcp' => 'GCP EU-West-3 (Frankfurt)',
    'eu-west3-gcp-free' => 'GCP EU-West-3 Free (Frankfurt)',
    'eu-west4-gcp' => 'GCP EU-West-4 (Netherlands)',
    'eu-west4-gcp-free' => 'GCP EU-West-4 Free (Netherlands)',
    'eu-west6-gcp' => 'GCP EU-West-6 (Zurich)',
    'eu-west6-gcp-free' => 'GCP EU-West-6 Free (Zurich)',
    'eu-west8-gcp' => 'GCP EU-West-8 (Italy)',
    'eu-west8-gcp-free' => 'GCP EU-West-8 Free (Italy)',
    'eu-west9-gcp' => 'GCP EU-West-9 (France)',
    'eu-west9-gcp-free' => 'GCP EU-West-9 Free (France)',
    'gcp-starter' => 'GCP Starter',
    'northamerica-northeast1-gcp' => 'GCP Northamerica-Northeast1',
    'northamerica-northeast1-gcp-free' => 'GCP Northamerica-Northeast1 Free',
    'southamerica-northeast2-gcp' => 'GCP Southamerica-Northeast2 (Toronto)',
    'southamerica-northeast2-gcp-free' => 'GCP Southamerica-Northeast2 Free (Toronto)',
    'southamerica-east1-gcp' => 'GCP Southamerica-East1 (Sao Paulo)',
    'southamerica-east1-gcp-free' => 'GCP Southamerica-East1 Free (Sao Paulo)',
    'us-central1-gcp' => 'GCP US-Central-1 (Iowa)',
    'us-central1-gcp-free' => 'GCP US-Central-1 Free (Iowa)',
    'us-east1-aws' => 'AWS US-East-1 (Virginia)',
    'us-east1-aws-free' => 'AWS US-East-1 Free (Virginia)',
    'us-east-1-aws' => 'AWS US-East-1 (Virginia)',
    'us-east-1-aws-free' => 'AWS US-East-1 Free (Virginia)',
    'us-east1-gcp' => 'GCP US-East-1 (South Carolina)',
    'us-east1-gcp-free' => 'GCP US-East-1 Free (South Carolina)',
    'us-east4-gcp' =>  'GCP US-East-4 (Virginia)',
    'us-east4-gcp-free' =>  'GCP US-East-4 Free (Virginia)',
    'us-west1-gcp' => 'GCP US-West-1 (N. California)',
    'us-west1-gcp-free' => 'GCP US-West-1 Free (N. California)',
    'us-west-2' => 'US-West-2',
    'us-west2-gcp' => 'GCP US-West-2 (Oregon)',
    'us-west2-gcp-free' => 'GCP US-West-2 Free (Oregon)',
    'us-west3-gcp' => 'GCP US-West-3 (Salt Lake City)',
    'us-west3-gcp-free' => 'GCP US-West-3 Free (Salt Lake City)',
    'us-west4-gcp' => 'GCP US-West-4 (Las Vegas)',
    'us-west4-gcp-free' => 'GCP US-West-4 Free (Las Vegas)'
);
if($wpaicg_embeddings_settings_updated){
    ?>
    <div class="notice notice-success">
        <p><?php echo esc_html__('Records updated successfully','gpt3-ai-content-generator')?></p>
    </div>
    <?php
}
?>
<style>
    /* Modal Style */
    #wpaicg_modal_overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }

    .wpaicg_modal {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 1px 15px rgba(0, 0, 0, 0.2);
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1001;
        width: 40%;
        min-width: 300px;
        max-width: 500px;
        padding: 20px;
        box-sizing: border-box;
    }

    .wpaicg_modal h2 {
        font-size: 20px;
        margin-top: 0;
    }

    .wpaicg_modal_content p {
        font-size: 14px;
        line-height: 1.5;
        color: #333;
    }

    .wpaicg_assign_footer {
        text-align: right;
        margin-top: 20px;
    }

    .wpaicg_assign_footer button {
        margin-left: 10px;
    }

    /* Responsive adjustments */
    @media screen and (max-width: 600px) {
        .wpaicg_modal {
            width: 80%;
        }
    }

</style>
<form action="" method="post">
    <?php
    wp_nonce_field('wpaicg_embeddings_settings');
    ?>
    <table class="form-table">
        <tr>
            <th scope="row"><?php echo esc_html__('Default Vector DB','gpt3-ai-content-generator')?></th>
            <td>
                <select name="wpaicg_vector_db_provider">
                    <option value="pinecone" <?php selected($wpaicg_vector_db_provider, 'pinecone'); ?>>Pinecone</option>
                    <option value="qdrant" <?php selected($wpaicg_vector_db_provider, 'qdrant'); ?>>Qdrant</option>
                </select>
            </td>
        </tr>
    </table>
    <!-- Pinecone Settings -->
    <div id="wpaicg_pinecone_settings">
        <table class="form-table">
            <tr>
                <th scope="row"><?php echo esc_html__('Pinecone API','gpt3-ai-content-generator')?></th>
                <td>
                    <input type="text" class="regular-text wpaicg_pinecone_api" name="wpaicg_pinecone_api" value="<?php echo esc_attr($wpaicg_pinecone_api)?>">
                    <a href="https://www.pinecone.io" target="_blank" style="margin-left: 10px;"><?php echo esc_html__('Get your API key', 'gpt3-ai-content-generator'); ?></a>
                </td>
            </tr>
            <tr>
                <th scope="row">&nbsp;</th>
                <td>
                    <button type="button" class="button button-primary wpaicg_pinecone_indexes"><?php echo esc_html__('Sync Indexes','gpt3-ai-content-generator')?></button>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo esc_html__('Pinecone Index','gpt3-ai-content-generator')?></th>
                <td>
                    <select class="wpaicg_pinecone_environment" name="wpaicg_pinecone_environment" old-value="<?php echo esc_attr($wpaicg_pinecone_environment)?>">
                        <option value=""><?php echo esc_html__('Select Index','gpt3-ai-content-generator')?></option>
                        <?php
                        foreach($wpaicg_pinecone_indexes as $wpaicg_pinecone_index){
                            echo '<option'.($wpaicg_pinecone_environment == $wpaicg_pinecone_index['url'] ? ' selected':'').' value="'.esc_html($wpaicg_pinecone_index['url']).'">'.esc_html($wpaicg_pinecone_index['name']).'</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
    </div>
    <!-- Qdrant Settings -->
    <div id="wpaicg_qdrant_settings" style="display:none;">
        <table class="form-table wpaicg_qdrant_settings">
            <tr>
                <th scope="row"><?php echo esc_html__('Qdrant API Key','gpt3-ai-content-generator')?></th>
                <td>
                    <input type="text" class="regular-text" name="wpaicg_qdrant_api_key" value="<?php echo esc_attr(get_option('wpaicg_qdrant_api_key', '')); ?>">
                    <a href="https://qdrant.tech" target="_blank" style="margin-left: 10px;"><?php echo esc_html__('Get your API key', 'gpt3-ai-content-generator'); ?></a>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo esc_html__('Cluster URL','gpt3-ai-content-generator')?></th>
                <td>
                    <input type="text" class="regular-text" name="wpaicg_qdrant_endpoint" value="<?php echo esc_attr(get_option('wpaicg_qdrant_endpoint', '')); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo esc_html__('Qdrant Collections','gpt3-ai-content-generator')?></th>
                <td>
                    <button type="button" class="button button-primary wpaicg_sync_qdrant_collections"><?php echo esc_html__('Sync Collections','gpt3-ai-content-generator')?></button>
                    <select class="wpaicg_qdrant_collections_dropdown" name="wpaicg_qdrant_collections" style="<?php echo empty($wpaicg_qdrant_collections) ? 'display: none;' : '' ?>">
                    <?php
                    $default_qdrant_collection = get_option('wpaicg_qdrant_default_collection', '');
                    foreach ($wpaicg_qdrant_collections as $collection):
                        $selected = ($collection === $default_qdrant_collection) ? ' selected' : '';
                        echo '<option value="'.esc_attr($collection).'"'.$selected.'>'.esc_html($collection).'</option>';
                    endforeach;
                    ?>
                </select>

                    <button type="button" class="button wpaicg_create_new_collection_btn"><?php echo esc_html__('Create New','gpt3-ai-content-generator')?></button>
                    <div class="wpaicg_new_collection_input" style="display:none; margin-top: 20px;">
                        <input type="text" class="regular-text wpaicg_new_collection_name">
                        <button type="button" class="button button-primary wpaicg_submit_new_collection"><?php echo esc_html__('Create','gpt3-ai-content-generator')?></button>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <?php if ($wpaicg_provider == 'OpenAI'): ?>
    <h3><?php echo esc_html__('Embeddings Model','gpt3-ai-content-generator')?></h3>
    <table class="form-table">
        <tr>
            <th scope="row"><?php echo esc_html__('Select Model','gpt3-ai-content-generator')?></th>
            <td>
                <select name="wpaicg_openai_embeddings">
                    <option value="text-embedding-3-small" <?php selected($wpaicg_openai_embeddings, 'text-embedding-3-small'); ?>><?php echo esc_html__('text-embedding-3-small','gpt3-ai-content-generator')?></option>
                    <option value="text-embedding-3-large" <?php selected($wpaicg_openai_embeddings, 'text-embedding-3-large'); ?>><?php echo esc_html__('text-embedding-3-large','gpt3-ai-content-generator')?></option>
                    <option value="text-embedding-ada-002" <?php selected($wpaicg_openai_embeddings, 'text-embedding-ada-002'); ?>><?php echo esc_html__('text-embedding-ada-002','gpt3-ai-content-generator')?></option>
                </select>
            </td>
        </tr>
    </table>
    <?php endif; ?>
    <h3><?php echo esc_html__('Instant Embedding','gpt3-ai-content-generator')?></h3>
    <p><?php echo esc_html__('Enable this option to get instant embeddings for your content. Go to your post, page or products page and select all your contents and click on Instant Embedding button.','gpt3-ai-content-generator')?></p>
    <table class="form-table">
        <tr>
            <th scope="row"><?php echo esc_html__('Enable','gpt3-ai-content-generator')?>:</th>
            <td>
                <div class="mb-5">
                    <label><input<?php echo $wpaicg_instant_embedding == 'yes' ? ' checked':'';?> type="checkbox" name="wpaicg_instant_embedding" value="yes">
                </div>
            </td>
        </tr>
    </table>
    <h3><?php echo esc_html__('Index Builder','gpt3-ai-content-generator')?></h3>
    <p><?php echo esc_html__('You can use index builder to build your index. Difference between index builder and instant embedding is that once you complete the cron job, index builder will monitor your content and will update the index automatically.','gpt3-ai-content-generator')?></p>
    <table class="form-table">
        <tr>
            <th scope="row"><?php echo esc_html__('Cron Indexing','gpt3-ai-content-generator')?></th>
            <td>
                <select name="wpaicg_builder_enable">
                    <option value=""><?php echo esc_html__('No','gpt3-ai-content-generator')?></option>
                    <option<?php echo esc_html($wpaicg_builder_enable) == 'yes' ? ' selected':'';?> value="yes"><?php echo esc_html__('Yes','gpt3-ai-content-generator')?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo esc_html__('Build Index for','gpt3-ai-content-generator')?>:</th>
            <td>
                <div class="mb-5">
                    <div class="mb-5"><label><input<?php echo in_array('post',$wpaicg_builder_types) ? ' checked':'';?> type="checkbox" name="wpaicg_builder_types[]" value="post">&nbsp;<?php echo esc_html__('Posts','gpt3-ai-content-generator')?></label></div>
                    <div class="mb-5"><label><input<?php echo in_array('page',$wpaicg_builder_types) ? ' checked':'';?> type="checkbox" name="wpaicg_builder_types[]" value="page">&nbsp;<?php echo esc_html__('Pages','gpt3-ai-content-generator')?></label></div>
                    <?php
                    if(class_exists('WooCommerce')):
                        ?>
                        <div class="mb-5">
                            <label><input<?php echo in_array('product',$wpaicg_builder_types) ? ' checked':'';?> type="checkbox" name="wpaicg_builder_types[]" value="product">&nbsp;<?php echo esc_html__('Products','gpt3-ai-content-generator')?></label>
                        </div>
                    <?php
                    endif;
                    ?>
                    <?php
                    if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()){
                        include WPAICG_LIBS_DIR.'views/builder/custom_post_type.php';
                    }
                    else{
                        include __DIR__.'/custom_post_type.php';
                    }
                    ?>
                </div>
            </td>
        </tr>
    </table>
    <button class="button button-primary" name="wpaicg_save_builder_settings"><?php echo esc_html__('Save','gpt3-ai-content-generator')?></button>
</form>
<!-- Modal HTML -->
<div id="embeddingModelChangeModal" style="display:none;">
    <div class="wpaicg_modal">
        <div class="wpaicg_modal_content">
            <p><strong><?php echo esc_html__('Important Notice', 'gpt3-ai-content-generator'); ?></strong></p>
            <p><?php echo esc_html__('Changing the embeddings model will require you to reindex all your content and delete old indexes.', 'gpt3-ai-content-generator'); ?></p>
            <p><?php echo esc_html__('Make sure to reindex all your content after the model change.', 'gpt3-ai-content-generator'); ?></p>
            <p><?php echo esc_html__('Do you want to proceed?', 'gpt3-ai-content-generator'); ?></p>
        </div>
        <div class="wpaicg_assign_footer">
            <button id="confirmModelChange" class="button button-primary"><?php echo esc_html__('Yes, Proceed', 'gpt3-ai-content-generator'); ?></button>
            <button id="cancelModelChange" class="button"><?php echo esc_html__('Cancel', 'gpt3-ai-content-generator'); ?></button>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function($){

        function toggleVectorDBSettings() {
            var selectedDB = $('select[name="wpaicg_vector_db_provider"]').val();
            if (selectedDB === 'qdrant') {
                $('#wpaicg_pinecone_settings').hide();
                $('#wpaicg_qdrant_settings').show();
            } else {
                $('#wpaicg_pinecone_settings').show();
                $('#wpaicg_qdrant_settings').hide();
            }
        }

        // Run on page load
        toggleVectorDBSettings();

        // Run on selection change
        $('select[name="wpaicg_vector_db_provider"]').change(function() {
            toggleVectorDBSettings();
        });

        // Function to show modal
        function showModal() {
            $('#wpaicg_modal_overlay').show();
            $('#embeddingModelChangeModal').show();
        }

        // Function to hide modal
        function hideModal() {
            $('#wpaicg_modal_overlay').hide();
            $('#embeddingModelChangeModal').hide();
        }

        // Detect change in the embeddings model dropdown
        $('select[name="wpaicg_openai_embeddings"]').change(function() {
            // Show modal when the model is changed
            showModal();
        });

        // Handle modal confirmation
        $('#confirmModelChange').click(function() {
            hideModal();
            // Add any additional actions you want to perform after confirmation
        });

        // Handle modal cancellation
        $('#cancelModelChange').click(function() {
            hideModal();
            // Reset the dropdown to its original value if needed
        });
        
        function wpaicgLoading(btn){
            btn.attr('disabled','disabled');
            if(!btn.find('spinner').length){
                btn.append('<span class="spinner"></span>');
            }
            btn.find('.spinner').css('visibility','unset');
        }
        function wpaicgRmLoading(btn){
            btn.removeAttr('disabled');
            btn.find('.spinner').remove();
        }

        function updateCollectionsDropdown(collections) {
            var dropdown = $('.wpaicg_qdrant_collections_dropdown');
            if (collections.length > 0) {
                dropdown.empty().show();
                $.each(collections, function(index, collection) {
                    dropdown.append($('<option></option>').attr('value', collection).text(collection));
                });
            } else {
                dropdown.hide();
            }
        }

        // Show the input field for new collection creation
        $('.wpaicg_create_new_collection_btn').click(function() {
            $('.wpaicg_new_collection_input').show();
        });

        // Handle the creation of new collection
        $('.wpaicg_submit_new_collection').click(function() {
            var collectionName = $('.wpaicg_new_collection_name').val().trim();
            var apiKey = $('input[name="wpaicg_qdrant_api_key"]').val().trim();
            var endpoint = $('input[name="wpaicg_qdrant_endpoint"]').val().trim();
            if (!collectionName) {
                alert('Please enter a collection name.');
                return;
            }

            wpaicgLoading($('.wpaicg_submit_new_collection'));

            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php') ?>',
                type: 'POST',
                data: {
                    action: 'wpaicg_create_collection',
                    nonce: '<?php echo wp_create_nonce('wpaicg-ajax-nonce') ?>',
                    collection_name: collectionName,
                    api_key: apiKey,
                    endpoint: endpoint
                },
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.status && result.status.error) {
                        alert('Error: ' + result.status.error);
                    } else {
                        // Add the new collection to the dropdown
                        $('.wpaicg_qdrant_collections_dropdown').append($('<option>', {
                            value: collectionName,
                            text: collectionName
                        })).val(collectionName);

                        // Update collections in the options table
                        var updatedCollections = $('.wpaicg_qdrant_collections_dropdown option').map(function() {
                            return $(this).val();
                        }).get();

                        $.post('<?php echo admin_url('admin-ajax.php') ?>', {
                            action: 'wpaicg_save_qdrant_collections',
                            nonce: '<?php echo wp_create_nonce('wpaicg-ajax-nonce') ?>',
                            collections: updatedCollections
                        });

                        $('.wpaicg_new_collection_input').hide();
                        $('.wpaicg_new_collection_name').val('');
                        $('.wpaicg_sync_qdrant_collections').click();
                    }
                    wpaicgRmLoading($('.wpaicg_submit_new_collection'));
                },
                error: function() {
                    alert('Error: Unable to create collection.');
                    wpaicgRmLoading($('.wpaicg_submit_new_collection'));
                }
            });
        });
        $('.wpaicg_sync_qdrant_collections').click(function() {
            var btn = $(this);
            // get api key
            var apiKey = $('input[name="wpaicg_qdrant_api_key"]').val().trim();
            if (!apiKey) {
                alert('Please enter a valid API key.');
                return;
            }
            // get endpoint
            var endpoint = $('input[name="wpaicg_qdrant_endpoint"]').val().trim();
            if (!endpoint) {
                alert('Please enter a valid endpoint.');
                return;
            }
            wpaicgLoading(btn);

            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php') ?>',
                type: 'POST',
                dataType: 'json', // Expecting JSON response
                data: {
                    action: 'wpaicg_show_collections',
                    nonce: '<?php echo wp_create_nonce('wpaicg-ajax-nonce') ?>',
                    api_key: apiKey,
                    endpoint: endpoint
                },
                success: function(response) {
                    if (response.success) {
                        var collections = response.data;
                        updateCollectionsDropdown(collections);

                        // Save the collections to the options table
                        $.post('<?php echo admin_url('admin-ajax.php') ?>', {
                            action: 'wpaicg_save_qdrant_collections',
                            nonce: '<?php echo wp_create_nonce('wpaicg-ajax-nonce') ?>',
                            collections: collections
                        });
                    } else {
                        // Handle error response
                        alert('Error: ' + (response.data.error || 'Unable to sync collections.'));
                    }
                    wpaicgRmLoading(btn);
                },
                error: function(xhr, status, error) {
                    // Handle low-level HTTP error
                    alert('Error: ' + (error || 'Unable to sync collections.'));
                    wpaicgRmLoading(btn);
                }
            });
        });


        // Initially hide the dropdown if it's empty
        var initialCollections = $('.wpaicg_qdrant_collections_dropdown option').length;
        if (initialCollections === 0) {
            $('.wpaicg_qdrant_collections_dropdown').hide();
        }

        $('.wpaicg_pinecone_indexes').click(function (){
            var btn = $(this);
            var wpaicg_pinecone_api = $('.wpaicg_pinecone_api').val();
            var old_value = $('.wpaicg_pinecone_environment').attr('old-value');
            if(wpaicg_pinecone_api !== ''){
                $.ajax({
                    url: 'https://api.pinecone.io/indexes',
                    headers: {"Api-Key": wpaicg_pinecone_api},
                    dataType: 'json',
                    beforeSend: function (){
                        wpaicgLoading(btn);
                        btn.html('<?php echo esc_html__('Syncing...','gpt3-ai-content-generator')?>');
                    },
                    success: function (res){
                        if(res.indexes && res.indexes.length){
                            var selectList = '<option value=""><?php echo esc_html__('Select Index','gpt3-ai-content-generator')?></option>';
                            var formattedIndexes = [];

                            res.indexes.forEach(function(index){
                                selectList += '<option value="'+index.host+'"'+(old_value === index.host ? ' selected':'')+'>'+index.name+'</option>';
                                formattedIndexes.push({name: index.name, url: index.host});
                            });

                            $('.wpaicg_pinecone_environment').html(selectList);

                            // Save formatted indexes to the database
                            $.post('<?php echo admin_url('admin-ajax.php')?>', {
                                action: 'wpaicg_pinecone_indexes',
                                nonce: '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>',
                                indexes: JSON.stringify(formattedIndexes),
                                api_key: wpaicg_pinecone_api
                            });
                        }
                        btn.html('<?php echo esc_html__('Sync Indexes','gpt3-ai-content-generator')?>');
                        wpaicgRmLoading(btn);
                    },
                    error: function (e){
                        btn.html('<?php echo esc_html__('Sync Indexes','gpt3-ai-content-generator')?>');
                        wpaicgRmLoading(btn);
                        alert(e.responseText);
                    }
                });
            }
            else{
                alert('<?php echo esc_html__('Please add Pinecone API key before start sync','gpt3-ai-content-generator')?>')
            }
        })
    })
</script>
