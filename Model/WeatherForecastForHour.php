<?php
namespace Lcn\WeatherForecastBundle\Model;

class WeatherForecastForHour extends BaseWeatherForecast {

  public function getIcon() {
    return $this->get('icon');
  }

  public function getSummary() {
    return $this->get('summary');
  }

}