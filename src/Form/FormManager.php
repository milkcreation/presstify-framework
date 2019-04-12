<?php

namespace tiFy\Form;

use Closure;
use tiFy\Contracts\Form\FormManager as FormManagerContract;
use tiFy\Contracts\Form\FormFactory;

class FormManager implements FormManagerContract
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
     * @inheritdoc
     */
    public function addonRegister($name, $controller)
    {
        app()->add("form.addon.{$name}", function ($name, $attrs, FormFactory $form) use ($controller) {
            if (is_object($controller) || $controller instanceof Closure) :
                return call_user_func_array($controller, [$name, $attrs, $form]);
            elseif (class_exists($controller)) :
                return new $controller($name, $attrs, $form);
            else :
                return app()->get('form.addon', [$name, $attrs, $form]);
            endif;
        });

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * @inheritdoc
     */
    public function buttonRegister($name, $controller)
    {
        app()->add("form.button.{$name}", function ($name, $attrs, FormFactory $form) use ($controller) {
            if (is_object($controller) || $controller instanceof Closure) :
                return call_user_func_array($controller, [$name, $attrs, $form]);
            elseif (class_exists($controller)) :
                return new $controller($name, $attrs, $form);
            else :
                return app()->get('form.button', [$name, $attrs, $form]);
            endif;
        });

        return $this;
    }

    /**
     * @inheritdoc
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
            return null;
        endif;

        $this->current = $form;

        $this->current->onSetCurrent();

        return $this->current;
    }

    /**
     * @inheritdoc
     */
    public function fieldRegister($name, $controller)
    {
        app()->add("form.field.{$name}", function ($name, $attrs, FormFactory $form) use ($controller) {
            if (is_object($controller) || $controller instanceof Closure) :
                return call_user_func_array($controller, [$name, $attrs, $form]);
            elseif (class_exists($controller)) :
                return new $controller($name, $attrs, $form);
            else :
                return app()->get('form.field', [$name, $attrs, $form]);
            endif;
        });

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function get($name)
    {
        return $this->items[$name] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function index($name)
    {
        $index = array_search($name, array_keys($this->items));

        return ($index !== false) ? $index : null;
    }

    /**
     * @inheritdoc
     */
    public function register($name, $attrs = [])
    {
        $controller = $attrs['controller'] ?? null;

        return $this->set(
            $name,
            ($controller ? new $controller($name, $attrs) : app()->get('form.factory', [$name, $attrs]))
        );
    }

    /**
     * @inheritdoc
     */
    public function reset()
    {
        if ($this->current instanceof FormFactory) :
            $this->current->onResetCurrent();
        endif;

        $this->current = null;
    }

    /**
     * @inheritdoc
     */
    public function resourcesDir($path = '')
    {
        $path = $path ? '/' . ltrim($path, '/') : '';

        return (file_exists(__DIR__ . "/Resources{$path}"))
            ? __DIR__ . "/Resources{$path}"
            : '';
    }

    /**
     * @inheritdoc
     */
    public function resourcesUrl($path = '')
    {
        $cinfo = class_info($this);
        $path = $path ? '/' . ltrim($path, '/') : '';

        return (file_exists($cinfo->getDirname() . "/Resources{$path}"))
            ? $cinfo->getUrl() . "/Resources{$path}"
            : '';
    }

    /**
     * @inheritdoc
     */
    public function set($name, FormFactory $form)
    {
        return $this->items[$name] = $form;
    }
}