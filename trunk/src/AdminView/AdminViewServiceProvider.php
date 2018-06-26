<?php

namespace tiFy\AdminView;

use tiFy\AdminView\AdminViewControllerInterface;
use tiFy\AdminView\AdminMenu\AdminMenuBaseController;
use tiFy\AdminView\AdminMenu\AdminMenuInterface;
use tiFy\AdminView\Notice\NoticeCollectionBaseController;
use tiFy\AdminView\Notice\NoticeCollectionInterface;
use tiFy\AdminView\Param\ParamCollectionBaseController;
use tiFy\AdminView\Param\ParamCollectionInterface;
use tiFy\AdminView\Request\RequestBaseController;
use tiFy\AdminView\Request\RequestInterface;
use tiFy\Apps\ServiceProvider\AbstractProviderCollection;
use tiFy\Components\Labels\LabelsBaseController;
use tiFy\Components\Labels\LabelsControllerInterface;

class AdminViewServiceProvider extends AbstractProviderCollection
{
    /**
     * Classe de rappel du controleur de l'interface d'administration associée.
     * @var AdminViewControllerInterface
     */
    protected $app;

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'admin_menu' => [
                'alias'     => AdminMenuInterface::class,
                'concrete'  => $this->app->getConcrete('admin_menu', AdminMenuBaseController::class),
                'bootable'  => true,
                'singleton' => true,
                'args'      => [$this->app->get('admin_menu', [])]
            ],
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