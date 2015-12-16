<?php
if( ! function_exists( 'donate' ) )
{
	function donate()
	{
		return new ThimPress_Donate();
	}
}

if( ! function_exists( 'donate_get_template' ) )
{
	function donate_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' )
	{
		if ( $args && is_array( $args ) ) {
	        extract( $args );
	    }

	    $located = donate_locate_template( $template_name, $template_path, $default_path );

	    if ( !file_exists( $located ) ) {
	        _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
	        return;
	    }
	    // Allow 3rd party plugin filter template file from their plugin
	    $located = apply_filters( 'donate_get_template', $located, $template_name, $args, $template_path, $default_path );

	    do_action( 'donate_before_template_part', $template_name, $template_path, $located, $args );

	    include( $located );

	    do_action( 'donate_after_template_part', $template_name, $template_path, $located, $args );
	}
}

if( ! function_exists( 'donate_template_path' ) )
{
	function donate_template_path(){
	    return apply_filters( 'donate_template_path', 'thim-event' );
	}
}

if( ! function_exists( 'donate_get_template_part' ) )
{
	function donate_get_template_part( $slug, $name = '' )
	{
		$template = '';

	    // Look in yourtheme/slug-name.php and yourtheme/courses-manage/slug-name.php
	    if ( $name ) {
	        $template = locate_template( array( "{$slug}-{$name}.php", donate_template_path() . "/{$slug}-{$name}.php" ) );
	    }

	    // Get default slug-name.php
	    if ( !$template && $name && file_exists( TP_DONATE_PATH . "/templates/{$slug}-{$name}.php" ) ) {
	        $template = TP_DONATE_PATH . "/templates/{$slug}-{$name}.php";
	    }

	    // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/courses-manage/slug.php
	    if ( !$template ) {
	        $template = locate_template( array( "{$slug}.php", donate_template_path() . "{$slug}.php" ) );
	    }

	    // Allow 3rd party plugin filter template file from their plugin
	    if ( $template ) {
	        $template = apply_filters( 'donate_get_template_part', $template, $slug, $name );
	    }
	    if ( $template && file_exists( $template ) ) {
	        load_template( $template, false );
	    }

	    return $template;
	}
}

if( ! function_exists( 'donate_locate_template' ) )
{
	function donate_locate_template( $template_name, $template_path = '', $default_path = '' )
	{

	    if ( !$template_path ) {
	        $template_path = donate_template_path();
	    }

	    if ( !$default_path ) {
	        $default_path = TP_DONATE_PATH . '/templates/';
	    }

	    $template = null;
	    // Look within passed path within the theme - this is priority
	    $template = locate_template(
            array(
                trailingslashit($template_path) . $template_name,
                $template_name
            )
        );
	    // Get default template
	    if ( !$template ) {
	        $template = $default_path . $template_name;
	    }

	    // Return what we found
	    return apply_filters( 'donate_locate_template', $template, $template_name, $template_path );
	}
}

if ( ! function_exists( 'is_event_taxonomy' ) ) {

    /**
     * Returns true when viewing a room taxonomy archive.
     * @return bool
     */
    function is_event_taxonomy() {
        return is_tax( get_object_taxonomies( 'tp_event' ) );
    }
}

/**
 * template hook function
 */
add_filter( 'the_content', 'donate_the_content' );
if( ! function_exists( 'donate_the_content' ) )
{
	function donate_the_content( $content )
	{
		return do_shortcode( $content );
	}
}

add_filter( 'the_post', 'donate_add_property_countdown' );
if( ! function_exists( 'donate_add_property_countdown' ) )
{
	/**
	 * add property inside the loop
	 * @param  [type] $post [description]
	 * @return [type]       [description]
	 */
	function donate_add_property_countdown( $post )
	{
		if( $post->post_type !== 'tp_event' )
			return $post;

		$date_start = get_post_meta( $post->ID, 'donate_date_start', true );
		$time_start = get_post_meta( $post->ID, 'donate_time_start', true );
		if( $date_start && $time_start )
		{
			$start = $date_start . ' ' . $time_start;
			$post->event_start = date( 'Y-m-d H:i:s', strtotime($start) );
		}
		else
		{
			$post->event_start = null;
		}

		$date_end = get_post_meta( $post->ID, 'donate_date_end', true );
		$time_end = get_post_meta( $post->ID, 'donate_time_end', true );
		if( $date_end && $time_end )
		{
			$end = $date_end . ' ' . $time_end;
			$post->event_end = date( 'Y-m-d H:i:s', strtotime($end) );
		}
		else
		{
			$post->event_end = null;
		}

		return $post;
	}

	/**
	 * get event start datetime
	 * @param  string $format [description]
	 * @return [type]         [description]
	 */
	function donate_start( $format = 'Y-m-d H:i:s' )
	{
		$post = get_post();
		if( ! $post->event_start )
			return null;

		return date( $format, strtotime( $post->event_start ) );
	}

	/**
	 * get event end datetime same as function
	 * @param  string $format [description]
	 * @return [type]         [description]
	 */
	function donate_end( $format = 'Y-m-d H:i:s' )
	{
		$post = get_post();
		if( ! $post->event_end )
			return null;

		return date( $format, strtotime( $post->event_end ) );
	}

}

add_action( 'donate_before_main_content', 'donate_before_main_content' );
if( ! function_exists( 'donate_before_main_content' ) )
{
	function donate_before_main_content()
	{

	}
}

add_action( 'donate_after_main_content', 'donate_after_main_content' );
if( ! function_exists( 'donate_after_main_content' ) )
{
	function donate_after_main_content()
	{

	}
}

add_action( 'donate_before_single_event', 'donate_before_single_event' );
if( ! function_exists( 'donate_before_single_event' ) )
{
	function donate_before_single_event()
	{

	}
}

add_action( 'donate_after_single_event', 'donate_after_single_event' );
if( ! function_exists( 'donate_after_single_event' ) )
{
	function donate_after_single_event()
	{

	}
}

/*template hook*/
add_action( 'donate_single_event_title', 'donate_single_event_title' );
if( ! function_exists( 'donate_single_event_title' ) )
{
	function donate_single_event_title()
	{
		donate_get_template('loop/title.php');
	}
}

add_action( 'donate_single_event_thumbnail', 'donate_single_event_thumbnail' );
if( ! function_exists( 'donate_single_event_thumbnail' ) )
{
	function donate_single_event_thumbnail()
	{
		donate_get_template('loop/thumbnail.php');
	}
}

add_action( 'donate_loop_event_countdown', 'donate_loop_event_countdown' );
if( ! function_exists( 'donate_loop_event_countdown' ) )
{
	function donate_loop_event_countdown()
	{
		donate_get_template('loop/countdown.php');
	}
}

add_action( 'donate_single_event_content', 'donate_single_event_content' );
if( ! function_exists( 'donate_single_event_content' ) )
{
	function donate_single_event_content()
	{
		donate_get_template('loop/content.php');
	}
}

if( ! function_exists( 'donate_get_currencies' ) )
{
	function donate_get_currencies()
	{
		$currencies = array();
		return apply_filters( 'donate_currencies', $currencies );
	}
}
