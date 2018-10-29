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
    public function defaultsFieldOptions()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}