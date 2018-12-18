<?php

namespace tiFy\View\Pattern;

use tiFy\Contracts\View\ViewPatternController;
use tiFy\Kernel\Params\ParamsBag;

class PatternBaseParams extends ParamsBag
{
    /**
     * Instance de la disposition.
     * @var ViewPatternController
     */
    protected $pattern;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs Liste des attributs de configuration.
     * @param ViewPatternController $pattern Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct($attrs, ViewPatternController $pattern)
    {
        $this->pattern = $pattern;

        parent::__construct($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'singular' => $this->pattern->label('singular') ? : $this->pattern->name(),
            'plural'   => $this->pattern->label('plural') ? : $this->pattern->name(),
        ];
    }
}