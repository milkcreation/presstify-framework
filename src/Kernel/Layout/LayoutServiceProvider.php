<?php

namespace tiFy\Kernel\Layout;

use tiFy\Kernel\Layout\LayoutControllerInterface;
use tiFy\Kernel\Layout\Notice\NoticeCollectionBaseController;
use tiFy\Kernel\Layout\Notice\NoticeCollectionInterface;
use tiFy\Kernel\Layout\Param\ParamCollectionBaseController;
use tiFy\Kernel\Layout\Param\ParamCollectionInterface;
use tiFy\Kernel\Layout\Request\RequestBaseController;
use tiFy\Kernel\Layout\Request\RequestInterface;
use tiFy\Apps\ServiceProvider\AbstractProviderCollection;

class LayoutServiceProvider extends AbstractProviderCollection
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutControllerInterface
     */
    protected $app;

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'notices' => [
                'alias'     => NoticeCollectionInterface::class,
                'concrete'  => $this->app->getConcrete('notices', NoticeCollectionBaseController::class),
                'bootable'  => true,
                'singleton' => true,
                'args'      => [$this->app->get('notices', [])]
            ],
            'params' => [
                'alias'     => ParamCollectionInterface::class,
                'concrete'  => $this->app->getConcrete('params', ParamCollectionBaseController::class),
                'bootable'  => true,
                'singleton' => true,
                'args'      => [$this->app->get('params', [])]
            ],
            'request' => [
                'alias'     => RequestInterface::class,
                'concrete'  => $this->app->getConcrete('request', RequestBaseController::class),
                'bootable'  => true,
                'singleton' => true
            ]
        ];
    }
}