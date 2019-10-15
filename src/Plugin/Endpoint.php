<?php declare(strict_types=1);

namespace GuzzleEndpointMock\Plugin;

use GuzzleHttp\Psr7\Response;

class Endpoint
{
    /** @var string */
    private $method;

    /** @var string */
    private $uri;

    /** @var Response */
    private $response;

    public function __construct(string $method, string $uri, Response $response)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->response = $response;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
