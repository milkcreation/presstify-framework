<?php

namespace tiFy\Layout\Share\ListTable\BulkAction;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\Layout\Share\ListTable\Contracts\BulkActionItemInterface;
use tiFy\Layout\Share\ListTable\Contracts\ListTableInterface;

class BulkActionItemController extends ParamsBag implements BulkActionItemInterface
{
    /**
     * Liste des attributs de configuration.
     * @return array
     */
    protected $attributes = [
        'value'   => null,
        'content' => '',
        'group'   => false,
        'attrs'   => [],
        'parent'  => ''
    ];

    /**
     * Instance de la disposition associée.
     * @var ListTableInterface
     */
    protected $layout;

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration personnalisés.
     * @param ListTableInterface $layout Instance de la disposition associée.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], ListTableInterface $layout)
    {
        $this->name = $name;
        $this->layout = $layout;

        parent::__construct($attrs);
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
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if ($this->get('value', null) === null) :
            $this->set('value', $this->name);
        endif;

        if (!$this->get('content')) :
            $this->set('content', $this->name);
        endif;
    }
}