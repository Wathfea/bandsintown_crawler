<?php


namespace Bandsintown\Response;

/**
 * Class Artist.
 *
 * @package Bandsintown\Response
 */
class Artist
{
    /** @var integer $id */
    protected $id;
    /** @var string $name */
    protected $name;
    /** @var string $url */
    protected $url;
    /** @var string $imageUrl */
    protected $imageUrl;
    /** @var string $thumbUrl */
    protected $thumbUrl;
    /** @var string $facebookPageUrl */
    protected $facebookPageUrl;
    /** @var string $mbid */
    protected $mbid;
    /** @var integer $trackerCount */
    protected $trackerCount;
    /** @var integer $upcomingEventCount */
    protected $upcomingEventCount;

    /**
     * Artist constructor.
     *
     * @param int    $id
     * @param string $name
     * @param string $url
     * @param string $imageUrl
     * @param string $thumbUrl
     * @param string $facebookPageUrl
     * @param string $mbid
     * @param int    $trackerCount
     * @param int    $upcomingEventCount
     */
    public function __construct(int $id, string $name, string $url, string $imageUrl, string $thumbUrl, string $facebookPageUrl, string $mbid, int $trackerCount, int $upcomingEventCount)
    {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
        $this->imageUrl = $imageUrl;
        $this->thumbUrl = $thumbUrl;
        $this->facebookPageUrl = $facebookPageUrl;
        $this->mbid = $mbid;
        $this->trackerCount = $trackerCount;
        $this->upcomingEventCount = $upcomingEventCount;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * @return string
     */
    public function getThumbUrl(): string
    {
        return $this->thumbUrl;
    }

    /**
     * @return string
     */
    public function getFacebookPageUrl(): string
    {
        return $this->facebookPageUrl;
    }

    /**
     * @return string
     */
    public function getMbid(): string
    {
        return $this->mbid;
    }

    /**
     * @return int
     */
    public function getTrackerCount(): int
    {
        return $this->trackerCount;
    }

    /**
     * @return int
     */
    public function getUpcomingEventCount(): int
    {
        return $this->upcomingEventCount;
    }
}
