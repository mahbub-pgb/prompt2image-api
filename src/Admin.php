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
        if ( strpos( $hook, 'toplevel_page_prompt2image-users' ) === false ) {
            return;
        }

        // DataTables CSS + JS (uses CDN)
    wp_enqueue_style( 'datatables', 'https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css' );
    wp_enqueue_script( 'datatables', 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['jquery'], null, true );

        wp_enqueue_style(
            'prompt2image-api',
            P2I_API_PLUGIN_URL . 'assets/css/admin.css',
            [],
            '1.0.0'
        );

        // Enqueue JS
        wp_enqueue_script(
            'prompt2image-admin-js',
            P2I_API_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'], // jQuery dependency
            '1.0.0',
            true // Load in footer
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
            [ $this, 'p2i_settings_page' ],                         // Callback
            'dashicons-admin-users',                                  // Icon
            20                                                       // Position
        );

    }

    /**
     * Display user list page.
     */
    public function p2i_settings_page() {
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

       <div class="prompt2image-admin-wrap">
        <h1><?php esc_html_e( 'Prompt2Image Dashboard', 'prompt2image-api' ); ?></h1>

        <!-- Tabs -->
        <div class="prompt2image-tabs">
            <button class="tab-button active" data-tab="users"><?php esc_html_e('Users', 'prompt2image-api'); ?></button>
            <button class="tab-button" data-tab="settings"><?php esc_html_e('Settings', 'prompt2image-api'); ?></button>
        </div>

        <!-- USERS TAB -->
        <div class="tab-content tab-users active">
            <table id="prompt2image-users-table" class="wp-list-table widefat fixed striped">
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
                        $status_class = $status == '1' ? 'Active' : 'Inactive';
                    ?>
                        <tr>
                            <td><?php echo esc_html( $user->display_name ); ?></td>
                            <td><?php echo esc_html( $user->user_email ); ?></td>
                            <td><?php echo esc_html( get_user_meta( $user->ID, '_prompt2image_api_key', true ) ); ?></td>
                            <td><?php echo esc_html( $status_class ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- SETTINGS TAB -->
        <div class="tab-content tab-settings" style="display:none;">
            <form method="post" action="options.php">
                <?php
                settings_fields( 'prompt2image_settings_group' );
                do_settings_sections( 'prompt2image_settings_page' );
                ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e('Default API Endpoint', 'prompt2image-api'); ?></th>
                        <td>
                            <input type="text" name="p2i_api_endpoint" value="<?php echo esc_attr( get_option('p2i_api_endpoint', '') ); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Enable Logging', 'prompt2image-api'); ?></th>
                        <td>
                            <input type="checkbox" name="p2i_enable_logging" value="1" <?php checked( get_option('p2i_enable_logging', 0), 1 ); ?>>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
    </div>




        <?php
    }

}
