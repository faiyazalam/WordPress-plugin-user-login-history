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
        Faulh_Template_Helper::plugin_name(), Faulh_Template_Helper::plugin_name(), 'manage_options', $menu_slug, [ $this, 'load_template']
        );
              //  add_submenu_page($menu_slug, __('Premium Benefits', 'faulh'), __('Premium Benefits', 'faulh'), 'manage_options', $this->plugin_name.'-get-premium', array($this, 'render_get_premium_page'));
        add_submenu_page($menu_slug, __('About', 'faulh'), __('About', 'faulh'), 'manage_options', $this->plugin_name.'-about', array($this, 'render_about_page'));
        add_submenu_page($menu_slug, __('Help', 'faulh'), __('Help', 'faulh'), 'manage_options', $this->plugin_name.'-help', array($this, 'render_help_page'));
    
    }
    

    public function render_about_page() {
        require_once   plugin_dir_path(dirname(__FILE__)) . 'admin/partials/about.php';
    }
    
    public function render_help_page() {
         require_once   plugin_dir_path(dirname(__FILE__)) . 'admin/partials/help.php';
    }
    public function render_get_premium_page() {
                 require_once   plugin_dir_path(dirname(__FILE__)) . 'admin/partials/premium.php';
    }



    /**
     * Loads the listing template file.
     * 
     *@access public
     */
  public function load_template() {
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

