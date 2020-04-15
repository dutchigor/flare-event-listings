<?php

/** 
 * CMB2 list field type that allows adding items through REST
 * and displays the result in the admin metabox
 * 
 * @since      0.1.0
 * @author    Igor Honhoff
 */
class FG_REST_List_field
{
    /* Hold the class instance. */
    private static $instance = null;

    /**
     * The object is created from within the class itself
     * only if the class has no instance.
     *
     * @return FG_REST_List_field
     **/
    public static function getInstance()
        {
        if (self::$instance == null)
        {
            self::$instance = new FG_REST_List_field();
        }
    
        return self::$instance;
        }
    
    /**
     * The constructor
     **/
    private function __construct() {
        add_action( 'cmb2_render_rest_list', [ $this, 'render' ], 10, 5 );
        add_filter( 'cmb2_get_rest_value_rest_list', [ $this, 'rest_value'] );
    }

    /**
     * Render list in backend Metabox
     */
    public function render( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
        echo '<ul>';
        if ( is_array( $escaped_value ) ) {
            foreach ($escaped_value as $i => $ctg ) {
                echo '<li>'
                    . $field_type_object->input( [
                        'name'  => $field_type_object->_name( '[' . $i .']' ),
                        'id'    => $field_type_object->_id( '_' . $i ),
                        'value' => $ctg,
                        'type'  => 'hidden',
                        'desc'  => ''
                    ])
                    . $ctg
                    . '</li>';
            }
        } else {
            echo '<li>' . $field_type_object->input( [ 'type' => 'hidden' ] );
            echo $escaped_value . '</li>';    
        }
        echo '</ul>';
        echo $field_type_object->_desc();
    }

    /**
     * Value to return if queried through REST API
     */
    public function rest_value( $value ) {
        return ( gettype( $value ) == 'array' ) ? $value : [];
    }
}

FG_REST_List_field::getInstance();
