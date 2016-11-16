<?php
/*
Plugin Name: Logo Sliders
Description: Add and Display Slider Items
Version: 1.0
Author: DMM
Text Domain: logo_slider
*/

include( plugin_dir_path( __FILE__ ) . 'logo_slider_post_types.php' );
add_action( 'init', 'setup_slider_types' );
add_action( 'init', 'create_text_slider_type' );
/*flush rewrites on activation */
function slider_rewrite_flush() {

    setup_slider_types();

    // ATTENTION: This is *only* done during plugin activation hook in this example!
    // You should *NEVER EVER* do this on every page load!!
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'slider_rewrite_flush' );
/*Register Slider Post Type*/




// Register Scripts and Styles
function logo_slider_scripts() {

	wp_register_script( 'slickslider', plugins_url( '/js/slick.min.js' , __FILE__ ), array( 'jquery' ), '1.6', true );
	wp_register_style( 'slick-css', plugins_url( '/css/slick.css' , __FILE__ ), false, false );
	wp_enqueue_style( 'slick-css' );
	wp_register_style( 'slick-theme-css', plugins_url( '/css/slick-theme.css' , __FILE__ ), false, false );
	wp_enqueue_style( 'slick-theme-css' );
	wp_register_style( 'slider-css', plugins_url( '/css/slider.css' , __FILE__ ), false, false );
	wp_enqueue_style( 'slider-css' );
	wp_register_script( 'logoslider', plugins_url( '/js/logo_slider.js' , __FILE__ ), array( 'jquery' ), '1.0', true );
	
}
add_action( 'wp_enqueue_scripts', 'edge_slider_scripts' );

function logo_display_sliders ( $atts, $content=null ) {
	wp_enqueue_script('slickslider' );
	wp_enqueue_script('logoslider' );
	$atts = shortcode_atts(
		array(
			'category' => '',
			'count'    => '-1',
			'filter'   => 'false',
			'slider_group' => '',
			'class'  => '',
			'id'	=> '',
		), $atts, 'logo_slider' );

	//$prev_arrow = plugins_url( '/images/on-prev-arrow.png' , __FILE__ );
	//$next_arrow = plugins_url( '/images/on-next-arrow.png' , __FILE__ );
	// WP_Query arguments
$args = array (
	'post_type'              => array( 'logo_slider' ),
	'post_status'            => array( 'publish' ),
	'posts_per_page'         => $atts['count'],
	'order'                  => 'ASC',
	'orderby'                => 'menu_order',
);
if ( $atts['category'] !== '' ) {
	$args['category_name'] =  $atts['category'];
}
if ($atts['slider_group'] !== '' ) {
$args['tax_query'] = array(
		array(
			'taxonomy' => 'slider_group',
			'field'    => 'slug',
			'terms'    => $atts['slider_group'],
		),
	);
}

// The Query
$query = new WP_Query( $args );
$cont = '';
$cat_name_arr = array();
$cat_slug_arr = array();
$cont .= '<div';
if ($atts['id'] !== '' ) { $cont .= ' id="' . $atts['id'] . '"'; }
$cont .= ' class="slider-outer-container';
if ($atts['class'] !== '' ) { $cont .= ' ' . $atts['class']; }
$cont .= '">';
$cont .= '<div class="slider-container" style="display:none;">';
while ( $query->have_posts() ) : $query->the_post();
if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
	$slide_img = get_the_post_thumbnail_url( $the_post -> ID , 'full' );
} 
$cats = get_the_category();
$cat_str = '';
$cat_name_str = '';


foreach ($cats as $key => $value) {
$cat_name_arr[] = $value -> name;
$cat_slug_arr[] = $value -> slug;
$cat_name_str .= $value -> name . ' ';
$cat_str .= ' ' . $value -> slug;
}
$page_link = get_permalink();
//$page_title = get_the_title( );
$slug = basename($page_link);
$custom_link =  get_post_meta( get_the_ID() , 'advanced_options_slider-link' , true );
$cont .='<div class="slider-item';
if ( $cat_str !== '' ) { $cont .= $cat_str;}
$cont .= '">';
$cont .= '<div class="slider-image-container">';
if ($custom_link !== '' ) { $cont .= '<a href="'.$custom_link.'">'; }
$cont .= '<img class="slider-image" data-lazy="'.$slide_img.'" />';
if ($custom_link !== '' ) { $cont .= '</a>'; }
$cont .= '</div>';
$cont .= '</div>';
endwhile;
$cont .= '</div>';
if (!empty($content)){ $cont .= '<div class="slider-overlay-container"><div class="slider-inner-container content-width">';
$cont .= $content;
$cont .= '</div></div>';
}
$cont .= '</div>';


$final_cont =  $cont;



return do_shortcode( $final_cont );
}

add_shortcode( 'logo_slider', 'logo_display_sliders' );


