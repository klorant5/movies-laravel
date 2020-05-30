<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PeopleResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param \Illuminate\Http\Request $request
   * @return array
   */
  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'description' => $this->description,
      'image_url' => $this->image_url,
      'popularity' => $this->popularity,
      'gender' => $this->gender,
      'birth_day' => $this->birth_day,
      'death_day' => $this->death_day,
      'movies' => $this->movies->map(function ($movie) {
        $movie->role = $movie->pivot->role;
        $movie->url = route('api.v1.movies.show', ['movie' => $movie->id]);
        $movie->title = $movie->getTitleAndReleaseYear();
        return $movie;
      })
    ];
  }
}
