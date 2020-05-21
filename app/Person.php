<?php

namespace App;

use App\Libraries\MoviesGrabber;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
  protected $guarded = [];
  protected $hidden = ['pivot'];

  public function movies()
  {
    return $this->belongsToMany(Movie::class)->withTimestamps();
  }

  public function getImageUrl(): string
  {
    $helper = MoviesGrabber::getInstance();
    return $helper->getPersonImageUrl($this);
  }
}
