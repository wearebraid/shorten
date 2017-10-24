<?php

namespace tests\mocks;

use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;

class Delegate implements DelegateInterface
{
    /**
     * A store of the request that was passed in.
     *
     * @var Psr\Http\Message\ServerRequestInterface
     */
    protected $request;

    /**
     * Process method to conform to the delegate interface.
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function process(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Get the request that was processed.
     *
     * @return void
     */
    public function getRequest()
    {
        return $this->request;
    }
}
