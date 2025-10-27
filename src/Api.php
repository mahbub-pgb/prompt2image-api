<?php
namespace Prompt2ImageApi;

use WP_REST_Request;
use WP_Error;

class API {

	const ROUTE_NAMESPACE 	= 'prompt2image-api/v1';
	const META_KEY_API    	= '_prompt2image_api_key';
	const META_KEY_USAGE  	= '_prompt2image_usage_count'; 
	const META_STATUS  		= '_prompt2image_user_status';

	public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

	/**
	 * Register REST API routes
	 */
	public function register_routes() {
		register_rest_route(
			self::ROUTE_NAMESPACE,
			'/register',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'register_user' ],
				'permission_callback' => '__return_true',
			]
		);

		register_rest_route(
			self::ROUTE_NAMESPACE,
			'/disconnect',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'disconnect_user' ],
				'permission_callback' => '__return_true',
			]
		);

		
	}

	/**
	 * Register a new user and generate an API key.
	 *
	 * @param WP_REST_Request $request The REST API request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function register_user( WP_REST_Request $request ) {

	    $email    = sanitize_email( $request->get_param( 'email' ) );
	    $username = sanitize_user( $request->get_param( 'username' ), true );
	    $api_key  = defined( 'GOOGLE_API_KEY' ) ? GOOGLE_API_KEY : '';

	    if ( empty( $email ) || empty( $username ) ) {
	        return new WP_Error(
	            'missing_fields',
	            esc_html__( 'Email and username are required.', 'prompt2image-api' ),
	            array( 'status' => 400 )
	        );
	    }

	    if ( ! is_email( $email ) ) {
	        return new WP_Error(
	            'invalid_email',
	            esc_html__( 'Invalid email address.', 'prompt2image-api' ),
	            array( 'status' => 400 )
	        );
	    }

	    // Check if user already exists.
	    $user = get_user_by( 'email', $email );
	    if ( $user ) {
	        update_user_meta( $user->ID, self::META_STATUS, 1 );

	        return rest_ensure_response(
	            array(
	                'message' => esc_html__( 'User already registered.', 'prompt2image-api' ),
	                'api_key' => $api_key,
	            )
	        );
	    }

	    // Generate a random password for new user.
	    $password = wp_generate_password( 12, false );

	    // Create user.
	    $user_id = wp_create_user( $username, $password, $email );

	    if ( is_wp_error( $user_id ) ) {
	        return new WP_Error(
	            'user_create_failed',
	            esc_html__( 'Failed to create user.', 'prompt2image-api' ),
	            array( 'status' => 500 )
	        );
	    }

	    // Update user meta status.
	    update_user_meta( $user_id, self::META_STATUS, 1 );

	    return rest_ensure_response(
	        array(
	            'message' => esc_html__( 'User registered successfully.', 'prompt2image-api' ),
	            'api_key' => $api_key,
	        )
	    );
	}


	/**
	 * REST API callback to disconnect user.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function disconnect_user( $request ) {
	    // Get email from request and sanitize it
	    $email = sanitize_email( $request->get_param( 'email' ) );

	    if ( empty( $email ) || ! is_email( $email ) ) {
	        return new \WP_REST_Response( [
	            'success' => false,
	            'message' => 'Invalid email address.',
	        ], 400 );
	    }

	    // Get user by email
	    $user = get_user_by( 'email', $email );

	    if ( ! $user ) {
	        return new \WP_REST_Response( [
	            'success' => false,
	            'message' => 'User not found with this email.',
	        ], 404 );
	    }

	    // Delete the API key user meta
	    update_user_meta( $user->ID, self::META_STATUS, 0 );

	    return new \WP_REST_Response( [
	        'success' => true,
	        'message' => 'Disconnected successfully. API key removed.',
	        'user_id' => $user->ID,
	        'email'   => $user->user_email,
	        'status'  => 'success',
	    ], 200 );
	}	
}
