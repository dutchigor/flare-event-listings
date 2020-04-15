<?php

/** 
 * CMB2 field that displays the default WordPress Tag metabox for the given Taxonomy 
 * 
 * @since      0.1.0
 * @author    Igor Honhoff
 */
class FG_Tag_field {
    /* Hold the class instance. */
    private static $instance = null;
   
  /**
   * The object is created from within the class itself
   * only if the class has no instance.
   *
   * @return FG_Tag_field
   **/
  public static function getInstance() {
        if ( self::$instance == null ) {
            self::$instance = new FG_Tag_field();
        }
    
        return self::$instance;
    }

  /**
   * The constructor
   **/
  private function __construct() {
        // Set up the field
        add_action( 'cmb2_render_wp_tags', [ $this, 'render' ], 10, 5 );
        add_filter( 'cmb2_override_meta_save', [$this, 'save' ], 10, 4 );
        add_action( 'do_meta_boxes', [ $this, 'remove_default_metabox' ] );

    }

    /**
     * Render the default WordPress Tag metabox in the place of this field
     */
    public function render( $field, $escaped_value, $object_id, $object_type, $field_type ) {

        // Only render field for post objects
        if ( 'post' !== $object_type ) {
            wp_die( 'This won\'t work for non-"post" object types!' );
        }
    
        // Determine taxonomy to display
        $taxonomy = $field->args( 'taxonomy' ) ?: 'post_tag';
    
        // Add script required by Tag metabox to footer 
        wp_enqueue_script( 'tags-box' );
        add_action( 'admin_footer', [$this, 'init_post_tag_box' ] );

        // Store tags in hidden field
        $attrs = $field->args['attributes'];
        $attrs['type'] = 'hidden';
        echo $field_type->input( $attrs );

        // Output default Tag metabox
        post_tags_meta_box( get_post( $object_id ), [
            'args' => array(
                'taxonomy' => $taxonomy,
            ),
        ] );
    }

    /**
     * Add script to initialize the Tag metabox
     */
    public function init_post_tag_box() {
        ?>
        <script type="text/javascript">
        jQuery( function( $ ) {
            window.tagBox && window.tagBox.init();
        });
        </script>
        <?php
    }
    
    /**
     * Prevent CMB2 from saving the term as a meta value
     */
    public function save( $null, $a, $field_args, $field ) {
        if ( 'wp_tags' === $field->args( 'type' ) ) {
            // Let WP handle it.
            return false;
        }
    
        return $null;
    }
    
    /**
     * Remove the Tags metabox from the default location
     * so that it only occurs in the CMB2 metabox.
     */
    public function remove_default_metabox() {
        // Find the registered wp_tags fields
        foreach ( CMB2_Boxes::get_all() as $cmb ) {
            foreach ( $cmb->prop( 'fields' ) as $field ) {
                if ( 'wp_tags' === $field['type'] ) {
                    // Remove the default metabox for the taxonomy on the given field
                    $taxonomy = isset( $field['taxonomy'] ) ? $field['taxonomy'] : 'post_tag';
                    remove_meta_box( "tagsdiv-{$taxonomy}", get_post_type(), 'side' );
                }
            }
        }
    }
}

FG_Tag_field::getInstance();