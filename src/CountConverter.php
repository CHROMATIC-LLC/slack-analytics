<?php
/**
 * @file
 * CountConverter.
 */

/**
 * Converts view counts.
 */
class CountConverter {
  /**
   * The integer view count.
   */
  private $count;

  /**
   * TRUE if the conversion should be made to thousands, else FALSE.
   */
  private $displayInThousands;

  /**
   * Constructor.
   *
   * @param int $count
   *   The view count.
   * @param bool $display_in_thousands
   *   TRUE if the view count should be displayed in increments of thousands,
   *   else FALSE.
   */
  public function __construct($count, $display_in_thousands) {
    $this->displayInThousands = $display_in_thousands;
    $this->count = $count;
  }

  /**
   * Convert count number.
   *
   * @return int
   *   The converted count integer.
   */
  public function convertCount() {
    return $this->displayInThousands ? floor($this->count / 1000) . 'k' : $this->count;
  }

}
