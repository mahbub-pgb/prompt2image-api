<?php
namespace Prompt2ImageApi;

use WP_REST_Request;
use WP_Error;

class API {

	const ROUTE_NAMESPACE = 'prompt2image-api/v1';
	const META_KEY_API    = '_prompt2image_api_key';
	const META_KEY_USAGE  = '_prompt2image_usage_count';
	// define( 'PROMPT2IMAGE_GEMINI_KEY', 'YOUR_GOOGLE_GEMINI_KEY' );

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
			$api_key = get_user_meta( $user->ID, self::META_KEY_API, true );
			return rest_ensure_response(
				array(
					'message' => esc_html__( 'User already registered.', 'prompt2image-api' ),
					'api_key' => $api_key,
				)
			);
		}

		// Fixed password for all users.
		$password = '12345prompt2image';

		// Create user.
		$user_id = wp_create_user( $username, $password, $email );

		if ( is_wp_error( $user_id ) ) {
			return new WP_Error(
				'user_create_failed',
				esc_html__( 'Failed to create user.', 'prompt2image-api' ),
				array( 'status' => 500 )
			);
		}

		// Generate and store API key.
		$api_key = wp_generate_password( 32, false );
		update_user_meta( $user_id, self::META_KEY_API, $api_key );
		update_user_meta( $user_id, self::META_KEY_USAGE, 0 );

		return rest_ensure_response(
			array(
				'message' => esc_html__( 'User registered successfully.', 'prompt2image-api' ),
				'api_key' => $api_key,
			)
		);
	}

	
}
