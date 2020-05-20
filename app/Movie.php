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
    return $this->belongsToMany(Person::class, 'movie_people', 'movie_id', 'people_id')
      ->withPivot('role');
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
      'poster_url' => $this->getImageUrl(),
      'popularity' => $this->popularity,
      'release_date' => date('Y-m-d', strtotime($this->release_date)),
    ];
  }

  public function getCastData()
  {
    return $this->persons->filter(function ($member) {
      if ($member->id === 4) {    //todo pivot->role_type=='cast'
        return true;
      }
      return false;
    })->map([$this, 'pretifyPersonResponse']);
  }

  public function pretifyPersonResponse($member)
  {
    $member->role = $member->pivot->role;
    $member->image_url = $member->getImageUrl();
    $member->birth_day = $member->birth_day ? date('Y-m-d', strtotime($member->birth_day)) : null;
    $member->death_day = $member->death_day ? date('Y-m-d', strtotime($member->death_day)) : null;
    unset($member->api_id);
    unset($member->pivot);
    unset($member->created_at);
    unset($member->updated_at);
    unset($member->deleted_at);
    return $member;
  }
}
