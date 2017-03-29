<?php
require __DIR__ . '/../../vendor/autoload.php';

use Goutte\Client;

$dataStore = array();
$crawling = true;
$dataStore['modifiedDate'] = time();
$client = new Client();

$crawler = $client->request('GET', 'http://webcomicname.com/tagged/oh+no/');

while ($crawling) {
    $crawler->filter('#posts article')->each(function ($post) use (&$dataStore) {
        //die($post->filter('.post img')->eq(0)->attr('src'));
        $dataStore['responses'][] = array(
            'imgURI' => $post->filter('.post img')->eq(0)->attr('src'),
            'postURI' => $post->filter('.post a')->eq(0)->attr('href'),
            'pubDate' => strtotime($post->filter('.panel .date-note-wrapper .post-date')->eq(0)->text())
        );
    });
    $paginated = $crawler->filter('#footer #pagination a.next');
    if ($paginated->count() == 1) {
        $crawler = $client->request('GET', $paginated->eq(0)->attr('href'));
        echo $paginated->eq(0)->attr('href') .'<br /><br />';
    } else {
        $crawling = false;
    }
}

$fp = fopen('../../responders/oh-no.json', 'w');
fwrite($fp, json_encode($dataStore, JSON_PRETTY_PRINT));
fclose($fp);
