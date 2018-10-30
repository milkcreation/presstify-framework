<?php

namespace tiFy\Contracts\Taxonomy;

use tiFy\Contracts\Kernel\ParamsBagInterface;

interface TermQueryItem extends ParamsBagInterface
{
    /**
     * Récupération de la description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Récupération de l'identifiant de qualification Wordpress du terme.
     *
     * @return int
     */
    public function getId();

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération du nom de qualification Wordpress du terme.
     *
     * @return string
     */
    public function getSlug();

    /**
     * Récupération de la taxonomie relative.
     *
     * @return string
     */
    public function getTaxonomy();

    /**
     * Récupération de l'object Terme Wordpress associé.
     *
     * @return \WP_Term
     */
    public function getTerm();
}