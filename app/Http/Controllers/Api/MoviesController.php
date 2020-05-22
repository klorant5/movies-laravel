<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MovieCollection;
use App\Http\Resources\MovieResource;
use App\Movie;
use Illuminate\Http\Request;

class MoviesController extends Controller
{
  /**
   * @param \Illuminate\Http\Request $request
   * @return \App\Http\Resources\MovieCollection
   */
  public function index(Request $request)
  {
    $movies = Movie::where('deleted_at', NULL)
      ->orderBy('release_date')
      ->paginate(10);

    return new MovieCollection($movies);
  }

  public function show(Movie $movie)
  {
    return new MovieResource($movie);
  }

  public function search(Request $request)
  {
    $search_string = $request->get('s');
    return [];
  }
}
