<?php

namespace tiFy\View\Pattern;

use tiFy\Contracts\View\ViewPatternController;

class PatternBaseAssets
{
    /**
     * Instance de la disposition.
     * @var ViewPatternController
     */
    protected $pattern;

    /**
     * CONSTRUCTEUR.
     *
     * @param ViewPatternController $pattern Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct(ViewPatternController $pattern)
    {
        $this->pattern = $pattern;
    }
}