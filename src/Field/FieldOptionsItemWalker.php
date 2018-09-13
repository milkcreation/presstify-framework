<?php

namespace tiFy\Field;

use tiFy\Kernel\Walker\WalkerItemBaseController;

class FieldOptionsItemWalker extends WalkerItemBaseController
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

        if ($this->has('value')) :
            $this->set('attrs.value', $this->get('value'));
        endif;
    }
}