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
