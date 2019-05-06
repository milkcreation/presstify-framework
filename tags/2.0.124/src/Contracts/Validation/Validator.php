<?php

namespace tiFy\Contracts\Validation;

interface Validator
{
    /**
     * Vérifie si une valeur contient un nombre de caractères défini.
     *
     * @param mixed $value Valeur à vérifier.
     * @param int $length Longueur de la chaîne à comparer.
     *
     * @return boolean
     */
    public function exactLength($value, int $length = 0): bool;

    /**
     * Vérifie si une valeur contient des caractères spéciaux.
     *
     * @param mixed $value Valeur à vérifier.
     *
     * @return boolean
     */
    public function hasSpecialChars($value): bool;

    /**
     * Vérifie si une valeur contient des majuscules.
     *
     * @param mixed $value Valeur à vérifier.
     *
     * @return boolean
     */
    public function hasMaj($value): bool;

    /**
     * Vérifie si une valeur ne contient que des lettres.
     *
     * @param mixed $value Valeur à vérifier.
     *
     * @return boolean
     */
    public function isAlpha($value): bool;

    /**
     * Vérifie si une valeur ne contient que des chiffres et des lettres.
     *
     * @param mixed $value Valeur à vérifier.
     *
     * @return boolean
     */
    public function isAlphaNum($value): bool;

    /**
     * Vérifie si une valeur est une date valide.
     *
     * @param mixed $value Valeur à vérifier.
     * @param string $format Format de la date à comparer.
     *
     * @return boolean
     */
    public function isDate($value, string $format = 'd/m/Y'): bool;

    /**
     * Vérifie si deux valeurs sont différentes.
     *
     * @param mixed $a Valeur 1 à comparer.
     * @param mixed $b Valeur 2 à comparer.
     *
     * @return boolean
     */
    public function isDifferent($a, $b): bool;

    /**
     * Vérifie si une valeur est un email valide.
     *
     * @param mixed $value Valeur à vérifier.
     *
     * @return boolean
     */
    public function isEmail($value): bool;

    /**
     * Vérifie si une valeur est vide.
     *
     * @param mixed $value Valeur à vérifier.
     *
     * @return boolean
     */
    public function isEmpty($value): bool;

    /**
     * Vérifie si deux valeurs sont égales.
     *
     * @param mixed $a Valeur 1 à comparer.
     * @param mixed $b Valeur 2 à comparer.
     *
     * @return boolean
     */
    public function isEqual($a, $b): bool;

    /**
     * Vérifie si une valeur ne contient que des chiffres.
     *
     * @param mixed $value Valeur à vérifier.
     *
     * @return boolean
     */
    public function isInteger($value): bool;

    /**
     * Vérifie si une valeur est une url valide.
     *
     * @param mixed $value Valeur à vérifier.
     *
     * @return boolean
     */
    public function isUrl($value): bool;

    /**
     * Vérifie si une valeur  ne contient un nombre de caractères maximum.
     *
     * @param mixed $value Valeur à vérifier.
     * @param int $max Nombre maximum d'élément.
     *
     * @return boolean
     */
    public function maxLength($value, int $max = 0): bool;

    /**
     * Vérifie si une valeur  contient un nombre de caractères minimum.
     *
     * @param mixed $value Valeur à vérifier.
     * @param int $min Nombre minimum d'élément.
     *
     * @return boolean
     */
    public function minLength($value, int $min = 0): bool;

    /**
     * Vérifie si une valeur n'est pas vide.
     *
     * @param mixed $value Valeur à vérifier.
     *
     * @return boolean
     */
    public function notEmpty($value): bool;

    /**
     * Vérifie si une valeur est un mot de passe valide.
     * {@internal Par défaut le mot de passe doit contenir au moins 1 chiffre, 1 minuscule, 1 majuscule et entre 8 et 16
     * caractères.}
     *
     * @param mixed $value Valeur à vérifier.
     * @param array $args {
     *      Liste des arguments de contrôle.
     *
     *      @var int $digit Nombre de chiffres requis.
     *      @var int $letter Nombre de lettres requises.
     *      @var int $maj Nombre de majuscules requises.
     *      @var int $max Nombre de caractère maximum.
     *      @var int $min Nombre de caractère minimum.
     *      @var int $special Nombre de caractères spéciaux requis.
     * }
     *
     * @return boolean
     */
    public function validPassword($value, array $args = []): bool;

    /**
     * Vérifie si une valeur  repond à un regex personnalisé.
     *
     * @param mixed $value Valeur à vérifier.
     * @param string $regex Formule de comparaison au format regex.
     *
     * @return boolean
     */
    public function regex($value, string $regex): bool;
}