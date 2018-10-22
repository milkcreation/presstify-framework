<?php

namespace tiFy\Form;

use tiFy\Contracts\Form\Manager as ManagerInterface;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Form\Addons\AddonsController;
use tiFy\Form\Buttons\ButtonsController;
use tiFy\Form\Fields\FieldTypesController;
use tiFy\Form\Forms\FormBaseController;

final class Manager implements ManagerInterface
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

            foreach($this->all() as $form) :
                $current = $this->current($form);
                $current->events('request.handle');
                $this->reset();
            endforeach;
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
        return isset($this->items[$name]) ? $this->items[$name] : null;
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