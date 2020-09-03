<?php declare(strict_types=1);

namespace tiFy\Routing\Middleware;

use Psr\Http\{
    Message\ResponseInterface as PsrResponse,
    Message\ServerRequestInterface as PsrRequest,
    Server\RequestHandlerInterface
};
use tiFy\Routing\BaseMiddleware;
use tiFy\Support\Proxy\Session;

class SessionMiddleware extends BaseMiddleware
{
    /**
     * @inheritDoc
     */
    public function process(PsrRequest $psrRequest, RequestHandlerInterface $handler): PsrResponse
    {
        if (!headers_sent()) {
            if (session_status() == PHP_SESSION_NONE) {
                $_SESSION['flag'] = true;
                Session::start();
            }
        }

        return $handler->handle($psrRequest);
    }
}