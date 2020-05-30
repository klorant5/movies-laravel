<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MovieCollection;
use App\Http\Resources\MovieResource;
use App\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MoviesController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\MovieCollection
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $movies = Cache::remember('movies_list_' . $page, 30 * 60, function () {
            return Movie::where('deleted_at', NULL)
                ->orderBy('release_date')
                ->paginate(12);
        });
        return new MovieCollection($movies);
    }

    public function show(Movie $movie)
    {
        $movieResource = new MovieResource($movie);
        return $movieResource;
    }

    public function search(Request $request)
    {
        $search_string = $request->get('s');
        return [];
    }
}
