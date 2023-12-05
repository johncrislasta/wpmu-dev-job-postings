<?php
namespace WPMU_Dev;

/**
 * Class Template
 * @package WPMU_Dev
 */
class Template {

	/**
	 * Path to the templates folder.
	 *
	 * @var string
	 */
	private string $template_dir;

	/**
	 * Template constructor.
	 */
	public function __construct() {
		$this->template_dir = WPMU_DEV_JOB_POSTINGS_DIR . 'templates/';
	}

	/**
	 * Load a template file.
	 *
	 * @param string $template_name Name of the template file.
	 * @param array  $data          Data to pass to the template.
	 */
	public function load( $template_name, $data = array() ): void {
		$template_path = $this->template_dir . $template_name;

		if ( file_exists( $template_path ) ) {
			extract( $data );
			include $template_path;
		} else {
			// Handle template not found
			echo 'Template not found: ' . esc_html( $template_name );
		}
	}
}
