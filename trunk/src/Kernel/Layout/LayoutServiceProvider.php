<?php

namespace tiFy\Kernel\Layout;

use tiFy\Kernel\Layout\LayoutControllerInterface;
use tiFy\Kernel\Layout\Db\DbBaseController;
use tiFy\Kernel\Layout\Db\DbControllerInterface;
use tiFy\Kernel\Layout\Labels\LabelsBaseController;
use tiFy\Kernel\Layout\Labels\LabelsControllerInterface;
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
    public function boot()
    {
        $db = $this->app->get('db');
        if ($db instanceof DbControllerInterface) :
            $this->providers['db'] = $db;
        endif;

        $labels = $this->get('labels');
        if ($labels instanceof LabelsControllerInterface) :
            $this->providers['labels'] = $labels;
        endif;

        parent::boot();
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'db' => [
                'alias'     => DbControllerInterface::class,
                'concrete'  => DbBaseController::class,
                'singleton' => true,
                'args'      => [$this->app->getName(), []]
            ],
            'labels' => [
                'alias'     => LabelsControllerInterface::class,
                'concrete'  => LabelsBaseController::class,
                'singleton' => true,
                'args'      => [$this->app->getName(), []]
            ],
            'notices' => [
                'alias'     => NoticeCollectionInterface::class,
                'concrete'  => NoticeCollectionBaseController::class,
                'bootable'  => true,
                'singleton' => true,
                'args'      => [$this->app->get('notices', [])]
            ],
            'params' => [
                'alias'     => ParamCollectionInterface::class,
                'concrete'  => ParamCollectionBaseController::class,
                'bootable'  => true,
                'singleton' => true,
                'args'      => [$this->app->get('params', [])]
            ],
            'request' => [
                'alias'     => RequestInterface::class,
                'concrete'  => RequestBaseController::class,
                'bootable'  => true,
                'singleton' => true
            ]
        ];
    }
}