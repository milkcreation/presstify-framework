<?php

namespace tiFy\Field;

use Illuminate\Support\Fluent;
use Illuminate\Support\Arr;
use tiFy\Components\Tools\Walkers\WalkerItemBaseController;
use tiFy\Kernel\Tools;

class WalkerOptionsItem extends WalkerItemBaseController
{
    /**
     * Traitement de la liste des attributs de balise HTML.
     *
     * @return array
     */
    public function parseHtmlAttrs()
    {
        if (empty($this->get('attrs.class', ''))) :
            $this->set('attrs.class', "{$this->walker->getOption('prefix')}Item {$this->walker->getOption('prefix')}Item--{$this->getName()}");
        endif;

        if ($this->has('attrs.value')) :
            $this->set('attrs.value', $this->get('value'));
        endif;
    }
}