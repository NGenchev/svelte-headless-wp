<?php

class App_API_Endopoints {
	private $filters;

	function __construct( $is_api_call = true ) {
		if ( $is_api_call ) {
			add_action( 'rest_api_init', function () {
				register_rest_route( 'app', '/tasks/', array(
					'methods'  => 'GET',
					'callback' => array( $this, 'get_tasks' ),
					'permission_callback' => '__return_true',
				) );
			} );

			$this->filters = [
				'page' => 1,
			];
		}
	}

	function get_tasks() {
		$tasks = new WP_Query( [
			'post_type' => 'app_task',
			'posts_per_page' => -1,
		] );

		$tasks = array_map( function( $task ) {
			return [
				'id' => $task->ID,
				'title' => $task->post_title,
				'content' => $task->post_content
			];
		}, $tasks->posts );

		return $tasks;
	}
}

new App_API_Endopoints();
