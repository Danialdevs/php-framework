<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Adjust the path as necessary
use App\Maish;
(new Maish())->text();
$request = \Danial\PhpFramework\Http\Request::createFromGlobals();

dd($request);