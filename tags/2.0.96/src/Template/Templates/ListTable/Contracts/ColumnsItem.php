<?php

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;

interface ColumnsItem extends ParamsBag
{
    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString();

    /**
     * Récupération du contenu d'affichage.
     *
     * @return string
     */
    public function content();

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Récupération du gabarit d'affichage du contenu de la colonne.
     *
     * @param string $default Valeur de retour par défaut.
     *
     * @return string
     */
    public function getTemplate($default = 'tbody-col');

    /**
     * Récupération de l'entête au format HTML.
     *
     * @param bool $with_id Activation de l'id de la balise HTML.
     *
     * @return string
     */
    public function header($with_id = true);

    /**
     * Vérification de maquage de la colonne.
     *
     * @return bool
     */
    public function isHidden();

    /**
     * Vérifie si la colonne est la colonne principale.
     *
     * @return bool
     */
    public function isPrimary();

    /**
     * Vérifie si la colonne peut être ordonnancée.
     *
     * @return bool
     */
    public function isSortable();

    /**
     * Affichage
     *
     * @return string
     */
    public function render();
}