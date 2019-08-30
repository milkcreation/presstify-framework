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