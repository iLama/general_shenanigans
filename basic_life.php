<?php
require __DIR__ . '/vendor/autoload.php';
use Mpociot\BotMan\BotManFactory;
use Mpociot\BotMan\BotMan;

$config = [
    'slack_token' => ''
];

// create an instance
$botman = BotManFactory::create($config);

// give the bot something to listen for.
$botman->hears('shenanigans', function (BotMan $bot) {
    $bot->reply('What!?');
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

// start listening
$botman->listen();