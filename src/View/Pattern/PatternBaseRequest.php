<?php

namespace tiFy\View\Pattern;

use tiFy\Contracts\View\ViewPatternController;
use tiFy\Kernel\Http\Request;

class PatternBaseRequest extends Request
{
    /**
     * Instance de la disposition.
     * @var ViewPatternController
     */
    protected $pattern;

    /**
     * Définition de l'instance du controleur de motif d'affichage.
     *
     * @param ViewPatternController $pattern Instance du controleur de motif d'affichage.
     *
     * @return $this
     */
    public function setPattern(ViewPatternController $pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }
}