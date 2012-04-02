<?php
/*
 * This file is part of the Buzy package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Simple autoloader that follow partially the PHP Standards Recommendation #0 (PSR-0)
 * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md for more informations.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */

spl_autoload_register(function($class) {
    return 0 === strpos($class, 'Buzy')
        && is_file($file = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php')
        && (bool) include $file;
});
