<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

use Exception;
use tiFy\Template\Factory\Actions as BaseActions;
use tiFy\Template\Templates\ListTable\Contracts\Actions as ActionsContract;

class Actions extends BaseActions implements ActionsContract
{
    /**
     * Instance du gabarit associé.
     * @var Factory
     */
    protected $factory;

    /**
     * @inheritDoc
     * @todo
     */
    public function doActivate()
    {
        if ($item = $this->factory->builder()->getItem($this->factory->request()->input('id'))) {
            return [
                'success' => true,
                'data'   => $item->getKeyValue()
            ];
        } else {
            throw new Exception(__('Impossible de récupérer l\'élément associé.', 'tify'));
        }
    }

    /**
     * @inheritDoc
     * @todo
     */
    public function doDeactivate()
    {
        if ($item = $this->factory->builder()->getItem($this->factory->request()->input('id'))) {
            return [
                'success' => true,
                'data'   => $item->getKeyValue()
            ];
        } else {
            throw new Exception(__('Impossible de récupérer l\'élément associé.', 'tify'));
        }
    }

    /**
     * @inheritDoc
     */
    public function doDelete()
    {
        if ($this->factory->builder()->deleteItem($this->factory->request()->input('id'))) {
            return $this->controller()->referer();
        } else {
            throw new Exception(__('Impossible de récupérer l\'élément associé.', 'tify'));
        }
    }

    /**
     * @inheritDoc
     * @todo
     */
    public function doDuplicate()
    {
        if ($item = $this->factory->builder()->getItem($this->factory->request()->input('id'))) {
            return [
                'success' => true,
                'data'   => $item->getKeyValue()
            ];
        } else {
            throw new Exception(__('Impossible de récupérer l\'élément associé.', 'tify'));
        }
    }

    /**
     * @inheritDoc
     * @todo
     */
    public function doTrash()
    {
        if ($item = $this->factory->builder()->getItem($this->factory->request()->input('id'))) {
            return [
                'success' => true,
                'data'   => $item->getKeyValue()
            ];
        } else {
            throw new Exception(__('Impossible de récupérer l\'élément associé.', 'tify'));
        }
    }

    /**
     * @inheritDoc
     * @todo
     */
    public function doUntrash()
    {
        if ($item = $this->factory->builder()->getItem($this->factory->request()->input('id'))) {
            return [
                'success' => true,
                'data'   => $item->getKeyValue()
            ];
        } else {
            throw new Exception(__('Impossible de récupérer l\'élément associé.', 'tify'));
        }
    }
}