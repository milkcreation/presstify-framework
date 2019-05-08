<?php declare(strict_types=1);

namespace tiFy\Routing\Strategy;

use League\Route\Strategy\ApplicationStrategy;
use League\Route\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Response as SfResponse;
use tiFy\Contracts\Routing\Route as RouteContract;
use tiFy\Contracts\View\ViewController;
use tiFy\Http\Response;
use Zend\Diactoros\Response as PsrResponse;

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

        $args = array_values($route->getVars());
        array_push($args, $request);
        $resolved = $controller(...$args);

        $psrResponse = new PsrResponse();
        if ($resolved instanceof ViewController) {
            $psrResponse->getBody()->write((string)$resolved);
        } elseif ($resolved instanceof ResponseInterface) {
            $psrResponse = $resolved;
        } elseif ($resolved instanceof SfResponse) {
            $psrResponse = Response::convertToPsr($resolved);
        } else {
            $psrResponse->getBody()->write((string)$resolved);
        }

        return $this->applyDefaultResponseHeaders($psrResponse);
    }
}