<?php

namespace App\Domain\Crawlers\Bandsintown\Factory;

use App\Models\BandsintownArtist;
use Bandsintown\Response\Artist;

/**
 * Class BandsintownArtistFactory.
 *
 * @package App\Domain\Crawlers\Bandsintown\Factory
 */
class BandsintownArtistFactory
{
    /**
     * @param Artist $artist
     *
     * @return BandsintownArtist
     */
    public function createFromArtistResponse(Artist $artist): BandsintownArtist
    {
        $bandsintownArtist = new BandsintownArtist();
        $bandsintownArtist->id = $artist->getId();
        $bandsintownArtist->name = $artist->getName();
        $bandsintownArtist->url = $artist->getUrl();
        $bandsintownArtist->image_url = $artist->getImageUrl();
        $bandsintownArtist->thumb_url = $artist->getThumbUrl();
        $bandsintownArtist->facebook_page_url = $artist->getFacebookPageUrl();
        $bandsintownArtist->mbid = $artist->getMbid();
        $bandsintownArtist->tracker_count = $artist->getTrackerCount();
        $bandsintownArtist->upcoming_event_count = $artist->getUpcomingEventCount();

        return $bandsintownArtist;
    }
}
