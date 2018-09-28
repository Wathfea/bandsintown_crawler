<?php


namespace Bandsintown;

use Bandsintown\Exceptions\ApiException;
use Bandsintown\Response\Artist;
use Bandsintown\Response\Event;
use Bandsintown\Response\EventInfo;
use Bandsintown\Response\Offer;
use Bandsintown\Response\Venue;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;



/**
 * Class Client.
 *
 * @package Bandsintown
 */
class Client
{
    /* @var HttpClient */
    private $httpClient;
    /** @var string */
    private $app_id;

    /**
     * Client constructor.
     *
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->app_id = 'SECRET_KEY';
    }


    /**
     * @param string $artist
     *
     * @return Artist
     *
     * @throws ApiException
     */
    public function collectArtistInfo(string $artist): ?Artist
    {
        try {
            $endpoint = "https://rest.bandsintown.com/artists/{$artist}?app_id={$this->app_id}";

            $response = $this->httpClient->request('GET', $endpoint);
        } catch (GuzzleException $exception) {
            throw new ApiException($exception->getMessage(), $exception->getCode());
        }

        $data = json_decode($response->getBody()->getContents());

        if (empty($data)) {
            return null;
        }

        $artist = new Artist(
            $data->id,
            $data->name,
            $data->url,
            $data->image_url,
            $data->thumb_url,
            $data->facebook_page_url,
            $data->mbid,
            $data->tracker_count,
            $data->upcoming_event_count
        );

        return $artist;
    }


    /**
     * @param string $artist
     *
     * @return Event[]|array
     *
     * @throws ApiException
     */
    public function collectArtistEvents(string $artist): array
    {
        $events = [];

        try {
            $endpoint = "https://rest.bandsintown.com/artists/{$artist}/events?app_id={$this->app_id}";

            $response = $this->httpClient->request('GET', $endpoint);
        } catch (GuzzleException $exception) {
            throw new ApiException($exception->getMessage(), $exception->getCode());
        }

        $eventsData = json_decode($response->getBody()->getContents());

        foreach ($eventsData as $data) {
            $offers = [];

            $venue = new Venue(
                $data->venue->name,
                $data->venue->latitude,
                $data->venue->longitude,
                $data->venue->city,
                $data->venue->region,
                $data->venue->country
            );

            foreach ($data->offers as $offer) {
                $offers[] = new Offer(
                    $offer->type,
                    $offer->url,
                    $offer->status
                );
            }

            $events[] = new Event(
                $data->id,
                $data->artist_id,
                $data->url,
                new \DateTime($data->on_sale_datetime),
                new \DateTime($data->datetime),
                $data->description,
                $venue,
                $offers,
                $data->lineup
            );
        }

        return $events;
    }

    /**
     * @param string $artist
     *
     * @return EventInfo
     *
     * @throws ApiException
     */
    public function collectEventInfoArtist(string $artist): EventInfo
    {
        return new EventInfo(
            $this->collectArtistInfo($artist),
            $this->collectArtistEvents($artist)
        );
    }
}
