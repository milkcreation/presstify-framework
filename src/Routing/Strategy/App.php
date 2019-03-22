<?php declare(strict_types=1);

namespace tiFy\Routing\Strategy;

use League\Route\Strategy\ApplicationStrategy;
use League\Route\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Response as SfResponse;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use tiFy\Contracts\Routing\Route as RouteContract;
use tiFy\Contracts\View\ViewController;
use Zend\Diactoros\Response;

class App extends ApplicationStrategy
{
    /**
     * @inheritdoc
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
        elseif ($resolved instanceof ResponseInterface) :
            $response = $resolved;
        elseif ($resolved instanceof SfResponse) :
            $response = (new DiactorosFactory())->createResponse($resolved);
        else :
            $response->getBody()->write((string)$resolved);
        endif;

        $response = $this->applyDefaultResponseHeaders($response);

        return $response;
    }
}