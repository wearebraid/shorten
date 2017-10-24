<?php

namespace App\Middleware;

use Illuminate\Database\Capsule\Manager;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;

class Database implements MiddlewareInterface
{
    /**
     * PSR-15 middleware callback
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return Psr\Http\Message\ServerRequestInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $capsule = new Manager;
        $capsule->addConnection($request->getAttribute('config')['database']);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        return $delegate->process($request->withAttribute('capsule', $capsule));
    }
}
