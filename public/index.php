<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Adjust the path as necessary
use App\Maish;
use Danial\PhpFramework\Http\Kernel;
$request = \Danial\PhpFramework\Http\Request::createFromGlobals();
$kernel = new Kernel();
$response = $kernel->handel($request);

$response->send();