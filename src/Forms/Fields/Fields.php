<?php

namespace tiFy\Forms\Fields;

use tiFy\Apps\AppController;
use tiFy\Forms\Fields\Html\Html;
use tiFy\Forms\Fields\Recaptcha\Recaptcha;
use tiFy\Forms\Fields\SimpleCaptchaImage\SimpleCaptchaImage;

final class Fields extends AppController
{
    /**
     * Liste des types de champs prédéfinis.
     * @var array
     */
    protected $predefined = [
        'html'                 => Html::class,
        'recaptcha'            => Recaptcha::class,
        'simple-captcha-image' => SimpleCaptchaImage::class,
    ];

    /**
     * Liste des types champs déclarés.
     * @var array
     */
    protected $registered = [];

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot()
    {
        foreach ($this->predefined as $name => $classname) :
            $this->register($name, $classname);
        endforeach;

        do_action('tify_form_field_register', $this);
    }

    /**
     * Déclaration d'un type de champ.
     *
     * @param string $name Nom de qualification du type de champ.
     * @param string $classname Nom de la classe de rappel du type de champ.
     * @param array $attrs Liste des variables passés en argument dans le controleur de type de champ.
     *
     * @return array
     */
    public function register($name, $classname, $args = [])
    {
        if (in_array($name, array_keys($this->registered))) :
            return;
        endif;

        if (! class_exists($classname)) :
            return;
        endif;

        return $this->registered[$name] = [
            'controller' => $classname,
            'args'       => $args
        ];
    }

    /**
     * Définition d'un champ pour un formulaire.
     *
     * @param string $name Nom de qualification du type de champ.
     * @param Form $form Classe de rappel du formulaire.
     * @param array $attrs Liste des attributs de configuration du champ.
     *
     * @return FieldControllerInterface
     */
    public function set($name, $form, $attrs = [])
    {
        if (! isset($this->registered[$name])) :
            return;
        endif;

        $classname = $this->registered[$name]['controller'];

        $instance = new $classname($this->registered[$name]['args']);
        $instance->make($name, $form, $attrs);

        return $instance;
    }
}