<?php
namespace Lcn\WeatherForecastBundle\Service;

use Lcn\WeatherForecastBundle\Model\Location;
use Lcn\WeatherForecastBundle\Model\WeatherForecastForDay;
use Lcn\WeatherForecastBundle\Model\WeatherForecastForHour;
use Lcn\WeatherForecastBundle\Provider\ForecastProviderInterface;

class Forecast
{

  /**
   * @var ForecastProviderInterface
   */
  private $provider;

  /**
   * Class constructor
   *
   * @param String $apiToken
   */
  public function __construct(ForecastProviderInterface $provider)
  {
    $this->provider = $provider;
  }

  /**
   * @param float $latitude
   * @param float $longitude
   * @return WeatherForecastForDay
   */
  public function getForToday($latitude, $longitude) {
    return $this->getForDay($latitude, $longitude, strtotime('today'));
  }

  /**
   * @param float $latitude
   * @param float $longitude
   * @return WeatherForecastForDay
   */
  public function getForDay($latitude, $longitude, $timestamp) {
    if (!$this->isValidTimestamp($timestamp)) {
      throw new \Exception('Invalid timestamp: '.$timestamp);
    }

    if (!$this->isValidLatitude($latitude)) {
      throw new \Exception('Invalid latitude: '.$latitude);
    }

    if (!$this->isValidLongitude($longitude)) {
      throw new \Exception('Invalid longitude: '.$longitude);
    }

    $timestamp = strtotime('midnight', $timestamp);

    try {
      $apiData = $this->provider->getForDay($latitude, $longitude, $timestamp);

      return new WeatherForecastForDay($apiData);
    }
    catch (\Exception $e) {
      return null;
    }
  }

  /**
   * Returns the forecast for the current hour
   *
   * @param int    $latitude
   * @param int    $longitude
   * @return WeatherForecastForHour
   */
  public function getForCurrentHour($latitude, $longitude)
  {
    return $this->getForHour($latitude, $longitude, strtotime('now'));

  }

  /**
   * Returns the forecast at a given time
   *
   * @param int    $latitude
   * @param int    $longitude
   * @param Integer $timestamp
   * @return WeatherForecastForHour
   */
  public function getForHour($latitude, $longitude, $timestamp)
  {
    $forecastForDay = $this->getForDay($latitude, $longitude, $timestamp);

    $hour = intval(date('G', $timestamp));

    return $forecastForDay->getHour($hour);
  }

  private function isValidTimestamp($timestamp) {
    return is_numeric($timestamp) && intval($timestamp) > 0;
  }

  private function isValidLatitude($latitude) {
    return preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/', $latitude);
  }

  private function isValidLongitude($longitude) {
    return preg_match('/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', $longitude);
  }
}