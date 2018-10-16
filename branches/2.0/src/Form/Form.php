<?php

namespace tiFy\Form;

use tiFy\Contracts\Form\Form as FormInterface;
use tiFy\Contracts\Form\FormItem as FormItemInterface;
use tiFy\Form\Addons\AddonsController;
use tiFy\Form\Buttons\ButtonsController;
use tiFy\Form\Fields\FieldTypesController;
use tiFy\Form\Forms\FormBaseController;

final class Form implements FormInterface
{
    /**
     * Liste des formulaires déclarés.
     * @var FormItemInterface[]
     */
    protected $items = [];

    /**
     * Formulaire courant.
     * @var FormItemInterface
     */
    protected $current;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        if (is_admin()) :
            add_action('admin_init', function () { $this->_init(); }, 999999);
        else :
            add_action('wp', function () { $this->_init(); }, 999999);
        endif;
    }

    /**
     * Initialisation des formulaires.
     *
     * @return void
     */
    private function _init()
    {
        if ($forms = config('form', [])) :
            foreach ($forms as $name => $attrs) :
                $this->_register($name, $attrs);
            endforeach;

            do_action('tify_form_loaded');
        endif;
    }

    /**
     * Déclaration d'un formulaire.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Attributs de configuration.
     *
     * @return void
     */
    public function _register($name, $attrs = [])
    {
        $controller = (isset($attrs['controller'])) ? $attrs['controller'] : FormBaseController::class;

        $resolved = new $controller($name, $attrs);

        return $this->items[$name] = app()
            ->bind(
                "form.{$name}",
                function () use ($resolved) {
                    return $resolved;
                }
            )
            ->build();
    }

    /**
     * {@inheritdoc}
     */
    public function add($name, $attrs = [])
    {
        config()->set(
            'form',
            array_merge(
                [$name => $attrs],
                config('form', [])
            )
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function current($form = null)
    {
        if (is_null($form)) :
            return $this->current;
        endif;

        if (is_string($form)) :
            $form = $this->get($form);
        endif;

        if (!$form instanceof FormItemInterface) :
            return;
        endif;

        $this->current = $form;
        $this->current->getForm()->onSetCurrent();

        return $this->current;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        return isset($this->items[$name]) ? $this->items[$name] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        if ($this->current instanceof FormItemInterface) :
            $this->current->getForm()->onResetCurrent();
        endif;

        $this->current = null;
    }
}