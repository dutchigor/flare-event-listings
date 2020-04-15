<?php
/** 
 * Event Listings Options page for the plugin
 * 
 * @since      0.1.0
 * @author    Igor Honhoff
 */

class FG_plugin_options {
    /* Hold the class instance. */
    private static $instance = null;
    
    /**
     * The object is created from within the class itself
     * only if the class has no instance.
     *
     * @return FG_plugin_options
     **/
    public static function getInstance()
    {
        if (self::$instance == null)
        {
        self::$instance = new FG_plugin_options();
        }
    
        return self::$instance;
    }

    /**
     * The constructor
     **/
    private function __construct() {
        // Hook in and register a metabox to handle a theme options page and adds a menu item.
        add_action( 'cmb2_admin_init', [ $this, 'register' ] );
    }

    /* Prefix to make CMB2 fields unique for the options metabox */
    protected const PREFIX = 'fg_';

    /**
     * Register options page menu item and form
     **/
    public function register() {
        $cmb_options = new_cmb2_box( [
            'id'           => self::PREFIX . 'option_mb',
            'title'        => esc_html__( 'Listing Settings', FG_TD ),
            'object_types' => array( 'options-page' ),
            'option_key'      => self::PREFIX . 'options', // The option key and admin menu page slug.
            'parent_slug'     => 'edit.php?post_type=listing', // Make options page a submenu item of the themes menu.
            'capability'      => 'manage_options', // Cap required to view options-page.
            // 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
            // 'save_button'     => esc_html__( 'Save Theme Options', 'myprefix' ), // The text for the options-page save button. Defaults to 'Save'.
        ] );

        $cmb_options->add_field( [
            'name'    => __( 'Map Background Picture', FG_TD ),
            'desc'    => __( 'Upload an image or enter an URL.', FG_TD ),
            'id'      => 'map_overlay',
            'type'    => 'file',
            'options' => array(
                'url' => false, // Hide the text input for the url
            ),
            'text'    => [
                'add_upload_file_text' => 'Upload Image' // Change upload button text. Default: "Add or Upload File"
            ],
            // query_args are passed to wp.media's library query.
            'query_args' => [
                //only allow gif, jpg, or png images
                'type' => [
                	'image/gif',
                	'image/jpeg',
                	'image/png',
                ],
            ],
            'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
        ] );
    }
}

FG_plugin_options::getInstance();
