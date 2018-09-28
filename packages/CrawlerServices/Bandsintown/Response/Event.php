<?php


namespace Bandsintown\Response;

/**
 * Class Event.
 *
 * @package Bandsintown\Response
 */
class Event
{
    /** @var string $id */
    protected $id;
    /** @var  string $artistId */
    protected $artistId;
    /** @var string $url */
    protected $url;
    /** @var string $onSaleDatetime */
    protected $onSaleDatetime;
    /** @var string $datetime */
    protected $datetime;
    /** @var string $description */
    protected $description;
    /** @var Venue $venue */
    protected $venue;
    /** @var array $offers */
    protected $offers;
    /** @var array $lineup */
    protected $lineup;

    /**
     * Event constructor.
     *
     * @param string    $id
     * @param string    $artistId
     * @param string    $url
     * @param \DateTime $onSaleDatetime
     * @param \DateTime $datetime
     * @param string    $description
     * @param Venue     $venue
     * @param array     $offers
     * @param array     $lineup
     */
    public function __construct(string $id, string $artistId, string $url, \DateTime $onSaleDatetime, \DateTime $datetime, string $description, Venue $venue, array $offers, array $lineup)
    {
        $this->id = $id;
        $this->artistId = $artistId;
        $this->url = $url;
        $this->onSaleDatetime = $onSaleDatetime;
        $this->datetime = $datetime;
        $this->description = $description;
        $this->venue = $venue;
        $this->offers = $offers;
        $this->lineup = $lineup;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getArtistId(): string
    {
        return $this->artistId;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return \DateTime
     */
    public function getOnSaleDatetime(): \DateTime
    {
        return $this->onSaleDatetime;
    }

    /**
     * @return \DateTime
     */
    public function getDatetime(): \DateTime
    {
        return $this->datetime;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return Venue
     */
    public function getVenue(): Venue
    {
        return $this->venue;
    }

    /**
     * @return array
     */
    public function getOffers(): array
    {
        return $this->offers;
    }

    /**
     * @return array
     */
    public function getLineup(): array
    {
        return $this->lineup;
    }


}