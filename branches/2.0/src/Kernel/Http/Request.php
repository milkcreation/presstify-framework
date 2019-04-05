<?php

namespace tiFy\Kernel\Http;

use Illuminate\Http\Request as LaraRequest;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request as SfRequest;
use Psr\Http\Message\ServerRequestInterface;
use tiFy\Contracts\Kernel\Request as RequestContract;

/**
 * Class Request
 * @package tiFy\Kernel\Http
 *
 * @deprecated Utiliser tiFy\Http\Request à la place
 */
class Request extends LaraRequest implements RequestContract
{
    /**
     * Instance du controleur basée sur la requête globale.
     * @var RequestContract
     */
    protected static $global;

    /**
     * {@inheritdoc}
     *
     * @deprecated
     */
    public function getProperty($property = '')
    {
        switch (strtolower($property)) :
            default :
                return $this;
                break;
            case 'post' :
            case 'request' :
                return $this->request;
                break;
            case 'get' :
            case 'query' :
                return $this->query;
                break;
            case 'cookie' :
            case 'cookies' :
                return $this->cookies;
                break;
            case 'attributes' :
                return $this->attributes;
                break;
            case 'files' :
                return $this->files;
                break;
            case 'server' :
                return $this->server;
                break;
            case 'headers' :
                return $this->headers;
                break;
        endswitch;
    }

    /**
     * @inheritdoc
     */
    public function createFromPsr(ServerRequestInterface $psrRequest)
    {
        $request = (new HttpFoundationFactory())->createRequest($psrRequest);

        return self::createFromBase($request);
    }

    /**
     * @inheritdoc
     */
    public function convertToPsr(?SfRequest $sfRequest = null): ServerRequestInterface
    {
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        return $psrHttpFactory->createRequest($sfRequest ?: $this);
    }
}