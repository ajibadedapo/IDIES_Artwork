<?php
/**
 * Roots initial setup and constants
 */
function roots_setup() {
  // Make theme available for translation
  // Community translations can be found at https://github.com/roots/roots-translations
  load_theme_textdomain('roots', get_template_directory() . '/lang');

  // Enable plugins to manage the document title
  // http://codex.wordpress.org/Function_Reference/add_theme_support#Title_Tag
  add_theme_support('title-tag');

  // Register wp_nav_menu() menus
  // http://codex.wordpress.org/Function_Reference/register_nav_menus
  register_nav_menus(array(
    'primary_navigation' => __('Primary Navigation', 'roots')
  ));

  // Add post thumbnails
  // http://codex.wordpress.org/Post_Thumbnails
  // http://codex.wordpress.org/Function_Reference/set_post_thumbnail_size
  // http://codex.wordpress.org/Function_Reference/add_image_size
  add_theme_support('post-thumbnails');

  // Add post formats
  // http://codex.wordpress.org/Post_Formats
  add_theme_support('post-formats', array('aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio'));

  // Add HTML5 markup for captions
  // http://codex.wordpress.org/Function_Reference/add_theme_support#HTML5
  add_theme_support('html5', array('caption', 'comment-form', 'comment-list'));

  // Tell the TinyMCE editor to use a custom stylesheet
  add_editor_style('/assets/css/editor-style.css');
}
add_action('after_setup_theme', 'roots_setup');

/**
 * Register sidebars
 */
function roots_widgets_init() {

	register_sidebar(array(
		'name'          => __('Primary', 'roots'),
		'id'            => 'sidebar-primary',
		'before_widget' => '<section class="widget %1$s %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
	));

	register_sidebar(array(
		'name'          => __('Footer', 'roots'),
		'id'            => 'sidebar-footer',
		'before_widget' => '<section class="widget %1$s %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
	));

}
add_action('widgets_init', 'roots_widgets_init');

/*
 *****************************************
 IDIES HOUSEKEEPING TASKS 
 *****************************************
 */
 
/*
 *
 * SET EVENTS THAT HAVE PASSED TO 'Past'
 * 
 */
function check_past_events(  ) {
	if ( is_admin() ) {
		
		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'events',
			'post_status'      => 'publish',
			'meta_key'         => 'Past',
			'meta_value'       => '',
		);
		
		$posts_array = get_posts( $args );

		$today = new DateTime();
		
		foreach ( $posts_array as $post ) {
			setup_postdata( $post );
						
			$start_date = new DateTime( get_cfc_field( 'events-details' , 'event-date' , $post->ID ) );
			$multiday = ( strcmp( "Yes" , get_cfc_field( 'events-details' , 'multi-day-event' , $post->ID ) ) === 0 ) ;
			$end_date = new DateTime( get_cfc_field( 'events-details' , 'event-end-date' , $post->ID ) );
			
			if ( ( $multiday && ( $end_date < $today ) ) ||  ( !($multiday) && ( $start_date < $today ) ) ) {
				// in the past
				//die( the_title() . " is in the past." );
				update_post_meta( $post->ID , 'Past' , 'Yes' );
			}
		}
	}
}
add_action('admin_init', 'check_past_events');

/***************************************
* IDIES
***************************************/

/**
 * IDIES initial setup and constants
 */
define('FOOTER_WIDGETS',4);
define('SPLASH_WIDGETS',5);

add_action('after_setup_theme', 'idies_setup');
function idies_setup() {

	// Add more menus
	register_nav_menus(array(
		'secondary_navigation' => __('Secondary Navigation', 'roots')
		));

}

/*
 * Change number of posts shown on Custom Post Types
*/
function idies_pagesize( $query ) {
    if ( is_admin() || ! $query->is_main_query() )
        return;

    if ( is_post_type_archive( 'presentation' ) ) {
        $query->set( 'posts_per_page', -1 );
        return;
    }
}
add_action( 'pre_get_posts', 'idies_pagesize', 1 );

/*
 * Add IDIES Roots Theme Option Page.
*/
add_action('admin_menu', 'idies_theme_menu');

function idies_theme_menu() {

	add_theme_page('IDIES Theme Options', 'IDIES Theme', 'edit_theme_options', 'idies-theme-page', 'idies_theme_options');
	function idies_theme_options() {

		//create custom top-level menu
		?>
		<div class="wrap">
		<h1>IDIES Theme Options</h1>
		<h1>Utilities</h1>
		<table class="form-table">
		<tr valign="top">
		<th scope="row">Disable Pingbacks on Existing Pages</th>
		<td><button class="button-primary disabled" onclick="location.href='#'">Disable Pingbacks</button></td>
		</tr>
		<tr valign="top">
		<th scope="row">Disable Trackbacks on Existing Pages</th>
		<td><button class="button-primary disabled" onclick="location.href='#'">Disable Trackbacks</button></td>
		</tr>
		<tr valign="top">
		<th scope="row">Disable Comments on Existing Pages</th>
		<td><button class="button-primary disabled" onclick="location.href='#'">Disable Comments</button></td>
		</tr>
		</table>
		</div>
		<?php

	}
}

/*
 * Add content to Admin All Pages view.
 * Remove all pingbacks, trackbacks [, and comments] 
 * from existing pages.
*/
add_action( 'admin_notices', 'idies_admin_notices' );
function idies_admin_notices() {
    $currentScreen = get_current_screen();
    if( $currentScreen->id === "edit-page" ) {
		?>
		<?php
	}
}

/**
 * Add IDIES sidebar widgets
 */
function idies_widgets_init(  ) {

	for ($indx=1;$indx <= SPLASH_WIDGETS; $indx++) {
		register_sidebar(array(
		'name'          => __('Splash'.$indx, 'roots'),
		'id'            => 'sidebar-splash-'.$indx,
		'before_widget' => '<section class="widget %1$s %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
		));
	}

	for ($jndx=1;$jndx <= FOOTER_WIDGETS; $jndx++) {
		register_sidebar(array(
		'name'          => __('Footer'.$jndx, 'roots'),
		'id'            => 'sidebar-footer-'.$jndx,
		'before_widget' => '<section class="widget %1$s %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2>',
		'after_title'   => '</h2>',
		));
	}
	
	register_sidebar(array(
		'name'          => __('Splash Slideshow'),
		'id'            => 'sidebar-slideshow',
		'before_widget' => '<section class="widget %1$s %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<p class="brand-heading">',
		'after_title'   => '</p>',
		));
}
add_action('widgets_init', 'idies_widgets_init' );

/***************************************
 * REWRITE TAGS
 ***************************************/
/**
 * Add a rewrite tag
 */
function idies_rewrite_tag() {
  //add_rewrite_tag('%idies_type%', '([^&]+)');
}
add_action('init', 'idies_rewrite_tag', 10, 0);
/**
 * Add a rewrite rule
 */
function idies_rewrite_rule() {
    //add_rewrite_rule('^affiliates/([^/]*)/([^/]*)/([^/]*)/([^/]*)/?','index.php?page_id=203&idies_type=$matches[1]&idies_dept=$matches[2]&idies_cent=$matches[3]&idies_sch=$matches[4]','top');
}
add_action('init', 'idies_rewrite_rule', 10, 0);

/***************************************
 * FILTERS
 ***************************************/
/**
 * Add extra query variables
 */
function idies_add_query_vars_filter( $vars ){
  $vars[] = "idies-form-action";
  $vars[] = "idies-form-cfc";
  $vars[] = "idies-form-target";
  $vars[] = "idies-form-which";
  $vars[] = "idies-affil-pane";
  $vars[] = "idies-affil-order";
  
  $vars[] = "idies-year";
  $vars[] = "idies-type";
  
  return $vars;
}
add_filter( 'query_vars', 'idies_add_query_vars_filter' );

/**
 * Show 12 affiliates at a time
 */
function idies_limits( $limits )
{
	if( !is_admin() && is_archive( 'affiliate' )  ) {
		$offset=16;
		// get limits
		$ok = preg_match_all('/\d+/i',$limits,$match_limits);
		if ($ok) return 'LIMIT ' . $offset * intval($match_limits[0][0] / $match_limits[0][1]) . ", " . $offset;
	}

  // not in glossary category, return default limits
  return $limits;
}
add_filter('post_limits', 'idies_limits' );

/**
 * Show affiliates in alphabetical order
 */
function idies_alphabetical( $orderby )
{
  if( !is_admin() && is_archive( 'affiliate' )  ) {
     // alphabetical order by post title
     return "post_title ASC";
  }

  // not in glossary category, return default order by
  return $orderby;
}
add_filter('posts_orderby', 'idies_alphabetical' );

/**
 * Add Custom Post Types to Search
 */
//add_filter( 'pre_get_posts', 'idies_cpt_search' );
/**
 * This function modifies the main WordPress query to include an array of 
 * post types instead of the default 'post' post type.
 *
 * @param object $query  The original query.
 * @return object $query The amended query.
 */
function idies_cpt_search( $query ) {
    if ( $query->is_search ) {
	$query->set( 'post_type', array( 'post', 'affiliate' , 'knowledgebase' ) );
    }
    return $query;   
}
