<?php
namespace Prompt2ImageApi;

/**
 * Plugin Name: Prompt2Image Api
 * Description: Centralized API system for Gemini API with user registration and API key management.
 * Version: 1.0.0
 * Author: Mahbub
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Define plugin constants
 */
if ( ! defined( 'P2I_API_VERSION' ) ) {
    define( 'P2I_API_VERSION', '1.0.0' );
}

if ( ! defined( 'P2I_API_PLUGIN_FILE' ) ) {
    define( 'P2I_API_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'P2I_API_PLUGIN_BASENAME' ) ) {
    define( 'P2I_API_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'P2I_API_PLUGIN_DIR' ) ) {
    define( 'P2I_API_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'P2I_API_PLUGIN_URL' ) ) {
    define( 'P2I_API_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'GOOGLE_API_KEY' ) ) {
    define( 'GOOGLE_API_KEY', 'AIzaSyBYVT4q9r_gi9v8OnNMwXuW5oj-S_SfTBQ' );
}



// Include Composer autoload (if exists)
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Main Plugin Class
 */
class Prompt2Image_Api {

    public function __construct() {
        add_action( 'init', [ $this, 'load_class' ] );
    }

    /**
     * Initialize plugin components
     */
    public function load_class() {
        if ( is_admin() ) {
            new Admin();
        }

        new API();
        new Front();
    }
}

// Initialize plugin
new Prompt2Image_Api();
