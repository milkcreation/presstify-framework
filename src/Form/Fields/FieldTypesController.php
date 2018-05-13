<?php

namespace tiFy\Form\Fields;

use Illuminate\Support\Str;
use tiFy\Apps\AppController;
use tiFy\Components\Forms\FieldTypes\Html\Html;
use tiFy\Components\Forms\FieldTypes\Native\Native;
use tiFy\Components\Forms\FieldTypes\Recaptcha\Recaptcha;
use tiFy\Components\Forms\FieldTypes\SimpleCaptchaImage\SimpleCaptchaImage;
use tiFy\Form\Fields\FieldItemController;

final class FieldTypesController extends AppController
{
    /**
     * Liste des types de champs prédéfinis.
     * @var array
     */
    protected $predefined = [
        'Html'               => Html::class,
        'Recaptcha'          => Recaptcha::class,
        'SimpleCaptchaImage' => SimpleCaptchaImage::class,
    ];

    /**
     * Liste de champs natifs.
     * @var array
     */
    public $natives = [
        'Button'       => ['request', 'wrapper'],
        'Checkbox'     => ['label', 'request', 'wrapper'],
        'DatetimeJs'   => ['label', 'request', 'wrapper'],
        'File'         => ['label', 'request', 'wrapper'],
        'Hidden'       => ['request'],
        'Label'        => ['wrapper'],
        'Number'       => ['label', 'request', 'wrapper'],
        'NumberJs'     => ['label', 'request', 'wrapper'],
        'Password'     => ['label', 'request', 'wrapper'],
        'Radio'        => ['label', 'request', 'wrapper'],
        'Repeater'     => ['label', 'request', 'wrapper'],
        'Select'       => ['label', 'request', 'wrapper'],
        'SelectJs'     => ['label', 'request', 'wrapper'],
        'Submit'       => ['request', 'wrapper'],
        'Text'         => ['label', 'request', 'wrapper'],
        'Textarea'     => ['label', 'request', 'wrapper'],
        'ToggleSwitch' => ['request', 'wrapper'],
    ];

    /**
     * Liste des alias de champs natifs.
     * @var array
     */
    public $alias = [
        'input' => 'Text',
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
    public function appBoot()
    {
        foreach ($this->predefined as $name => $classname) :
            $this->register($name, $classname);
        endforeach;

        foreach ($this->natives as $name => $support) :
            $this->register($name, Native::class, ["$name", $support]);
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

        if (!class_exists($classname)) :
            return;
        endif;

        $this->appServiceAdd("tfy.form.field_type.{$name}", $classname)
            ->withArguments($args);

        return $this->registered[$name] = $args;
    }

    /**
     * Définition d'un champ pour un formulaire.
     *
     * @param string $name Nom de qualification du type de champ.
     * @param FieldItemController $field Classe de rappel du champ.
     *
     * @return FieldTypeControllerInterface
     */
    public function set($name, $field)
    {
        if (isset($this->alias[$name])) :
            $name = $this->alias[$name];
        endif;

        $name = Str::studly($name);

        if (!isset($this->registered[$name])) :
            \wp_die(
                sprintf(__('Le type de champ "%s" n\'est pas valide', 'tify'), $name),
                __('Type de champ invalide', 'tify'),
                500
            );
        endif;

        $instance = $this->appServiceGet("tfy.form.field_type.{$name}");
        $instance->make($name, $field);

        return $instance;
    }
}