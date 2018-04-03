<?php

/**
 * This abstract class can help to create admin menu for listing page.
 *
 * @link       https://github.com/faiyazalam
 * 
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam
 * @access private
 */
if(!class_exists('Faulh_Admin_Menu'))
{
 class Faulh_Admin_Menu {

    
/**
 *Holds the object for the listing table class.
 * 
 * @access public
 * @var object 
 */
    public $list_table;
    
/**
     * The unique identifier of this plugin.
     *
     * @access   protected
     * @var      string    $plugin_name
     */
    protected $plugin_name;
    
 
    
     /**
     * The version of this plugin.
     *
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    protected $version;

    /**
     * Initialize the class and set its properties.
     *
     * @access public
     * @param      string    $plugin_name       The name of this plugin.
     * 
     */
    public function __construct($plugin_name, $version) {
        
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    
    
/**
 * The callback function for the action hook - admin menu.
 * 
 * @access public
 */
    public function plugin_menu() {
        $menu_slug = $this->plugin_name."-admin-listing";
        $hook = add_menu_page(
        Faulh_Template_Helper::plugin_name(), Faulh_Template_Helper::plugin_name(), 'manage_options', $menu_slug, array($this, 'render_list_table'),plugin_dir_url(__FILE__) . 'images/icon.png',
                30
        );
        add_submenu_page($menu_slug, esc_html__('Login List', 'faulh'), esc_html__('Login List', 'faulh'), 'manage_options', $menu_slug, array($this, 'render_list_table'));
        add_submenu_page($menu_slug, esc_html__('About', 'faulh'), esc_html__('About', 'faulh'), 'manage_options', $this->plugin_name.'-about', array($this, 'render_about_page'));
        add_submenu_page($menu_slug, esc_html__('Help', 'faulh'), esc_html__('Help', 'faulh'), 'manage_options', $this->plugin_name.'-help', array($this, 'render_help_page'));
    
    }
    

    public function render_about_page() {
        require_once   plugin_dir_path(dirname(__FILE__)) . 'admin/partials/about.php';
    }
    
    public function render_help_page() {
         require_once   plugin_dir_path(dirname(__FILE__)) . 'admin/partials/help.php';
    }
  



    /**
     * Loads the listing template file.
     * 
     *@access public
     */
  public function render_list_table() {
           $UserProfile = new Faulh_User_Profile($this->plugin_name, $this->version);
           if(is_network_admin()){
         $this->list_table =   new Faulh_Network_Admin_List_Table(null, $this->plugin_name, FAULH_TABLE_NAME, $UserProfile->get_current_user_timezone());
           }
 else {
             $this->list_table =   new Faulh_Admin_List_Table(null, $this->plugin_name, FAULH_TABLE_NAME, $UserProfile->get_current_user_timezone());
 }
        require  plugin_dir_path(dirname(__FILE__)) . 'admin/partials/listing.php';

    }
    
}   
}

