<?php

namespace App\Middleware;

use Zend\Diactoros\Response\HtmlResponse;
use App\Models\Redirect as RedirectModel;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;

class Redirect implements MiddlewareInterface
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
        $path = substr($request->getUri()->getPath(), 1);
        $redirect = RedirectModel::where('hash', $path)->first();
        if ($redirect) {
            $redirect->increment('count');
            return new RedirectResponse($redirect->redirect_to, 301);
        }
        return new HtmlResponse(view('404.php'), 404);
    }
}
