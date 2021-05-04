<?php

declare(strict_types=1);

namespace tiFy\Template\Templates\FileManager;

use League\Route\Http\Exception\MethodNotAllowedException;
use tiFy\Template\Factory\HttpXhrController as BaseHttpXhrController;
use tiFy\Template\Templates\FileManager\Contracts\Factory;
use tiFy\Template\Templates\FileManager\Contracts\HttpXhrController as HttpXhrControllerContract;

class HttpXhrController extends BaseHttpXhrController implements HttpXhrControllerContract
{
    /**
     * Instance du gabarit d'affichage.
     * @var Factory
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function handlePost()
    {
        $action = $this->factory->request()->input('action');
        $path = rawurldecode($this->factory->request()->input('path'));
        $response = null;

        if (method_exists($this, $action)) {
            $response = $this->{$action}($path);
        }

        if (is_null($response)) {
            throw new MethodNotAllowedException();
        }

        return $response;
    }
}