<?php
namespace Lcn\WeatherForecastBundle\Model;

class Location {

  /**
   * @var Float
   */
  private $latitude;

  /**
   * @var Float
   */
  private $longitude;

  /**
   * @var String
   */
  private $timezone;

  /**
   * @var Integer
   */
  private $offset;

  public function __construct($latitude, $longitude, $timezone, $offset) {
    $this->latitude = $latitude;
    $this->longitude = $longitude;
    $this->timezone = $timezone;
    $this->offset = $offset;
  }

}