<?php

namespace App;

use App\Libraries\MoviesGrabber;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{

  protected $fillable = [
    'title', 'description', 'poster_url', 'api_movie_id', 'popularity', 'release_date', 'vote_average', 'runtime'
  ];

  protected $hidden = ['pivot', 'api_movie_id', 'runtime', 'created_at', 'updated_at', 'deleted_at'];

  public function persons()
  {
    return $this->belongsToMany(Person::class, 'movie_people', 'movie_id', 'people_id')
      ->withPivot(['role', 'cast_type']);
  }

  public function getImageUrl(): string
  {
    $helper = MoviesGrabber::getInstance();
    return $helper->getMovieImageUrl($this);
  }

  public function getCastData($type = MoviesGrabber::CAST_DATA_TYPE_CAST)
  {
    return $this->persons->filter(function ($member) use ($type) {
      if ($member->pivot->cast_type === $type) {
        return true;
      }
      return false;
    })->map([$this, 'pretifyPersonResponse']);
  }

  public function pretifyPersonResponse($member)
  {
    $member->role = $member->pivot->role;
    $member->image_url = $member->getImageUrl();
    $member->person_url = route('api.v1.people.show', ['person' => $member->id]);
    $member->birth_day = $member->birth_day ? date('Y-m-d', strtotime($member->birth_day)) : null;
    $member->death_day = $member->death_day ? date('Y-m-d', strtotime($member->death_day)) : null;

    return $member;
  }
}
