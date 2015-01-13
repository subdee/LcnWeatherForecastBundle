<?php
namespace Lcn\WeatherForecastBundle\Model;

class WeatherForecastForDay extends BaseWeatherForecast {

  /**
   * @var Location
   */
  private $location;

  /**
   * @var Array
   */
  private $hours = array();

  public function __construct(array $data) {
    parent::__construct($data);

    $this->location = new Location($data['latitude'], $data['longitude'], $data['timezone'], $data['offset']);

    foreach ($data['hourly']['data'] as $hourlyData) {
      $this->hours[] = new WeatherForecastForHour($hourlyData);
    }
  }

  public function getSummary() {
    $hourly = $this->get('hourly');

    return $hourly['summary'];
  }

  public function getIcon() {
    $hourly = $this->get('hourly');

    return $hourly['icon'];
  }

  public function getHours() {
    return $this->hours;
  }

  public function getHour($hour) {
    if (!is_numeric($hour) || intval($hour) < 0 || intval($hour) > 23) {
      throw new \Exception('Hour must be a number between 0 and 23, '.$hour.' given');
    }

    return $this->hours[$hour];
  }

  /**
   * @return Location
   */
  public function getLocation() {
    return $this->location;
  }

}