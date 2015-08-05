<?php
/**
 * @file
 * GoogleAnalyticsManager.
 */

/**
 * Google Analytics manager.
 */
class GoogleAnalyticsManager {
  /**
   * A configured Google_Service_Analytics object.
   *
   * @var Google_Service_Analytics
   */
  private $analytics;

  /**
   * A Google Analytics profile ID.
   *
   * @var string
   */
  private $profileId;

  /**
   * Constructor.
   *
   * @param Google_Service_Analytics $analytics
   *   A configured Google_Service_Analytics object.
   * @param string $profile_id
   *   A Google Analytics profile ID.
   */
  public function __construct(Google_Service_Analytics $analytics, $profile_id) {
    if (empty($profile_id) || !is_string($profile_id)) {
      throw new GoogleAnalyticsManagerProfileIdNotValidException('Google Analytics profile ID invalid or not present.');
    }
    $this->analytics = $analytics;
    $this->profileId = $profile_id;
  }

  /**
   * Retrieve page views for dates.
   *
   * @param string $start_date
   *   A DateInterval formatted start date.
   * @param string $end_date
   *   A DateInterval formatted end date.
   *
   * @return string
   *   The number of page views for the specified date range.
   */
  public function pageViewsForDates($start_date, $end_date) {
    $result = $this->analytics->data_ga->get(
      'ga:' . $this->profileId,
      $start_date,
      $end_date,
      'ga:pageviews'
    );

    return $result->rows[0][0];
  }

}

/**
 * GA profile ID is not present or not valid exception.
 */
class GoogleAnalyticsManagerProfileIdNotValidException extends Exception {}
