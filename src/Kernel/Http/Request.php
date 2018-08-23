<?php

namespace tiFy\Kernel\Http;

use Illuminate\Http\Request as IlluminateHttpRequest;

class Request extends IlluminateHttpRequest
{
    /**
     * {@inheritdoc}
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
}