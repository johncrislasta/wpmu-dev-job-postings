<?php
/**
 * Plugin Name: WPMU DEV Job Postings
 * Description: A plugin for managing job postings on your WordPress site.
 * Version: 1.1.0
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * Author: John Cris Lasta
 * Author URI:
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:
 * Text Domain: wpmu-dev-job-postings
 * Domain Path:
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'WPMU_DEV_JOB_POSTINGS_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPMU_DEV_JOB_POSTINGS_URL', plugin_dir_url( __FILE__ ) );

// Include the loader script
require_once plugin_dir_path( __FILE__ ) . 'includes/loader.php';

// Instantiate the main class
$wpmu_dev_job_postings = new \WPMU_Dev\Job_Postings();

// Instantiate the api class
$wpmu_dev_job_postings_api = new \WPMU_Dev\Job_Postings_API();

// Run the plugin
$wpmu_dev_job_postings->run();
