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
        
        p2i_print_r(  GOOGLE_API_KEY );
    }
}
