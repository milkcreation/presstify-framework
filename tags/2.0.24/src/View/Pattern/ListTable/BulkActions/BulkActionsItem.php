<?php

namespace tiFy\View\Pattern\ListTable\BulkActions;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\View\Pattern\ListTable\Contracts\BulkActionsItem as BulkActionsItemContract;
use tiFy\View\Pattern\ListTable\Contracts\ListTable;

class BulkActionsItem extends ParamsBag implements BulkActionsItemContract
{
    /**
     * Instance du motif d'affichage associé.
     * @var ListTable
     */
    protected $pattern;

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
     * @param ListTable $pattern Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct($name, $attrs, ListTable $pattern)
    {
        $this->name = $name;
        $this->pattern = $pattern;

        parent::__construct($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'value'   => $this->getName(),
            'content' => $this->getName(),
            'group'   => false,
            'attrs'   => [],
            'parent'  => ''
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}