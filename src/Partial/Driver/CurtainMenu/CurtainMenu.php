<?php declare(strict_types=1);

namespace tiFy\Partial\Driver\CurtainMenu;

use tiFy\Contracts\Partial\{CurtainMenu as CurtainMenuContract, PartialDriver as PartialDriverContract};
use tiFy\Partial\PartialDriver;

class CurtainMenu extends PartialDriver implements CurtainMenuContract
{
    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            /**
             * @var array $items Liste des éléments.
             */
            'items'     => [],
            /**
             * @var mixed $selected
             */
            'selected'  => null,
            /**
             * @var string $theme Theme d'affichage. light|dark.
             */
            'theme'     => 'light'
        ]);
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): PartialDriverContract
    {
        parent::parseParams();

        if ($theme = $this->get('theme')) {
            $this->set('attrs.class', trim($this->get('attrs.class') . " CurtainMenu--{$theme}"));
        }

        $this->set('attrs.data-control', 'curtain-menu');

        $this->set('attrs.data-id', $this->getId());

        $this->set('attrs.data-options', []);

        $this->parseItems();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseItems(): PartialDriverContract
    {
        $items = $this->get('items', []);
        if (!$items instanceof CurtainMenuItems) {
            $items = new CurtainMenuItems($items, $this->get('selected'));
        }
        $this->set('items', $items->prepare($this));

        return $this;
    }
}