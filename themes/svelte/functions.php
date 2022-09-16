<?php
define( 'APP_THEME_DIR', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );

# Enqueue JS and CSS assets on the front-end
add_action( 'wp_enqueue_scripts', 'app_enqueue_assets' );
function app_enqueue_assets() {
	$template_dir = get_template_directory_uri();

	app_enqueue_style( 'theme-styles', $template_dir . '/style.css' );
}

# Enqueue JS and CSS assets on admin pages
add_action( 'admin_enqueue_scripts', 'app_admin_enqueue_scripts' );
function app_admin_enqueue_scripts() {
	$template_dir = get_template_directory_uri();

	if ( app_is_gutenberg_editor() ) {
		// wp_enqueue_style(
		// 	'theme-admin-css-bundle',
		// 	$template_dir . '/dist/css/administration/admin-styles.css'
		// );
	}

	# Enqueue Scripts
	# @app_enqueue_script attributes -- id, location, dependencies, in_footer = false
	// app_enqueue_script( 'theme-admin-functions', $template_dir . '/js/admin-functions.js', array( 'jquery' ) );

	# Enqueue Styles
	# @app_enqueue_style attributes -- id, location, dependencies, media = all
	# app_enqueue_style( 'theme-admin-styles', $template_dir . '/css/admin-style.css' );

	# Editor Styles
	# add_editor_style( 'css/custom-editor-style.css' );
}

# Attach Custom Post Types and Custom Taxonomies
add_action( 'init', 'app_attach_post_types_and_taxonomies', 0 );
function app_attach_post_types_and_taxonomies() {
	# Attach Custom Post Types
	include_once( APP_THEME_DIR . 'options/post-types.php' );

	# Attach Custom Taxonomies
	include_once( APP_THEME_DIR . 'options/taxonomies.php' );
}

add_action( 'after_setup_theme', 'app_setup_theme' );

# To override theme setup process in a child theme, add your own app_setup_theme() to your child theme's
# functions.php file.
if ( ! function_exists( 'app_setup_theme' ) ) {
	function app_setup_theme() {
		# Additional libraries and includes
		include_once( APP_THEME_DIR . 'includes/rest-api.php' );

		# Theme supports
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'menus' );
		add_theme_support( 'html5', array( 'gallery' ) );

		// Add support for full and wide align images.
		add_theme_support( 'align-wide' );

		// Add support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );

		# Manually select Post Formats to be supported - http://codex.wordpress.org/Post_Formats
		// add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat' ) );

		# Register Theme Menu Locations
		/*
		register_nav_menus( array(
			'main-menu' => __( 'Main Menu', 'app' ),
		) );
		*/

		# Attach custom shortcodes
		// include_once( APP_THEME_DIR . 'options/shortcodes.php' );

		# Add Filters
		add_filter( 'excerpt_more', 'app_excerpt_more' );
		add_filter( 'excerpt_length', 'app_excerpt_length', 999 );
		add_filter( 'app_theme_favicon_uri', function() {
			return get_template_directory_uri() . '/dist/images/favicon.ico';
		} );
	}
}

function app_excerpt_more() {
	return '...';
}

function app_excerpt_length() {
	return 55;
}

/**
 * Sometimes, when using Gutenberg blocks the content output
 * contains empty unnecessary paragraph tags.
 *
 * In WP v5.2 this will be fixed, however, until then this function
 * acts as a temporary solution.
 *
 * @see https://core.trac.wordpress.org/ticket/45495
 *
 * @param  string $content
 * @return string
 */
remove_filter( 'the_content', 'wpautop' );
add_filter( 'the_content', 'app_fix_empty_paragraphs_in_blocks' );
function app_fix_empty_paragraphs_in_blocks( $content ) {
	global $wp_version;

	if ( version_compare( $wp_version, '6.0', '<' ) && has_blocks() ) {
		return $content;
	}

	return wpautop( $content );
}

/**
 * Helper function to check is the current page using Gutenberg Editor
 *
 * @return bool
 */
function app_is_gutenberg_editor() {
	if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
		return true;
	}

	$current_screen = get_current_screen();

	if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
		return true;
	}

	return false;
}