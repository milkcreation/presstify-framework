<?php

namespace tiFy\Template\Templates\PostListTable\Labels;

use tiFy\PostType\PostTypeLabelsBag;
use tiFy\Template\Templates\PostListTable\Contracts\PostListTable;

class Labels extends PostTypeLabelsBag
{
    /**
     * Instance de la disposition.
     * @var PostListTable
     */
    protected $template;

    /**
     * CONSTRUCTEUR.
     *
     * @param string Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     * @param PostListTable $template Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct($name, $attrs, PostListTable $template)
    {
        $this->template = $template;

        parent::__construct($name, $attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return array_merge(
            [
                'all_items'    => __('Tous les éléments', 'tify'),
                'search_items' => __('Rechercher un élément', 'tify'),
                'no_items'     => __('Aucun élément trouvé.', 'tify'),
                'page_title'   => __('Tous les éléments', 'tify')
            ],
            parent::defaults()
        );
    }
}