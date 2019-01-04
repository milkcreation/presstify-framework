<?php

namespace tiFy\View\Pattern;

use tiFy\Kernel\Labels\LabelsBag;
use tiFy\Contracts\View\ViewPatternController;

class PatternBaseLabels extends LabelsBag
{
    /**
     * Instance de la disposition.
     * @var ViewPatternController
     */
    protected $pattern;

    /**
     * CONSTRUCTEUR.
     *
     * @param string Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     * @param ViewPatternController $pattern Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct($name, $attrs, ViewPatternController $pattern)
    {
        $this->pattern = $pattern;

        parent::__construct($name, $attrs);
    }
}