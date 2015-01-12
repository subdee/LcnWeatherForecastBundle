<?php

namespace Lcn\WeatherForecastBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DemoController extends Controller
{
    public function indexAction()
    {
        $forecast = $this->container->get('lcn.weather_forecast');

        $lat = 53.553521;
        $lng = 9.948773;


        $forecastForToday = $forecast->getForToday($lat, $lng);
        $forecastForCurrentHour = $forecast->getForCurrentHour($lat, $lng);

        return $this->render('LcnWeatherForecastBundle:Demo:index.html.twig', array(
            'forecastForToday' => $forecastForToday,
            'forecastForCurrentHour' => $forecastForCurrentHour,
        ));
    }
}
