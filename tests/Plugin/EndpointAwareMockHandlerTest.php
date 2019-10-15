<?php declare(strict_types=1);

namespace Tests\GuzzleEndpointMock\Plugin;

use GuzzleEndpointMock\Plugin\Endpoint;
use GuzzleEndpointMock\Plugin\EndpointAwareMockHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

class EndpointAwareMockHandlerTest extends TestCase
{
    /** @var EndpointAwareMockHandler */
    private $handler;

    /** @var Client */
    private $client;

    protected function setUp(): void
    {
        $this->handler = new EndpointAwareMockHandler();

        $this->client = new Client([
            'handler' => HandlerStack::create($this->handler),
            'http_errors' => false,
        ]);
    }

    public function testWhenGivenDifferentObjectToHandlerThenThrowError(): void
    {
        $handler = new stdClass();

        $this->expectException(TypeError::class);

        new EndpointAwareMockHandler([$handler]);
    }

    /**
     * @dataProvider uriResponseDataset
     * @throws GuzzleException
     */
    public function testWhenGivenTheEndpointThenMatchResponse(Endpoint $endpoint): void
    {
        $this->handler->append($endpoint);

        $response = $this->client->request($endpoint->getMethod(), $endpoint->getUri());

        $this->assertEquals($endpoint->getResponse(), $response);
    }

    public function uriResponseDataset(): array
    {
        return [
            'GET 200' => [new Endpoint('get', '/users/123', new Response(200))],
            'GET 404' => [new Endpoint('get', '/users/987', new Response(404))],
            'GET 200 query' => [new Endpoint('get', '/users/123?active=true', new Response(200))],
            'GET 200 domain' => [new Endpoint('get', 'http://localhost/users/123', new Response(200))],
            'GET 404 domain' => [new Endpoint('get', 'http://localhost/users/987', new Response(404))],
            'POST 201' => [new Endpoint('post', '/users', new Response(201))],
            'PATCH 204' => [new Endpoint('patch', '/users/123', new Response(204))],
            'PUT 204' => [new Endpoint('put', '/users/123', new Response(204))],
            'DELETE 202' => [new Endpoint('delete', '/users/123', new Response(202))],
        ];
    }

    /**
     * @throws GuzzleException
     */
    public function testWhenGivenInvalidEndpointThenThrowError(): void
    {
        $method = 'POST';
        $uri = '/foo/bar';

        $this->expectException(OutOfBoundsException::class);

        $this->client->request($method, $uri);
    }

    /**
     * @throws GuzzleException
     */
    public function testWhenEndpointCalledMoreThenOnceThenReturnBody(): void
    {
        $endpoint = new Endpoint('get', '/users/123', new Response(200, [], '{"body":"anything"}'));
        $this->handler->append($endpoint);

        $firstResponse = $this->client
            ->request($endpoint->getMethod(), $endpoint->getUri())
            ->getBody()
            ->getContents();

        $secondResponse = $this->client
            ->request($endpoint->getMethod(), $endpoint->getUri())
            ->getBody()
            ->getContents();

        $this->assertEquals(
            [
                '{"body":"anything"}',
                '{"body":"anything"}',
            ],
            [
                $firstResponse,
                $secondResponse,
            ]
        );
    }
}
