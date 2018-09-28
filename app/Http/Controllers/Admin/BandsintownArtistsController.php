<?php


namespace App\Http\Controllers\Admin;

use App\Domain\Crawlers\Bandsintown\BandsintownService as BandsintownCrawler;
use App\Http\Controllers\Controller;
use App\Services\BandsintownService;
use Illuminate\Http\Request;

/**
 * Class BandsintownArtistsController.
 *
 * @package App\Http\Controllers\Admin
 */
class BandsintownArtistsController extends Controller
{
    /** @var BandsintownService */
    private $bandsintownService;
    /** @var BandsintownCrawler */
    private $bandsInTownCrawler;

    /**
     * BandsintownArtistsController constructor.
     *
     * @param BandsintownService $bandsInTownCrudService
     * @param BandsintownCrawler $bandsInTownCrawler
     */
    public function __construct(
        BandsintownService $bandsInTownCrudService,
        BandsintownCrawler $bandsInTownCrawler
    )
    {
        $this->bandsintownService = $bandsInTownCrudService;
        $this->bandsInTownCrawler = $bandsInTownCrawler;
    }

    /**
     * @param Request            $request
     * @param BandsintownService $bandsInTownArtistService
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, BandsintownService $bandsInTownArtistService)
    {
        return view('admin.bandsintown-artists.index', ['artists' => $bandsInTownArtistService->filter($request)]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $artistsTextArea = $request->get('artist_names');

        $names = preg_split('/\r\n|[\r\n]/', $artistsTextArea);
        $errors = [];

        foreach ($names as $name) {
            try {
                $this->bandsInTownCrawler->crawl($name);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if ($errors) {
            return redirect()->back()->with('flash-error', 'Something went wrong with ' . implode(', ', $errors) . ' Please try again!');
        }

        return redirect()->back();
    }
}