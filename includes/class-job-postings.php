<?php
namespace WPMU_Dev;

use WPMU_Dev\Models\Job_Postings_Model;

/**
 * Class Job_Postings
 * @package WPMU_Dev
 */
class Job_Postings {

	/**
	 * @var Job_Postings_Model The model for database interactions.
	 */
	private Job_Postings_Model $model;

	/**
	 * @var Template The template class instance.
	 */
	private Template $template;

	/**
	 * Constructor.
	 * Sets up the actions and filters for the plugin.
	 */
	public function __construct() {
		// Add hooks, actions, and filters here
		add_action( 'init', array( $this, 'register_shortcodes' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Add activation hook
		register_activation_hook( __FILE__, array( $this, 'activate' ) );

		// Initialize the model
		$this->model = Job_Postings_Model::get_instance();

		// Initialize the template class
		$this->template = new Template();
	}

	/**
	 * Run the plugin.
	 * Additional setup or initialization can be done here.
	 */
	public function run(): void {
		// Additional setup or initialization
	}

	/**
	 * Register shortcodes.
	 * Shortcodes for this plugin should be registered here.
	 */
	public function register_shortcodes(): void {
		add_shortcode( 'display_job_postings', array( $this, 'display_job_postings_shortcode' ) );
		add_shortcode( 'submit_job_postings', array( $this, 'submit_job_postings_shortcode' ) );
	}

	/**
	 * Enqueue scripts.
	 * Scripts for the plugin should be enqueued here.
	 */
	public function enqueue_scripts(): void {
		// Enqueue the script for AJAX
		wp_enqueue_script( 'wpmu-dev-job-postings-scripts', WPMU_DEV_JOB_POSTINGS_URL . 'assets/js/wpmu-dev-job-postings-scripts.js', array( 'jquery' ), '1.0.0', true );

		// Pass AJAX URL to script
		wp_localize_script( 'wpmu-dev-job-postings-scripts', 'wpmudev_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

		// Enqueue the styles
		wp_enqueue_style( 'wpmu-dev-job-postings-styles', WPMU_DEV_JOB_POSTINGS_URL . 'assets/css/wpmu-dev-job-postings-styles.css' );
	}

	/**
	 * Shortcode callback to display job postings.
	 *
	 *
	 * @return string HTML output for the job postings.
	 */
	public function display_job_postings_shortcode(): string {
		ob_start(); // Start output buffering

		// Get job postings using the plugin class method
		$job_postings = $this->get_job_postings();

		// Load the template file for job postings display
		$this->template->load( 'shortcodes/job-postings-display.php', array( 'job_postings' => $job_postings ) );

		// Get the buffered content
		return ob_get_clean();
	}

	/**
	 * Shortcode callback to display job postings submit form.
	 *
	 *
	 * @return string HTML output for the job postings.
	 */
	public function submit_job_postings_shortcode(): string {
		ob_start(); // Start output buffering


		// Load the template file for job postings form
		$this->template->load( 'shortcodes/job-postings-form.php' );

		// Get the buffered content
		return ob_get_clean();
	}

	/**
	 * AJAX handler for job postings search.
	 */
	public function job_postings_search(): void {
		$search_value = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';

		// Get job postings using the plugin class method
		$job_postings = $this->get_job_postings( $search_value );

		ob_start(); // Start output buffering

		// Include the template file for job postings display
		include plugin_dir_path( __FILE__ ) . 'templates/shortcodes/job-postings-display.php';

		$output = ob_get_clean();

		echo $output;

		wp_die(); // This is required to terminate immediately and return a proper response
	}


	/**
	 * Get job postings from the database.
	 *
	 * @param string $search_term
	 *
	 * @return array|null|object
	 */
	public function get_job_postings( string $search_term = '' ): object|array|null {
		return $this->model->get_job_postings( $search_term );
	}

	/**
	 * Activation hook.
	 * Perform tasks upon plugin activation, such as table creation.
	 */
	public function activate() {
		$this->model->create_table();
	}

	/**
	 * Hook to add AJAX actions.
	 */
	public function add_ajax_actions() {
		add_action( 'wp_ajax_job_postings_search', array( $this, 'job_postings_search' ) );
		add_action( 'wp_ajax_nopriv_job_postings_search', array( $this, 'job_postings_search' ) );
	}
}
