<?php

namespace tiFy\Form\Factory;

use Illuminate\Support\Str;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Contracts\Form\FactoryValidation;

class Validation implements FactoryValidation
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
     * @inheritdoc
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
     * @inheritdoc
     */
    public function __return_true($value)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function compare($value, $tags, $raw = true)
    {
        $value2 = $this->fieldTagValue($tags, $raw);

        if ($value !== $value2) :
            return false;
        endif;

        return true;
    }
}