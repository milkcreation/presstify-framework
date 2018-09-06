<?php

namespace tiFy\Kernel\Templates;

use Illuminate\Support\Arr;
use League\Plates\Template\Template;
use tiFy\Kernel\Tools;

class TemplateController extends Template implements TemplateInterface
{
    /**
     * Instance of the template engine.
     * @var Engine
     */
    protected $engine;

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = '')
    {
        return Arr::get($this->data, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return Arr::has($this->data, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function htmlAttrs($attrs, $linearized = true)
    {
        return Tools::Html()->parseAttrs($attrs, $linearized);
    }

    /**
     * {@inheritdoc}
     */
    public function reset($name)
    {
        $this->start($name); $this->stop();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function share($datas)
    {
        return $this->engine->addData($datas);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        return Arr::set($this->data, $key, $value);
    }
}