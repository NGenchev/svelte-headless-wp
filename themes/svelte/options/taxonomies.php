<?php
// Register Task Tags
register_taxonomy(
	'app_task_tag', # Taxonomy name
	array( 'app_task' ), # Post Types
	array( # Arguments
		'labels'            => array(
			'name'                       => __( 'Tags', 'app' ),
			'singular_name'              => __( 'Custom Tag', 'app' ),
			'search_items'               => __( 'Search Tags', 'app' ),
			'popular_items'              => __( 'Popular Tags', 'app' ),
			'all_items'                  => __( 'All Tags', 'app' ),
			'view_item'                  => __( 'View Custom Tag', 'app' ),
			'edit_item'                  => __( 'Edit Custom Tag', 'app' ),
			'update_item'                => __( 'Update Custom Tag', 'app' ),
			'add_new_item'               => __( 'Add New Custom Tag', 'app' ),
			'new_item_name'              => __( 'New Custom Tag Name', 'app' ),
			'separate_items_with_commas' => __( 'Separate Tags with commas', 'app' ),
			'add_or_remove_items'        => __( 'Add or remove Tags', 'app' ),
			'choose_from_most_used'      => __( 'Choose from the most used Tags', 'app' ),
			'not_found'                  => __( 'No Tags found.', 'app' ),
			'menu_name'                  => __( 'Tags', 'app' ),
		),
		'hierarchical'          => false,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'task-tag' ),
	)
);