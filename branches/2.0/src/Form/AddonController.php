<?php

namespace tiFy\Form;

use tiFy\Contracts\Form\Addon as AddonInterface;
use tiFy\Contracts\Form\FormFactory as FormFactoryInterface;
use tiFy\Form\Factory\ResolverTrait;

abstract class AddonController implements AddonInterface
{
    use ResolverTrait;

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name;

    /**
     * Liste des options par défaut du formulaire associé.
     * @var array
     */
    protected $defaultFormOptions = [];

    /**
     * Liste des options par défaut des champs du formulaire associé.
     * @var array
     */
    protected $defaultFieldOptions = [];

    /**
     * Liste des méthodes de rappel de court-circuitage.
     * @var array
     */
    protected $callbacks = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     * @param FormFactoryInterface $form Formulaire associé.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], FormFactoryInterface $form)
    {
        $this->name = $name;
        $this->form = $form;

        $this->parseCallbacks();
        $this->parseDefaultFormOptions();

        $this->boot();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getFieldOption($field, $key, $default = '')
    {
        return $field->getAddonOption($this->getName(), $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormOption($key, $default = null)
    {
        return $this->getForm()->getAddonOption($this->getName(), $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function parseCallbacks()
    {
        foreach ($this->callbacks as $hookname => $args) :
            if (is_callable($args)) :
                $this->getForm()
                    ->callbacks()
                    ->set(
                        $hookname,
                        $args
                    );
            elseif (isset($args['function']) && is_callable($args['function'])) :
                $args = array_merge(
                    ['order' => 10],
                    $args
                );
                $this->getForm()
                    ->callbacks()
                    ->set(
                        $hookname,
                        $args['function'],
                        $args['order']
                    );
            endif;
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function parseDefaultFieldOptions($field)
    {
        return $field->parseDefaultAddonOptions($this->getName(), $this->defaultFieldOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function parseDefaultFormOptions()
    {
        return $this->form()->parseDefaultAddonOptions($this->getName(), $this->defaultFormOptions);
    }
}