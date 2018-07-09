<?php

namespace tiFy\Apps\Layout;

use tiFy\Apps\Layout\LayoutControllerInterface;
use tiFy\Apps\Layout\Db\DbBaseController;
use tiFy\Apps\Layout\Db\DbControllerInterface;
use tiFy\Apps\Layout\Labels\LabelsBaseController;
use tiFy\Apps\Layout\Labels\LabelsControllerInterface;
use tiFy\Apps\Layout\Notice\NoticeCollectionBaseController;
use tiFy\Apps\Layout\Notice\NoticeCollectionInterface;
use tiFy\Apps\Layout\Param\ParamCollectionBaseController;
use tiFy\Apps\Layout\Param\ParamCollectionInterface;
use tiFy\Apps\Layout\Request\RequestBaseController;
use tiFy\Apps\Layout\Request\RequestInterface;
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