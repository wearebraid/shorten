<?php

namespace App\Middleware;

use App\Models\Redirect;
use Zend\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;

class Api implements MiddlewareInterface
{
    /**
     * The request object.
     *
     * @var Psr\Http\Message\ServerRequestInterface
     */
    protected $request;
    /**
     * Return the current request.
     *
     * @return Psr\Http\Message\ServerRequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set the current request object.
     *
     * @return void
     */
    public function setRequest(ServerRequestInterface $request)
    {
        $route = strtoupper($request->getMethod()) . ' ' . strtolower($request->getUri()->getPath());
        $this->request = $request->withAttribute('route', $route);
    }

    /**
     * Checks if the current request is an authorized api request.
     *
     * @return boolean
     */
    public function isApi()
    {
        $this->request = $this->request->withAttribute('isApi', !!$this->request->getHeaderLine('Authorization'));
        return $this->request->getAttribute('isApi');
    }

    /**
     * Check if the current request is authorized for api access.
     *
     * @return boolean
     */
    public function isAuthorized()
    {
        $auth = substr($this->request->getHeaderLine('Authorization'), 7);
        return $this->request->getAttribute('config')['api_secret'] === $auth;
    }

    /**
     * List all redirects.
     *
     * @return void
     */
    public function show()
    {
        return new JsonResponse(Redirect::orderBy('created_at', 'DESC')->get());
    }

    /**
     * Create a new redirect.
     *
     * @return App\Models\Create
     */
    public function create()
    {
        $body = $this->request->getAttribute('body');
        if (isset($body['url'])) {
            return new JsonResponse(Redirect::createUnique($body['url']));
        }
        return new JsonResponse([
            'message' => 'Request must include json body with url property.'
        ], 400);
    }

    /**
     * Respond to the api request.
     *
     * @return Zend\Diactoros\Response\JsonResponse
     */
    public function response()
    {
        if ($this->isAuthorized()) {
            switch ($this->request->getAttribute('route')) {
                case "GET /redirects":
                    return $this->show();
                case "POST /redirects":
                    return $this->create();
                default:
                    return new JsonResponse(['status' => 'No such api endpoint'], 404);
            }
        }
        return new JsonResponse(['message' => 'Not authorized'], 403);
    }

    /**
     * PSR-15 middleware callback
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return Psr\Http\Message\ServerRequestInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $this->setRequest($request);
        return ($this->isApi()) ? $this->response() : $delegate->process($this->getRequest());
    }
}
