<?php
//* Start the engine
require_once( get_template_directory() . '/lib/init.php' );

//* Setup Theme
include_once( get_stylesheet_directory() . '/lib/theme-defaults.php' );

//* Set Localization (do not remove)
load_child_theme_textdomain( 'encora', apply_filters( 'child_theme_textdomain', get_stylesheet_directory() . '/languages', 'encora' ) );

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', __( 'encora-2', 'encora' ) );
define( 'CHILD_THEME_URL', 'http://cmsthemefactory.com/themes/encora-2/' );
define( 'CHILD_THEME_VERSION', '3.1.2' );

//* Add HTML5 markup structure
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//* Enqueue Scripts
add_action( 'wp_enqueue_scripts', 'encora_load_scripts' );
function encora_load_scripts() {

	wp_enqueue_script( 'encora-responsive-menu', get_bloginfo( 'stylesheet_directory' ) . '/js/responsive-menu.js', array( 'jquery' ), '1.0.0' );
	
	wp_enqueue_style( 'dashicons' );
	
	wp_enqueue_style( 'google-font', '//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700', array(), CHILD_THEME_VERSION );

}

//* Add new image sizes
add_image_size( 'featured', 350, 122, TRUE );
add_image_size( 'announcements', 300, 200, TRUE );
add_image_size( 'slider', 1140, 445, TRUE );
add_image_size( 'sermonizer', 1100, 87, TRUE );

//* Add support for custom background
add_theme_support( 'custom-background' );

//* Add support for custom header
add_theme_support( 'custom-header', array(
	'width'           => 320,
	'height'          => 120,
	'header-selector' => '.site-title a',
	'header-text'     => false
) );

//* Unregister layout settings
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );

//* Unregister secondary sidebar
unregister_sidebar( 'sidebar-alt' );

//* Load Admin Stylesheet
add_action( 'admin_enqueue_scripts', 'encora_load_admin_styles' );
function encora_load_admin_styles() {

	wp_register_style( 'custom_wp_admin_css', get_stylesheet_directory_uri() . '/lib/admin-style.css', false, '1.0.0' );
	wp_enqueue_style( 'custom_wp_admin_css' );

}

//* Remove the site description
remove_action( 'genesis_site_description', 'genesis_seo_site_description' );

//* Reposition the secondary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_footer', 'genesis_do_subnav', 7 );

//* Reduce the secondary navigation menu to one level depth
add_filter( 'wp_nav_menu_args', 'encora_secondary_menu_args' );
function encora_secondary_menu_args( $args ){

	if( 'secondary' != $args['theme_location'] )
	return $args;

	$args['depth'] = 1;
	return $args;

}

//* Create Portfolio Type custom taxonomy
add_action( 'init', 'encora_type_taxonomy' );
function encora_type_taxonomy() {

	register_taxonomy( 'portfolio-type', 'portfolio',
		array(
			'labels' => array(
				'name'          => _x( 'Types', 'taxonomy general name', 'encora' ),
				'add_new_item'  => __( 'Add New Portfolio Type', 'encora' ),
				'new_item_name' => __( 'New Portfolio Type', 'encora' ),
			),
			'exclude_from_search' => true,
			'has_archive'         => true,
			'hierarchical'        => true,
			'rewrite'             => array( 'slug' => 'portfolio-type', 'with_front' => false ),
			'show_ui'             => true,
			'show_tagcloud'       => false,
		)
	);

}

//* Create portfolio custom post type
add_action( 'init', 'encora_portfolio_post_type' );
function encora_portfolio_post_type() {

	register_post_type( 'portfolio',
		array(
			'labels' => array(
				'name'          => __( 'Portfolio', 'encora' ),
				'singular_name' => __( 'Portfolio', 'encora' ),
			),
			'has_archive'  => true,
			'hierarchical' => true,
			'menu_icon'    => get_stylesheet_directory_uri() . '/lib/icons/portfolio.png',
			'public'       => true,
			'rewrite'      => array( 'slug' => 'portfolio', 'with_front' => false ),
			'supports'     => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'revisions', 'page-attributes', 'genesis-seo', 'genesis-cpt-archives-settings' ),
			'taxonomies'   => array( 'portfolio-type' ),

		)
	);
	
}

//* Add Portfolio Type Taxonomy to columns
add_filter( 'manage_taxonomies_for_portfolio_columns', 'encora_portfolio_columns' );
function encora_portfolio_columns( $taxonomies ) {

    $taxonomies[] = 'portfolio-type';
    return $taxonomies;

}

//* Remove the site description
remove_action( 'genesis_site_description', 'genesis_seo_site_description' );

//* Reposition the secondary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_footer', 'genesis_do_subnav', 7 );



//* Relocate the post info
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
add_action( 'genesis_entry_header', 'genesis_post_info', 5 );

//* Change the number of portfolio items to be displayed (props Bill Erickson)
add_action( 'pre_get_posts', 'encora_portfolio_items' );
function encora_portfolio_items( $query ) {

	if( $query->is_main_query() && !is_admin() && is_post_type_archive( 'portfolio' ) ) {
		$query->set( 'posts_per_page', '12' );
	}

}

//* Customize Portfolio post info and post meta
add_filter( 'genesis_post_info', 'encora_portfolio_post_info_meta' );
add_filter( 'genesis_post_meta', 'encora_portfolio_post_info_meta' );
function encora_portfolio_post_info_meta( $output ) {

     if( 'portfolio' == get_post_type() )
        return '';

    return $output;

}


//* Remove comment form allowed tags
add_filter( 'comment_form_defaults', 'encora_remove_comment_form_allowed_tags' );
function encora_remove_comment_form_allowed_tags( $defaults ) {
	
	$defaults['comment_notes_after'] = '';
	return $defaults;

}

//* Add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 3 );

//* Remove entry widget
remove_action( 'genesis_after_entry', 'genesis_after_entry_widget_area' );


//* Register widget areas
genesis_register_sidebar( array(
	'id'          => 'home-slider',
	'name'        => __( 'Home - Slider', 'encora' ),
	'description' => __( 'This is the slider section on the home page.', 'encora' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-top',
	'name'        => __( 'Home - Top', 'encora' ),
	'description' => __( 'This is the top section of the home page.', 'encora' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-middle',
	'name'        => __( 'Home - Middle', 'encora' ),
	'description' => __( 'This is the middle section of the home page.', 'encora' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-welcome',
	'name'        => __( 'Home - Welcome', 'encora' ),
	'description' => __( 'This is the welcome section of the home page.', 'encora' ),
) );
genesis_register_sidebar( array(
	'id'          => 'home-sidemenu',
	'name'        => __( 'Home - SideMenu', 'encora' ),
	'description' => __( 'This is the sidemenu section of the home page.', 'encora' ),
) );

/**
 * Filter the excerpt "read more" string.
 *
 * @param string $more "Read more" excerpt string.
 * @return string (Maybe) modified "read more" excerpt string.
 */
function wpdocs_excerpt_more( $more ) {
    return '...';
}
add_filter( 'excerpt_more', 'wpdocs_excerpt_more' );

//* Change the footer text
add_filter('genesis_footer_creds_text', 'sp_footer_creds_filter');
function sp_footer_creds_filter( $creds ) {
	$creds = '[footer_copyright] &middot; <a href="http://cmsthemefactory.com">Genesis Framework WordPress Church Theme by CMSTF</a>';
	return $creds;
}

function new_excerpt_length($length) {
    return 16;
}
add_filter('excerpt_length', 'new_excerpt_length');


//* Modify the Genesis content limit read more link
add_filter( 'get_the_content_more_link', 'sp_read_more_link' );
function sp_read_more_link() {
	return '... <a class="more-link" href="' . get_permalink() . '">READ MORE</a>';
}