<?php
$autoloader = require __DIR__.'/../vendor/autoload.php';
/** @var \Composer\Autoload\ClassLoader $autoloader */
$autoloader->add('DS', __DIR__.'/../src');

$container = new \DS\Demo\DependencyInjection\Container();

$app = new \Cilex\Application('DS Demo');
$app->command($container['ds.worker.command.start']);
$app->run();