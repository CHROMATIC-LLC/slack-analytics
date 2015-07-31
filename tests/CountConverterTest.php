<?php

/**
 * @file
 * CountConverterTest.
 */

// @TODO: Why does auto-loading not work?
include_once '../src/CountConverter.php';

/**
 * CountConverterTest unit tests.
 */
class CountConverterTest extends PHPUnit_Framework_TestCase {
  /**
   * Test conversion of integer into thousands.
   */
  public function testConversion() {
    $count_converter = new CountConverter(1000, TRUE);
    $converted_count = $count_converter->convertCount();
    $this->assertEquals('1k', $converted_count);
  }

  /**
   * Test rounding down of integer converted into thousands.
   */
  public function testConversionRounding() {
    $count_converter = new CountConverter(1455, TRUE);
    $converted_count = $count_converter->convertCount();
    $this->assertEquals('1k', $converted_count);
  }

  /**
   * Test no conversion of count integer.
   */
  public function testNoConversion() {
    $count_converter = new CountConverter(1000, FALSE);
    $converted_count = $count_converter->convertCount();
    $this->assertEquals('1000', $converted_count);
  }

}
