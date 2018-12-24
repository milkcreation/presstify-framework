<?php

namespace tiFy\View\Pattern;

use tiFy\Contracts\View\ViewPatternController;
use tiFy\Kernel\Notices\Notices;

class PatternBaseNotices extends Notices
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