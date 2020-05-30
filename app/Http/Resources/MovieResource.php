<?php

namespace App\Http\Resources;

use App\Libraries\MoviesGrabber;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
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
      'title' => $this->getTitleAndReleaseYear(),
      'description' => $this->description,
      'poster_url' => $this->poster_url,
      'popularity' => $this->popularity,
      'vote_average' => $this->vote_average,
      'release_date' => date('Y-m-d', strtotime($this->release_date)),
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
      MoviesGrabber::CAST_DATA_TYPE_CAST => $this->getCastData(),
      MoviesGrabber::CAST_DATA_TYPE_CREW => $this->getCastData(MoviesGrabber::CAST_DATA_TYPE_CREW),
    ];
  }
}
