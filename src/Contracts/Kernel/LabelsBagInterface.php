<?php

namespace tiFy\Contracts\Kernel;

use tiFy\Contracts\Kernel\ParamsBag;

interface LabelsBagInterface extends ParamsBag
{
    /**
     * Récupération du déterminant de qualification d'une chaîne de caractère.
     *
     * @param string $string Chaîne de caractère à traiter.
     * @param bool $gender Genre de la chaîne de caractère à traiter (false : masculin, true : féminin).
     *
     * @return string
     */
    public function getDeterminant($string, $gender = false);

    /**
     * Récupération du genre.
     *
     * @return bool
     */
    public function getGender();

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération de la forme plurielle.
     *
     * @return string
     */
    public function getPlural();

    /**
     * Récupération de la forme singulière.
     *
     * @return string
     */
    public function getSingular();

    /**
     * Permet de vérifier si la première lettre d'une chaîne de caractère est une voyelle.
     *
     * @param string $string Chaîne de caractère à traiter.
     *
     * @return string
     */
    public function isFirstVowel($string);
}