<?php

namespace tiFy\Components\Tools\Checker;

class Checker
{
    /**
     * Vérifie si une chaine de caractères est vide.
     *
     * @param mixed $value Valeur à controler.
     *
     * @return bool
     */
    public function isEmpty($str)
    {
        if (empty($str)) :
            return false;
        endif;

        return true;
    }

    /**
     * Vérifie si une chaine de caractères ne contient que des chiffres.
     *
     * @param mixed $value Valeur à controler.
     *
     * @return bool
     */
    public function isInteger($value)
    {
        if (!preg_match('/^[[:digit:]]*$/', $value)) :
            return false;
        endif;

        return true;
    }

    /**
     * Vérifie si une chaine de caractères ne contient que des lettres.
     *
     * @param mixed $value Valeur à controler.
     *
     * @return bool
     */
    public function isAlpha($value)
    {
        if (!preg_match('/^[[:alpha:]]*$/', $value)) :
            return false;
        endif;

        return true;
    }

    /**
     * Vérifie si une chaine de caractères ne contient que des chiffres et des lettres.
     *
     * @param mixed $value Valeur à controler.
     *
     * @return bool
     */
    public function isAlphaNum($value)
    {
        if (!preg_match('/^[[:alnum:]]*$/', $value)) :
            return false;
        endif;

        return true;
    }

    /**
     * Vérifie si une chaine de caractères est un email valide.
     *
     * @param mixed $value Valeur à controler.
     *
     * @return bool
     */
    public function isEmail($value)
    {
        if (!preg_match('/^[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}$/', $value)) :
            return false;
        endif;

        return true;
    }

    /**
     * Vérifie si une chaine de caractères est une url valide.
     *
     * @param mixed $value Valeur à controler.
     *
     * @return bool
     */
    public function isUrl($value)
    {
        if (!preg_match('@^(https?|ftp)://[^\s/$.?#].[^\s]*$@iS', $value)) :
            return false;
        endif;

        return true;
    }

    /**
     * Vérifie si une chaîne de caractères est une date valide.
     *
     * @param mixed $value Valeur à controler.
     * @param string $format Format de la date à comparer.
     *
     * @return bool
     */
    public function isDate($value, $format = 'd/m/Y')
    {
        switch ($format) :
            default :
                $regex = '^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$';
                break;
        endswitch;

        if (!preg_match('/' . $regex . '/', $value)) :
            return false;
        endif;

        return true;
    }

    /**
     * Vérifie si une chaine de caractères repond à un regex personnalisé.
     *
     * @param mixed $value Valeur à controler.
     * @param string $regex Formule de comparaison au format regex.
     *
     * @return bool
     */
    public function regex($value, $regex)
    {
        if (!preg_match('#' . $regex . '#', $value)) :
            return false;
        endif;

        return true;
    }

    /**
     * Vérifie si une chaine de caractères ne contient un nombre de caractères maximum.
     *
     * @param mixed $value Valeur à controler.
     * @param int $max Nombre maximum d'élément.
     *
     * @return bool
     */
    public function maxLength($value, $max = 0)
    {
        if (strlen($value) > $max) {
            return false;
        }
        return true;
    }

    /**
     * Vérifie si une chaine de caractères contient un nombre de caractères minimum.
     *
     * @param mixed $value Valeur à controler.
     * @param int $min Nombre minimum d'élément.
     *
     * @return bool
     */
    public function minLength($value, $min = 0)
    {
        if (strlen($value) < $min) :
            return false;
        endif;

        return true;
    }

    /**
     * Vérifie si une chaine de caractères contient un nombre de caractères défini.
     *
     * @param mixed $value Valeur à controler.
     * @param int $length Longueur de la chaîne à comparer.
     *
     * @return bool
     */
    public function exactLength($value, $length = 0)
    {
        if (strlen($value) != $length) :
            return false;
        endif;

        return true;
    }

    /**
     * Vérifie si une chaine de caractères contient des caractères spéciaux.
     *
     * @param mixed $value Valeur à controler.
     *
     * @return bool
     */
    public function hasSpecialChars($value)
    {
        if (!preg_match('/^.*(?=.{7,})(?=.*\d)(?=.*[a-z])(?=.*[\W_]).*$/', $value)) :
            return false;
        endif;

        return true;
    }

    /**
     * Vérifie si une chaine de caractères contient des majuscules.
     *
     * @param mixed $value Valeur à controler.
     *
     * @return bool
     */
    public function hasMaj($value)
    {
        if (!preg_match('/^.*(?=.*[A-Z]).*$/', $value)) :
            return false;
        endif;

        return true;
    }

    /**
     * Vérifie si une chaine est un mot de passe valide.
     * @internal Par défaut le mot de passe doit contenir au moins 1 chiffre, 1 minuscule, 1 majuscule et entre 8 et 16 caractères.
     * @param mixed $value Valeur à controler.
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
    public function isValidPassword($value, $args = [])
    {
        $args = array_merge(
            [
                'digit'        => 1,
                'letter'       => 1,
                'maj'          => 1,
                'special_char' => 0,
                'min'          => 8,
                'max'          => 16,
            ],
            $args
        );
        extract($args);

        if ($min && (strlen($value) < (int)$min)) :
            return false;
        endif;
        if ($max && (strlen($value) > (int)$max)) :
            return false;
        endif;

        $regex = "";
        if ($digit) :
            $regex .= "(?=(?:.*\d){" . (int)$digit . ",})";
        endif;
        if ($letter) :
            $regex .= "(?=(?:.*[a-z]){" . (int)$letter . ",})";
        endif;
        if ($maj) :
            $regex .= "(?=(?:.*[A-Z]){" . (int)$maj . ",})";
        endif;
        if ($special_char) :
            $regex .= "(?=(?:.*[!@#$%^&*()\[\]\-_=+{};:,<.>]){" . (int)$special_char . ",})";
        endif;

        if (preg_match('/' . $regex . '/', $value)) :
            return true;
        endif;

        return false;
    }

    /**
     * Vérifie si deux valeurs sont égales.
     *
     * @param mixed $a Valeur 1 à comparer.
     * @param mixed $b Valeur 2 à comparer.
     *
     * @return bool
     */
    public function isEqual($a, $b)
    {
        if ($a != $b) :
            return false;
        endif;

        return true;
    }

    /**
     * Vérifie si deux valeurs sont différentes.
     *
     * @param mixed $a Valeur 1 à comparer.
     * @param mixed $b Valeur 2 à comparer.
     *
     * @return bool
     */
    public function isDifferent($a, $b)
    {
        return !$this->checkerIsEqual($a, $b);
    }
}