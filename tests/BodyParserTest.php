<?php

namespace Tests;

use App\Middleware\Api;
use Zend\Diactoros\Uri;
use tests\mocks\Delegate;
use App\Middleware\Database;
use App\Middleware\BodyParser;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\CallbackStream;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response\JsonResponse;

class BodyParserTest extends TestCase
{
    /**
     * HTTP Api Request
     *
     * @return void
     */
    protected $request;

    /**
     * Initialize our api and request.
     *
     * @return void
     */
    public function setUp(): void
    {
        require_once __DIR__ . "/../app/helpers.php";        
        $request = ServerRequestFactory::fromGlobals(
            $server = [
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/redirects'
            ],
            $query = [],
            $body = [],
            $cookies = [],
            $files = []
        );
        $this->request = $request->withBody(new CallbackStream(function () {
            return json_encode(['hello' => 'world']);
        }));
    }

    /**
     * Test the middleware delegate.
     *
     * @return void
     */
    public function testBodyParser(): void
    {
        $delegate = new Delegate();
        $parser = new BodyParser();
        $parser->process($this->request, $delegate);
        $parsed = $delegate->getRequest();
        $this->assertEquals(['hello' => 'world'], $parsed->getAttribute('body'));
    }
}
