<?php

namespace tiFy\Contracts\Field;

use tiFy\Contracts\Kernel\ParamsBag;

interface SelectOption extends ParamsBag
{
    /**
     * Récupération du contenu de la balise.
     *
     * @return string
     */
    public function getContent();

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération de la valeur.
     *
     * @return string
     */
    public function getValue();

    /**
     * Récupération du groupe parent.
     *
     * @return string
     */
    public function getParent();

    /**
     * Vérification d'existance d'un groupe parent.
     *
     * @return boolean
     */
    public function hasParent();

    /**
     * Vérifie si l'option est désactivée.
     *
     * @return boolean
     */
    public function isDisabled();

    /**
     * Vérifie si l'option est un groupe.
     *
     * @return boolean
     */
    public function isGroup();

    /**
     * Vérifie si l'option est sélectionnée.
     *
     * @return boolean
     */
    public function isSelected();

    /**
     * Définition du niveau de profondeur.
     *
     * @return $this
     */
    public function setDepth($depth = 0);

    /**
     * Balise de fermeture.
     *
     * @return string
     */
    public function tagClose();

    /**
     * Contenu de la balise.
     *
     * @return string
     */
    public function tagContent();

    /**
     * Balise d'ouverture.
     *
     * @return string
     */
    public function tagOpen();
}