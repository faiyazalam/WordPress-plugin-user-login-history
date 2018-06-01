<?php

/**
  The public-facing functionality of the plugin.
 *
 * @link       https://github.com/faiyazalam
 * @since      1.4.1
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin
 * @author     Er Faiyaz Alam
 */
?>
<?php

class User_Login_History_Public {

    /**
     * The name of this plugin.
     *
     * @since    1.4.1
     * @access   private
     * @var      string    $name    The name of this plugin.
     */
    private $name;

    /**
     * The version of this plugin.
     *
     * @since    1.4.1
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.4.1
     * @var      string    $name       The name of the plugin.
     * @var      string    $version    The version of this plugin.
     */
    public function __construct($name, $version) {

        $this->name = $name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.4.1
     */
    public function enqueue_styles() {
        wp_register_style($this->name . '-jquery-ui.min.css', plugin_dir_url(__FILE__) . 'css/jquery-ui.min.css', array(), $this->version, 'all');
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.4.1
     */
    public function enqueue_scripts() {
        wp_register_script($this->name . '-jquery-ui.min.js', plugin_dir_url(__FILE__) . 'js/jquery-ui.min.js', array(), $this->version, 'all');
        wp_register_script($this->name . '-custom.js', plugin_dir_url(__FILE__) . 'js/custom.js', array(), $this->version, 'all');
        wp_localize_script($this->name . '-custom.js', 'ulh_custom_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce(ULH_PLUGIN_OPTION_PREFIX.'ulh_public_select_timezone_ajax_nonce'),
            'internal_error_message' => __('Something went wrong!', 'user-login-history'),
        ));
    }

    /**
     * Shortcode to show listing table for frontend user.
     *
     * @since    1.4.1
     */
    public function shortcode_user_table() {
        wp_enqueue_script($this->name . '-jquery-ui.min.js');
        wp_enqueue_style($this->name . '-jquery-ui.min.css');
        wp_enqueue_script($this->name . '-custom.js');
        require_once plugin_dir_path(__FILE__) . 'partials/listing/listing.php';
    }

    /**
     * Frontend user can select timezone.
     *
     * @since    1.4.1
     */
    public function ulh_public_select_timezone() {
        check_ajax_referer( ULH_PLUGIN_OPTION_PREFIX.'ulh_public_select_timezone_ajax_nonce', 'secure_nonce' );
        
        if ("" != $_POST['timezone']) {
            global $current_user;
            $option_name = ULH_PLUGIN_OPTION_PREFIX . "user_timezone";
            update_user_meta($current_user->ID, $option_name, $_POST['timezone']);
        }
        exit;
    }

}
