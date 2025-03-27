<?php

namespace App\Models\Movie;

use App\Models\BackendModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class CrawlerModel extends BackendModel
{
    public $searchUrl = 'https://ophim1.com/v1/api/tim-kiem?keyword=';
    public function __construct()
    {
        $this->table = config('constants.TABLE_MOVIE');
        parent::__construct();
    }
    public function listItem($params = null, $options = null){
        $result = [];
        if ($options['task'] == 'crawler-data') {

            if (!empty($params['page_from']) && !empty($params['page_to'])) {
                if (str_contains($params['url'], 'ophim1.com')) {
                    $responses = Http::pool(fn ($pool) => 
                        array_map(fn ($i) => 
                        $pool->get($params['url'] . '?page=' . $i), range($params['page_from'], $params['page_to']))
                    );
                    $result = array_merge(...array_map(fn ($response) => 
                        optional($response->json())['items'] ?? [], $responses
                    ));
                }
                else {
                    $responses = Http::pool(fn ($pool) => 
                        array_map(fn ($i) => 
                        $pool->get($this->searchUrl . $params['url'] . '?page=' . $i), range($params['page_from'], $params['page_to']))
                    );
                    $result = array_merge(...array_map(fn ($response) => 
                        optional($response->json())['data']['items'] ?? [], $responses
                    ));
                   
                }
            }
            
            $movies = collect($result)->map(fn ($response) => [
                'name'      => optional($response)['name'] ?? null,
                'slug'      => optional($response)['slug'] ?? null,
                'existed'   => 0,
            ])->toArray();

            $existingSlugs = self::whereIn('slug', array_column($movies, 'slug'))->pluck('slug')->toArray();
            $existingSlugs = array_flip($existingSlugs);
            
            foreach ($movies as &$movie) {
                if (isset($existingSlugs[$movie['slug']])) {
                    $movie['existed'] = 1;
                }
            }
            $result = $movies;
        }
        return $result;
    }
}
