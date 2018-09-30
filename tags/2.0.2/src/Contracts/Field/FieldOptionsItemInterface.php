<?php

namespace tiFy\Contracts\Field;

use tiFy\Kernel\Item\ItemIteratorInterface;

interface FieldOptionsItemInterface extends ItemIteratorInterface
{
    /**
     * Récupération de la valeur.
     *
     * @return string
     */
    public function getValue();

    /**
     * Vérification si l'élément est un sous-élément.
     *
     * @return bool
     */
    public function hasParent();

    /**
     * Vérification de l'état de désactivation.
     *
     * @return bool
     */
    public function isDisabled();

    /**
     * Vérification s'il s'agit d'un groupe d'options.
     *
     * @return bool
     */
    public function isGroup();

    /**
     * Vérification s'il s'agit d'une option selectionné.
     *
     * @return bool
     */
    public function isSelected();
}