<?php declare(strict_types=1);

namespace tiFy\Template\Factory;

use Psr\Http\Message\ServerRequestInterface;
use tiFy\Contracts\Template\FactoryHttpXhrController as FactoryHttpXhrControllerContract;
use League\Route\Http\Exception\MethodNotAllowedException;

class FactoryHttpXhrController extends FactoryHttpController implements FactoryHttpXhrControllerContract
{
    /**
     * @inheritDoc
     */
    public function __invoke(ServerRequestInterface $psrRequest)
    {
        $method = strtolower($psrRequest->getMethod());
        $response = null;

        if (method_exists($this, $method)) {
            $this->factory->prepare();
            $response = $this->{$method}($psrRequest);
        }

        if (is_null($response)) {
            throw new MethodNotAllowedException();
        }

        return $response;
    }
}