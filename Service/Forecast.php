<?php
namespace Lcn\WeatherForecastBundle\Service;

use Doctrine\Common\Cache\Cache;
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
   * @var Cache
   */
  private $cache;

  /**
   * Class constructor
   *
   * @param String $apiToken
   */
  public function __construct(ForecastProviderInterface $provider, Cache $cache)
  {
    $this->provider = $provider;
    $this->cache = $cache;
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

    $latitude = $this->normalizeGeoCoordinate($latitude);
    $longitude = $this->normalizeGeoCoordinate($longitude);

    $timestamp = $this->normalizeTimestamp($timestamp);

    $cacheKey = md5($latitude.$longitude.$timestamp);

    if (false === ($apiData = $this->cache->fetch($cacheKey))) {
      try {
        $apiData = $this->provider->getForDay($latitude, $longitude, $timestamp);
        $this->cache->save($cacheKey, $apiData, 3600*6); //TTL 6h
      }
      catch (\Exception $e) {
        return null;
      }
    }

    return new WeatherForecastForDay($apiData);
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

    $forecastTz = $forecastForDay->getLocation()->getTimezone();

    $hour = \DateTime::createFromFormat('U', $timestamp)->setTimezone(new \DateTimeZone($forecastTz))->format('G');

    return $forecastForDay->getHour($hour);
  }

  private function normalizeGeoCoordinate($value) {
    return round($value, 2);
  }

  private function normalizeTimestamp($value) {
    return strtotime('midnight', $value);
  }

  private function isValidTimestamp($timestamp) {
    return is_numeric($timestamp) && intval($timestamp) > 0;
  }

  private function isValidLatitude($latitude) {
    return floatval($latitude) >= -90 && floatval($latitude) <= 90;
  }

  private function isValidLongitude($longitude) {
    return floatval($longitude) >= -180 && floatval($longitude) <= 180;
  }
}
