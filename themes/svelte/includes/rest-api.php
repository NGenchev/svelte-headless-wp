<?php
class App_API_Endopoints {
	private $filters;

	function __construct( $is_api_call = true ) {
		if ( $is_api_call ) {
			add_action( 'rest_api_init', function () {
				register_rest_route( 'app', '/register/', array(
					'methods'  => 'POST',
					'callback' => array( $this, 'create_user' ),
					'permission_callback' => '__return_true',
				) );

				register_rest_route( 'app', '/tasks/', array(
					'methods'  => 'GET',
					'callback' => array( $this, 'get_tasks' ),
					'permission_callback' => '__return_true',
				) );

				register_rest_route( 'app', '/tasks/', array(
					'methods'  => 'POST',
					'callback' => array( $this, 'add_task' ),
					'permission_callback' => '__return_true',
				) );

				register_rest_route( 'app', '/tasks/', array(
					'methods'  => 'DELETE',
					'callback' => array( $this, 'remove_task' ),
					'permission_callback' => '__return_true',
				) );
			} );

			$this->filters = [
				'page' => 1,
			];
		}
	}

	function get_tasks() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$id = get_current_user_id();

		$tasks = new WP_Query( [
			'post_type' 	 => 'app_task',
			'posts_per_page' => -1,
			'author' 		 => $id,
		] );

		$tasks = array_map( function( $task ) {
			return [
				'id' 		=> $task->ID,
				'title' 	=> $task->post_title,
				'content' 	=> $task->post_content
			];
		}, $tasks->posts );

		if ( empty( $tasks ) ) {
			return [
				'loading' => false,
				'errors' => "No More Tasks",
			];
		}

		return $tasks;
	}

	function create_user( $atts ) {
		$userinfo = $atts->get_body();

		if ( empty( $userinfo ) ) {
			return [];
		}

		$userinfo = json_decode( $userinfo, true );

		$user_id = wp_insert_user( [
			'user_pass' 	=> $userinfo['password'],
			'user_login' 	=> $userinfo['username'],
			'user_nicename' => $userinfo['username'],
			'user_email' 	=> $userinfo['email'],
			'role'   		=> 'editor',
		] );

		if ( is_wp_error( $user_id ) ) {
			return new WP_REST_Response( [ 'message' => array_values( $user_id->errors )[0] ], 403 );
		}

		$token = app_generate_token( $user_id );

		return [
			'token' 			=> $token,
			'user_display_name' => $userinfo['username'],
			'user_email' 		=> $userinfo['email'],
			'user_nicename' 	=> $userinfo['username'],
		];
	}

	function add_task( $atts ) {
		$params = json_decode( $atts->get_body(), true );

		if ( isset( $params['task'] ) && isset( $params['user'] ) ) {
			if ( isset( $params['user']['isLogged'] ) && $params['user']['isLogged'] === true ) {
				$username = $params['user']['username'];
				$task 	  = $params['task'];

				$user_id = get_user_by( 'login', $username );

				$task_id = wp_insert_post( [
					'post_type' 	=> 'app_task',
					'post_status' 	=> 'publish',
					'post_title' 	=> $task['title'],
					'post_content' 	=> $task['content'],
					'post_author'  	=> $user_id->ID,
				] );
			}
		}
		
		return $this->get_tasks();
	}

	function remove_task( $atts ) {
		global $wpdb;

		$params = json_decode( $atts->get_body(), true );

		if ( isset( $params['task'] ) && isset( $params['user'] ) ) {
			if ( isset( $params['user']['isLogged'] ) && $params['user']['isLogged'] === true ) {
				$task 	  = (int) $params['task'];
				$user_id  = get_current_user_id();

				$result = $wpdb->delete( $wpdb->posts, [ 'post_author' => $user_id, 'ID' => $task ], [ '%d', '%d' ] );
			}
		}

		return $this->get_tasks();
	}
}

new App_API_Endopoints();
