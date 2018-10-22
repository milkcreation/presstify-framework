<?php

namespace tiFy\Form\Factory;

use Illuminate\Support\Str;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Form\Factory\ResolverTrait;

class Validation
{
    use ResolverTrait;

    /**
     * Cartographie des alias de fonction de contrôle d'intégrité
     * @var array
     */
    protected $alias = [
        // Vérifie si une valeur contient un nombre exact de caractères défini.
        'exact-length',
        // Vérifie si une valeur contient des caractères spéciaux.
        'has-special-chars',
        // Vérifie si une valeur contient des majuscules.
        'has-maj',
        // Vérifie si une valeur ne contient que des lettres.
        'is-alpha',
        // Vérifie si une valeur ne contient que des chiffres et des lettres (spéciale dédicace à Bertrand Renard).
        'is-alphanum',
        // Vérifie si une valeur est différente d'une valeur donnée.
        'is-diff',
        // Vérifie si une valeur est un email valide.
        'is-email',
        // Vérifie si une valeur est vide.
        'is-empty',
        // Vérifie si une valeur est égale à une valeur donnée.
        'is-equal',
        // Vérifie si une valeur ne contient que des chiffres.
        'is-integer',
        // Vérifie si une valeur est une url valide.
        'is-url',
        // Vérifie si une valeur est une date.
        'is-date',
        // Vérifie si une valeur n'atteint pas un nombre maximum de caractères.
        'max-length',
        // Vérifie si une valeur contient un  nombre minimun un nombre de caractères.
        'min-length',
        // Vérifie si une valeur n'est pas vide.
        'not-empty',
        // Vérifie si une valeur repond au critères d'une chaîne regex.
        'regex',
        // Vérifie si la valeur est un mot de passe valide.
        'valid-password',
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param FormFactory $form Instance du contrôleur de formulaire.
     *
     * @return void
     */
    public function __construct(FormFactory $form)
    {
        $this->form = $form;
    }

    /**
     * Appel d'un test d'intégrité de valeur.
     *
     * @param string|callable $cb Fonction de traitement de vérification.
     * @param mixed $value Valeur à vérifier.
     * @param array $args Liste des variables passées en argument.
     *
     * @return boolean
     */
    public function call($callback, $value, $args = [])
    {
        array_unshift($args, $value);

        if (is_string($callback)) :
            $callback = in_array($callback, $this->alias) ? Str::camel($callback) : $callback;
            if (method_exists(app('validator'), $callback)) :
                return call_user_func_array([app('validator'), $callback], $args);
            elseif (is_callable([$this, $callback])) :
                return call_user_func_array([$this, $callback], $args);
            elseif (function_exists($callback)) :
                return call_user_func_array($callback, $args);
            endif;
        elseif (is_callable($callback)) :
            return call_user_func_array($callback, $args);
        endif;

        return false;
    }

    /**
     * Méthode de controle par défaut.
     *
     * @param mixed $value Valeur à vérifier.
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