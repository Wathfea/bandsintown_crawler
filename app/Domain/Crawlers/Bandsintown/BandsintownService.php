<?php

namespace App\Domain\Crawlers\Bandsintown;

use App\Domain\Crawlers\Bandsintown\Factory\BandsintownArtistFactory;
use App\Domain\Crawlers\Bandsintown\Factory\BandsintownEventFactory;
use App\Domain\Crawlers\Bandsintown\Factory\TickethingEventCategoryFactory;
use App\Domain\Crawlers\Bandsintown\Factory\TickethingEventFactory;
use App\Models\BandsintownArtist;
use App\Models\BandsintownEvent;
use App\Models\City;
use App\Models\Country;
use App\Models\Event;
use App\Services\Language\LocaleService;
use Bandsintown\Client;
use Bandsintown\Exceptions\ApiException;
use Bandsintown\Response\Artist;
use Bandsintown\Response\Offer;
use Illuminate\Support\Collection;

/**
 * Class BandsintownService.
 *
 * @package App\Domain\Crawlers\Bandsintown
 */
class BandsintownService
{
    /** @var Client */
    private $client;
    /** @var LocaleService */
    private $localeService;
    /** @var BandsintownArtistFactory */
    private $bandsintownArtistFactory;
    /** @var BandsintownEventFactory */
    private $bandsintownEventFactory;
    /** @var TickethingEventFactory */
    private $tickethingEventFactory;
    /** @var TickethingEventCategoryFactory */
    private $tickethingEventCategoryFactory;

    /**
     * BandsintownService constructor.
     *
     * @param Client                         $client
     * @param LocaleService                  $localeService
     * @param BandsintownArtistFactory       $bandsintownArtistFactory
     * @param BandsintownEventFactory        $bandsintownEventFactory
     * @param TickethingEventFactory         $tickethingEventFactory
     * @param TickethingEventCategoryFactory $tickethingEventCategoryFactory
     */
    public function __construct(
        Client $client,
        LocaleService $localeService,
        BandsintownArtistFactory $bandsintownArtistFactory,
        BandsintownEventFactory $bandsintownEventFactory,
        TickethingEventFactory $tickethingEventFactory,
        TickethingEventCategoryFactory $tickethingEventCategoryFactory
    )
    {
        $this->client = $client;
        $this->localeService = $localeService;
        $this->bandsintownArtistFactory = $bandsintownArtistFactory;
        $this->bandsintownEventFactory = $bandsintownEventFactory;
        $this->tickethingEventFactory = $tickethingEventFactory;
        $this->tickethingEventCategoryFactory = $tickethingEventCategoryFactory;
    }

    /**
     * @param $bandsInTownArtist
     *
     * @throws ApiException
     */
    public function crawl($bandsInTownArtist)
    {
        $eventInfoResponse = $this->client->collectEventInfoArtist($bandsInTownArtist);
        $artistResponse = $eventInfoResponse->getArtist();
        $eventsResponse = $eventInfoResponse->getEvents();
        $countries = Country::all();
        $cities = City::all();

        // Create or update BandsintownArtist
        $bandsInTownArtist = BandsintownArtist::where('name', $artistResponse->getName())->first();

        if (!$bandsInTownArtist) {
            $bandsInTownArtist = $this->createBandsInTownArtist($artistResponse);
        } else {
            $bandsInTownArtist = $this->updateBandsInTownArtist($bandsInTownArtist, $artistResponse);
        }

        // Create or update BandsintownEvents
        $bandsintownEvents = collect($eventsResponse)->map(function (\Bandsintown\Response\Event $eventResponse) {
            $bandsInTownEvent = BandsintownEvent::where('id', $eventResponse->getId())->first();

            if (!$bandsInTownEvent) {
                $bandsintownEvent = $this->createBandsInTownEvent($eventResponse);
            } else {
                $bandsintownEvent = $this->updateBandsInTownEvent($bandsInTownEvent, $eventResponse);
            }

            return $bandsintownEvent;
        });

        // Create or update Tickething Events
        $coverImage = $this->getCoverImage($bandsInTownArtist);

        $bandsintownEvents->map(function (BandsintownEvent $bandsintownEvent) use ($countries, $cities, $coverImage) {
            $venue = json_decode($bandsintownEvent->venue);
            $lineup = json_decode($bandsintownEvent->lineup);

            $eventName = $lineup . ' - ' . $venue->name;
            $eventPlace = $venue->name;
            $eventStart = $bandsintownEvent->datetime;

            $country = $this->getOrCreateCountry($countries, $venue->country);
            $city = $this->getOrCreateCity($cities, $country->id, $venue->city);

            $tickethingEvent = Event::where('city_id', $city->id)
                ->where('place', $eventPlace)
                ->where('start', $eventStart)
                ->where('name->en', $eventName)
                ->first();

            if (!$tickethingEvent) {
                $tickethingEvent = $this->createTickethingEvent($bandsintownEvent, $country, $city, $coverImage);
            } else {
                $tickethingEvent = $this->updateTickethingEvent($tickethingEvent);
            }

            // @TODO review this later
            // Attach bands in town event if event freshly created
            $bandsintownEvent->event_id = $tickethingEvent->id;
            $bandsintownEvent->save();

            return $tickethingEvent;
        });
    }

    /**
     * @TODO delegate to repository
     *
     * @param Artist $artistResponse
     *
     * @return BandsintownArtist
     */
    protected function createBandsInTownArtist(Artist $artistResponse): BandsintownArtist
    {
        $bandsInTownArtist = $this->bandsintownArtistFactory->createFromArtistResponse($artistResponse);
        $bandsInTownArtist->save();

        return $bandsInTownArtist;
    }

    /**
     * @TODO delegate to repository
     *
     * @param BandsintownArtist $bandsintownArtist
     * @param Artist            $artist
     *
     * @return BandsintownArtist
     */
    protected function updateBandsInTownArtist(BandsintownArtist $bandsintownArtist, Artist $artist): BandsintownArtist
    {
        $bandsintownArtist->id = $artist->getId();
        $bandsintownArtist->name = $artist->getName();
        $bandsintownArtist->url = $artist->getUrl();
        $bandsintownArtist->image_url = $artist->getImageUrl();
        $bandsintownArtist->thumb_url = $artist->getThumbUrl();
        $bandsintownArtist->facebook_page_url = $artist->getFacebookPageUrl();
        $bandsintownArtist->mbid = $artist->getMbid();
        $bandsintownArtist->tracker_count = $artist->getTrackerCount();
        $bandsintownArtist->upcoming_event_count = $artist->getUpcomingEventCount();

        $bandsintownArtist->save();

        return $bandsintownArtist;
    }

    /**
     * @TODO delegate to repository
     *
     * @param \Bandsintown\Response\Event $eventResponse
     *
     * @return BandsintownEvent
     */
    protected function createBandsInTownEvent(\Bandsintown\Response\Event $eventResponse): BandsintownEvent
    {
        $bandsintownEvent = $this->bandsintownEventFactory->createFromEventResponse($eventResponse);
        $bandsintownEvent->save();

        return $bandsintownEvent;
    }

    /**
     * @TODO delegate to repository
     *
     * @param BandsintownEvent            $bandsInTownEvent
     * @param \Bandsintown\Response\Event $event
     *
     * @return BandsintownEvent
     */
    protected function updateBandsInTownEvent(BandsintownEvent $bandsInTownEvent, \Bandsintown\Response\Event $event): BandsintownEvent
    {
        $offersArray = [];

        $venueArray = [
            'name' => $event->getVenue()->getName(),
            'latitude' => $event->getVenue()->getLatitude(),
            'longitude' => $event->getVenue()->getLongitude(),
            'city' => $event->getVenue()->getCity(),
            'region' => $event->getVenue()->getRegion(),
            'country' => $event->getVenue()->getCountry(),
        ];

        /** @var Offer $offer */
        foreach ($event->getOffers() as $offer) {
            $offersArray = [
                'type' => $offer->getType(),
                'url' => $offer->getUrl(),
                'status' => $offer->getStatus(),
            ];
        }

        $bandsInTownEvent->id = $event->getId();
        $bandsInTownEvent->artist_id = $event->getArtistId();
        $bandsInTownEvent->url = $event->getUrl();
        $bandsInTownEvent->on_sale_datetime = $event->getOnSaleDatetime();
        $bandsInTownEvent->datetime = $event->getDatetime();
        $bandsInTownEvent->description = $event->getDescription();
        $bandsInTownEvent->venue = json_encode($venueArray);
        $bandsInTownEvent->offers = json_encode($offersArray);
        $bandsInTownEvent->lineup = json_encode($event->getLineup()[0]);

        $bandsInTownEvent->save();

        return $bandsInTownEvent;
    }

    /**
     * @TODO delegate to repository
     *
     * @param BandsintownEvent $bandsintownEvent
     * @param Country          $country
     * @param City             $city
     * @param string           $coverImage
     *
     * @return Event
     */
    protected function createTickethingEvent(BandsintownEvent $bandsintownEvent, Country $country, City $city, string $coverImage): Event
    {
        $tickethingEvent = $this->tickethingEventFactory->createFromBandsintownEvent($bandsintownEvent, $country, $city, $coverImage);
        $tickethingEvent->save();

        $tickethingEventDefaultCategory = $this->tickethingEventCategoryFactory->createFromTickethingEvent($tickethingEvent);
        $tickethingEventDefaultCategory->save();

        return $tickethingEvent;
    }

    /**
     * @TODO delegate to repository
     *
     * @param Event $tickethingEvent
     *
     * @return Event
     */
    protected function updateTickethingEvent(Event $tickethingEvent): Event
    {
        return $tickethingEvent;
    }

    /**
     * @param $artist
     *
     * @return string
     */
    protected function getCoverImage(BandsintownArtist $artist): string
    {
        $coverImg = 'https://tickething.com/img/facebook-share.png';

        $isImg = @getimagesize($artist->image_url);

        if (is_array($isImg) && $artist->image_url != 'https://s3.amazonaws.com/bit-photos/artistLarge.jpg') {
            $coverImg = $artist->image_url;
        }

        return $coverImg;
    }

    /**
     * @TODO delegate to repository
     *
     * @param Collection $countries
     * @param string     $bandsintownEventCountryName
     *
     * @return Country
     */
    protected function getOrCreateCountry(Collection $countries, string $bandsintownEventCountryName): Country
    {
        /** Country filter and creation */
        $country = $countries->filter(function (Country $country) use ($bandsintownEventCountryName) {
            return $country->name == $bandsintownEventCountryName;
        })->first();

        if (!$country) {
            $country = new Country();
            $country->name = ['en' => $bandsintownEventCountryName];
            $country->save();
        }

        return $country;
    }

    /**
     * @TODO delegate to repository
     *
     * @param Collection $cities
     * @param int        $bandsintownEventCountryId
     * @param string     $bandsintownEventCity
     *
     * @return City
     */
    protected function getOrCreateCity(Collection $cities, int $bandsintownEventCountryId, string $bandsintownEventCity): City
    {
        $city = $cities->filter(function (City $city) use ($bandsintownEventCity) {
            return $city->name == $bandsintownEventCity;
        })->first();

        if (!$city) {
            $city = new City();
            $city->country_id = $bandsintownEventCountryId;
            $city->name = ['en' => $bandsintownEventCity];
            $city->save();
        }

        return $city;
    }
}
