<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

use tiFy\Template\Factory\FactoryLabels;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;

class Labels extends FactoryLabels
{
    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'all_items' => sprintf(
                $this->gender() ? __('Toutes les %s', 'tify') : __('Tous les %s', 'tify'),
                $this->plural()
            ),
            'no_item' => sprintf(
                $this->gender() ? __('Aucune %s trouvée.', 'tify') : __('Aucun %s trouvé.', 'tify'),
                $this->singular()
            ),
            'page_title' => sprintf(
                $this->gender() ? __('Toutes les %s', 'tify') : __('Tous les %s', 'tify'),
                $this->plural()
            ),
            'search_item' => sprintf(
                $this->gender() ? __('Rechercher une %s', 'tify') : __('Rechercher un %s', 'tify'),
                $this->singular()
            )
        ]);
    }
}