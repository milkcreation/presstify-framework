<?php

namespace tiFy\Field\SelectImage;

use tiFy\Contracts\View\ViewEngine;
use tiFy\Field\SelectJs\SelectJsChoice;

class SelectImageChoice extends SelectJsChoice
{
    /**
     * Instance du controleur de gestion des gabarits d'affichage.
     * @var ViewEngine
     */
    protected $viewer;

    /**
     * {@inheritdoc}
     */
    public function isGroup()
    {
        return false;
    }
}