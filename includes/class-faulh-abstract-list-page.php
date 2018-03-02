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
if(!class_exists('Faulh_Abstract_List_Page'))
{
 abstract class Faulh_Abstract_List_Page {
/**
 * Default limit.
 */
    const DEFAULT_OPTION_VALUE = 20;
    
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
     * Holds the option name for the limit.
     *
     * @access   private
     * @var      string    $option_name
     */
    private $option_name;
    
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
        $this->option_name = $this->plugin_name."_rows_per_page";
    }

    /**
     * The callback function for the filter hook - set-screen-option.
     * 
     * @access public
     * @param type $status
     * @param type $option
     * @param string $value
     * @return string
     */
    public function set_screen($status, $option, $value) {
        if ($option == $this->option_name) {
            return $value;
        }
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
        add_action("load-$hook", [ $this, 'screen_option']);
        
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
 * The callback function for the action hook -  load-customHook.
 * It sets screen options for the current loaded hook i.e. when listing page loaded.
 * 
 * @access public
 */
    public function screen_option() {
        $args = array(
            'label' => __('Records per page', 'faulh'),
            'default' => self::DEFAULT_OPTION_VALUE,
            'option' => $this->option_name //option name should not contain hyphen. 
            //WP replaces hyphen with underscore while processing request. See the definition of set_screen_options()
        );

        add_screen_option('per_page', $args);
        $this->list_table = $this->get_list_table_object();
    }

    /**
     * Loads the listing template file.
     * 
     *@access public
     */
  public function load_template() {
        require  plugin_dir_path(dirname(__FILE__)) . 'admin/partials/listing.php';
    }
    
 /**
     * This function will return object of list table class e.g. new WP_List_Table().
     */
    abstract function get_list_table_object();
}   
}

