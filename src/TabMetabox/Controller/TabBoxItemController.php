<?php

namespace tiFy\TabMetabox\Controller;

class TabBoxItemController extends AbstractTabItemController
{
    /**
     * Traitement de la liste des attributs de configuration.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     *
     *
     * }
     * @return array
     */
    protected function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->attributes = array_merge(
            [
                'name'          => md5("box-{$this->alias}-". $this->getIndex()),
                'title'         => '',
                'attrs'         => []
            ],
            $this->attributes
        );

        if (!$this->get('title')) :
            $this->set('title', $this->get('name'));
        endif;
    }
}