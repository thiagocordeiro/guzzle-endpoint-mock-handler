# Guzzle endpoint mock handler

This plugin allows you to define responses for given endpoints on a guzzle level, it means you can run your tests without mocking guzzle itself.


## How to use

All you have to do is set the mock handler on guzzle constructor and starting mock your endpoints.
```
<?php declare(strict_types=1);

namespace Tests\Integration\Something;
  
use GuzzleEndpointMock\Plugin\Endpoint;
use GuzzleEndpointMock\Plugin\EndpointAwareMockHandler;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class MyTest extends TestCase
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

    public function testSomething(): void
    {
        $this->handler->append(
            new Endpoint('GET', '/users/123', new Response(200, [], '{"body":"anything"}'))
        );

		// the thing you are testing which uses guzzle to call `/users/123` endpoint
        $this->theThingImTesting->doSomething();

        ...
    }
}
```

You can setup different responses code or body for a given endpoint, guzzle will behave exactly as performing a real call.


## Contributing
Ideas for improvements feel free to submit your pull request
