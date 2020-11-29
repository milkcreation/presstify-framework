<?php declare(strict_types=1);

namespace tiFy\Template\Templates\FileManager;

use tiFy\Template\Factory\HttpController as BaseHttpController;
use tiFy\Template\Templates\FileManager\Contracts\{Factory, HttpController as HttpControllerContract};

class HttpController extends BaseHttpController implements HttpControllerContract
{
    /**
     * Instance du gabarit d'affichage.
     * @var Factory
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function handleGet()
    {
        $action = $this->factory->request()->input('action');
        $path = $this->factory->request()->input('path');
        $response = null;

        if (method_exists($this, $action)) {
            $response = $this->{$action}($path);
        }

        if (is_null($response)) {
            $response = $this->response('php://memory', 405);
        }

        return $response;
    }
}