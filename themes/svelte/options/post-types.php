<?php
// Register Task Post Type
register_post_type( 'app_task', array(
	'labels' => array(
		'name' => __( 'Tasks', 'app' ),
		'singular_name' => __( 'Task', 'app' ),
		'add_new' => __( 'Add New', 'app' ),
		'add_new_item' => __( 'Add new Task', 'app' ),
		'view_item' => __( 'View Task', 'app' ),
		'edit_item' => __( 'Edit Task', 'app' ),
		'new_item' => __( 'New Task', 'app' ),
		'view_item' => __( 'View Task', 'app' ),
		'search_items' => __( 'Search Tasks', 'app' ),
		'not_found' =>  __( 'No Tasks found', 'app' ),
		'not_found_in_trash' => __( 'No Tasks found in trash', 'app' ),
	),
	'public' => false,
	'exclude_from_search' => true,
	'show_ui' => true,
	'capability_type' => 'post',
	'hierarchical' => false,
	'_edit_link' => 'post.php?post=%d',
	'rewrite' => array(
		'slug' => 'task',
		'with_front' => false,
	),
	'query_var' => true,
	'menu_icon' => 'dashicons-list-view',
	'supports' => array( 'title', 'editor' ),
) );