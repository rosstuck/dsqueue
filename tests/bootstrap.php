<?php
// Configure the autoloader for PHPUnit
$autoloader = require __DIR__.'/../vendor/autoload.php';
/** @var \Composer\Autoload\ClassLoader $autoloader */
$autoloader->add('DS', [__DIR__.'/../src', __DIR__.'/../tests']);
