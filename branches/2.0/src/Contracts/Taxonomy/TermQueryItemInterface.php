<?php

namespace tiFy\Contracts\Taxonomy;

interface TermQueryItemInterface
{
    /**
     * Récupération de l'identifiant de qualification Wordpress du terme
     *
     * @return int
     */
    public function getId();

    /**
     * Récupération de l'intitulé de qualification
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération du nom de qualification Wordpress du terme
     *
     * @return string
     */
    public function getSlug();

    /**
     * Récupération de la taxonomie relative
     *
     * @return string
     */
    public function getTaxonomy();

    /**
     * Récupération de l'object Post Wordpress associé
     *
     * @return \WP_Term
     */
    public function getTerm();
}