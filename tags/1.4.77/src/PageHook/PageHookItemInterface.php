<?php

namespace tiFy\PageHook;

use tiFy\Kernel\Item\ItemInterface;

interface PageHookItemInterface extends ItemInterface
{
    /**
     * Récupération de l'identifiant de qualification de la page d'accroche associée.
     *
     * @return int
     */
    public function getId();

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
     * Récupération du permalien de la page d'accroche associée.
     *
     * @return string
     */
    public function getPermalink();

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Vérifie si la page d'affichage courante correspond à la page d'accroche associée.
     *
     * @param null|int|\WP_Post Page d'affichage courante|Identifiant de qualification|Objet post Wordpress à vérifier.
     *
     * @return bool
     */
    public function isCurrent($post = null);
}