<?php

namespace tiFy\Form;

use tiFy\Contracts\Form\AddonController as AddonControllerInterface;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Form\Factory\ResolverTrait;
use tiFy\Kernel\Parameters\ParamsBagController;

class AddonController extends ParamsBagController implements AddonControllerInterface
{
    use ResolverTrait;

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name;

    /**
     * CONSTRUCTEUR.
     *
     * @param $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     * @param FormFactory $form Formulaire associé.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], FormFactory $form)
    {
        $this->name = $name;
        $this->form = $form;

        parent::__construct($attrs);

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