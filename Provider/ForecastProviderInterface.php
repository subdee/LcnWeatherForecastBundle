<?php
namespace Lcn\WeatherForecastBundle\Provider;

interface ForecastProviderInterface
{

  /**
   * @param float $latitude
   * @param float $longitude
   * @return array
   */
  public function getForDay($latitude, $longitude, $timestamp);

}