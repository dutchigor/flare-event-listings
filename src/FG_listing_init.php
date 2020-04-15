<?php
/** 
 * Create Custom Post Type to manage listings
 * 
 * @since      0.1.0
 * @author    Igor Honhoff
 */

class FG_listing_init {
  /* Hold the class instance. */
  private static $instance = null;
   
  /**
   * The object is created from within the class itself
   * only if the class has no instance.
   *
   * @return FG_listing_init
   **/
  public static function getInstance()
  {
    if (self::$instance == null)
    {
      self::$instance = new FG_listing_init();
    }
 
    return self::$instance;
  }

  /**
   * The constructor
   **/
  private function __construct() {
    // Set up the Listing
    add_action( 'init', [ $this, 'init_listing_cpt' ] );
    add_action( 'init', [ $this, 'init_listing_tax' ] );
    add_action( 'do_meta_boxes', [ $this, 'comments_mod' ] );
    add_action( 'pre_comment_user_ip', [ $this, 'remove_comments_ip' ] );
  }

  /** @var WP_Post_Type $listing_cpt The registered Listing CPT object */
  public $listing_cpt = null;

  /**
   * Register Listing custom post type
   */
  public function init_listing_cpt() {
    // UI labels
    $labels = array(
      'name' => _x( 'Listings', 'Post Type General Name', FG_TD ),
      'singular_name' => _x( 'Listing', 'Post Type Singular Name', FG_TD ),
      'menu_name' => _x( 'Listings', 'Admin Menu text', FG_TD ),
      'name_admin_bar' => _x( 'Listing', 'Add New on Toolbar', FG_TD ),
      'archives' => __( 'Listing Archives', FG_TD ),
      'attributes' => __( 'Listing Attributes', FG_TD ),
      'all_items' => __( 'All Listings', FG_TD ),
      'add_new_item' => __( 'Add New Listing', FG_TD ),
      'new_item' => __( 'New Listing', FG_TD ),
      'edit_item' => __( 'Edit Listing', FG_TD ),
      'update_item' => __( 'Update Listing', FG_TD ),
      'view_item' => __( 'View Listing', FG_TD ),
      'view_items' => __( 'View Listings', FG_TD ),
      'search_items' => __( 'Search Listing', FG_TD ),
      'insert_into_item' => __( 'Insert into Listing', FG_TD ),
      'uploaded_to_this_item' => __( 'Uploaded to this Listing', FG_TD ),
      'items_list' => __( 'Listings list', FG_TD ),
      'filter_items_list' => __( 'Filter Listings list', FG_TD ),
      'delete_with_user' => false
    );

    // CPT parameters
    $args = array(
      'label' => __( 'Listing', FG_TD ),
      'description' => __( 'Event service provider or pogram item', FG_TD ),
      'labels' => $labels,
      'menu_icon' => 'dashicons-admin-comments',
      'supports' => array( 'title', 'author', 'comments' ),
      'taxonomies' => array(),
      'show_ui' => true,
      'show_in_menu' => true,
      'menu_position' => 5,
      'show_in_admin_bar' => false,
      'show_in_nav_menus' => false,
      'can_export' => true,
      'has_archive' => false,
      'hierarchical' => false,
      'exclude_from_search' => true,
      'show_in_rest' => true,
      'publicly_queryable' => false,
      'capability_type' => 'post',
      'rewrite' => false,
    );

    // Register CPT
    $this->listing_cpt = register_post_type( 'listing', $args );
  }

  /**
   * Register Listing taxonomies
   */
  public function init_listing_tax() {
    // Service Provider category UI labels
    $sp_labels = array(
      'name'              => _x( 'SP Categories', 'taxonomy general name', FG_TD ),
      'singular_name'     => _x( 'SP Category', 'taxonomy singular name', FG_TD ),
      'search_items'      => __( 'Search SP Categories', FG_TD ),
      'all_items'         => __( 'All SP Categories', FG_TD ),
      'parent_item'       => __( 'Parent SP Category', FG_TD ),
      'parent_item_colon' => __( 'Parent SP Category:', FG_TD ),
      'edit_item'         => __( 'Edit SP Category', FG_TD ),
      'update_item'       => __( 'Update SP Category', FG_TD ),
      'add_new_item'      => __( 'Add New SP Category', FG_TD ),
      'new_item_name'     => __( 'New SP Category Name', FG_TD ),
      'menu_name'         => __( 'SP Category', FG_TD ),
    );

    // Service Provider category configuration
    $sp_args = array(
      'labels' => $sp_labels,
      'description' => __( 'Categories that can apply to service providers', FG_TD ),
      'hierarchical' => false,
      'public' => false,
      'publicly_queryable' => true,
      'show_ui' => true,
      'show_in_menu' => true,
      'show_in_nav_menus' => false,
      'show_tagcloud' => false,
      'show_in_quick_edit' => false,
      'show_admin_column' => false,
      'show_in_rest' => true,
      'rewrite' => false,
    );

    // Register Service Provider category taxonomy
    register_taxonomy( 'sp_categories', array('listing'), $sp_args );

    // Program Item category UI labels
    $pi_labels = array(
      'name'              => _x( 'PI Categories', 'taxonomy general name', FG_TD ),
      'singular_name'     => _x( 'PI Category', 'taxonomy singular name', FG_TD ),
      'search_items'      => __( 'Search PI Categories', FG_TD ),
      'all_items'         => __( 'All PI Categories', FG_TD ),
      'parent_item'       => __( 'Parent PI Category', FG_TD ),
      'parent_item_colon' => __( 'Parent PI Category:', FG_TD ),
      'edit_item'         => __( 'Edit PI Category', FG_TD ),
      'update_item'       => __( 'Update PI Category', FG_TD ),
      'add_new_item'      => __( 'Add New PI Category', FG_TD ),
      'new_item_name'     => __( 'New PI Category Name', FG_TD ),
      'menu_name'         => __( 'PI Category', FG_TD ),
    );

    // Program Item category configuration
    $pi_args = array(
      'labels' => $pi_labels,
      'description' => __( 'Categories that can apply to program items', FG_TD ),
      'hierarchical' => false,
      'public' => false,
      'publicly_queryable' => true,
      'show_ui' => true,
      'show_in_menu' => true,
      'show_in_nav_menus' => false,
      'show_tagcloud' => false,
      'show_in_quick_edit' => false,
      'show_admin_column' => false,
      'show_in_rest' => true,
      'rewrite' => false,
    );

    // Register Program Item category taxonomy
    register_taxonomy( 'pi_categories', array('listing'), $pi_args );

    // Program location UI labels
    $loc_labels = array(
      'name'              => _x( 'Program locations', 'taxonomy general name', FG_TD ),
      'singular_name'     => _x( 'Program location', 'taxonomy singular name', FG_TD ),
      'search_items'      => __( 'Search Program locations', FG_TD ),
      'all_items'         => __( 'All Program locations', FG_TD ),
      'parent_item'       => __( 'Parent Program location', FG_TD ),
      'parent_item_colon' => __( 'Parent Program location:', FG_TD ),
      'edit_item'         => __( 'Edit Program location', FG_TD ),
      'update_item'       => __( 'Update Program location', FG_TD ),
      'add_new_item'      => __( 'Add New Program location', FG_TD ),
      'new_item_name'     => __( 'New Program location Name', FG_TD ),
      'menu_name'         => __( 'Program location', FG_TD ),
    );

    // Program Location configuration
    $loc_args = array(
      'labels' => $loc_labels,
      'description' => __( 'Locations with a centrally managed program. E.g. stage', FG_TD ),
      'hierarchical' => false,
      'public' => false,
      'publicly_queryable' => true,
      'show_ui' => true,
      'show_in_menu' => true,
      'show_in_nav_menus' => false,
      'show_tagcloud' => false,
      'show_in_quick_edit' => false,
      'show_admin_column' => false,
      'show_in_rest' => true,
      'rewrite' => false,
    );

    // Register Program Location taxonomy
    register_taxonomy( 'program_locs', array('listing'), $loc_args );
  }

  /**
   * On the Listings CPT
   * rename the Comments metabox to Discussion
   * and remove the Comments status metabox
   */
  public function comments_mod() {
    remove_meta_box( 'commentstatusdiv', 'listing', 'normal' );
    remove_meta_box( 'commentsdiv', 'listing', 'normal' );
    add_meta_box( 'commentsdiv', __( 'Discussion', FG_TD ), 'post_comment_meta_box', 'listing', 'normal', 'high' );
  }

  /**
   * Stop WordPress from recording commentor IP addresses
   */
  public function remove_comments_ip() {
    return date( 'd/m/Y H:i' );
  }
}

FG_listing_init::getInstance();
