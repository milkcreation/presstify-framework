<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\BulkActions;

use tiFy\Field\Fields\Select\SelectChoice;
use tiFy\Template\Templates\ListTable\Contracts\BulkActionsItem as BulkActionsItemContract;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;

class BulkActionsItem extends SelectChoice implements BulkActionsItemContract
{
    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

    /**
     * CONSTRUCTEUR.
     *
     * @param string|int $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration personnalisés.
     * @param ListTable $factory Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct($name, array $attrs, ListTable $factory)
    {
        $this->factory = $factory;

        parent::__construct($name, $attrs);
    }
}