<?php

namespace tiFy\Form;

use tiFy\Contracts\Form\FormManager as FormManagerContract;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Form\Addons\AddonsController;
use tiFy\Form\Buttons\ButtonsController;
use tiFy\Form\Fields\FieldTypesController;
use tiFy\Form\Forms\FormBaseController;

final class FormManager implements FormManagerContract
{
    /**
     * Liste des formulaires déclarés.
     * @var FormFactory[]
     */
    protected $items = [];

    /**
     * Formulaire courant.
     * @var FormFactory
     */
    protected $current;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action(
            'init',
            function () {
                foreach (config('form', []) as $name => $attrs) :
                    $this->_register($name, $attrs);
                endforeach;
            },
            999999
        );

        add_action(
            'wp',
            function () {
                foreach($this->all() as $form) :
                    $current = $this->current($form);
                    $current->prepare();
                    $this->reset();
                endforeach;
            },
            999999
        );
    }

    /**
     * Déclaration d'un formulaire.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Attributs de configuration.
     *
     * @return void
     */
    private function _register($name, $attrs = [])
    {
        return $this->items[$name] = app('form.factory', [$name, $attrs]);
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

        if (!$form instanceof FormFactory) :
            return;
        endif;

        $this->current = $form;
        $this->current->onSetCurrent();

        return $this->current;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        return $this->items[$name] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function index($name)
    {
        $index = array_search($name, array_keys($this->items));

        return ($index !== false) ? $index : null;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        if ($this->current instanceof FormFactory) :
            $this->current->onResetCurrent();
        endif;

        $this->current = null;
    }

    /**
     * {@inheritdoc}
     */
    public function resourcesDir($path = '')
    {
        $path = $path ? '/' . ltrim($path, '/') : '';

        return (file_exists(__DIR__ . "/Resources{$path}"))
            ? __DIR__ . "/Resources{$path}"
            : '';
    }

    /**
     * {@inheritdoc}
     */
    public function resourcesUrl($path = '')
    {
        $cinfo = class_info($this);
        $path = $path ? '/' . ltrim($path, '/') : '';

        return (file_exists($cinfo->getDirname() . "/Resources{$path}"))
            ? $cinfo->getUrl() . "/Resources{$path}"
            : '';
    }
}