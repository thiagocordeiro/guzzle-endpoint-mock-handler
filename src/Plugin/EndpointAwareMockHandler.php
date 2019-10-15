<?php declare(strict_types=1);

namespace GuzzleEndpointMock\Plugin;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use OutOfBoundsException;
use Psr\Http\Message\RequestInterface;

class EndpointAwareMockHandler
{
    /** @var Response[] */
    private $endpoints = [];

    /**
     * @param Endpoint[] $endpoints
     */
    public function __construct(array $endpoints = [])
    {
        array_map([$this, 'append'], $endpoints);
    }

    public function __invoke(RequestInterface $request): PromiseInterface
    {
        $key = $this->key($request->getMethod(), (string) $request->getUri());

        if (!array_key_exists($key, $this->endpoints)) {
            throw new OutOfBoundsException($key);
        }

        $response = $this->endpoints[$key];
        $response->getBody()->rewind();

        return new FulfilledPromise($response);
    }

    public function append(Endpoint $endpoint): void
    {
        $key = $this->key($endpoint->getMethod(), $endpoint->getUri());
        $this->endpoints[$key] = $endpoint->getResponse();
    }

    private function key(string $method, string $url): string
    {
        $method = strtoupper($method);

        return "{$method} {$url}";
    }
}
