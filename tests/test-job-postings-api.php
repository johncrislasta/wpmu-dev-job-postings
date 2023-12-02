<?php

class Job_Postings_APITest extends WP_UnitTestCase {

	public function setUp(): void {
		parent::setUp();

		$this->class_instance = new Job_Postings_API();
	}

	/**
	 * @test
	 */
	public function test_insert_job_posting() {
		$api = new Job_Postings_API();

		// Mocking a POST request data
		$data = array(
			'job_title'       => 'Software Developer',
			'job_description' => 'A description of the software developer role.',
		);

		// Performing the insert
		$result = $api->insert_job_posting( $data );

		// Asserting that the insert was successful
		$this->assertTrue( $result['success'] );
	}

	/**
	 * @test
	 */
	public function test_select_job_postings() {
		$api = new Job_Postings_API();

		// Mocking a GET request data
		$data = array(
			'search' => 'Software Developer',
		);

		// Performing the select
		$results = $api->select_job_postings( $data );

		// Asserting that at least one result is returned
		$this->assertNotEmpty( $results );
	}
}
