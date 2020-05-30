<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PeopleCollection;
use App\Http\Resources\PeopleResource;
use App\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PeopleController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\PeopleCollection
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $people = Cache::remember('people_list_' . $page, 30 * 60, function () {
            return Person::where('deleted_at', NULL)
                ->paginate(12);
        });

        return new PeopleCollection($people);
    }

    public function show(Person $person)
    {
        return new PeopleResource($person);
    }
}
