<?php declare(strict_types=1);

namespace tiFy\Template\Templates\PostListTable;

use tiFy\Contracts\Template\FactoryLabels as FactoryLabelsContract;
use tiFy\PostType\PostTypeLabelsBag;
use tiFy\Template\Factory\FactoryAwareTrait;
use tiFy\Template\Templates\PostListTable\Contracts\PostListTable;

class Labels extends PostTypeLabelsBag implements FactoryLabelsContract
{
    use FactoryAwareTrait;

    /**
     * Instance du gabarit d'affichage.
     * @var PostListTable
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'all_items'    => __('Tous les éléments', 'tify'),
            'search_items' => __('Rechercher un élément', 'tify'),
            'no_items'     => __('Aucun élément trouvé.', 'tify'),
            'page_title'   => __('Tous les éléments', 'tify')
        ]);
    }

    public function all_items()
    {
        return sprintf(__('Tous les %s', 'tify'), ucfirst($this->getPlural()));
    }

    public function page_title()
    {
        return sprintf(__('Tous les %s', 'tify'), ucfirst($this->getPlural()));
    }
}