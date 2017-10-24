<?php

namespace Tests;

use App\Middleware\Api;
use Zend\Diactoros\Uri;
use tests\mocks\Delegate;
use App\Middleware\Database;
use App\Middleware\Redirect;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\ServerRequestFactory;

class RedirectTest extends TestCase
{
    /**
     * HTTP Api Request
     *
     * @return void
     */
    protected $request;

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
        require_once __DIR__ . "/../app/helpers.php";
        $request = ServerRequestFactory::fromGlobals(
            $server = [
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/ghijkl'
            ],
            $query = [],
            $body = [],
            $cookies = [],
            $files = []
        );

        // Setup the database connection
        $config = config('tests/samples/config.php');
        touch($config['database']['database']);
        $delegate = new Delegate();
        $database = new Database();
        $database->process($request->withAttribute('config', $config), $delegate);
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
     * Test the middleware delegate.
     *
     * @return void
     */
    public function testFailedRedirect(): void
    {
        $delegate = new Delegate();
        $redirect = new Redirect();
        $response = $redirect->process($this->request, $delegate);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Test a successful redirect request.
     *
     * @return void
     */
    public function testSuccessfulRedirect(): void
    {
        $this->db->query(
            'INSERT INTO redirects (`hash`, `redirect_to`, `count`, `created_at`, `updated_at`)
            VALUES("abcdef", "http://example.com", 0, "2017-10-10", "2017-10-10")'
        );
        $request = $this->request->withUri(new Uri(config()['base_url'] . '/abcdef'));
        $delegate = new Delegate();
        $redirect = new Redirect();
        $response = $redirect->process($request, $delegate);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals('http://example.com', $response->getHeaderLine('Location'));
        $this->assertEquals(1, $this->db->query('SELECT `count` FROM `redirects`')->fetch(\PDO::FETCH_ASSOC)['count']);
    }
}
