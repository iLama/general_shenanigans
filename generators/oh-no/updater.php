<?php
require __DIR__ . '/../../vendor/autoload.php';

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

$client = new Client();

$response = $client->request('GET', 'http://webcomicname.com/tagged/oh-no/rss');

//if RSS feed loaded successfully
//because better safe then not
if ($response->getStatusCode() === 200) {
    //Prep feed for parsing
    $feed = simplexml_load_string($response->getBody()->getContents());

    //feed was loaded successfully
    //so go ahead and load the data store
    $jsonStore = '../../responders/oh-no.json';
    $fp = file_get_contents($jsonStore);
    $dataStore = json_decode($fp, true);

    //store the last time the json was updated
    $dateUpdated = $dataStore['modifiedDate'];

    //set new modified date to now
    //because why make it complicated
    $dataStore['modifiedDate'] = time();
    
    foreach ($feed->channel->item as $post) {
        // $meh = new Crawler();

        //set tmp var for the item's publish dated
        $pubDate = strtotime($post->pubDate);

        //parse the item content as html so that we can grab the image
        $dom = new DOMDocument();
        $dom->loadHTML(htmlspecialchars_decode((string)$post->description));

        $description_sxml = simplexml_import_dom($post->description);

        //find the image and grab the src value
        $image = $dom->getElementsByTagName('img')->item(0)->getAttribute('src');


        //if JSON freshness is older than the most recent published item
        //go ahead and add that sucker to the data store
        //else we're done here
        if ($dateUpdated < $pubDate) {
            $dataStore['responses'][] = array(
                'imgURI' => $image,
                'postURI' => $post->link,
                'pubDate' => $pubDate
            );
        } else {
            echo 'exit';
            //write the updated data store to JSON
            //and close up shop
            file_put_contents($jsonStore, json_encode($dataStore, JSON_PRETTY_PRINT));
            exit();
        }
    }
}


