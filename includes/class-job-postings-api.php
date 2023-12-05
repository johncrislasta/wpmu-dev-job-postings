<?php

namespace WPMU_Dev;

use WPMU_Dev\Models\Job_Postings_Model;

/**
 * Extends WordPress REST API to expose Insert and Select
 */
class Job_Postings_API {

	/**
	 * @var Job_Postings_Model The model for database interactions.
	 */
	private Job_Postings_Model $model;

	/**
	 * Initializes the Class
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );

		// Initialize the model
		$this->model = Job_Postings_Model::get_instance();
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

		$result = $this->model->insert_job_posting( $data['job_title'], $data['job_description'] );

		return array(
			'success' => $result > 0,
			'message' => ( $result > 0 ) ?
				'Job posting inserted successfully.' :
				'There was an error in creating the job posting', // @TODO: create a function to describe the errors thrown by the model
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

		$results = $this->model->get_job_postings( $data['search'] );

		if ( $results ) {
			return $results;
		} else {
			return array( 'message' => 'No job postings found.' );
		}
	}
}
