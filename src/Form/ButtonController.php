<?php

namespace tiFy\Form;

use tiFy\Contracts\Form\ButtonController as ButtonControllerInterface;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Form\Factory\ResolverTrait;
use tiFy\Kernel\Parameters\ParamsBagController;

class ButtonController extends ParamsBagController implements ButtonControllerInterface
{
    use ResolverTrait;

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name;

    /**
     * Attributs de configuration.
     * @var array
     */
    protected $attributes = [
        'label'           => '',
        'before'          => '',
        'after'           => '',
        'wrapper'         => true,
        'attrs'           => [],
        'type'            => '',
        'position'        => 0
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     * @param FormFactory $form Instance du contrôleur de formulaire associé.
     *
     * @void
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
    public function __toString()
    {
        return (string)$this->render();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->get('position', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return field(
            'button',
            $this->all()
        );
    }
}