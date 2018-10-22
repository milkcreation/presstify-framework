<?php

namespace tiFy\Contracts\Kernel;

interface Validator
{
    /**
     * Vérifie si une valeur contient un nombre de caractères défini.
     *
     * @param mixed $value Valeur à vérifier.
     * @param int $length Longueur de la chaîne à comparer.
     *
     * @return bool
     */
    public function exactLength($value, $length = 0);

    /**
     * Vérifie si une valeur contient des caractères spéciaux.
     *
     * @param mixed $value Valeur à vérifier.
     *
     * @return bool
     */
    public function hasSpecialChars($value);

    /**
     * Vérifie si une valeur contient des majuscules.
     *
     * @param mixed $value Valeur à vérifier.
     *
     * @return bool
     */
    public function hasMaj($value);

    /**
     * Vérifie si une valeur ne contient que des lettres.
     *
     * @param mixed $value Valeur à vérifier.
     *
     * @return bool
     */
    public function isAlpha($value);

    /**
     * Vérifie si une valeur ne contient que des chiffres et des lettres.
     *
     * @param mixed $value Valeur à vérifier.
     *
     * @return bool
     */
    public function isAlphaNum($value);

    /**
     * Vérifie si une valeur est une date valide.
     *
     * @param mixed $value Valeur à vérifier.
     * @param string $format Format de la date à comparer.
     *
     * @return bool
     */
    public function isDate($value, $format = 'd/m/Y');

    /**
     * Vérifie si deux valeurs sont différentes.
     *
     * @param mixed $a Valeur 1 à comparer.
     * @param mixed $b Valeur 2 à comparer.
     *
     * @return bool
     */
    public function isDifferent($a, $b);

    /**
     * Vérifie si une valeur est un email valide.
     *
     * @param mixed $value Valeur à vérifier.
     *
     * @return bool
     */
    public function isEmail($value);

    /**
     * Vérifie si une valeur est vide.
     *
     * @param mixed $value Valeur à vérifier.
     *
     * @return bool
     */
    public function isEmpty($value);

    /**
     * Vérifie si deux valeurs sont égales.
     *
     * @param mixed $a Valeur 1 à comparer.
     * @param mixed $b Valeur 2 à comparer.
     *
     * @return bool
     */
    public function isEqual($a, $b);

    /**
     * Vérifie si une valeur ne contient que des chiffres.
     *
     * @param mixed $value Valeur à vérifier.
     *
     * @return bool
     */
    public function isInteger($value);

    /**
     * Vérifie si une valeur est une url valide.
     *
     * @param mixed $value Valeur à vérifier.
     *
     * @return bool
     */
    public function isUrl($value);

    /**
     * Vérifie si une valeur est un mot de passe valide.
     * @internal Par défaut le mot de passe doit contenir au moins 1 chiffre, 1 minuscule, 1 majuscule et entre 8 et 16 caractères.
     *
     * @param mixed $value Valeur à vérifier.
     * @param array $args {
     *      Liste des arguments de contrôle.
     *
     *      @var int $digit Nombre de chiffres requis.
     *      @var int $letter Nombre de lettres requises.
     *      @var int $maj Nombre de majuscules requises.
     *      @var int $special_char Nombre de caractères spéciaux requis.
     *      @var int $min Nombre de caractère minimum.
     *      @var int $min Nombre de caractère maximum.
     * }
     *
     * @return bool
     */
    public function isValidPassword($value, $args = []);

    /**
     * Vérifie si une valeur  ne contient un nombre de caractères maximum.
     *
     * @param mixed $value Valeur à vérifier.
     * @param int $max Nombre maximum d'élément.
     *
     * @return bool
     */
    public function maxLength($value, $max = 0);

    /**
     * Vérifie si une valeur  contient un nombre de caractères minimum.
     *
     * @param mixed $value Valeur à vérifier.
     * @param int $min Nombre minimum d'élément.
     *
     * @return bool
     */
    public function minLength($value, $min = 0);

    /**
     * Vérifie si une valeur n'est pas vide.
     *
     * @param mixed $value Valeur à vérifier.
     *
     * @return bool
     */
    public function notEmpty($value);

    /**
     * Vérifie si une valeur  repond à un regex personnalisé.
     *
     * @param mixed $value Valeur à vérifier.
     * @param string $regex Formule de comparaison au format regex.
     *
     * @return bool
     */
    public function regex($value, $regex);
}