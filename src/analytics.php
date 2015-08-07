<?php
/**
 * @file
 * Analytics.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'setup.php';

run_main_demo($analytics);

/**
 * Run main demo.
 */
function run_main_demo(&$analytics) {
  try {
    // Step 2. Get the user's first view (profile) ID.
    $profile_id = GOOGLE_ANALYTICS_PROFILE_ID;

    if (isset($profile_id)) {

      // Step 3. Query the Core Reporting API.
      $results = get_results($analytics, $profile_id);

      // Step 4. Output the results.
      print_results($results);
    }

  }
  catch (apiServiceException $e) {
    // Error from the API.
    print 'There was an API error : ' . $e->getCode() . ' : ' . $e->getMessage();

  }
  catch (Exception $e) {
    print 'There was a general error : ' . $e->getMessage();
  }
}

/**
 * Get results.
 */
function get_results(&$analytics, $profile_id) {
  return $analytics->data_ga->get(
    'ga:' . $profile_id,
    '2014-05-12',
    '2014-05-12',
    'ga:pageviews',
    array('filters' => 'ga:pagePath==/2014/05/09/color-for-the-colorblind.html')
  );
}

/**
 * Print results.
 */
function print_results(&$results) {
  if (count($results->getRows()) > 0) {
    $profile_name = $results->getProfileInfo()->getProfileName();
    $rows = $results->getRows();
    $sessions = $rows[0][0];

    print "<p>$profile_name</p>";
    print "<p>Total sessions: $sessions</p>";

  }
  else {
    print '<p>No results found.</p>';
  }
}
