<?php

namespace tiFy\Field\SelectJs;

use tiFy\Contracts\Field\SelectJsChoice as SelectJsChoiceContract;
use tiFy\Contracts\View\ViewEngine;
use tiFy\Field\Select\SelectChoice;

class SelectJsChoice extends SelectChoice implements SelectJsChoiceContract
{
    /**
     * Instance du controleur de gestion des gabarits d'affichage.
     * @var ViewEngine
     */
    protected $viewer;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification de l'élément.
     * @param array|string $attrs Liste des attributs de configuration|Intitulé de qualification de l'option.
     * @param ViewEngine $viewer Instance du controleur de gestion des gabarits d'affichage.
     *
     * @return void
     */
    public function __construct($name, $attrs, ViewEngine $viewer)
    {
        $this->name = $name;
        $this->viewer = $viewer;

        parent::__construct($name, $attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function isGroup()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $picker = (string)$this->viewer->make('picker', $this->all());
        $selection = (string)$this->viewer->make('selection', $this->all());

        $this->set('picker', $picker);
        $this->set('selection', $selection);
    }
}