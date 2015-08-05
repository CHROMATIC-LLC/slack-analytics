<?php

/**
 * @file
 * Daily Digest.
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
    // Step 2. Get the user's first view (profile) ID.
    $profile_id = GOOGLE_ANALYTICS_PROFILE_ID;

    if (isset($profile_id)) {
      $yesterday = new DateTime();
      $yesterday->sub(new DateInterval('P1D'));

      $ga_manager = new GoogleAnalyticsManager($analytics, $profile_id);
      $yesterday_pageviews = $ga_manager->pageViewsForDates($yesterday->format('Y-m-d'), $yesterday->format('Y-m-d'));
      $yesterday_count_converter = new CountConverter($yesterday_pageviews, PAGEVIEWS_DISPLAY_IN_THOUSANDS);
      $yesterday_pageviews_output = $yesterday_count_converter->convertCount();

      $yesterday->sub(new DateInterval('P1D'));

      $lastweek = new DateTime();
      $lastweek->sub(new DateInterval('P8D'));

      $lastweek_pageviews = $ga_manager->pageViewsForDates($lastweek->format('Y-m-d'), $lastweek->format('Y-m-d'));
      $lastweek_count_converter = new CountConverter($lastweek_pageviews, PAGEVIEWS_DISPLAY_IN_THOUSANDS);
      $lastweek_pageviews_output = $lastweek_count_converter->convertCount();

      $message = "DAILY DIGEST: ";
      $message .= "Yesterday (" . $yesterday->format('l') . "), we did <https://www.google.com/analytics/web/?hl=en#report/visitors-overview/" . GOOGLE_ANALYTICS_WEB_ID . "/%3F_u.date00%3D" . $yesterday->format('Ymd') . "%26_u.date01%3D" . $yesterday->format('Ymd') . "%26overview-graphOptions.selected%3Danalytics.nthHour/|" . $yesterday_pageviews_output . " pageviews>.";
      $message .= " Last " . $lastweek->format('l') . ", we did <https://www.google.com/analytics/web/?hl=en#report/visitors-overview/" . GOOGLE_ANALYTICS_WEB_ID . "/%3F_u.date00%3D" . $lastweek->format('Ymd') . "%26_u.date01%3D" . $lastweek->format('Ymd') . "%26overview-graphOptions.selected%3Danalytics.nthHour/|" . $lastweek_pageviews_output . " pageviews>";

      if (!PAGEVIEWS_DISPLAY_IN_THOUSANDS) {
        $message .= ".";
      }
      else {
        $message .= " (" . ($yesterday_pageviews - $lastweek_pageviews > 1000 ? "+" : "") . floor(($yesterday_pageviews - $lastweek_pageviews) / 1000) . "k).";
      }

      // Step 4. Output the results.
      slack_message($message, SLACK_NOTIFICATION_CHANNEL);
    }

  }
  catch (apiServiceException $e) {
    // Error from the API.
    print 'There was an API error : ' . $e->getCode() . ' : ' . $e->getMessage();

  }
  catch (Exception $e) {
    print 'There wan a general error : ' . $e->getMessage();
  }
}
