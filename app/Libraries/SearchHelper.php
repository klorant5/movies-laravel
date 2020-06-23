<?php


namespace App\Libraries;


use App\Movie;
use App\Person;

class SearchHelper
{
    const SEARCH_TYPE_MOVIES = 'movies';
    /**
     * @var string
     */
    private $searchString;
    /**
     * @var array
     */
    private $filters;

    private $searchTypeRegex = '^(?<search_type>movies|actors) .*';
    private $regexes = [
        self::SEARCH_TYPE_MOVIES => [
            '(?<from>(and was released in|from) ((\d{4})( and (\d{4}))*))?',
            '(?<directed>( and )?directed by ([a-zA-Z-\' ]*(?<= and )))?',
            '(?<played_by>( and )?(played by|starring) ([0-9a-zA-Z-\' ]*))?',
        ]
    ];

    //movies from 2018 and directed by asd daae and eeta
    //movies directed by asd daae and eeta and was released in 2015
    //movies directed by asd daae and eeta and played by sdaegeg efg asd
    //movies directed by asd daae and eeta and starring 50 cent
    //movies starring 50 cent and directed by asd daae

    //^(?<search_type>movies|actors) (?<from>(and was released in|from) ((\d{4})( and (\d{4}))*))?(?<directed>( and )?directed by ([a-zA-Z-' ]*(?<= and )))?(?<played_by>( and )?(played by|starring) ([0-9a-zA-Z-' ]*(?<= and )))?

    /**
     * SearchHelper constructor.
     * @param string $searchString
     */
    public function __construct(string $searchString = '')
    {
        $this->searchString = $searchString;
        $this->filters = [];
    }

    public function getSearchQuery()
    {

        $pattern = '/' . implode('', $this->regexes) . '/';
        dump($pattern);
        preg_match($this->searchTypeRegex, $this->searchString, $matches);
        if ($matches['search_type'] == self::SEARCH_TYPE_MOVIES) {
            $search_query = Movie::where('deleted_at', NULL);
            $this->setFromYearFilters($matches, $search_query);
        } else {
            $search_query = Person::where('deleted_at', NULL);
        }
        dump($matches);
//            $search_query->dd();
    }

    /**
     * @param $matches
     * @param $search_query
     * @return mixed
     */
    protected function setFromYearFilters($matches, &$search_query)
    {
        if (!empty($matches['from'])) {
            $str = str_replace('from ', '', $matches['from']);
            $years = explode(' and ', $str);
            $this->filters[] = ['from' => $years];
            if (count($years) > 1) {
                $search_query->where(function ($query) use ($years) {
                    foreach ($years as $year) {
                        $query->orWhereYear('release_date', $year);
                    }
                });

            } elseif (count($years) === 1) {
                $search_query->whereYear('release_date', $years[0]);
            }
        }
    }
}
