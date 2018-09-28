<?php


namespace Bandsintown\Response;

/**
 * Class Offer.
 *
 * @package Bandsintown\Response
 */
class Offer
{
    /** @var string type */
    protected $type;
    /** @var string url */
    protected $url;
    /** @var string status */
    protected $status;

    /**
     * Offer constructor.
     *
     * @param string $type
     * @param string $url
     * @param string $status
     */
    public function __construct(string $type, string $url, string $status)
    {
        $this->type = $type;
        $this->url = $url;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
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
    public function getStatus(): string
    {
        return $this->status;
    }


}