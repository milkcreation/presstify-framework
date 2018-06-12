<?php

namespace tiFy\Partial;

use Illuminate\Support\Arr;
use League\Plates\Engine;
use tiFy\Kernel\Templates\TemplateBaseController;

class TemplateController extends TemplateBaseController
{
    /**
     * CONSTRUCTEUR.
     *
     * @param Engine $engine
     * @param string $name
     * @param array $args Liste des variables passées en argument
     *
     * @return void
     */
    public function __construct(Engine $engine, $name, $args = [])
    {
        $this->args = $args;

        parent::__construct($engine, $name, $args);
    }

    /**
     * Récupération de l'identifiant de qualification du controleur.
     *
     * @return string
     */
    public function getId()
    {
        return $this->getArg('id');
    }

    /**
     * Récupération de l'indice de la classe courante.
     *
     * @return int
     */
    public function getIndex()
    {
        return $this->getArg('index', 0);
    }
}