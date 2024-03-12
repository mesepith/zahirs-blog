<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if (isset($_GET['search']) && !empty($_GET['search']) && !wp_verify_nonce($_GET['wpaicg_nonce'], 'wpaicg_singlelog_search_nonce')) {
    die(esc_html__('Nonce verification failed','gpt3-ai-content-generator'));
}
$wpaicg_log_page = isset($_GET['wpage']) && !empty($_GET['wpage']) ? sanitize_text_field($_GET['wpage']) : 1;
$args = array(
    'post_type' => 'wpaicg_slog',
    'posts_per_page' => 40,
    'paged' => $wpaicg_log_page,
    'order' => 'DESC',
    'orderby' => 'date'
);
$search = '';
if(isset($_GET['search']) && !empty($_GET['search'])){
    $search = sanitize_text_field($_GET['search']);
    $args['s'] = $search;
}
$wpaicg_single_logs = new WP_Query($args);
?>
<div>
    <div class="wpaicg-mb-10">
        <form action="" method="GET">
            <?php wp_nonce_field('wpaicg_singlelog_search_nonce', 'wpaicg_nonce'); ?>
            <input type="hidden" name="page" value="wpaicg_single_content">
            <input type="hidden" name="action" value="logs">
            <input value="<?php echo esc_html($search)?>" name="search" type="text" placeholder="<?php echo esc_html__('Search','gpt3-ai-content-generator')?>">
            <button class="button button-primary"><?php echo esc_html__('Search','gpt3-ai-content-generator')?></button>
        </form>
    </div>
</div>
<table class="wp-list-table widefat fixed striped table-view-list posts">
    <thead>
    <tr>
        <th width="40"><?php echo esc_html__('ID','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Title','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Date','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Duration','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Token','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Estimated','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Provider','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Model','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Author','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Source','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Category','gpt3-ai-content-generator')?></th>
        <th><?php echo esc_html__('Word Count','gpt3-ai-content-generator')?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if($wpaicg_single_logs->have_posts()) {
        foreach ($wpaicg_single_logs->posts as $wpaicg_single_log) {
            $wpaicg_duration = get_post_meta($wpaicg_single_log->ID,'wpaicg_duration',true);
            $wpaicg_ai_model = get_post_meta($wpaicg_single_log->ID,'wpaicg_ai_model',true);
            $wpaicg_usage_token = get_post_meta($wpaicg_single_log->ID,'wpaicg_usage_token',true);
            $wpaicg_word_count = get_post_meta($wpaicg_single_log->ID,'wpaicg_word_count',true);
            $wpaicg_post_id = get_post_meta($wpaicg_single_log->ID,'wpaicg_post_id',true);
            $wpaicg_source_log = get_post_meta($wpaicg_single_log->ID,'wpaicg_source_log',true);
            $post_categories = wp_get_post_categories( $wpaicg_post_id ,array('fields' => 'names'));
            $wpaicg_provider = get_post_meta($wpaicg_single_log->ID,'wpaicg_provider',true);
            // Define pricing per 1K tokens
            $pricing = array(
                'gpt-4' => 0.06,
                'gpt-4-32k' => 0.12,
                'gpt-4-1106-preview' => 0.01,
                'gpt-4-vision-preview' => 0.01,
                'gpt-3.5-turbo' => 0.002,
                'gpt-3.5-turbo-instruct' => 0.002,
                'gpt-3.5-turbo-16k' => 0.004,
                'text-davinci-003' => 0.02,
                'text-curie-001' => 0.002,
                'text-babbage-001' => 0.0005,
                'text-ada-001' => 0.0004,
                'gemini-pro' => 0.000375
            );
            $wpaicg_estimated = 0;
            // Calculate estimated cost
            if (!empty($wpaicg_usage_token)) {
                if (array_key_exists($wpaicg_ai_model, $pricing)) {
                    $wpaicg_estimated = $pricing[$wpaicg_ai_model] * $wpaicg_usage_token / 1000;
                } else {
                    // Default pricing if the model is not listed
                    $wpaicg_estimated = 0.02 * $wpaicg_usage_token / 1000;
                }
            } else {
                $wpaicg_estimated = 0; // Ensure estimated cost is 0 if there are no usage tokens
            }
            if($wpaicg_source_log == 'speech' && $wpaicg_duration > 0){
                $wpaicg_estimated +=  $wpaicg_duration*0.0001;
            }
            ?>
            <tr>
                <td><?php echo esc_html($wpaicg_single_log->ID)?></td>
                <td>
                    <a href="<?php echo admin_url('post.php?post='.esc_html($wpaicg_post_id).'&action=edit')?>">
                        <?php echo str_replace('WPAICGLOG:','',esc_html($wpaicg_single_log->post_title))?>
                    </a>
                </td>
                <td><?php echo esc_html(gmdate('d.m.Y H:i',strtotime($wpaicg_single_log->post_date)))?></td>
                <td><?php echo esc_html(WPAICG\WPAICG_Content::get_instance()->wpaicg_seconds_to_time((int)$wpaicg_duration))?></td>
                <td>
                    <?php
                    // Check if $wpaicg_usage_token is set, not empty, and is numeric
                    if (isset($wpaicg_usage_token) && !empty($wpaicg_usage_token) && is_numeric($wpaicg_usage_token)) {
                        echo esc_html(round((float)$wpaicg_usage_token));
                    } else {
                        // If $wpaicg_usage_token does not meet the criteria, display a default value or handle as needed
                        echo esc_html('0');
                    }
                    ?>
                </td>
                <td><?php echo esc_html(number_format($wpaicg_estimated,5))?>$</td>
                <td><?php echo esc_html($wpaicg_provider)?></td>
                <td><?php echo esc_html($wpaicg_ai_model)?></td>
                <td><?php echo esc_html(get_the_author_meta( 'display_name' , $wpaicg_single_log->post_author ))?></td>
                <td><?php echo $wpaicg_source_log == 'speech' ? 'Speech-to-Post' : ($wpaicg_source_log == 'custom' ? esc_html__('Custom Mode','gpt3-ai-content-generator') :esc_html__('Express Writer','gpt3-ai-content-generator'))?></td>
                <td><?php echo esc_html(implode(',',$post_categories))?></td>
                <td><?php echo esc_html($wpaicg_word_count)?></td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>
<div class="wpaicg-paginate">
    <?php
    echo paginate_links( array(
        'base'         => admin_url('admin.php?page=wpaicg_single_content&action=logs&wpage=%#%'),
        'total'        => $wpaicg_single_logs->max_num_pages,
        'current'      => $wpaicg_log_page,
        'format'       => '?wpage=%#%',
        'show_all'     => false,
        'prev_next'    => false,
        'add_args'     => false,
    ));
    ?>
</div>
