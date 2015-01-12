<?php
namespace Lcn\WeatherForecastBundle\Model;

class BaseWeatherForecast {

  private $data;

  public function __construct(array $data) {
    $this->data = $data;
  }

  public function get($key, $default = null) {
    if (array_key_exists($key, $this->data)) {
      return $this->data[$key];
    }

    return $default;
  }

}