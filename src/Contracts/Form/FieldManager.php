<?php declare(strict_types=1);

namespace tiFy\Contracts\Form;

interface FieldManager
{
    /**
     * Récupération des attributs de support.
     *
     * @param string $type Nom de qualification du type de champ.
     *
     * @return array
     */
    public function supports(string $type);
}