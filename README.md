# Guzzle endpoint mock handler

This plugin allows you to define responses for given endpoints on a guzzle level, it means you can run your tests without mocking guzzle itself.


## How to install

```
composer require thiagocordeiro/guzzle-endpoint-mock-handler --dev
```

## How to use

All you have to do is set the mock handler on guzzle constructor and starting mock your endpoints.

```
$handler = new \GuzzleEndpointMock\Plugin\EndpointAwareMockHandler();
$handler->append(
    new \GuzzleEndpointMock\Plugin\Endpoint('GET', '/users/123', new \GuzzleHttp\Psr7\Response(200, [], '{"body":"anything"}'))
);

$client = new \GuzzleHttp\Client([
    'handler' => \GuzzleHttp\HandlerStack::create(\GuzzleEndpointMock\Plugin\EndpointAwareMockHandler),
    'http_errors' => false,
]);

$response = $client->request('GET', '/users/123');
```

#### Or Using on tests
```
<?php

namespace Tests\Feature;

use GuzzleEndpointMock\Plugin\Endpoint;
use GuzzleEndpointMock\Plugin\EndpointAwareMockHandler;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class MyClassUsingGuzzleTest extends TestCase
{
    /** @var EndpointAwareMockHandler */
    private $handler;

    /** @var MyClassUsingGuzzle */
    private $service;

    protected function setUp(): void
    {
        $this->handler = new EndpointAwareMockHandler();

        $client = new Client([
            'handler' => HandlerStack::create($this->handler),
            'http_errors' => false,
        ]);

        $this->service = new MyClassUsingGuzzle($client);
    }

    public function testSomething(): void
    {
        $this->handler->append(
            new Endpoint('GET', '/users/123', new Response(200, [], '{"body":"anything"}'))
        );

        // the thing you are testing which uses guzzle to call `/users/123` endpoint
        $this->service->doSomething();

        ...
    }
}
```

You can setup different responses code or body for a given endpoint, guzzle will behave exactly as performing a real call.


## Contributing
Ideas for improvements feel free to submit your pull request
