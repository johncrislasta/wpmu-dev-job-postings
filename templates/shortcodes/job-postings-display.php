<!-- Search Form -->
<form id="job-postings-search-form">
	<label for="search">Search:</label>
	<input type="text" id="search" name="search">
	<input type="submit" value="Search">
</form>

<?php
// Output HTML for job postings
if ( $job_postings ) {
	echo '<div id="job-postings-container">';
	echo '<ul>';
	foreach ( $job_postings as $job ) {
		echo '<li>';
		echo '<p class="job-title">' . esc_html( $job['job_title'] ) . '</p>';
		echo '<p class="job-description">' . esc_html( $job['job_description'] ) . '</p>';
		echo '</li>';
	}
	echo '</ul>';
	echo '</div>';
} else {
	echo 'No job postings found.';
}
?>
