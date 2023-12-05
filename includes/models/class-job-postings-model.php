<?php
namespace WPMU_Dev\Models;

/**
 * Class Job_Postings_Model
 * @package WPMU_Dev\Models
 */
class Job_Postings_Model {

	/**
	 * @var Job_Postings_Model The single instance of the class.
	 */
	private static $instance;

	/**
	 * @var string The database table name.
	 */
	private string $table_name;

	/**
	 * Error code: Empty job title.
	 */
	const ERROR_EMPTY_JOB_TITLE = -101;

	/**
	 * Error code: Empty job description.
	 */
	const ERROR_EMPTY_JOB_DESCRIPTION = -102;

	/**
	 * Private constructor to enforce singleton pattern.
	 */
	private function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'job_postings';
	}

	/**
	 * Get the single instance of the class.
	 * @return Job_Postings_Model
	 */
	public static function get_instance(): Job_Postings_Model {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Create the database table.
	 * This function is called during plugin activation.
	 */
	public function create_table(): void {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$this->table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            job_title varchar(255) NOT NULL,
            job_description text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Insert a job posting into the database.
	 *
	 * @param string $job_title
	 * @param string $job_description
	 *
	 * @return false|int
	 */
	public function insert_job_posting( string $job_title, string $job_description ): bool|int {
		global $wpdb;

		// Validate job title and job description
		if ( empty( $job_title ) ) {
			return self::ERROR_EMPTY_JOB_TITLE;
		}

		if ( empty( $job_description ) ) {
			return self::ERROR_EMPTY_JOB_DESCRIPTION;
		}

		$job_title       = sanitize_text_field( $job_title );
		$job_description = sanitize_textarea_field( $job_description );

		$data = array(
			'job_title'       => $job_title,
			'job_description' => $job_description,
		);

		return $wpdb->insert( $this->table_name, $data );
	}

	/**
	 * Get job postings from the database.
	 *
	 * @param string|null $search_term
	 *
	 * @return array|null|object
	 */
	public function get_job_postings( string $search_term = null ): object|array|null {
		global $wpdb;

		$search_term = ! empty( $search_term ) ? sanitize_text_field( $search_term ) : '';

		$where_clause = $search_term ? "WHERE job_title LIKE '%$search_term%' OR job_description LIKE '%$search_term%'" : '';

		$sql = "SELECT * FROM {$this->table_name} $where_clause";

		return $wpdb->get_results( $sql, ARRAY_A );
	}
}
