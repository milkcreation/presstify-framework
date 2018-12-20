<?php

namespace tiFy\View\Pattern\ListTable\BulkActions;

use tiFy\Field\Select\SelectChoice;
use tiFy\View\Pattern\ListTable\Contracts\BulkActionsItem as BulkActionsItemContract;
use tiFy\View\Pattern\ListTable\Contracts\ListTable;

class BulkActionsItem extends SelectChoice implements BulkActionsItemContract
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
        $this->pattern = $pattern;

        parent::__construct($name, $attrs);
    }
}