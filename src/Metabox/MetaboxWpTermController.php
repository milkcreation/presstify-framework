<?php

namespace tiFy\Metabox;

use tiFy\Contracts\Metabox\MetaboxWpTermController as MetaboxWpTermControllerContract;
use tiFy\Contracts\Metabox\MetaboxFactory;
use WP_Term;

abstract class MetaboxWpTermController extends MetaboxController implements MetaboxWpTermControllerContract
{
    /**
     * CONSTRUCTEUR.
     *
     * @param MetaboxFactory $item Instance de l'élément.
     * @param array $attrs Liste des variables passées en arguments.
     *
     * @return void
     */
    public function __construct(MetaboxFactory $item, $args = [])
    {
        parent::__construct($item, $args);

        foreach ($this->metadatas() as $meta => $single) :
            if (is_numeric($meta)) :
                $meta = (string) $single;
                $single = true;
            endif;

            taxonomy()->term_meta()->register($this->getTaxonomy(), $meta, $single);
        endforeach;
    }

    /**
     * {@inheritDoc}
     *
     * @param WP_Term $term
     */
    public function content($term = null, $taxonomy = null, $args = null)
    {
        return parent::content($term, $taxonomy, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxonomy()
    {
        return $this->getObjectName();
    }

    /**
     * {@inheritdoc}
     */
    public function header($term = null, $taxonomy = null, $args = null)
    {
        return parent::header($term, $taxonomy, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function metadatas()
    {
        return [];
    }
}