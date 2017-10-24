<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    /**
     * Includes the functions file manually.
     *
     * @return void
     */
    public function setUp()
    {
        require_once __DIR__ . '/../app/helpers.php';
    }

    /**
     * Tests the view importer.
     *
     * @return void
     */
    public function testView(): void
    {
        $hash = bin2hex(openssl_random_pseudo_bytes(16));
        $value = view('/../tests/samples/view.php', ['hash' => $hash]);
        $this->assertEquals('Test value: ' . $hash, $value);
    }

    /**
     * tests the configuration importer.
     *
     * @return void
     */
    public function testConfig(): void
    {
        $hash = '9575d687c61ce66fc190cd2bed464cef';
        $value = config('/tests/samples/config.php');
        $this->assertEquals($hash, $value['api_secret']);
    }
}
