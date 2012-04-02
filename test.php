<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

require __DIR__.'/vendor/.composer/autoload.php';
require __DIR__.'/src/autoload.php';

$cache = new Doctrine\Common\Cache\ArrayCache();
$cacheListener = new Buzy\Cache\CacheListener($cache);

$d = new Symfony\Component\EventDispatcher\EventDispatcher;
$d->addSubscriber($cacheListener);
$b = new Buzy\Browser(null, $d);

//$response = $b->get('http://static.lexpress.fr/imgs/uploads/static/0e1/taneange_avatar_88x88.jpg');
$response = $b->get('http://js.lexpress.fr/scripts/oas.js');
var_dump($response->getAge());
sleep(2);
$response = $b->get('http://js.lexpress.fr/scripts/oas.js');
var_dump($response->getAge());

