<?php
/**
 * Extends WordPress REST API to expose Insert and Select
 */
class Job_Postings_API {

	/**
	 * Initializes the Class
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Registers the routes to use
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			'job-postings/v1',
			'/insert/',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'insert_job_posting' ),
				'permission_callback' => function () {
					return current_user_can( 'publish_posts' );
				},
			)
		);

		register_rest_route(
			'job-postings/v1',
			'/select/',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'select_job_postings' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Inserts the submitted job posting
	 *
	 * @param WP_REST_Request $data An array containing job_title and job_description keys.
	 *
	 * @return array
	 */
	public function insert_job_posting( $data ) {
		global $wpdb;
		$table_name      = $wpdb->prefix . 'job_postings';
		$job_title       = sanitize_text_field( $data['job_title'] );
		$job_description = sanitize_textarea_field( $data['job_description'] );

		if ( empty( $job_title ) || empty( $job_description ) ) {
			return array(
				'success' => false,
				'message' => "Job title or description can't be empty",
			);
		}
		$wpdb->insert(
			$table_name,
			array(
				'job_title'       => $job_title,
				'job_description' => $job_description,
			)
		);
		return array(
			'success' => true,
			'message' => 'Job posting inserted successfully.',
		);
	}

	/**
	 * Returns job postings
	 *
	 * @param $data
	 *
	 * @return array|object|stdClass[]|string[]
	 */
	public function select_job_postings( $data ) {
		global $wpdb;
		$table_name   = $wpdb->prefix . 'job_postings';
		$search_term  = isset( $data['search'] ) ? sanitize_text_field( $data['search'] ) : '';
		$where_clause = $search_term ? "WHERE job_title LIKE '%$search_term%' OR job_description LIKE '%$search_term%'" : '';
		$results      = $wpdb->get_results( "SELECT * FROM $table_name $where_clause", ARRAY_A );
		if ( $results ) {
			return $results;
		} else {
			return array( 'message' => 'No job postings found.' );
		}
	}
}
