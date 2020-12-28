<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers;

use Exception;
use tiFy\Partial\Drivers\Tab\TabCollection;
use tiFy\Partial\Drivers\Tab\TabCollectionInterface;
use tiFy\Partial\Drivers\Tab\TabView;
use tiFy\Partial\PartialDriver;
use tiFy\Partial\PartialDriverInterface;
use tiFy\Support\Proxy\Url;
use tiFy\Support\Proxy\Request;
use tiFy\Support\Proxy\Session;

class TabDriver extends PartialDriver implements TabDriverInterface
{
    /**
     * Collection des éléments déclaré.
     * @var TabCollectionInterface
     */
    private $tabCollection;

    /**
     * @inheritDoc
     */
    public function addItem($def): TabDriverInterface
    {
        $this->getTabCollection()->add($def);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            /**
             * @var string|null $active Nom de qualification de l'élément actif.
             */
            'active'   => null,
            /**
             * @var array $items {
             * Liste des onglets de navigation.
             * @type string $name Nom de qualification.
             * @type string $parent Nom de qualification de l'élément parent.
             * @type string|callable $content
             * @type int $position Ordre d'affichage dans le
             * }
             */
            'items'    => [],
            /**
             * @var array $rotation Rotation des styles d'onglet. left|top|default|pills.
             */
            'rotation' => [],
            /**
             * Activation du traitement de la requête HTML XHR
             */
            'ajax'     => true,
        ]);
    }

    /**
     * Récupération de l'élément actif.
     *
     * @return string
     */
    protected function getActive(): string
    {
        if (!$active = $this->get('active')) {
            $sessionName = md5(Url::current()->path() . $this->getId());
            if ($this->get('ajax') && ($store = Session::registerStore($sessionName))) {
                $active = $store->get('active', '');
                $this->set('attrs.data-options.ajax.data.session', $sessionName);
            }
        }

        return $active;
    }

    /**
     * @inheritDoc
     */
    public function getTabCollection(): TabCollectionInterface
    {
        if (is_null($this->tabCollection)) {
            $this->tabCollection = (new TabCollection([]))->setTabManager($this);
        }

        return $this->tabCollection;
    }

    /**
     * @inheritDoc
     */
    public function getTabStyle(int $depth = 0): string
    {
        return $this->get("rotation.{$depth}") ?: 'default';
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): PartialDriverInterface
    {
        parent::parseParams();

        $items = $this->pull('items', []);

        if ($items instanceof TabCollectionInterface) {
            $this->setTabCollection($items);
        } elseif (is_array($items)) {
            foreach ($items as $item) {
                $this->addItem($item);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        if ($ajax = $this->get('ajax', false)) {
            $defaultsAjax = [
                'data'     => [],
                'dataType' => 'json',
                'method'   => 'post',
                'url'      => $this->partialManager()->getXhrRouteUrl('tab'),
            ];
            $this->set('attrs.data-options.ajax', is_array($ajax) ? array_merge($defaultsAjax, $ajax) : $defaultsAjax);
        }

        $this->set([
            'attrs.data-control' => 'tab',
            'attrs.data-options.active' => $this->getActive() ?: ''
        ]);

        try {
            $items = $this->getTabCollection()->boot()->getGrouped();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        $this->set(compact('items'));

        return $this->view('index', $this->all());
    }

    /**
     * @inheritDoc
     */
    public function setTabCollection(TabCollectionInterface $tabCollection): TabDriverInterface
    {
        $this->tabCollection = $tabCollection->setTabManager($this);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function view(?string $view = null, $data = [])
    {
        if (is_null($this->viewEngine)) {
            $viewEngine = parent::view();
            $viewEngine->setFactory(TabView::class);
        }

        return parent::view($view, $data);
    }

    /**
     * @inheritDoc
     */
    public function xhrResponse(...$args): array
    {
        if (($sessionName = Request::input('session')) && $store = Session::registerStore($sessionName)) {
            $store->put('active', Request::input('active'));

            return ['success' => true];
        } else {
            return ['success' => false];
        }
    }
}