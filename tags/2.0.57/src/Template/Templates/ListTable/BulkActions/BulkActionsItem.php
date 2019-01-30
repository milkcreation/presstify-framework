<?php

namespace tiFy\Template\Templates\ListTable\BulkActions;

use tiFy\Field\Fields\Select\SelectChoice;
use tiFy\Template\Templates\ListTable\Contracts\BulkActionsItem as BulkActionsItemContract;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;

class BulkActionsItem extends SelectChoice implements BulkActionsItemContract
{
    /**
     * Instance du motif d'affichage associé.
     * @var ListTable
     */
    protected $template;

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
     * @param ListTable $template Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct($name, $attrs, ListTable $template)
    {
        $this->template = $template;

        parent::__construct($name, $attrs);
    }
}