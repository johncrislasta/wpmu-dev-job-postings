<?php
/*
Plugin Name: Job Postings Plugin
Description: A simple WordPress plugin with a custom table for storing job postings.
Version: 1.0.2
Author: John Cris Lasta
*/

// Activation hook to create the custom table
register_activation_hook( __FILE__, 'job_postings_plugin_activation' );
function job_postings_plugin_activation(): void {
	global $wpdb;
	$table_name = $wpdb->prefix . 'job_postings';

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		job_title varchar(255) NOT NULL,
		job_description text NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
}

// Shortcode to display a form for job postings
add_shortcode( 'job_posting_form', 'job_posting_form_shortcode' );
function job_posting_form_shortcode(): bool|string {
	ob_start();
	?>
	<form method="post">
		<label for="job_title">Job Title:</label>
		<input name="job_title" required type="text">

		<label for="job_description">Job Description:</label>
		<textarea name="job_description" required rows="4"></textarea>

		<input type="submit" value="Submit">
	</form>
	<?php
	if ( 'POST' === $_SERVER['REQUEST_METHOD'] &&
		isset( $_POST['job_title'], $_POST['job_description'] ) ) {

		global $wpdb;
		$table_name      = $wpdb->prefix . 'job_postings';
		$job_title       = sanitize_text_field( $_POST['job_title'] );
		$job_description = sanitize_textarea_field( $_POST['job_description'] );
		$wpdb->insert(
			$table_name,
			array(
				'job_title'       => $job_title,
				'job_description' => $job_description,
			)
		);
	}
	return ob_get_clean();
}

// Shortcode to display job postings with search functionality
add_shortcode( 'display_job_postings', 'display_job_postings_shortcode' );
function display_job_postings_shortcode(): bool|string {
	ob_start();
	?>
	<form method="get">
		<label for="search">Search:</label>
		<input type="text" name="search" value="<?php echo isset( $_GET['search'] ) ? esc_attr( $_GET['search'] ) : ''; ?>">
		<input type="submit" value="Search">
	</form>
	<?php
	$search_term  = isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';
	$where_clause = $search_term ? "WHERE job_title LIKE '%{$search_term}%' OR job_description LIKE '%{$search_term}%'" : '';
	global $wpdb;
	$table_name = $wpdb->prefix . 'job_postings';
	$results    = $wpdb->get_results( "SELECT * FROM $table_name $where_clause", ARRAY_A );
	if ( $results ) {
		echo '<ul>';
		foreach ( $results as $result ) {
			echo '<li>';
			echo '<strong>' . esc_html( $result['job_title'] ) . '</strong>';
			echo '<p>' . esc_html( $result['job_description'] ) . '</p>';
			echo '</li>';
		}
		echo '</ul>';
	} else {
		echo '<p>No job postings found.</p>';
	}
	return ob_get_clean();
}

// Include the JobPostingsAPI class for REST API functionality
require_once plugin_dir_path( __FILE__ ) . 'class-job-postings-api.php';
new Job_Postings_API();
