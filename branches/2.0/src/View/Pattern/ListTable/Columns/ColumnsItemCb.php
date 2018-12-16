<?php

namespace tiFy\View\Pattern\ListTable\Columns;

use tiFy\Kernel\Tools;

class ColumnsItemCb extends ColumnsItem
{
    static $headerIndex = 0;

    /**
     * {@inheritdoc}
     */
    public function header($with_id = true)
    {
        $classes = ['manage-column', "column-{$this->getName()}", 'check-column'];

        if ($this->isHidden()) :
            $classes[] = 'hidden';
        endif;

        $attrs = [];
        if ($with_id) :
            $attrs['id'] = $this->getName();
        endif;

        $attrs['class'] = join(' ', $classes);

        return $this->pattern->viewer(
            'thead-col_cb',
            [
                'attrs' => Tools::Html()->parseAttrs($attrs),
                'index' => ++self::$headerIndex
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isPrimary()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->pull('attrs.data-colname');

        $this->set('attrs.class', 'check-column');

        $this->set('attrs.scope', 'row');
    }
}