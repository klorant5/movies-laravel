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

  public function getImageUrl(): string
  {
    $helper = MoviesGrabber::getInstance();
    return $helper->getPersonImageUrl($this);
  }
}
