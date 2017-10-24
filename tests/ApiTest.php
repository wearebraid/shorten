<?php

namespace Tests;

use App\Middleware\Api;
use Zend\Diactoros\Uri;
use tests\mocks\Delegate;
use App\Middleware\Database;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\CallbackStream;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response\JsonResponse;

class ApiTest extends TestCase
{
    /**
     * HTTP Api Request
     *
     * @return void
     */
    protected $request;

    /**
     * The API middleware.
     *
     * @var App\Middleware\Api
     */
    protected $api;

    /**
     * The PDO isntance from Eloquent.
     *
     * @var \PDO
     */
    protected $db;

    /**
     * Initialize our api and request.
     *
     * @return void
     */
    public function setUp(): void
    {
        
        $config = config('/tests/samples/config.php');
        $this->api = new Api();
        $this->request = ServerRequestFactory::fromGlobals(
            $server = [
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/redirects'
            ],
            $query = [],
            $body = [
                ''
            ],
            $cookies = [],
            $files = []
        );
        $request = $this->request->withAttribute('config', config());

        // Setup the database connection
        touch($config['database']['database']);
        $delegate = new Delegate();
        $database = new Database();
        $database->process($request, $delegate);
        $this->request = $delegate->getRequest();
        $this->db = $this->request->getAttribute('capsule')->getConnection()->getPdo();
        $this->db->query("CREATE TABLE `redirects` (
            `id`	        INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `hash`	        TEXT NOT NULL,
            `redirect_to`	TEXT NOT NULL,
            `count`	        INTEGER NOT NULL DEFAULT 0,
            `created_at`	TEXT NOT NULL,
            `updated_at`	TEXT NOT NULL
        );");
        $this->api->setRequest($this->request);
    }

    /**
     * Set up and tear down the database.
     *
     * @return void
     */
    public function tearDown(): void
    {
        unlink($this->request->getAttribute('config')['database']['database']);
    }

    /**
     * Tests the view importer.
     *
     * @return void
     */
    public function testRoute(): void
    {
        $path = $this->api->getRequest()->getAttribute('route');
        $this->assertEquals('GET /redirects', $path);
    }

    /**
     * Tests requests dont pass if they are not apis.
     *
     * @return void
     */
    public function testNotApi(): void
    {
        $this->assertFalse($this->api->isApi());
    }

    /**
     * Tests requests that are apis pass the test.
     *
     * @return void
     */
    public function testIsApi(): void
    {
        $request = $this->request->withHeader('Authorization', 'testing');
        $this->api->setRequest($request);
        $this->assertTrue($this->api->isApi());
    }

    /**
     * Test the authorization method fails.
     *
     * @return void
     */
    public function testNotAuthorized(): void
    {
        $this->assertFalse($this->api->isAuthorized());
    }

    /**
     * Test the authorization method succeeds.
     *
     * @return void
     */
    public function testIsAuthorized(): void
    {
        $request = $this->request
            ->withHeader('Authorization', 'Bearer 9575d687c61ce66fc190cd2bed464cef');
        $this->api->setRequest($request);
        $this->assertTrue($this->api->isAuthorized());
    }

    /**
     * Test for failed authorization codes.
     *
     * @return void
     */
    public function testFailedAuthorization(): void
    {
        $request = $this->request->withHeader('Authorization', 'this-should-fail');
        $this->api->setRequest($request);
        $response = $this->api->response();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * Test that the show method returns json
     *
     * @return void
     */
    public function testFailedCreate(): void
    {
        $request = $this->request
            ->withMethod('POST')
            ->withHeader('Authorization', 'Bearer 9575d687c61ce66fc190cd2bed464cef');
        $this->api->setRequest($request);
        $response = $this->api->response();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * Test that the show method returns json
     *
     * @return void
     */
    public function testSuccessCreate(): void
    {
        $request = $this->request
            ->withMethod('POST')
            ->withHeader('Authorization', 'Bearer 9575d687c61ce66fc190cd2bed464cef')
            ->withAttribute('body', ['url' => 'http://example.com']);
        $this->api->setRequest($request);
        $response = $this->api->response();
        $results = $this->db->query(
            'SELECT * FROM `redirects` WHERE `redirect_to`="http://example.com"',
            \PDO::FETCH_ASSOC
        );
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, count($results));
    }

    /**
     * Test retrieving a list of all
     *
     * @return void
     */
    public function testShow(): void
    {
        $request = $this->request
            ->withMethod('GET')
            ->withHeader('Authorization', 'Bearer 9575d687c61ce66fc190cd2bed464cef');
        $this->api->setRequest($request);
        $response = $this->api->response();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(is_array(json_decode($response->getBody(), true)));
    }

    /**
     * Test for 404 responses.
     *
     * @return void
     */
    public function testNotFound(): void
    {
        $request = $this->request
            ->withMethod('GET')
            ->withUri(new Uri('http://www.example.com/pizza'))
            ->withHeader('Authorization', 'Bearer 9575d687c61ce66fc190cd2bed464cef');
        $this->api->setRequest($request);
        $response = $this->api->response();
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Test the middleware delegate.
     *
     * @return void
     */
    public function testMiddlewareDelegate(): void
    {
        $request = $this->request->withoutHeader('Authorization');
        $delegate = new Delegate();
        $this->api->process($request, $delegate);

        $this->assertEquals($this->api->getRequest(), $delegate->getRequest());
    }
}
