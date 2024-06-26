<?php
namespace Danial\PhpFramework\Http;
class Response
{
    public function __construct(
        private mixed $content,
        private int $statusCode = 200,
        private array $headers = []
    )
    {
    }

    public function send(){
        echo $this->content;
    }
}