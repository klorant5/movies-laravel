<?php

namespace App;

use App\Libraries\MoviesGrabber;
use Illuminate\Database\Eloquent\Model;

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

}
