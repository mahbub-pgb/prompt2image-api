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
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_styles' ] );
    }

    /**
     * Enqueue admin CSS for Prompt2Image Users page.
     */
    function enqueue_admin_styles( $hook ) {

        // pri( $hook );
        // Optional: Load only on your plugin’s admin page
        if ( strpos( $hook, 'toplevel_page_prompt2image-users' ) === false ) {
            return;
        }

        wp_enqueue_style(
            'prompt2image-api',
            P2I_API_PLUGIN_URL . 'assets/css/admin.css',
            [],
            '1.0.0'
        );
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
            'meta_key'    => '_prompt2image_user_status',
            'meta_compare'=> 'EXISTS',
        ];
        $users = get_users( $args );
        ?>

        <div class="prompt2image-admin-table-wrap">
            <h1><?php esc_html_e( 'Prompt2Image Users', 'prompt2image-api' ); ?></h1>

            <table class="wp-list-table widefat fixed striped prompt2image-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Name', 'prompt2image-api' ); ?></th>
                        <th><?php esc_html_e( 'Email', 'prompt2image-api' ); ?></th>
                        <th><?php esc_html_e( 'API Key', 'prompt2image-api' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'prompt2image-api' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $users as $user ) :
                        $status = get_user_meta( $user->ID, '_prompt2image_user_status', true );
                        $status_text = $status == '1' 
                            ? '<span class="status-active">Active</span>' 
                            : '<span class="status-inactive">Inactive</span>';
                    ?>
                        <tr>
                            <td><?php echo esc_html( $user->display_name ); ?></td>
                            <td><?php echo esc_html( $user->user_email ); ?></td>
                            <td><?php echo esc_html( get_user_meta( $user->ID, '_prompt2image_api_key', true ) ); ?></td>
                            <td><?php echo $status_text; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

}
