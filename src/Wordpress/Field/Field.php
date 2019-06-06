<?php

namespace tiFy\Wordpress\Field;

use tiFy\Contracts\Field\FieldManager;

class Field
{
    /**
     * Instance du gestionnaire des champs.
     * @var FieldManager
     */
    protected $manager;

    /**
     * CONSTRUCTEUR
     *
     * @param FieldManager $manager Instance du gestionnaire des champs.
     *
     * @return void
     */
    public function __construct(FieldManager $manager)
    {
        $this->manager = $manager;
    }
}