<?php

/**
 * @file
 * Top posts real time.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once 'autoload.php';
include_once 'setup.php';
include_once 'functions.php';

create_digest($analytics);

/**
 * Create digest.
 */
function create_digest(&$analytics) {
  try {
    // Get the user's first view (profile) ID.
    $profile_id = GOOGLE_ANALYTICS_PROFILE_ID;

    if (isset($profile_id)) {
      $ga_manager = new GoogleAnalyticsManager($analytics, $profile_id);
      $total_active_users = $ga_manager->currentActiveUsers();

      $page_paths = page_path($analytics, $profile_id);

      $page_paths = array_reverse($page_paths);

      $top_posts = '';
      $count = 0;

      for ($count = 0; $count <= 6; $count++) {
        if (strcmp($page_paths[$count][0], '/') !== 0) {
          $top_posts .= "\n<https://www.google.com/analytics/web/?hl=en#realtime/rt-content/" . GOOGLE_ANALYTICS_WEB_ID . "/%3Ffilter.list%3D10%3D%3D" . urlencode($page_paths[$count][0]) . "|" . $page_paths[$count][1] . ">\t<" . YOUR_DOMAIN . $page_paths[$count][0] . "|" . str_replace(".html", "", preg_replace('/\/\d+\/\d+\/\d+\//', '', $page_paths[$count][0])) . ">";
        }
      }

      $message = "Current users on the site: <https://www.google.com/analytics/web/?hl=en#realtime/rt-overview/" . GOOGLE_ANALYTICS_WEB_ID . "/|$total_active_users>\n\nTop posts:\n" . $top_posts;

      // Output the results.
      slack_message($message, SLACK_NOTIFICATION_CHANNEL);
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
 * Page path.
 *
 * @todo Move to GoogleAnalyticsManager class.
 */
function page_path(&$analytics, $profile_id) {
  $result = $analytics->data_realtime->get(
    'ga:' . $profile_id,
    'rt:activeUsers',
    array(
      'sort' => 'rt:activeUsers',
      'dimensions' => 'rt:pagePath',
    )
  );

  return $result->rows;
}
