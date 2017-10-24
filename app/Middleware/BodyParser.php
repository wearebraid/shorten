<?php

namespace App\Middleware;

use Zend\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;

class BodyParser implements MiddlewareInterface
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
        return $delegate->process(
            $request->withAttribute('body', json_decode($request->getBody(), true))
        );
    }
}
