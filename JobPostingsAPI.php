<?php

class JobPostingsAPI {
	public function __construct() {
		add_action('rest_api_init', array($this, 'register_routes'));
	}

	public function register_routes() {
		register_rest_route('job-postings/v1', '/insert/', array(
			'methods'  => 'POST',
			'callback' => array($this, 'insert_job_posting'),
			'permission_callback' => function () {
				return current_user_can('publish_posts');
			},
		));

		register_rest_route('job-postings/v1', '/select/', array(
			'methods'  => 'GET',
			'callback' => array($this, 'select_job_postings'),
			'permission_callback' => function () {
				return current_user_can('read');
			},
		));
	}

	public function insert_job_posting($data) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'job_postings';
		$job_title = sanitize_text_field($data['job_title']);
		$job_description = sanitize_textarea_field($data['job_description']);
		$wpdb->insert($table_name, array('job_title' => $job_title, 'job_description' => $job_description));
		return array('success' => true, 'message' => 'Job posting inserted successfully.');
	}

	public function select_job_postings($data) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'job_postings';
		$search_term = isset($data['search']) ? sanitize_text_field($data['search']) : '';
		$where_clause = $search_term ? "WHERE job_title LIKE '%$search_term%' OR job_description LIKE '%$search_term%'" : '';
		$results = $wpdb->get_results("SELECT * FROM $table_name $where_clause", ARRAY_A);
		if ($results) {
			return $results;
		} else {
			return array('message' => 'No job postings found.');
		}
	}
}
