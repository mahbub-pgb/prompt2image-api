<?php
namespace Prompt2ImageApi;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Front
 *
 * Handles front-end REST API calls, user verification, and Gemini API requests.
 */
class Front {

    /**
     * Constructor.
     *
     * Hooks front-end functionality into the `wp_head` action.
     */
    public function __construct() {
        add_action( 'wp_head', [ $this, 'front' ] );
    }

    /**
     * Print "Hi" in the head.
     */
    public function front() {
        $remaining_limit = get_user_meta( 1, '_prompt2image_user_limit', true );

        echo '<pre>';
        print_r( $remaining_limit );
        echo '</pre>';
    }
}
