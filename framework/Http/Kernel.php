<?php

namespace Danial\PhpFramework\Http;

class Kernel
{
    public function handel(Request $request): Response
    {
        $content = "<h1>dssad</h1>";
        return new Response($content);
    }
}