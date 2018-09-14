<?php

namespace tiFy\Partial\Sidebar;

use tiFy\Kernel\Item\AbstractItemIterator;
use tiFy\Kernel\Tools;

class SidebarItem extends AbstractItemIterator
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'name'      => $this->name,
            'attrs'     => [],
            'content'   => '',
            'position'  => 0
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set(
            'attrs.class',
            trim(
                sprintf(
                    'tiFyPartial-SidebarItem %s',
                    $this->get('attrs.class', '')
                        ?
                        : ''
                )
            )
        );
    }

    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString()
    {
        $content = $this->get('content', '');

        return (Tools::Functions()->isCallable($content)) ? call_user_func($content) : $content;
    }
}