<?php

namespace tiFy\Metabox;

use tiFy\Contracts\Metabox\MetaboxDisplayTermInterface;
use tiFy\Contracts\Metabox\MetaboxItemInterface;
use tiFy\Taxonomy\Metadata\Term as TermMeta;

abstract class AbstractMetaboxDisplayTermController
    extends AbstractMetaboxDisplayController
    implements MetaboxDisplayTermInterface
{
    /**
     * CONSTRUCTEUR.
     *
     * @param MetaboxItemInterface $item Instance de l'élément.
     * @param array $attrs Liste des variables passées en arguments.
     *
     * @return void
     */
    public function __construct(MetaboxItemInterface $item, $args = [])
    {
        parent::__construct($item, $args);

        /** @var TermMeta $termMeta */
        $termMeta = app(TermMeta::class);
        foreach ($this->metadatas() as $meta => $single) :
            if (is_numeric($meta)) :
                $meta = (string) $single;
                $single = true;
            endif;

            $termMeta->register($this->getTaxonomy(), $meta, $single);
        endforeach;
    }

    /**
     * {@inheritdoc}
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