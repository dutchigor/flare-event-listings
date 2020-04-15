<?php
/** 
 * Create a metabox to manage custom fields on the Listing CPT
 * 
 * @since      0.1.0
 * @author    Igor Honhoff
 */

class FG_listing_cmb {
  /* Hold the class instance. */
  private static $instance = null;
   
  /**
   * The object is created from within the class itself
   * only if the class has no instance.
   *
   * @return FG_listing_cmb
   **/
  public static function getInstance()
  {
    if (self::$instance == null)
    {
      self::$instance = new FG_listing_cmb();
    }
 
    return self::$instance;
  }

  /**
   * The constructor
   **/
  private function __construct() {
    // Set up the Listing metabox
    add_action( 'cmb2_init', [ $this, 'init_listing_mb' ] );

    // Fetch post values for default fields
    add_filter( 'cmb2_override_post_content_meta_value', [ $this, 'override_post_content_field_value' ], 10, 2 );
    add_filter( 'cmb2_override_post_excerpt_meta_value', [ $this, 'override_post_excerpt_field_value' ], 10, 2 );

    // Prevent CMB2 from saving default fields as post meta
    add_filter( 'cmb2_override_post_content_meta_save', '__return_true' );
    add_filter( 'cmb2_override_post_excerpt_meta_save', '__return_true' );
  }

  /* Prefix to make CMB2 fields unique for listings */
  protected const PREFIX = 'fg_';


  /**
   * Register the listing metabox to manage custom fields on the Listing CPT 
   */
  public function init_listing_mb()
  {
    $metabox = new_cmb2_box( [
      'id'            => 'listing_details',
      'title'         => __( 'Details', 'cmb2' ),
      'object_types'  => [ 'listing' ], // Post type
      'context'       => 'normal',
      'priority'      => 'high',
      'show_names'    => true,
      'show_in_rest'  => WP_REST_Server::ALLMETHODS
    ] );

    // For title and status the standard wordpress inputs will be used
    // Add Party type field as dropdown
    $metabox->add_field( [
      'name'             => __( 'Listing type', FG_TD ),
      'desc'             => __(
          'Program Activity for activities at a scheduled time, managed by the event orgainiser<br>'
          . 'Service Provider for activities managed at your own time and at your own discretion',
        FG_TD ),
      'id'               => self::PREFIX . 'type',
      'type'             => 'radio_inline',
      // 'show_option_none' => __( 'Select', FG_TD ),
      // 'default'          => 'custom',
      'column'           => true,
      'options'          => [
        'pi'             => __( 'Program item', FG_TD ),
        'sp'             => __( 'Service provider', 'cmb2' ),
      ],
      'attributes'     => [ 'required' => true ],
      'rest_query'     => 'type',
      ] );

    // Add Description wysiwyg and point to standard wordpress excerpt
    $metabox->add_field( [
      'id'   => 'post_excerpt', // Saves to WP post content, allows the_content()
      'name' => 'Title',
      'desc' => 'Enter a sub title for on the card',
      'type' => 'textarea',
      'attributes'     => [ 'required' => true ],
    ] );

    // Add Description wysiwyg and point to standard wordpress post content
    $metabox->add_field( [
      'id'   => 'post_content', // Saves to WP post content, allows the_content()
      'name' => 'Description',
      'desc' => 'Enter a brief description',
      'type' => 'wysiwyg',
      'options' => [
        'media_buttons' => false, // show insert/upload button(s)
        'teeny' => true,
      ],
      'attributes'     => [ 'required' => true ],
    ] );

    // Add Image and point to standard featured image
    $metabox->add_field( [
      'id'      => '_thumbnail', // Saves to WP post thumbnail, allows the_post_thumbnail()
      'name'    => 'Image',
      'desc'    => 'Upload/Select an image.',
      'type'    => 'file',
      'options' => [
        'url' => false,
      ],
      'text' => [
        'add_upload_file_text' => 'Add Image'
      ],
      'attributes'     => [ 'required' => true ],
      'preview_size' => 'thumbnail',
      'show_in_rest'  => false
     ] );

    // Add Homepage as url field
    $metabox->add_field( [
      'name' => __( 'Homepage', FG_TD ),
      'id'   => self::PREFIX . 'homepage',
      'type' => 'text_url',
      'protocols' => [ 'http', 'https' ],
      'rest_query'     => 'homepage'
    ] );

    // Add Suggested categories as list items for information only
    $metabox->add_field( [
      'name' => __( 'Category proposals', FG_TD ),
      'desc' => __( 'The Event Organiser will take the poposals in to consideration when assigning categories', FG_TD ),
      'type' => 'rest_list',
      'id'   =>  self::PREFIX . 'ctg_proposals',
      'rest_query'     => 'ctg_proposals'
    ] );

    // SP Categories - Only for service providers
    $metabox->add_field( [
      'id'   => self::PREFIX . 'sp_categories',
      'name' => __( 'Categories', FG_TD ),
      'type' => 'wp_tags',
      'taxonomy' => 'sp_categories',
      'column' => true,
      'attributes'     => [
        'data-conditional-id'     => self::PREFIX . 'type',
        'data-conditional-value'  => 'sp'
      ],
      'show_in_rest'  => false
    ] );

    // PI Categories - Only for program items
    $metabox->add_field( [
      'id'   => self::PREFIX . 'pi_categories',
      'name' => __( 'Categories', FG_TD ),
      'type' => 'wp_tags',
      'taxonomy' => 'pi_categories',
      'column' => true,
      'attributes'     => [
        'data-conditional-id'     => self::PREFIX . 'type',
        'data-conditional-value'  => 'pi'
      ],
      'show_in_rest'  => false
    ] );

    // Map (on image or live map) - Only for service providers
    $metabox->add_field( [
      'id' => self::PREFIX . 'location',
      'name' => __('Coordinates'),
      'desc' => __('Drag the marker after finding the right spot to set the exact coordinates'),
      'type' => 'image_map',
      'attributes'     => [
        'data-conditional-id'     => self::PREFIX . 'type',
        'data-conditional-value'  => 'sp'
      ],
      'base_layer'    => cmb2_get_option( 'fg_options', 'map_overlay_id' ),
      'base_type'     => 'image',
      'map_options'   => [
        'maxZoom'     => 1
      ]
    ] );

    // Stage - Only for program items
    $metabox->add_field( [
      'name'           => __( 'Location', FG_TD ),
      'desc'           => __( 'Location where the program item will take place', FG_TD ),
      'id'             => self::PREFIX . 'program_loc',
      'taxonomy'       => 'program_locs', // Enter Taxonomy Slug
      'type'           => 'taxonomy_radio_inline',
      'text'           => [
        'no_terms_text' => __( 'No locations have been created yet.', FG_TD ) // Default: "No terms"
      ],
      'remove_default' => 'true', // Removes the default metabox provided by WP core.
      'column'         => true,
      // 'query_args' => array(
        // 'orderby' => 'slug',
        // 'hide_empty' => true,
      // ),
      'attributes'     => [
        'data-conditional-id'     => self::PREFIX . 'type',
        'data-conditional-value'  => 'pi'
      ],
    ] );

    // From - Only for program items
    $metabox->add_field( [
      'name' => __( 'Start time', FG_TD ),
      'id' => self::PREFIX . 'start',
      'type' => 'text_time',
      // Override default time-picker attributes:
      'attributes' => [
      	'data-timepicker' => json_encode( [
          // 'timeOnlyTitle' => __( 'Choose your Time', 'cmb2' ),
          'timeFormat' => 'HH:mm',
          // 'stepMinute' => 1, // 1 minute increments instead of the default 5
        ] ),
        'data-conditional-id'     => self::PREFIX . 'type',
        'data-conditional-value'  => 'pi'
      ],
      'column' => true,
      // 'time_format' => 'h:i:s A',
      'rest_query'     => 'start',
    ] );

		// To - Only for program items
    $metabox->add_field( [
      'name' => __( 'End time', FG_TD ),
      'id' => self::PREFIX . 'end',
      'type' => 'text_time',
      // Override default time-picker attributes:
      'attributes' => [
      	'data-timepicker' => json_encode( [
      		// 'timeOnlyTitle' => __( 'Choose your Time', 'cmb2' ),
      		'timeFormat' => 'HH:mm',
          // 'stepMinute' => 1, // 1 minute increments instead of the default 5
        ] ),
        'data-conditional-id'     => self::PREFIX . 'type',
        'data-conditional-value'  => 'pi'
      ],
      'column' => true,
      // 'time_format' => 'h:i:s A',
      'rest_query'     => 'end',
    ] );

    // Event Organiser Comments
    $metabox->add_field( [
      'id'   => self::PREFIX . 'eo_notes', // Saves to WP post content, allows the_content()
      'name' => 'Internal notes',
      'desc' => 'These notes will only be visible to the Event Organisers',
      'type' => 'wysiwyg',
      'options' => [
        'media_buttons' => false, // show insert/upload button(s)
        'teeny' => true,
      ],
      'column' => true,
      'show_in_rest'  => false
    ] );

    $this->listing_mb = $metabox;
  }

  /**
   * Override description field with the post content on display.
   * 
   * @return string The value of the post_content field.
   **/
  public function override_post_content_field_value( $data, $post_id ) {
    return get_post_field( 'post_content', $post_id );
  }

    /**
   * Override title field with the post excerpt on display.
   * 
   * @return string The value of the post_excerpt field.
   **/

  public function override_post_excerpt_field_value( $data, $post_id ) {
    return get_post_field( 'post_excerpt', $post_id );
  }
}

FG_listing_cmb::getInstance();
