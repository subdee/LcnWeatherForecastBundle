<?php
namespace Lcn\WeatherForecastBundle\Provider;

class ForecastIoProvider implements ForecastProviderInterface
{

  /**
   * @var String   */
  private $apiToken;

  /**
   * Class constructor
   *
   * @param String $apiToken
   */
  public function __construct($apiToken)
  {

    if (0 === strpos($apiToken, 'define your token')) {
      throw new \Exception('Invalid Forecast.io token: '.$apiToken);
    }

    $this->apiToken = $apiToken;
  }

  /**
   * @param float $latitude
   * @param float $longitude
   * @return array
   */
  public function getForDay($latitude, $longitude, $timestamp) {
    $url = sprintf(
      'https://api.forecast.io/forecast/{api-token}/%s,%s,%s?exclude=minutely,flags,alerts',
      $latitude,
      $longitude,
      $timestamp
    );

    $url = str_replace('{api-token}', $this->apiToken, $url);

    $rawResult = file_get_contents($url);
    if (!$rawResult) {
      throw new \Exception('Could not retrieve Forecast.io weather api data');
    }

    return json_decode($rawResult, true);
  }

}