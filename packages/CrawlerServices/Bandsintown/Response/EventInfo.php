<?php


namespace Bandsintown\Response;

/**
 * Class EventInfo.
 *
 * @package Bandsintown\Response
 */
class EventInfo
{
    /** @var Artist $artist */
    protected $artist;
    /** @var array $events */
    protected $events;

    /**
     * EventInfo constructor.
     *
     * @param Artist $artist
     * @param array  $events
     */
    public function __construct(Artist $artist, array $events)
    {
        $this->artist = $artist;
        $this->events = $events;
    }

    /**
     * @return Artist
     */
    public function getArtist(): Artist
    {
        return $this->artist;
    }

    /**
     * @return array
     */
    public function getEvents(): array
    {
        return $this->events;
    }
}
