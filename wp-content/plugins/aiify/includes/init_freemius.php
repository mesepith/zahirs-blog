<?php

namespace AIIFY;


if ( !function_exists( 'aii_fs' ) ) {
    // Create a helper function for easy SDK access.
    function aii_fs()
    {
        global  $aii_fs ;
        
        if ( !isset( $aii_fs ) ) {
            // Include Freemius SDK.
            require_once AIIFY_VENDOR . 'freemius/wordpress-sdk/start.php';
            $aii_fs = fs_dynamic_init( array(
                'id'             => '12312',
                'slug'           => 'aiify',
                'type'           => 'plugin',
                'public_key'     => 'pk_a7b5b78425551a1837226a2d8e765',
                'is_premium'     => false,
                'premium_suffix' => 'Pro',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'trial'          => array(
                'days'               => 7,
                'is_require_payment' => true,
            ),
                'menu'           => array(
                'slug'    => 'aiify',
                'support' => false,
            ),
                'is_live'        => true,
            ) );
        }
        
        return $aii_fs;
    }
    
    function connect_message_on_update(
        $message,
        $user_first_name,
        $plugin_title,
        $user_login,
        $site_link,
        $freemius_link
    )
    {
        return sprintf(
            __( 'Hey %1$s', 'aiify' ) . ',<br>' . __( 'Please help us improve %2$s! If you opt-in, some data about your usage of %2$s will be sent to %5$s. If you skip this, that\'s okay! %2$s will still work just fine.', 'aiify' ),
            $user_first_name,
            '<b>' . $plugin_title . '</b>',
            '<b>' . $user_login . '</b>',
            $site_link,
            $freemius_link
        );
    }
    
    // Init Freemius.
    $aii_fs = aii_fs();
    $aii_fs->add_filter(
        'connect_message_on_update',
        __NAMESPACE__ . '\\connect_message_on_update',
        10,
        6
    );
    // Signal that SDK was initiated.
    do_action( 'aii_fs_loaded' );
}
