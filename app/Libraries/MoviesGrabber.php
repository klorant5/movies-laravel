<?php

namespace App\Libraries;

use App\Movie;
use App\MoviesPeople;
use App\Person;
use App\Providers\AppServiceProvider;
use GuzzleHttp\Client;

class MoviesGrabber
{
  const MAX_CAST_MEMBER_PER_MOVIE = 10;
  const MAX_CREW_MEMBER_PER_MOVIE = 5;
  const CAST_DATA_TYPE_CAST = 'cast';
  const CAST_DATA_TYPE_CREW = 'crew';
  /**
   * @var MoviesGrabber
   */
  private static $instance;

  /**
   * @var Client
   */
  private $client;
  protected $savedMovieIds;
  /**
   * @var mixed
   */
  public $lastId;
  /**
   * @var string
   */
  public $lastOperation;
  /**
   * api_id => our saved_id
   * @var array
   */
  protected $savedCastMembers = [];

  public function __construct()
  {
    $this->savedMovieIds = Movie::all()->pluck('api_movie_id')->toArray();
    $this->savedCastMembers = Person::all()->pluck('id', 'api_id')->toArray();
    $this->client = new Client();
  }

  public function getDiscoverData(int $page, int $year = 2020): ?array
  {
    $url = $this->getAPIAccess()['discover'];
    $url .= '&primary_release_date.gte=' . $year .
      '-01-01&primary_release_date.lte=' . $year . '-12-31&page=' . $page;
    return $this->getPage($url);
  }

  public function getCastData(Movie $movie): ?array
  {
    $url = $this->getAPIAccess()['cast'];
    $url = str_replace('#movieid#', $movie->api_movie_id, $url);
    $api_response = $this->getPage($url);
    $ret = NULL;
    if ($api_response && isset($api_response['content']) && $api_response['http_code'] == 200) {
      $cast_data = json_decode($api_response['content'], TRUE);
      if (is_array($cast_data['cast']) && !empty($cast_data['cast']) && !json_last_error()) {
        $ret = $cast_data;
      }
    }

    return $ret;
  }

  public function getAPIAccess(): array
  {
    return [
      'apikeyv3' => '25c4fe756826598f848dd6e1f29f20e5',
      'imageurl' => 'http://image.tmdb.org/t/p/w185/#imageurl#',
      'exampleapireq' => 'https://api.themoviedb.org/3/movie/550?api_key=25c4fe756826598f848dd6e1f29f20e5',
      'apiReadAccToken' => 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIyNWM0ZmU3NTY4MjY1OThmODQ4ZGQ2ZTFmMjlmMjBlNSIsInN1YiI6IjVlODc2YWYzZGExMGYwMDAxNGEyOWU1MyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.O7g43BEH8D_Vaz-YbNxCu4Z97e1RbQhGigu-49vDcxc',
      'discover' => 'https://api.themoviedb.org/3/discover/movie?api_key=25c4fe756826598f848dd6e1f29f20e5',
      'cast' => 'https://api.themoviedb.org/3/movie/#movieid#/credits?api_key=25c4fe756826598f848dd6e1f29f20e5',
      'person' => 'https://api.themoviedb.org/3/person/#personid#?api_key=25c4fe756826598f848dd6e1f29f20e5&language=en-US'
    ];
  }

  public function getMovieImageUrl(Movie $movie): string
  {
    if (empty($movie->poster_url)) {
      return '';
    }
    $url = $this->getAPIAccess()['imageurl'];
    return str_replace('#imageurl#', $movie->poster_url, $url);
  }

  public function getPersonImageUrl(Person $person): string
  {
    if (empty($person->image_url)) {
      return '';
    }
    $url = $this->getAPIAccess()['imageurl'];
    return str_replace('#imageurl#', $person->image_url, $url);
  }

  public static function getInstance()
  {
    if (empty(self::$instance)) {
      self::$instance = new static();
    }

    return self::$instance;
  }

  protected function getPage(string $url): ?array
  {
    try {
      $ret = [];
      $response = $this->client->get($url);
      $ret['http_code'] = $response->getStatusCode();
      $ret['content'] = $response->getBody()->getContents();

      return $ret;
    } catch (\Exception $exception) {
      return NULL;
    }
  }

  public function parseMoviesDiscoverData(?array $data): void
  {
    $this->lastId = $data['id'];
    if (in_array((string)$data['id'], $this->savedMovieIds)) {
      $this->lastOperation = "SKIP";
      return;
    }
    $values = [
      'title' => $data['title'],
      'description' => $data['overview'],
      'poster_url' => $data['poster_path'] ?? '',
      'api_movie_id' => $data['id'],
      'release_date' => date('Y-m-d H:i:s', strtotime($data['release_date'])),
      'vote_average' => $data['vote_average'],
      'popularity' => $data['popularity'],
      'runtime' => 0,
    ];

    try {
      $movie = Movie::create($values);

      $this->lastOperation = "SAVE";
      $this->savedMovieIds[] = (string)$data['id'];

      $cast_data = $this->getCastData($movie);
      if ($cast_data) {
        $this->parseCastData($cast_data, $movie);
        $this->parseCastData($cast_data, $movie, self::CAST_DATA_TYPE_CREW);
      }
    } catch (\Exception $exception) {
      $this->lastOperation = "ERROR:" . $exception->getMessage();
    }

  }

  protected function getPersonAPIData($id): ?array
  {
    $url = $this->getAPIAccess()['person'];
    $url = str_replace('#personid#', $id, $url);

    $apiResponse = $this->getPage($url);
    $ret = NULL;
    if ($apiResponse && isset($apiResponse['content']) && $apiResponse['http_code'] == 200) {
      $person_data = json_decode($apiResponse['content'], TRUE);
      if (is_array($person_data) && !empty($person_data) && !json_last_error()) {
        $ret = $person_data;
      }
    }
    return $ret;
  }

  /**
   * @param array|null $cast_data
   * @param $movie
   * @param string $cast_type
   */
  protected function parseCastData(?array $cast_data, $movie, $cast_type = self::CAST_DATA_TYPE_CAST): void
  {
    $max = ($cast_type === self::CAST_DATA_TYPE_CAST) ? self::MAX_CAST_MEMBER_PER_MOVIE : self::MAX_CREW_MEMBER_PER_MOVIE;
    for ($j = 0; $j < $max; $j++) {
      if (!isset($cast_data[$cast_type][$j])) {
        break;
      }
      $cast_member = $cast_data[$cast_type][$j];
      if (isset($this->savedCastMembers[$cast_member['id']])) {
        //cast member exists
        $person_id = $this->savedCastMembers[$cast_member['id']];
      } else {
        //cast member doesn't exist in our database
        //api call
        $person_data = $this->getPersonAPIData($cast_member['id']);
        $person = Person::create([
          'api_id' => $person_data['id'],
          'name' => $person_data['name'],
          'description' => $person_data['biography'],
          'image_url' => $person_data['profile_path'],
          'gender' => $person_data['gender'],
          'popularity' => $person_data['popularity'],
          'birth_day' => $person_data['birthday'] ? date('Y-m-d H:i:s', strtotime($person_data['birthday'])) : null,
          'death_day' => $person_data['deathday'] ? date('Y-m-d H:i:s', strtotime($person_data['deathday'])) : null,
        ]);
        $person_id = $person->id;
        $this->savedCastMembers[$cast_member['id']] = $person_id;
      }

      $role_name = 'character';
      if ($cast_type !== self::CAST_DATA_TYPE_CAST) {
        $role_name = 'job';
      }
      $role = substr($cast_member[$role_name], 0, AppServiceProvider::VARCHAR_DB_MAXLENGTH - 1);
      MoviesPeople::create([
        'movie_id' => $movie->id,
        'people_id' => $person_id,
        'role' => $role
      ]);
    }
  }

}
