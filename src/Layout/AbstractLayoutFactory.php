<?php

namespace tiFy\Layout;

use tiFy\Contracts\Layout\LayoutFactoryInterface;
use tiFy\Contracts\Layout\LayoutDisplayInterface;
use tiFy\Kernel\Parameters\AbstractParametersBag;

abstract class AbstractLayoutFactory extends AbstractParametersBag implements LayoutFactoryInterface
{
    /**
     * Nom de qualification de la disposition associée.
     * @var string
     */
    protected $name = '';

    /**
     * Instance de la disposition associée.
     * @var LayoutDisplayInterface
     */
    protected $layout;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification de la disposition associée.
     * @param array $attrs Attributs de configuration de la disposition associée.
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        $this->name = $name;

        parent::__construct($attrs);

        if ($controller = $this->get('content')) :
            $this->layout = new $controller($this);
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnv()
    {
        return $this->isAdmin() ? 'admin' : 'user';
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
    public function isAdmin()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function layout()
    {
        return $this->layout;
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {

    }
}