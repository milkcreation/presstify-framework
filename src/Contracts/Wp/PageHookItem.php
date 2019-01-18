<?php

namespace tiFy\Contracts\Wp;

use tiFy\Contracts\Kernel\ParamsBag;

interface PageHookItem extends ParamsBag
{
    /**
     * Vérification d'existance du post associé.
     *
     * @return boolean
     */
    public function exists();

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération du type d'objet Wordpress.
     *
     * @return string
     */
    public function getObjectType();

    /**
     * Récupération du nom de qualification de l'objet Wordpress
     *
     * @return string
     */
    public function getObjectName();

    /**
     * Récupération du nom de qualification d'enregistrement en base de donnée.
     *
     * @return string
     */
    public function getOptionName();

    /**
     * Vérifie si la page d'affichage courante correspond à la page d'accroche associée.
     *
     * @param null|int|\WP_Post Page d'affichage courante|Identifiant de qualification|Objet post Wordpress à vérifier.
     *
     * @return bool
     */
    public function is($post = null);

    /**
     * Récupération de l'instance du post associé.
     *
     * @return Post
     */
    public function post();
}