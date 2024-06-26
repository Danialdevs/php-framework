<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Adjust the path as necessary
use App\Maish;

(new Maish())->text();
$response = new \Danial\PhpFramework\Http\Response('<h1>sadsad</h1>', 200, []);
$response->send();