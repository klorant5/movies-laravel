<?php

namespace App;

use App\Libraries\MoviesGrabber;
use http\Url;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;

class Movie extends Model
{

  protected $fillable = [
    'title', 'description', 'poster_url', 'api_movie_id', 'popularity', 'release_date', 'vote_average', 'runtime'
  ];


  public function persons()
  {
    return $this->belongsToMany(Person::class)->withTimestamps();
  }

  public function getImageUrl(): string
  {
    $helper = MoviesGrabber::getInstance();
    return $helper->getMovieImageUrl($this);
  }

  public function toArray()
  {
    return [
      'id' => $this->id,
      'title' => $this->title,
      'url' => route('api.v1.movies.show', ['movie' => $this->id]),
      'poster' => $this->getImageUrl(),
      'popularity' => $this->popularity,
      'release_date' => date('Y-m-d', strtotime($this->release_date)),
    ];
  }
}
