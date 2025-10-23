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
use Prompt2ImageApi\Front;

class Prompt2Image_Api {

    public function __construct() {
        add_action( 'init', array( $this, 'load_class' ) );
    }

    /**
     * Initialize REST API class
     */
    public function load_class() {
        
            new API();      
            new Front();
           
        
        
    }
}

new Prompt2Image_Api();
