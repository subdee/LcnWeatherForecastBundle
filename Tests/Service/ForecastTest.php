<?php

namespace Lcn\WeatherForecastBundle\Tests\Controller;

use Lcn\WeatherForecastBundle\Model\Day;
use Lcn\WeatherForecastBundle\Model\WeatherForecastForDay;
use Lcn\WeatherForecastBundle\Model\WeatherForecastForHour;
use Lcn\WeatherForecastBundle\Service\Forecast;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ForecastTest extends \PHPUnit_Framework_TestCase
{

    private $apiToken = '9f636811b51dded9de7e7ca811d325f7';
    private $lat = 53.553521;
    private $lng = 9.948773;

    private $timestamp = 1404165600;
    private $timestamp2 = 1421017200;

    /**
     * @var Forecast
     */
    private $forecast;

    public function setUp() {

        $forecastProvider = $this->getMockBuilder('\Lcn\WeatherForecastBundle\Provider\ForecastIoProvider')
          ->setConstructorArgs(array('test-token'))
          ->getMock();

        $todayTimestamp = $this->timestamp2;

        $forecastProvider->expects($this->any())->method('getForDay')->will($this->returnCallback(function($lat, $lng, $timestamp) use ($todayTimestamp) {
            if ($timestamp == strtotime('today')) {
                $timestamp = $todayTimestamp;
            }
            $filename = __DIR__ . '/../fixtures/' . intval($timestamp) . '.json';
            if (file_exists($filename)) {
                return json_decode(file_get_contents($filename), true);
            }
            else {
                throw new \Exception('Invalid timestamp given: '.$timestamp);
            }
        }));

        $this->forecast = new Forecast($forecastProvider);
    }

    public function testInvalidTimestamps()
    {
        try {
            $this->forecast->getForDay($this->lat, $this->lng, 'no-timestamp');
            $this->fail('Invalid timestamps should throw Exceptions');
        }
        catch(\Exception $e) {}

        try {
            $this->forecast->getForDay($this->lat, $this->lng, -1);
            $this->fail('Invalid timestamps should throw Exceptions');
        }
        catch(\Exception $e) {}

        try {
            $this->forecast->getForDay($this->lat, $this->lng, 1);
            $this->fail('Out of range timestamps should return null');
        }
        catch(\Exception $e) {}
    }

    public function testInvalidGeoCoordinates()
    {
        try {
            $this->forecast->getForDay('x', 'y', $this->timestamp);
            $this->fail('Invalid geo coordinates should throw Exceptions');
        }
        catch(\Exception $e) {}

        try {
            $this->forecast->getForDay(333.444, 111.222, $this->timestamp);
            $this->fail('Out of range geo coordinates should throw Exceptions');
        }
        catch(\Exception $e) {}
    }

    public function testGetForToday()
    {
        $forecastForDay = $this->forecast->getForToday($this->lat, $this->lng);
        $this->assertForecastForDay($forecastForDay);

        $this->assertEquals('Light rain overnight and in the afternoon and breezy starting in the morning.', $forecastForDay->getSummary());
        $this->assertEquals('rain', $forecastForDay->getIcon());
    }

    public function testGetForDay()
    {
        $forecastForDay = $this->forecast->getForDay($this->lat, $this->lng, $this->timestamp);
        $this->assertForecastForDay($forecastForDay);

        $this->assertEquals('Clear throughout the day.', $forecastForDay->getSummary());
        $this->assertEquals('clear-day', $forecastForDay->getIcon());

        //test string timestamp should work, too
        $forecastForDay = $this->forecast->getForDay($this->lat, $this->lng, (string)$this->timestamp2);
        $this->assertForecastForDay($forecastForDay);

        $this->assertEquals('Light rain overnight and in the afternoon and breezy starting in the morning.', $forecastForDay->getSummary());
        $this->assertEquals('rain', $forecastForDay->getIcon());
    }

    public function testGetForCurrentHour()
    {

        $expectedResults = array(
            0 => array(
              'summary' => 'Light Rain and Breezy',
              'icon' => 'rain',
            ),
            1 => array(
              'summary' => 'Light Rain and Breezy',
              'icon' => 'rain',
            ),
            2 => array(
              'summary' => 'Drizzle and Breezy',
              'icon' => 'rain',
            ),
            3 => array(
              'summary' => 'Mostly Cloudy',
              'icon' => 'partly-cloudy-night',
            ),
            4 => array(
              'summary' => 'Breezy and Overcast',
              'icon' => 'wind',
            ),
            5 => array(
              'summary' => 'Overcast',
              'icon' => 'cloudy',
            ),
            6 => array(
              'summary' => 'Mostly Cloudy',
              'icon' => 'partly-cloudy-night',
            ),
            7 => array(
              'summary' => 'Mostly Cloudy',
              'icon' => 'partly-cloudy-night',
            ),
            8 => array(
              'summary' => 'Mostly Cloudy',
              'icon' => 'partly-cloudy-night',
            ),
            9 => array(
              'summary' => 'Breezy and Overcast',
              'icon' => 'wind',
            ),
            10 => array(
              'summary' => 'Breezy and Overcast',
              'icon' => 'wind',
            ),
            11 => array(
              'summary' => 'Breezy and Overcast',
              'icon' => 'wind',
            ),
            12 => array(
              'summary' => 'Breezy and Overcast',
              'icon' => 'wind',
            ),
            13 => array(
              'summary' => 'Breezy and Overcast',
              'icon' => 'wind',
            ),
            14 => array(
              'summary' => 'Breezy and Overcast',
              'icon' => 'wind',
            ),
            15 => array(
              'summary' => 'Drizzle and Windy',
              'icon' => 'rain',
            ),
            16 => array(
              'summary' => 'Drizzle and Windy',
              'icon' => 'rain',
            ),
            17 => array(
              'summary' => 'Drizzle and Breezy',
              'icon' => 'rain',
            ),
            18 => array(
              'summary' => 'Drizzle and Breezy',
              'icon' => 'rain',
            ),
            19 => array(
              'summary' => 'Drizzle and Breezy',
              'icon' => 'rain',
            ),
            20 => array(
              'summary' => 'Drizzle and Breezy',
              'icon' => 'rain',
            ),
            21 => array(
              'summary' => 'Light Rain and Breezy',
              'icon' => 'rain',
            ),
            22 => array(
              'summary' => 'Light Rain and Breezy',
              'icon' => 'rain',
            ),
            23 => array(
              'summary' => 'Light Rain and Breezy',
              'icon' => 'rain',
            ),
        );

        $forecastForHour = $this->forecast->getForCurrentHour($this->lat, $this->lng);
        $this->assertForecastForHour($forecastForHour);

        $this->assertEquals($expectedResults[date('G')]['summary'], $forecastForHour->getSummary());
        $this->assertEquals($expectedResults[date('G')]['icon'], $forecastForHour->getIcon());
    }

    public function testGetForHour()
    {
        $forecastForHour = $this->forecast->getForHour($this->lat, $this->lng, $this->timestamp);
        $this->assertForecastForHour($forecastForHour);
        $this->assertEquals('Clear', $forecastForHour->getSummary());
        $this->assertEquals('clear-night', $forecastForHour->getIcon());

        $forecastForHour = $this->forecast->getForHour($this->lat, $this->lng, $this->timestamp + 3600 * 6);
        $this->assertForecastForHour($forecastForHour);
        $this->assertEquals('Clear', $forecastForHour->getSummary());
        $this->assertEquals('clear-day', $forecastForHour->getIcon());
    }

    private function assertForecastForDay($forecastForDay) {
        $this->assertTrue($forecastForDay instanceof WeatherForecastForDay);

        $this->assertTrue(is_string($forecastForDay->getIcon()));
        $this->assertTrue(is_string($forecastForDay->getSummary()));

        try {
            $forecastForDay->getHour(-1);
            $this->fail('Negative hours should throw Exceptions');
        }
        catch(\Exception $e) {}

        try {
            $forecastForDay->getHour(24);
            $this->fail('Too high hours should throw Exceptions');
        }
        catch(\Exception $e) {}

        for ($i = 0; $i < 24; $i++) {
            $this->assertForecastForHour($forecastForDay->getHour($i));
        }
    }

    private function assertForecastForHour($forecastForHour) {
        $this->assertTrue($forecastForHour instanceof WeatherForecastForHour);
        $this->assertTrue(is_string($forecastForHour->getIcon()));
        $this->assertTrue(is_string($forecastForHour->getSummary()));
        $this->assertTrue($forecastForHour->get('icon') !== null);
        $this->assertTrue($forecastForHour->get('unsupported-property') === null);
        $this->assertTrue($forecastForHour->get('unsupported-property', 'fallback') === 'fallback');
    }

}
