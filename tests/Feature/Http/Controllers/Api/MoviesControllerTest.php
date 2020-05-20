<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\MoviesController
 */
class MoviesControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function index_behaves_as_expected()
    {
        $movies = factory(Movies::class, 3)->create();

        $response = $this->get(route('movie.index'));
    }
}
