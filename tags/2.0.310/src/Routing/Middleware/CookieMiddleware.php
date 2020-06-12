<?php declare(strict_types=1);

namespace tiFy\Routing\Middleware;

use Psr\Http\Message\ResponseInterface as PsrResponse;
use tiFy\Contracts\Routing\Emitter;
use tiFy\Routing\BaseMiddleware;
use tiFy\Http\Response;
use tiFy\Cookie\Cookie;

class CookieMiddleware extends BaseMiddleware
{
    /**
     * @inheritDoc
     */
    public function emit(PsrResponse $psrResponse, Emitter $emitter): PsrResponse
    {
        if (!headers_sent() && ($cookies = Cookie::getQueued())) {
            $response = Response::createFromPsr($psrResponse);

            foreach ($cookies as $cookie) {
                $response->headers->setCookie($cookie);
            }

            $psrResponse = Response::convertToPsr($response);
        }

        return $psrResponse;
    }
}