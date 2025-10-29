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
     * Hooks admin menu initialization and enqueueing scripts.
     */
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
     * Register plugin settings.
     */
    public function register_settings() {
        register_setting(
            'prompt2image_settings_group', // Settings group.
            'prompt2image_request_limit',  // Option name.
            [
                'type'              => 'integer',
                'sanitize_callback' => 'absint',
                'default'           => 10,
            ]
        );
    }

    /**
     * Enqueue admin CSS and JS.
     *
     * @param string $hook Current admin page.
     */
    public function enqueue_admin_assets( $hook ) {
        if ( strpos( $hook, 'toplevel_page_prompt2image-users' ) === false ) {
            return;
        }

        // DataTables CSS and JS (CDN)
        wp_enqueue_style(
            'datatables-css',
            'https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css',
            [],
            '1.13.6'
        );

        wp_enqueue_script(
            'datatables-js',
            'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js',
            [ 'jquery' ],
            '1.13.6',
            true
        );

        // Plugin admin CSS
        wp_enqueue_style(
            'prompt2image-admin-css',
            P2I_API_PLUGIN_URL . 'assets/css/admin.css',
            [],
            '1.0.0'
        );

        // Plugin admin JS
        wp_enqueue_script(
            'prompt2image-admin-js',
            P2I_API_PLUGIN_URL . 'assets/js/admin.js',
            [ 'jquery' ],
            '1.0.0',
            true
        );
    }

    /**
     * Register admin menu page.
     */
    public function register_admin_menu() {

        add_menu_page(
            esc_html__( 'Prompt2Image Users', 'prompt2image-api' ), // Page title.
            esc_html__( 'Prompt2Image', 'prompt2image-api' ),       // Menu title.
            'manage_options',                                        // Capability.
            'prompt2image-users',                                    // Menu slug.
            [ $this, 'render_admin_page' ],                          // Callback.
            'dashicons-admin-users',                                 // Icon.
            20                                                       // Position.
        );
    }

    /**
     * Render admin page content (Users + Settings tabs).
     */
    public function render_admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $users = get_users(
            [
                'meta_key'     => '_prompt2image_user_status',
                'meta_compare' => 'EXISTS',
            ]
        );

        $limit = get_option( 'prompt2image_request_limit', 10 );
        ?>

        <div class="wrap prompt2image-admin-wrap">
            <h1><?php esc_html_e( 'Prompt2Image Dashboard', 'prompt2image-api' ); ?></h1>

            <div class="prompt2image-tabs">
                <button class="tab-button" data-tab="users"><?php esc_html_e( 'Users', 'prompt2image-api' ); ?></button>
                <button class="tab-button" data-tab="settings"><?php esc_html_e( 'Settings', 'prompt2image-api' ); ?></button>
            </div>

            <!-- USERS TAB -->
            <div class="tab-content tab-users">
                <h2><?php esc_html_e( 'All Users', 'prompt2image-api' ); ?></h2>
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
                            $status      = get_user_meta( $user->ID, '_prompt2image_user_status', true );
                            $status_text = ( '1' === $status )
                                ? '<span class="status-active">' . esc_html__( 'Active', 'prompt2image-api' ) . '</span>'
                                : '<span class="status-inactive">' . esc_html__( 'Inactive', 'prompt2image-api' ) . '</span>';
                            $api_key     = get_user_meta( $user->ID, '_prompt2image_api_key', true );
                        ?>
                            <tr>
                                <td><?php echo esc_html( $user->display_name ); ?></td>
                                <td><?php echo esc_html( $user->user_email ); ?></td>
                                <td><?php echo esc_html( $api_key ); ?></td>
                                <td><?php echo $status_text; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- SETTINGS TAB -->
            <div class="tab-content tab-settings">
                <h2><?php esc_html_e( 'Settings', 'prompt2image-api' ); ?></h2>
                <form method="post" action="options.php">
                    <?php
                    settings_fields( 'prompt2image_settings_group' );
                    do_settings_sections( 'prompt2image_settings_group' );
                    ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="prompt2image_request_limit"><?php esc_html_e( 'Request Limit per User', 'prompt2image-api' ); ?></label>
                            </th>
                            <td>
                                <input type="number"
                                    name="prompt2image_request_limit"
                                    id="prompt2image_request_limit"
                                    value="<?php echo esc_attr( $limit ); ?>"
                                    min="1"
                                    step="1"
                                    class="regular-text"
                                />
                                <p class="description"><?php esc_html_e( 'Maximum number of requests each user can make.', 'prompt2image-api' ); ?></p>
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
