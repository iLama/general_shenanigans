<?php namespace Cats;

require __DIR__ . '/../../vendor/autoload.php';

use \GuzzleHttp\Client;
use \Symfony\Component\DomCrawler\Crawler;

class Cats
{

    protected $client;
    
    public function __construct()
    {
        $this->client = new Client();
    }

    public function getCat()
    {
        $response = $this->client->request('GET', 'http://thecatapi.com/api/images/get', [
            'query' => [
                            'api_key'           => '{{CAT API KEY GOES HERE}}',
                            'format'            => 'xml',
                            'results_per_page'  => 1
            ]
        ]);

        if ($response->getStatusCode() === 200) {
            //Prep Cat for response
            $resultCat = simplexml_load_string($response->getBody()->getContents());
        
            $catArr['url'] = $resultCat->data->images->image->url->__toString();
            $catArr['source_url'] = $resultCat->data->images->image->source_url->__toString();
        
            return $catArr;
        } else {
            return false;
        }
    }
}
