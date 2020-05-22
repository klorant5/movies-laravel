<?php

namespace App;

use App\Libraries\MoviesGrabber;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
  protected $guarded = [];
  protected $hidden = ['pivot', 'api_id', 'created_at', 'updated_at', 'deleted_at'];

  public function movies()
  {
    return $this->belongsToMany(Movie::class, 'movie_people', 'people_id', 'movie_id')
      ->withPivot(['role']);
  }

  public function getImageUrl(?string $url = null): string
  {
    $helper = MoviesGrabber::getInstance();
    $param = !empty($url) ? $url : $this;
    return $helper->getMovieImageUrl($param);
  }

  public function getImageUrlAttribute($value)
  {
    return $this->getImageUrl($value);
  }

  public function getBirthDayAttribute($value)
  {
    return !empty($value) ? date('Y-m-d', strtotime($value)) : null;
  }

  public function getDeathDayAttribute($value)
  {
    return !empty($value) ? date('Y-m-d', strtotime($value)) : null;
  }
}
