<?php
namespace Prompt2ImageApi;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Admin
 *
 * Handles admin menus for Prompt2Image: user list and settings page.
 */
class Admin {

    /**
     * Constructor.
     *
     * Hooks admin menu initialization.
     */
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'p2i_register_admin_menu' ] );
    }

    /**
     * Register admin menu and submenu pages.
     */
    public function p2i_register_admin_menu() {

        // Main menu - Users
        add_menu_page(
            esc_html__( 'Prompt2Image Users', 'prompt2image-api' ), // Page title
            esc_html__( 'Prompt2Image', 'prompt2image-api' ),       // Menu title
            'manage_options',                                        // Capability
            'prompt2image-users',                                    // Menu slug
            [ $this, 'p2i_user_list_page' ],                         // Callback
            'dashicons-admin-users',                                  // Icon
            20                                                       // Position
        );

    }

    /**
     * Display user list page.
     */
    public function p2i_user_list_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // Get all users with API key
        $args  = [
            'meta_key'    => '_prompt2image_api_key',
            'meta_compare'=> 'EXISTS',
        ];
        $users = get_users( $args );
        ?>

        <div class="wrap">
            <h1><?php esc_html_e( 'Prompt2Image Users', 'prompt2image-api' ); ?></h1>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Name', 'prompt2image-api' ); ?></th>
                        <th><?php esc_html_e( 'Email', 'prompt2image-api' ); ?></th>
                        <th><?php esc_html_e( 'API Key', 'prompt2image-api' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( ! empty( $users ) ) : ?>
                        <?php foreach ( $users as $user ) : ?>
                            <tr>
                                <td><?php echo esc_html( $user->display_name ); ?></td>
                                <td><?php echo esc_html( $user->user_email ); ?></td>
                                <td><?php echo esc_html( get_user_meta( $user->ID, '_prompt2image_api_key', true ) ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="3"><?php esc_html_e( 'No users found.', 'prompt2image-api' ); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>


        <?php
    }

}
