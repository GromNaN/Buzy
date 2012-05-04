<?php

/**
 * Use composer autoloader depending of the installation context.
 */
if (file_exists($file = __DIR__.'/../../../.composer/autoload.php')
 || file_exists($file = __DIR__.'/../vendor/.composer/autoload.php')) {
    $loader = require $file;
    $loader->add('Buzy\\Tests', __DIR__);
} else {
    die('You must set up the project dependencies, run the following commands:'.PHP_EOL.
        'curl -s http://getcomposer.org/installer | php'.PHP_EOL.
        'php composer.phar install --dev'.PHP_EOL);
}
