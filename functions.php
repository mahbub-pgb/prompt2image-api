<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Example helper function
 */
function p2i_sanitize_prompt( $prompt ) {
    return sanitize_text_field( $prompt );
}

/**
 * Another helper: generate secure random key
 */
function p2i_generate_api_key( $length = 32 ) {
    return wp_generate_password( $length, false );
}

function pri( $data ) {
    echo '<pre>';
    print_r( $data );
    echo '</pre>';
}

/**
 * Verify a user by API key.
 *
 * @param string $api_key The user's API key.
 *
 * @return int|false Returns the user ID if valid, false if invalid.
 */
if ( ! function_exists( 'p2i_verify_user_by_api_key' ) ) {
    function p2i_verify_user_by_api_key( $api_key ) {
        if ( empty( $api_key ) ) {
            return false;
        }



        $user_query = new \WP_User_Query( array(
            'meta_key'   => '_prompt2image_api_key',
            'meta_value' => sanitize_text_field( $api_key ),
            'number'     => 1,
            'fields'     => 'ID',
        ) );

        $users = $user_query->get_results();

        if ( empty( $users ) ) {
            return false;
        }

        return true;
    }
}

