<?php

namespace tiFy\Components\Labels;

interface LabelsControllerInterface
{
    /**
     * Récupération de la liste des attributs définis.
     *
     * @param string $key Clé d'index de qualification de l'attribut.
     * @param string $default Valeur de retour par défaut.
     *
     * @return string
     */
    public function all();

    /**
     * Récupération de la valeur d'un attribut défini.
     *
     * @param string $key Clé d'index de qualification de l'attribut.
     * @param string $default Valeur de retour par défaut.
     *
     * @return string
     */
    public function get($key, $default = '');

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
     * Définition d'un attribut.
     *
     * @param string $key Clé d'index de qualification de l'attribut.
     * @param mixed $value Valeur de l'attribut.
     *
     * @return $this
     */
    public function set($key, $value);

    /**
     * Permet de vérifier si la première lettre d'une chaîne de caractère est une voyelle.
     *
     * @param string $string Chaîne de caractère à traiter.
     *
     * @return string
     */
    public function isFirstVowel($string);

    /**
     * Récupération du déterminant de qualification d'une chaîne de caractère.
     *
     * @param string $string Chaîne de caractère à traiter.
     * @param bool $gender Genre de la chaîne de caractère à traiter (false : masculin, true : féminin).
     *
     * @return string
     */
    public function getDeterminant($string, $gender = false);
}