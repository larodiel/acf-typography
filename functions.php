<?php
    function acf_field_typography_get_fonts_to_enqueue() {
        if( is_singular() ) {
            global $post;
            $post_fields = get_field_objects( $post->ID );
        }
        $post_fields = ( empty( $post_fields ) ) ? array() : $post_fields;
        $option_fields = get_field_objects( 'options' );

        $option_fields = ( empty( $option_fields ) ) ? array() : $option_fields;
        $fields = array_merge( $post_fields, $option_fields );
        $font_fields = array();
        foreach( $fields as $field ) {
            if( !empty( $field['type'] ) && 'typography' == $field['type'] && !empty( $field['value'] ) ) {
                $typography =  $field['value'];
                $font_fields[] = $typography;
            }
        }

        $font_fields = apply_filters( 'acf_field_typography/enqueued_fonts', $font_fields );

        return $font_fields;
    }

    /**
    * @uses acf_field_typography_get_fonts_to_enqueue()
    *
    */
    function acf_field_typography_google_font_enqueue(){
        $fonts = acf_field_typography_get_fonts_to_enqueue();

        if( empty( $fonts ) ) {
            return;
        }
        $subsets = array();
        $font_element = array();
        foreach( $fonts as $font ) {
            $font_name = str_replace( ' ', '+', $font['font-family'] );
            if( $font['font-weight'] == '' || $font['font-weight'] == '400' || $font['font-weight'] == 'regular' ) {
                $font_element[] = $font_name;
            }
            else {
                $font_element[] = $font_name . ':' . $font['font-weight'];
            }
        }
        $font_string = implode( '|', $font_element );
        $request = '//fonts.googleapis.com/css?family=' . $font_string;
        wp_enqueue_style( 'acf_field_typography-enqueue-fonts', $request );
    }

    /**
     * Get field CSS
     */

    function getCSSTypography($field) {
        $css = '';

        if (!empty($field)) {
            foreach($field as $key=>$value) {
                if (!empty($value)) {
                    if ($key != "backupfont") {
                       $css .= $key.":".$value.";";
                    }
                }
            }
        }
        return $css;
    }

    /**
     * [hex2rgb converts hex to rgb]
     */
    function hex2rgb($hex) {
        $hex = str_replace("#", "", $hex);

        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        $rgb = 'rgb('.$r.",". $g.",". $b.")";

        // Returns an array with the rgb values.
        return $rgb;
    }