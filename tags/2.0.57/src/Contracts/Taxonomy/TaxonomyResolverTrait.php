<?php

namespace tiFy\Contracts\Taxonomy;

interface TaxonomyResolverTrait
{
    /**
     * Récupération de l'instance du controleur de metadonnées de terme.
     *
     * @return TaxonomyTermMeta
     */
    public function term_meta();
}