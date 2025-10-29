<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Sanitize a user prompt.
 *
 * @param string $prompt The prompt to sanitize.
 *
 * @return string Sanitized prompt.
 */
if ( ! function_exists( 'p2i_sanitize_prompt' ) ) {
    function p2i_sanitize_prompt( string $prompt ): string {
        return sanitize_text_field( $prompt );
    }
}

/**
 * Print readable array or object for debugging.
 *
 * @param mixed $data Data to print.
 */
if ( ! function_exists( 'p2i_print_r' ) ) {
    function p2i_print_r( $data ): void {
        echo '<pre>';
        print_r( $data );
        echo '</pre>';
    }
}

/**
 * Generate a random 32-character API key.
 *
 * @return string Random API key.
 */
if ( ! function_exists( 'p2i_generate_api_key' ) ) {
    function p2i_generate_api_key(): string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $api_key    = '';
        $length     = 32;

        for ( $i = 0; $i < $length; $i++ ) {
            $api_key .= $characters[ wp_rand( 0, strlen( $characters ) - 1 ) ];
        }

        return $api_key;
    }
}

/**
 * Verify a user by API key.
 *
 * @param string $api_key The user's API key.
 *
 * @return int|false Returns the user ID if valid, false if invalid.
 */
if ( ! function_exists( 'p2i_verify_user_by_api_key' ) ) {
    function p2i_verify_user_by_api_key( string $api_key ) {
        $api_key = sanitize_text_field( $api_key );

        if ( empty( $api_key ) ) {
            return false;
        }

        $user_query = new \WP_User_Query(
            [
                'meta_key'   => '_prompt2image_api_key',
                'meta_value' => $api_key,
                'number'     => 1,
                'fields'     => 'ID',
            ]
        );

        $users = $user_query->get_results();

        if ( empty( $users ) ) {
            return false;
        }

        // Return user ID if found.
        return (int) $users[0];
    }
}
