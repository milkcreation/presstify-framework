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
    public function defaults()
    {
        return array_merge(parent::defaults(), [
            'all_items'    => __('Tous les éléments', 'tify'),
            'search_items' => __('Rechercher un élément', 'tify'),
            'no_items'     => __('Aucun élément trouvé.', 'tify'),
            'page_title'   => __('Tous les éléments', 'tify')
        ]);
    }
}