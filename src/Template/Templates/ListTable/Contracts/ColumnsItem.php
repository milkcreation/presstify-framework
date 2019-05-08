<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Support\ParamsBag;

interface ColumnsItem extends ParamsBag
{
    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Attributs HTML de la balise d'encapsulation de la cellule.
     *
     * @return string
     */
    public function cellAttrs(): string;

    /**
     * Récupération du contenu d'affichage.
     *
     * @return string
     */
    public function content(): string;

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Récupération du gabarit d'affichage du contenu de la colonne.
     *
     * @param string $default Valeur de retour par défaut.
     *
     * @return string
     */
    public function getTemplate(string $default = 'tbody-col'): string;

    /**
     * Récupération de l'entête au format HTML.
     *
     * @param boolean $with_id Activation de l'id de la balise HTML.
     *
     * @return string
     */
    public function header(bool $with_id = true): string;

    /**
     * Vérification de maquage de la colonne.
     *
     * @return boolean
     */
    public function isHidden(): bool;

    /**
     * Vérifie si la colonne est la colonne principale.
     *
     * @return boolean
     */
    public function isPrimary(): bool;

    /**
     * Vérifie si la colonne peut être ordonnancée.
     *
     * @return boolean
     */
    public function isSortable(): bool;

    /**
     * Vérification d'affichage de la colonne.
     *
     * @return boolean
     */
    public function isVisible(): bool;

    /**
     * {@inheritdoc}
     *
     * @return static
     */
    public function parse();

    /**
     * Affichage
     *
     * @return string
     */
    public function render(): string;
}