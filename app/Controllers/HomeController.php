<?php

namespace App\Controllers;

use App\Services\YouTubeService;
use Danial\Framework\Controller\AbstractController;
use Danial\Framework\Http\Response;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly YouTubeService $youTube,
    ) {
    }

    public function index(): Response
    {
      return "Hello world";
    }
}
