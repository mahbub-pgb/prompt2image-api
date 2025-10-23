<?php
/**
 * Plugin Name: Prompt2Image Api
 * Description: Centralized Api system for Gemini API with user registration and API key management.
 * Version: 1.0.0
 * Author: Mahbub
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include Composer autoload
require_once __DIR__ . '/vendor/autoload.php';

use Prompt2ImageApi\API;

class Prompt2Image_Api {

    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'init_api' ) );
    }

    /**
     * Initialize REST API class
     */
    public function init_api() {
        $api = new API();
        $api->register_routes();
    }
}

new Prompt2Image_Api();
