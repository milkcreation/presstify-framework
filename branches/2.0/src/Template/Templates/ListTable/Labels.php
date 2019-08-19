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
                $this->hasGender() ? __('Toutes les %s', 'tify') : __('Tous les %s', 'tify'),
                $this->getPlural()
            ),
            'no_item' => sprintf(
                $this->hasGender() ? __('Aucune %s trouvée.', 'tify') : __('Aucun %s trouvé.', 'tify'),
                $this->getSingular()
            ),
            'page_title' => sprintf(
                $this->hasGender() ? __('Toutes les %s', 'tify') : __('Tous les %s', 'tify'),
                $this->getPlural()
            ),
            'search_item' => sprintf(
                $this->hasGender() ? __('Rechercher une %s', 'tify') : __('Rechercher un %s', 'tify'),
                $this->getSingular()
            )
        ]);
    }
}