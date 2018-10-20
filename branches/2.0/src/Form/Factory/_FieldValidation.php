<?php

namespace tiFy\Form\Fields;

use tiFy\Form\AbstractCommonDependency;
use tiFy\Form\Fields\FieldItemController;
use tiFy\Form\Forms\FormItemController;
use tiFy\Components\Tools\Checker\CheckerTrait;

class FieldValidation extends AbstractCommonDependency
{
    use CheckerTrait;

    /**
     * Classe de rappel du controleur de champ associé.
     * @var Field
     */
    protected $field;

    /**
     * Cartographie des alias de fonction de contrôle d'intégrité
     * @var array
     */
    protected $alias = [
        // Vérifie si une chaine de caractères est vide
        'is_empty'           => 'checkerIsEmpty',
        // Vérifie si une chaine de caractères ne contient que des chiffres
        'is_integer'         => 'checkerIsInteger',
        // Vérifie si une chaine de caractères ne contient que des lettres
        'is_alpha'           => 'checkerIsAlpha',
        // Vérifie si une chaine de caractères ne contient que des chiffres et des lettres (spéciale dédicace à Bertrand Renard)
        'is_alphanum'        => 'checkerIsAlphaNum',
        // Vérifie si une chaine de caractères est un email valide
        'is_email'           => 'checkerIsEmail',
        // Vérifie si une chaine de caractères est une url valide
        'is_url'             => 'checkerIsUrl',
        // Vérifie si une chaîne de caractères est une date
        'is_date'            => 'checkerIsDate',
        // Vérifie si une chaine de caractères repond à un regex personnalisé
        'check_regex'        => 'checkerRegex',
        // Vérifie si une chaine de caractères contient un nombre de caractères maximum
        'check_max_length'   => 'checkerMaxLength',
        // Vérifie si une chaine de caractères contient un nombre de caractères minimum
        'check_min_length'   => 'checkerMinLength',
        // Vérifie si une chaine de caractères contient un nombre de caractères défini
        'check_equal_length' => 'checkerExactLength',
        // Vérifie si une chaine de caractères contient des caractères spéciaux
        'check_specialchars' => 'checkerHasSpecialChars',
        // Vérifie si une chaine de caractères contient des majuscules
        'check_maj'          => 'checkerHasMaj',
        // Vérifie si la valeur d'un champ est un mot de passe valide
        'is_valid_password'  => 'checkerIsValidPassword',
        // Vérifie si la valeur d'un champ est égale à une valeur donnée
        'is_equal'           => 'checkerIsEqual',
        // Vérifie si la valeur d'un champ est différente d'une valeur donnée
        'is_diff'            => 'checkerIsDifferent',
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param FieldItemController $field Classe de rappel du controleur de champ associé.
     *
     * @return void
     */
    public function __construct(FieldItemController $field)
    {
        $this->field = $field;

        parent::__construct($this->field->getForm());
    }

    /**
     * Appel d'un controle d'intégrité.
     *
     * @param mixed $value Valeur à contrôler.
     * @param string|callable $cb Fonction de traitement de vérification.
     * @param array $args Liste des variables passées en argument.
     *
     * @return bool
     */
    public function check($value, $cb, $args = [])
    {
        $valid = false;
        array_unshift($args, $value);

        if (is_string($cb)) :
            $cb = !isset($this->alias[$cb]) ? $cb : $this->alias[$cb];
            if (is_callable([$this, $cb])) :
                $valid = call_user_func_array([$this, $cb], $args);
            elseif (function_exists($cb)) :
                $valid = call_user_func_array($cb, $args);
            endif;
        elseif (is_callable($cb)) :
            $valid = call_user_func_array($cb, $args);
        endif;

        return $valid;
    }

    /**
     * Méthode de controle par défaut.
     *
     * @return bool
     */
    public function __return_true($value)
    {
        return true;
    }

    /**
     * Compare deux chaînes de caractères.
     * @internal ex. mot de passe <> confirmation mot de passe
     *
     * @param mixed $value Valeur du champ courant à comparer.
     * @param string $slug Identifiant de qualification du champ à comparer.
     *
     * @return bool
     */
    public function compare($value, $slug)
    {
        if ($field = $this->getField($slug)) :
            $value2 = $field->getValue(true);
        else :
            return false;
        endif;

        if ($value !== $value2) :
            return false;
        endif;

        return true;
    }
}