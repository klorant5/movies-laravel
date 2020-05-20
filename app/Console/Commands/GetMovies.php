<?php

namespace App\Console\Commands;

use App\Libraries\MoviesGrabber;
use Illuminate\Console\Command;

class GetMovies extends Command
{
  const MAX_PAGE_PER_REQUEST = 10;
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'movies:get {year} {page?}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Get movies from API';
  /**
   * @var MoviesGrabber
   */
  protected $library;
  protected $error_query_count = 0;
  private $dev_mode = FALSE;

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->library = new MoviesGrabber();
  }

  /**
   * Execute the movies:get command.
   *
   * @return mixed
   */
  public function handle()
  {
    $year = $this->argument('year');
    $page = $this->argument('page') ?? 1;

    $this->line('STARTING');

    $this->line('Querying movie data from year: ' . $year . ', starting from page: ' . $page);


    $i = (int)$page;
    $max_page_number = self::MAX_PAGE_PER_REQUEST + $i;
    for (; $i <= $max_page_number; $i++) {
      $api_response = $this->library->getDiscoverData($i, $year);
      if (is_null($api_response)) {
        $this->warn('API response error.');
        return false;
      }

      $this->line('PAGE: ' . $i);
      if ($this->error_query_count == 10) {
        break;
      } elseif ($this->hasHttpErrors($api_response)) {
        continue;
      }

      $movie_data = json_decode($api_response['content'], TRUE);
      if (is_array($movie_data['results']) && empty($movie_data['results']) && !json_last_error()) {
        $this->warn('NO MORE CONTENT');
        break;
      }

      foreach ($movie_data['results'] as $data) {
        if (is_null($data)) {
          continue;
        }
        $this->library->parseMoviesDiscoverData($data);
        $this->line('ID: ' . $this->library->lastId . '; OP: ' . $this->library->lastOperation);

        if ($this->dev_mode) {
          break;
        }
      }
      if ($this->dev_mode) {
        break;
      }
    }

    return true;
  }

  protected function hasHttpErrors($data): bool
  {
    $ret = FALSE;
    if ($data['http_code'] !== 200) {
      if ($data['http_code'] == 404) {
        $this->line('NO CONTENT');
      }

      $this->error_query_count++;
      $ret = TRUE;
    }

    return $ret;
  }

}
