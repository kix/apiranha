<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in('./src');

return new Sami($iterator, [
    'title' => 'kix/apiranha',
    'build_dir' => __DIR__.'/apidoc/build',
    'cache_dir' => __DIR__.'/apidoc/cache',
    'default_opened_level' => 2,
]);
