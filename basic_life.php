<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/generators/catapi/Cats.php';

use Mpociot\BotMan\BotManFactory;
use Mpociot\BotMan\BotMan;
use Cats\Cats;

$config = [
    'slack_token' => '{{slack-token}}'
];

// create an instance
$botman = BotManFactory::create($config);

// give the bot something to listen for.
$botman->hears('shenanigans where do you live?', function (BotMan $bot) {
    $bot->reply('https://github.com/iLama/general_shenanigans');
});

$botman->hears('shenanigans', function (BotMan $bot) {
    $bot->reply('https://media.giphy.com/media/UNeHvSzLsXwE8/giphy.gif');
});

$botman->hears('(.*\bbees\b.*)', function (BotMan $bot) {
    $bot->reply('http://i.imgur.com/qrLEV.gif');
});

$botman->hears('(.*\boh no\b.*)', function (BotMan $bot) {
    $responses = json_decode(file_get_contents('./responders/oh-no.json'), true);
    $luck = mt_rand(0, count($responses['responses']) - 1);
    $bot->reply('OH YEAH!', [
        'attachments' => [
            [
                'title' => 'Via <'.$responses['responses'][$luck]['postURI'].'|WebComicName.com>',
                'image_url' => $responses['responses'][$luck]['imgURI']
            ]
        ]
    ]);
});

$botman->hears('my nameplate medallion says', function (BotMan $bot) {
    $bot->reply('never trust a Hal 9000');
});

$botman->hears('what do you say general', function (BotMan $bot) {
    $bot->reply('http://www.reactiongifs.us/wp-content/uploads/2013/04/nodding_clint_eastwood.gif');
});

$botman->hears('best design ever', function (BotMan $bot) {
    $bot->reply('https://www.gifcities.org/?q=under+construction');
});

$botman->hears('{name}\+\+', function (BotMan $bot, $name) {
    // Store information for the currently logged in user.
    // You can also pass a user-id / key as a second parameter.
    
     $thing = $bot->driverstorage()->get();
    // $bot->reply('Seriously?'. $name.'?');
    if ($thing->has($name)) {
        $popularity = $thing->get($name) + 1;

        $bot->driverStorage()->save([
            $name => $popularity
        ]);
        $bot->reply('Look at '.$name.' with '.$popularity.' ++s');
    } else {
        $bot->driverStorage()->save([
            $name => 1
        ]);

        $bot->reply($name .' is gaining popularity with just 1 ++');
    }
});

$botman->hears('{name}\-\-', function (BotMan $bot, $name) {
    // Store information for the currently logged in user.
    // You can also pass a user-id / key as a second parameter.
    
     $thing = $bot->driverstorage()->get();
    // $bot->reply('Seriously?'. $name.'?');
    if ($thing->has($name)) {
        $popularity = $thing->get($name) - 1;

        $bot->driverStorage()->save([
            $name => $popularity
        ]);
        $bot->reply('Look at '.$name.' with '.$popularity.' ++s');
    } else {
        $bot->driverStorage()->save([
            $name => -1
        ]);

        $bot->reply($name .' is losing popularity with just -1 ++');
    }
});

$botman->hears('{name}\?\?', function (BotMan $bot, $name) {
    // Store information for the currently logged in user.
    // You can also pass a user-id / key as a second parameter.
    
     $thing = $bot->driverstorage()->get();
    // $bot->reply('Seriously?'. $name.'?');
    if ($thing->has($name)) {
        $popularity = $thing->get($name);

        $bot->reply('Look at '.$name.' with '.$popularity.' ++s');
    } else {
        $bot->driverStorage()->save([
            $name => 0
        ]);

        $bot->reply($name .' has no ++s');
    }
});

$botman->hears('(\bcat(s)?\b)', function (BotMan $bot) {

    $cats = new Cats();

    $catResult = $cats->getCat();

    $bot->reply('', [
        'attachments' => [
            [
                'title'      => 'Cat provided by the Cat API',
                'title_link' => $catResult['source_url'],
                'image_url'  => $catResult['url'],
            ]
        ]
    ]);
});

// start listening
$botman->listen();
