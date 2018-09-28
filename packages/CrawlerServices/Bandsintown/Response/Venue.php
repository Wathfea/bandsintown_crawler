<?php


namespace Bandsintown\Response;


/**
 * Class Venue.
 *
 * @package Bandsintown\Response
 */
class Venue
{
    /** @var string name */
    private $name;
    /** @var string $latitude */
    private $latitude;
    /** @var string $longitude */
    private $longitude;
    /** @var string $city */
    private $city;
    /** @var string $region */
    private $region;
    /** @var string $country */
    private $country;

    /**
     * Venue constructor.
     *
     * @param string $name
     * @param string $latitude
     * @param string $longitude
     * @param string $city
     * @param string $region
     * @param string $country
     */
    public function __construct(string $name, string $latitude, string $longitude, string $city, string $region, string $country)
    {
        $this->name = $name;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->city = $city;
        $this->region = $region;
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLatitude(): string
    {
        return $this->latitude;
    }

    /**
     * @return string
     */
    public function getLongitude(): string
    {
        return $this->longitude;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }
}
