<?php declare(strict_types=1);

namespace tiFy\Template\Templates\PostListTable;

use tiFy\PostType\PostTypeLabelsBag;
use tiFy\Template\Templates\PostListTable\Contracts\PostListTable;

class Labels extends PostTypeLabelsBag
{
    /**
     * Instance du gabarit d'affichage.
     * @var PostListTable
     */
    protected $factory;

    /**
     * CONSTRUCTEUR.
     *
     * @param PostListTable $factory Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct(PostListTable $factory)
    {
        $this->factory = $factory;

        parent::__construct($factory->name(), $factory->get('labels', []));
    }

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
}