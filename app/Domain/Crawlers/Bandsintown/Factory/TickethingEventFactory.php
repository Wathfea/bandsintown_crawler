<?php

namespace App\Domain\Crawlers\Bandsintown\Factory;


use App\Helpers\EventSlugGenerator;
use App\Models\BandsintownEvent;
use App\Models\City;
use App\Models\Country;
use App\Models\Event;
use App\Services\Language\LocaleService;

class TickethingEventFactory
{
    /** @var EventSlugGenerator */
    private $eventSlugGenerator;
    /** @var LocaleService */
    private $localeService;


    /**
     * TickethingEventFactory constructor.
     *
     * @param EventSlugGenerator $eventSlugGenerator
     * @param LocaleService      $localeService
     */
    public function __construct(
        EventSlugGenerator $eventSlugGenerator,
        LocaleService $localeService
    )
    {
        $this->eventSlugGenerator = $eventSlugGenerator;
        $this->localeService = $localeService;
    }

    /**
     * @TODO delegate to repository insted aof factory usage
     *
     * @param BandsintownEvent $bandsintownEvent
     * @param Country          $country
     * @param City             $city
     *
     * @param string           $coverImg
     *
     * @return Event
     */
    public function createFromBandsintownEvent(BandsintownEvent $bandsintownEvent, Country $country, City $city, string $coverImg): Event
    {
        $venue = json_decode($bandsintownEvent->venue);
        $lineup = json_decode($bandsintownEvent->lineup);
        $name = $lineup . ' - ' . $venue->name;

        /** Slug creation */
        $slug = $this->eventSlugGenerator->generate($name);

        /** Event save */
        $event = new Event();

        $event->country_id = ($country) ? $country->id : null;
        $event->city_id = ($city) ? $city->id : null;
        $event->name = ['en' => $name];
        $event->slug = $slug;
        $event->start = $bandsintownEvent->datetime;
        $event->end = $bandsintownEvent->datetime;
        $event->buyer_fee = config('pricing.fee.buyer');
        $event->seller_fee = config('pricing.fee.seller');
        $event->place = $venue->name;
        $event->cover_image = $coverImg;
        $event->cover_image_source = 'bandsintown';

        /** Meta save */
        foreach ($this->localeService->getAvailable() as $locale) {
            $localeName = $event->getTranslation('name', $locale);
            $event->setTranslation('meta_title', $locale, trans('meta.event.title', ['name' => $localeName], $locale));
            $event->setTranslation('meta_description', $locale, trans('meta.event.description', ['name' => $localeName], $locale));
            $event->setTranslation('meta_keywords', $locale, trans('meta.event.keywords', ['name' => $localeName], $locale));
        }

        return $event;
    }
}