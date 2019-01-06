<?php declare(strict_types=1);

namespace tiFy\Routing\Strategy;

use League\Route\Strategy\JsonStrategy;
use League\Route\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use tiFy\Contracts\Routing\Route as RouteContract;
use tiFy\Contracts\View\ViewController;
use Zend\Diactoros\Response;

class Json extends JsonStrategy
{
    /**
     * {@inheritdoc}
     */
    public function invokeRouteCallable(Route $route, ServerRequestInterface $request): ResponseInterface
    {
        /** @var RouteContract $route */
        $route->setCurrent();

        $controller = $route->getCallable($this->getContainer());

        $resolved = call_user_func_array($controller, $route->getVars());

        $response = new Response();
        if ($resolved instanceof ViewController) :
            $response->getBody()->write((string)$resolved);
        elseif ($this->isJsonEncodable($resolved)) :
            $body = json_encode($resolved);
            $response = $this->responseFactory->createResponse(200);
            $response->getBody()->write($body);
        endif;

        $response = $this->applyDefaultResponseHeaders($response);

        return $response;
    }
}