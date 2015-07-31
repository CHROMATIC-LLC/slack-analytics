<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once('setup.php');
include_once('functions.php');

createDigest($analytics);



function createDigest(&$analytics) {
  try {

    // Step 2. Get the user's first view (profile) ID.
    $profileId = GOOGLE_ANALYTICS_PROFILE_ID;

    if (isset($profileId)) {

      $yesterday = new DateTime();
      $yesterday->sub(new DateInterval('P1D'));

      $yesterday_pageviews = pageviewsForDates($analytics, $profileId, $yesterday->format('Y-m-d'), $yesterday->format('Y-m-d'));
      $yesterday_pageviews_output = PAGEVIEWS_DISPLAY_IN_THOUSANDS ? floor($yesterday_pageviews / 1000) . "k" : $yesterday_pageviews;

      $yesterday->sub(new DateInterval('P1D'));

      $lastweek = new DateTime();
      $lastweek->sub(new DateInterval('P8D'));

      $lastweek_pageviews = pageviewsForDates($analytics, $profileId, $lastweek->format('Y-m-d'), $lastweek->format('Y-m-d'));
      $lastweek_pageviews_output = PAGEVIEWS_DISPLAY_IN_THOUSANDS ? floor($lastweek_pageviews / 1000) . "k" : $lastweek_pageviews;

      $message = "DAILY DIGEST: ";
      $message .= "Yesterday (" . $yesterday->format('l') . "), we did <https://www.google.com/analytics/web/?hl=en#report/visitors-overview/" . GOOGLE_ANALYTICS_WEB_ID . "/%3F_u.date00%3D" . $yesterday->format('Ymd') . "%26_u.date01%3D" . $yesterday->format('Ymd') . "%26overview-graphOptions.selected%3Danalytics.nthHour/|" . $yesterday_pageviews_output . " pageviews>."
      $message .= " Last " . $lastweek->format('l') . ", we did <https://www.google.com/analytics/web/?hl=en#report/visitors-overview/" . GOOGLE_ANALYTICS_WEB_ID . "/%3F_u.date00%3D" . $lastweek->format('Ymd') . "%26_u.date01%3D" . $lastweek->format('Ymd') . "%26overview-graphOptions.selected%3Danalytics.nthHour/|" . $lastweek_pageviews_output . " pageviews>";

      if (PAGEVIEWS_DISPLAY_IN_THOUSANDS) {
        $message .= " (" . ($yesterday_pageviews - $lastweek_pageviews > 1000 ? "+" : "" ) . floor(($yesterday_pageviews - $lastweek_pageviews) / 1000) . "k).";
      }

      // Step 4. Output the results.
      slackMessage($message, SLACK_NOTIFICATION_CHANNEL);
    }

  } catch (apiServiceException $e) {
    // Error from the API.
    print 'There was an API error : ' . $e->getCode() . ' : ' . $e->getMessage();

  } catch (Exception $e) {
    print 'There wan a general error : ' . $e->getMessage();
  }
}

function pageviewsForDates(&$analytics, $profileId, $start_date, $end_date) {
   $result = $analytics->data_ga->get(
     'ga:' . $profileId,
     $start_date,
     $end_date,
     'ga:pageviews'
     );

   return $result->rows[0][0];
}


?>
