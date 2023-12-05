jQuery(document).ready(function($) {
    // AJAX for job postings search
    const $searchForm = $('#job-postings-search-form');
    const $jobsContainer = $('#job-postings-container');

    if ($searchForm.length) {
        $searchForm.submit(function(event) {
            event.preventDefault(); // Prevent the form from submitting traditionally

            const searchValue = $('#search').val();

            updateJobPostingsContainer( searchValue );

        });
    }

    function updateJobPostingsContainer( searchValue ) {
        $jobsContainer.addClass('loading');

        $.ajax({
            type: 'GET',
            url: '/wp-json/job-postings/v1/select',
            data: {
                search: searchValue
            },
            success: function(response) {
                let jobPostingsHTML = "No job postings found!";
                if( response.length > 0 ) {
                    jobPostingsHTML = "<ul>";

                    for( let jobs of response ){
                        jobPostingsHTML += `<li><p class="job-title">${jobs.job_title}</p><p class="job-description">${jobs.job_description}</p></li>`;
                    }
                    jobPostingsHTML += "</ul>";
                }

                $jobsContainer.html(jobPostingsHTML);
                $jobsContainer.removeClass('loading');

            },
            error: function(xhr, status, error) {
                console.log('Request failed with status:', xhr.status, error);
            }
        });
    }

    // AJAX for updating job listings

    const $submitForm = $('#job-postings-form');
    $submitForm.submit(function(event) {
        event.preventDefault();

        const jobTitle = $('#job-title').val();
        const jobDescription = $('#job-description').val();

        $.ajax({
            type: 'POST',
            url: '/wp-json/job-postings/v1/insert',
            data: {
                job_title: jobTitle,
                job_description: jobDescription
            },
            beforeSend: function(xhr) {
                xhr.setRequestHeader('Authorization', 'Basic ' + btoa('admin:ZpXP bZTC H5kU 2WV6 lacv HlWZ')); // @TODO: Refactor to hide this through a server request
            },
            success: function(response) {
                // Check if the insertion was successful
                if (response.success) {
                    // Update job listings after successful insertion
                    updateJobPostingsContainer( '' );
                } else {
                    console.error('Insertion failed:', response.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('Request failed with status:', xhr.status, error);
            }
        });
    });
});