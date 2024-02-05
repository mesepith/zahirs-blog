<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$token = get_post_meta($wpaicg_embedding->ID,'wpaicg_embedding_token',true);
$wpaicg_source = get_post_meta($wpaicg_embedding->ID,'wpaicg_source',true);
$wpaicg_indexed = get_post_meta($wpaicg_embedding->ID,'wpaicg_indexed',true);
$wpaicg_completed = get_post_meta($wpaicg_embedding->ID,'wpaicg_completed',true);
$wpaicg_start = get_post_meta($wpaicg_embedding->ID,'wpaicg_start',true);
$wpaicg_error_msg = get_post_meta($wpaicg_embedding->ID,'wpaicg_error_msg',true);

// Safely retrieve AI Provider and Vector DB fields
$wpaicg_provider = get_post_meta($wpaicg_embedding->ID, 'wpaicg_provider', true);
$wpaicg_index = get_post_meta($wpaicg_embedding->ID, 'wpaicg_index', true);
// Check if wpaicg_index exists and is not empty, then proceed to determine the DB provider
if (!empty($wpaicg_index)) {
    // Check if wpaicg_index contains 'pinecone.io'
    if (strpos($wpaicg_index, 'pinecone.io') !== false) {
        $dbProvider = 'Pinecone';
        // Parse Vector DB information specific to Pinecone
        $parts = explode('-', $wpaicg_index);
        $indexName = $parts[0];
        $projectName = substr($parts[1], 0, strpos($parts[1], '.svc'));
        $vectorDBInfo = "<div style='font-size: 90%;'><strong>DB:</strong> $dbProvider<br><strong>Project:</strong> $projectName<br><strong>Index:</strong> $indexName</div>";
    } else {
        // Default to Qdrant if 'pinecone.io' is not found
        $dbProvider = 'Qdrant';
        $vectorDBInfo = "<div style='font-size: 90%;'><strong>DB:</strong> $dbProvider<br><strong>Collection:</strong> $wpaicg_index</div>";
    }
} else {
    // Assign to Pinecone by default if wpaicg_index is not set or empty
    $dbProvider = 'Pinecone';
    $vectorDBInfo = "<div style='font-size: 90%;'><strong>DB:</strong> Pinecone</div>";
}

// Define allowed HTML tags for wp_kses
$allowed_html = array(
    'div' => array(
        'style' => array()
    ),
    'strong' => array(),
    'br' => array(),
);
$wpaicg_emb_model = get_post_meta($wpaicg_embedding->ID, 'wpaicg_model', true);

// Display empty or placeholder if fields are not available
$wpaicg_provider_display = !empty($wpaicg_provider) ? esc_html($wpaicg_provider) : '';

$wpaicg_emb_model_display = !empty($wpaicg_emb_model) ? esc_html($wpaicg_emb_model) : 'text-embedding-ada-002';

// Calculate estimated cost based on the model
if (!empty($wpaicg_emb_model)) {
    switch ($wpaicg_emb_model) {
        case 'text-embedding-3-small':
            $costPerToken = 0.00002 / 1000;
            break;
        case 'text-embedding-3-large':
            $costPerToken = 0.00013 / 1000;
            break;
        default:
            // Default to the cost of 'text-embedding-ada-002' if the model is not recognized
            $costPerToken = 0.00010 / 1000;
    }
} else {
    // Use 'text-embedding-ada-002' cost if model is empty
    $costPerToken = 0.00010 / 1000;
}
$estimatedCost = !empty($token) ? number_format((int)esc_html($token) * $costPerToken, 8) . '$' : '--';
?>
<tr id="wpaicg-builder-<?php echo esc_html($wpaicg_embedding->ID)?>">
    <th scope="row" class="check-column">
        <input class="cb-select-embedding" id="cb-select-<?php echo esc_html($wpaicg_embedding->ID);?>" type="checkbox" name="ids[]" value="<?php echo esc_html($wpaicg_embedding->ID);?>">
    </th>
    <td><a data-content="<?php echo esc_html($wpaicg_embedding->post_content)?>" href="javascript:void(0)" class="wpaicg-embedding-content"><?php echo esc_html($wpaicg_embedding->post_title)?></a></td>
    <td><?php echo esc_html($token)?></td>
    <td><?php echo esc_html($estimatedCost)?></td>
    <td>
        <?php
        if($wpaicg_source == 'post'){
            echo esc_html__('Post','gpt3-ai-content-generator');
        }
        if($wpaicg_source == 'page'){
            echo esc_html__('Page','gpt3-ai-content-generator');
        }
        if($wpaicg_source == 'product'){
            echo esc_html__('Product','gpt3-ai-content-generator');
        }
        ?>
    </td>
    <td><?php echo esc_html($wpaicg_provider_display) ?></td>
    <td><?php echo esc_html($wpaicg_emb_model_display) ?></td>
    <td><?php echo wp_kses($vectorDBInfo, $allowed_html); ?></td>
    <td class="builder-status">
        <?php
        if($wpaicg_indexed == '' || $wpaicg_indexed == 'yes'){
            echo '<span style="color: #018b25;font-weight: bold;">'.esc_html__('Indexed','gpt3-ai-content-generator').'</span>';
        }
        if($wpaicg_indexed == 'error'){
            echo '<span style="color: #ff0000;font-weight: bold;">'.esc_html__('Error','gpt3-ai-content-generator').'</span>';
            if(!empty($wpaicg_error_msg)){
                echo '<p>'.esc_html($wpaicg_error_msg).'</p>';
            }
        }
        if($wpaicg_indexed == 'reindex'){
            echo '<span style="color: #d73e1c;font-weight: bold;">'.esc_html__('Pending','gpt3-ai-content-generator').'</span>';
        }
        ?>
    </td>
    <td>
        <?php
        if(!empty($wpaicg_start)){
            echo esc_html(gmdate('d.m.Y H:i',$wpaicg_start));
        }
        ?>
    </td>
    <td>
        <?php
        if(!empty($wpaicg_completed)){
            echo esc_html(gmdate('d.m.Y H:i',$wpaicg_completed));
        }
        ?>
    </td>
    <td>
        <?php
        if($wpaicg_indexed != 'reindex'):
        ?>
        <button data-id="<?php echo esc_html($wpaicg_embedding->ID)?>" class="button button-primary button-small wpaicg_reindex"><?php echo esc_html__('Re-Index','gpt3-ai-content-generator')?></button>
        <?php
        endif;
        ?>
        <button data-id="<?php echo esc_html($wpaicg_embedding->ID)?>" class="button button-link-delete button-small wpaicg_delete"><?php echo esc_html__('Delete','gpt3-ai-content-generator')?></button>
    </td>
</tr>
