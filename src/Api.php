<?php
namespace Prompt2ImageApi;

use WP_REST_Request;
use WP_Error;
use WP_REST_Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class API
 *
 * Handles REST API endpoints for Prompt2Image plugin.
 */
class API {

	/**
	 * REST route namespace.
	 */
	public const ROUTE_NAMESPACE = 'prompt2image-api/v1';

	/**
	 * User meta key for API key.
	 */
	public const META_KEY_API = '_prompt2image_api_key';

	/**
	 * User meta key for usage count.
	 */
	public const META_KEY_USAGE = '_prompt2image_usage_count';

	/**
	 * User meta key for status.
	 */
	public const META_STATUS = '_prompt2image_user_status';

	/**
	 * Constructor.
	 *
	 * Hooks REST API routes.
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Register REST API routes.
	 */
	public function register_routes(): void {
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
	 * Register a new user and generate a random API key.
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
				[ 'status' => 400 ]
			);
		}

		if ( ! is_email( $email ) ) {
			return new WP_Error(
				'invalid_email',
				esc_html__( 'Invalid email address.', 'prompt2image-api' ),
				[ 'status' => 400 ]
			);
		}

		// Check if user already exists.
		$user = get_user_by( 'email', $email );

		if ( $user instanceof \WP_User ) {
			// If exists, generate a new API key anyway.
			$api_key = $this->generate_api_key();
			update_user_meta( $user->ID, self::META_KEY_API, $api_key );
			update_user_meta( $user->ID, self::META_STATUS, 1 );

			return rest_ensure_response(
				[
					'message' => esc_html__( 'User already registered.', 'prompt2image-api' ),
					'api_key' => $api_key,
				]
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
				[ 'status' => 500 ]
			);
		}

		// Generate random API key and save in user meta.
		$api_key = p2i_generate_api_key();
		update_user_meta( $user_id, self::META_KEY_API, $api_key );
		update_user_meta( $user_id, self::META_STATUS, 1 );

		return rest_ensure_response(
			[
				'message' => esc_html__( 'User registered successfully.', 'prompt2image-api' ),
				'api_key' => $api_key,
			]
		);
	}

	/**
	 * Disconnect user by setting status to inactive.
	 *
	 * @param WP_REST_Request $request The REST API request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function disconnect_user( WP_REST_Request $request ) {
		$email = sanitize_email( $request->get_param( 'email' ) );

		if ( empty( $email ) || ! is_email( $email ) ) {
			return new WP_REST_Response(
				[
					'success' => false,
					'message' => esc_html__( 'Invalid email address.', 'prompt2image-api' ),
				],
				400
			);
		}

		$user = get_user_by( 'email', $email );

		if ( ! $user instanceof \WP_User ) {
			return new WP_REST_Response(
				[
					'success' => false,
					'message' => esc_html__( 'User not found with this email.', 'prompt2image-api' ),
				],
				404
			);
		}

		// Set user status to inactive.
		update_user_meta( $user->ID, self::META_STATUS, 0 );

		return new WP_REST_Response(
			[
				'success' => true,
				'message' => esc_html__( 'Disconnected successfully. API key removed.', 'prompt2image-api' ),
				'user_id' => $user->ID,
				'email'   => $user->user_email,
				'status'  => 'success',
			],
			200
		);
	}
}
