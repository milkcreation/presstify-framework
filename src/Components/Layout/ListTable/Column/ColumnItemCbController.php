<?php

namespace tiFy\Components\Layout\ListTable\Column;

use tiFy\Partial\Partial;

class ColumnItemCbController extends ColumnItemController
{
    static $counter = 1;

    /**
     * {@inheritdoc}
     */
    public function display($item)
    {
        return (($db = $this->app->db()) && ($primary = $db->getPrimary()) && isset($item->{$primary})) ? sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $primary, $item->{$primary}) : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($with_id = true)
    {
        $class = ['manage-column', "column-{$this->getName()}", 'check-column'];

        $attrs = [
            'tag' => 'td',
            'attrs'  => [
                'class' => join(' ', $class),
                'scope' => 'col'
            ],
            'content' => "<label class=\"screen-reader-text\" for=\"cb-select-all-" . self::$counter . "\">" .
                __( 'Select All' ) .
                "</label>" .
                "<input id=\"cb-select-all-" . self::$counter . "\" type=\"checkbox\" />"
        ];

        self::$counter++;

        if ($with_id) :
            $attrs['attrs']['id'] = $this->getName();
        endif;

        return (string)Partial::Tag($attrs);
    }
}