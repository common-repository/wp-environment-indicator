<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://jonheller.net
 * @since      1.0.0
 *
 * @package    Wp_Environment_Indicator
 * @subpackage Wp_Environment_Indicator/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Environment_Indicator
 * @subpackage Wp_Environment_Indicator/admin
 * @author     Jon Heller <jon@jonheller.net>
 */
class Wp_Environment_Indicator_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    private $hostName;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->hostName = $this->get_host_name();
    }

    private function get_host_name()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $hostName = $_SERVER['HTTP_HOST'];
        } elseif (isset($_SERVER['SERVER_NAME'])) {
            $hostName = $_SERVER['SERVER_NAME'];
        } else {
            $hostName = $_SERVER['VIRTUAL_HOST'];
        }

        return $hostName;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * Enqueues the admin style, but only if we're on the dev host
         *
         * An instance of this class should be passed to the run() function
         * defined in Wp_Environment_Indicator_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wp_Environment_Indicator_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        $devServer = get_option('wpei_dev_hostname_field');
        $devServer = str_replace("http://", "", $devServer);
        $devServer = str_replace("https://", "", $devServer);

        if (strpos($devServer, $this->hostName) !== false) {
            if (get_option('wpei_dev_hostname_color') == "yellow") {
                wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-environment-indicator-admin-yellow.css', array(), $this->version, 'all');
            } else {
                wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-environment-indicator-admin-red.css', array(), $this->version, 'all');
            }
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         *
         *
         * An instance of this class should be passed to the run() function
         * defined in Wp_Environment_Indicator_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wp_Environment_Indicator_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        // wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-environment-indicator-admin.js', array( 'jquery' ), $this->version, false );
    }

    public function create_settings_page()
    {
        // Add the menu item and page
        $page_title = 'WP Environment Indicator Settings';
        $menu_title = 'WP Environment Indicator';
        $capability = 'manage_options';
        $slug = 'wp_environment_indicator_fields';
        $callback = array( $this, 'plugin_settings_page_content' );
        $icon = 'dashicons-admin-plugins';
        $position = 100;

        add_submenu_page('options-general.php', $page_title, $menu_title, $capability, $slug, $callback);
    }

    public function plugin_settings_page_content()
    {
        ?>
		<div class="wrap">
		<h2>WP Environment Indicator Settings</h2>
		<form method="post" action="options.php">
            <?php
                settings_fields('wp_environment_indicator_fields');
        do_settings_sections('wp_environment_indicator_fields');
        submit_button(); ?>
		</form>
	</div> <?php
    }

    public function setup_sections()
    {
        add_settings_section('environment_urls_section', 'Environment Host Names', array( $this, 'section_callback' ), 'wp_environment_indicator_fields');
    }

    public function section_callback($arguments)
    {
        echo '<p>Enter the host name of your development environment below. When visiting the site at this host name, an indicator will be added to the top of the WordPress admin.</p>';
        echo '<em>Current Host</em>: ' . $this->hostName;
    }

    public function setup_fields()
    {
        add_settings_field('wpei_dev_hostname_field', 'Development Host Name', array( $this, 'hostname_field' ), 'wp_environment_indicator_fields', 'environment_urls_section');
        register_setting('wp_environment_indicator_fields', 'wpei_dev_hostname_field');

        add_settings_field('wpei_dev_hostname_color', 'Development Banner Color', array( $this, 'banner_color' ), 'wp_environment_indicator_fields', 'environment_urls_section');
        register_setting('wp_environment_indicator_fields', 'wpei_dev_hostname_color');
    }

    public function hostname_field($arguments)
    {
        echo '<input name="wpei_dev_hostname_field" id="wpei_dev_hostname_field" type="text" value="' . get_option('wpei_dev_hostname_field') . '" />';
    }

    public function banner_color($arguments)
    {
        echo '<select name="wpei_dev_hostname_color" id="wpei_dev_hostname_color">';
        if (get_option('wpei_dev_hostname_color') == 'red') {
            echo '<option value="yellow">Yellow</option>
                  <option value="red" selected>Red</option>';
        } else {
            echo '<option value="yellow" selected>Yellow</option>
                  <option value="red">Red</option>';
        }
    }
}
