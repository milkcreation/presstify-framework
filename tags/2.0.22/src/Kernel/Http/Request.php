<?php

namespace tiFy\Kernel\Http;

use Illuminate\Http\Request as IlluminateHttpRequest;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\ServerBag;
use Psr\Http\Message\ServerRequestInterface;

class Request extends IlluminateHttpRequest
{
    /**
     * Récupération de la classe de rappel de propriété de la requête globale.
     * @see https://laravel.com/api/5.6/Illuminate/Http/Request.html
     * @see https://symfony.com/doc/current/components/http_foundation.html
     * @see http://api.symfony.com/4.0/Symfony/Component/HttpFoundation/ParameterBag.html
     *
     * @param string $property Propriété de la requête à traiter.
     * $_POST (alias post, request)|$_GET (alias get, query)|$_COOKIE (alias cookie, cookies)|attributes
     * |$_FILES (alias files)|SERVER (alias server)|headers.
     *
     * @return Request|FileBag|HeaderBag|ParameterBag|ServerBag
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
     * Création d'une instance depuis une requête PSR-7.
     *
     * @param ServerRequestInterface $psrRequest Requête PSR
     *
     * @return self|IlluminateHttpRequest
     */
    public function createFromPsr(ServerRequestInterface $psrRequest)
    {
        $request = (new HttpFoundationFactory())->createRequest($psrRequest);

        return self::createFromBase($request);
    }
}