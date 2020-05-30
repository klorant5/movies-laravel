<?php

namespace App;

use App\Libraries\MoviesGrabber;
use Carbon\Carbon;
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

  public function getImageUrl(?string $url = null): string
  {
    $helper = MoviesGrabber::getInstance();
    $param = !empty($url) ? $url : $this;
    return $helper->getMovieImageUrl($param);
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
    $member->person_url = route('api.v1.people.show', ['person' => $member->id]);

    return $member;
  }

  public function getPosterUrlAttribute($value)
  {
    return $this->getImageUrl($value);
  }

  public function getReleaseDateAttribute($value)
  {
    return !empty($value) ? date('Y-m-d', strtotime($value)) : null;
  }

    public function getTitleAndReleaseYear()
    {
        return $this->title . ' (' . Carbon::create($this->release_date)->format('Y') . ')';
  }
}
